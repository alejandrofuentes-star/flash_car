<?php

namespace App\Http\Controllers;

use App\Mail\NuevaRentaAdmin;
use App\Mail\RentaSolicitada;
use App\Models\Renta;
use App\Models\SiteSetting;
use App\Models\SiteStat;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            \Log::warning('Stripe webhook: firma inválida — ' . $e->getMessage());
            return response()->json(['error' => 'Firma inválida'], 400);
        } catch (\UnexpectedValueException $e) {
            \Log::warning('Stripe webhook: payload inválido — ' . $e->getMessage());
            return response()->json(['error' => 'Payload inválido'], 400);
        }

        $intent = $event->data->object;

        match ($event->type) {
            'payment_intent.succeeded'       => $this->onPaymentSucceeded($intent),
            'payment_intent.payment_failed'  => $this->onPaymentFailed($intent),
            'charge.refunded'                => $this->onChargeRefunded($intent),
            default                          => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function onPaymentSucceeded($intent): void
    {
        $renta = Renta::where('payment_intent_id', $intent->id)->first();

        if ($renta) {
            // Confirmar que el registro quedó bien marcado
            if (!$renta->payment_intent_id) {
                $renta->update(['payment_intent_id' => $intent->id, 'metodo_pago' => 'tarjeta']);
            }
            return;
        }

        // Pago cobrado sin renta registrada (browser cerró antes de enviar el formulario)
        $renta = $this->crearRentaDesdeMetadata($intent);

        if (!$renta) {
            \Log::warning('Stripe webhook: pago sin renta registrada y sin datos suficientes en metadata para recuperarla', [
                'payment_intent_id' => $intent->id,
                'amount'            => ($intent->amount ?? 0) / 100,
                'currency'          => $intent->currency ?? 'mxn',
                'nombre'            => $intent->metadata->nombre_completo ?? '—',
            ]);
        }
    }

    /**
     * Reconstruye la renta a partir de la metadata guardada en el PaymentIntent
     * (ver RentaController::createPaymentIntent) cuando el cliente pagó pero su
     * navegador nunca llegó a completar el submit del formulario hacia store().
     */
    private function crearRentaDesdeMetadata($intent): ?Renta
    {
        $meta = $intent->metadata ?? null;
        if (!$meta) return null;

        $requeridos = [
            'nombre_completo', 'telefono', 'correo', 'ciudad',
            'fecha_entrega', 'hora_entrega', 'lugar_entrega',
            'fecha_devolucion', 'hora_devolucion', 'lugar_devolucion',
            'num_pasajeros', 'total_dias', 'costo_total',
        ];
        foreach ($requeridos as $campo) {
            if (empty($meta->{$campo})) return null;
        }

        $vehicleId = $meta->vehicle_id ?? null;
        if (!$vehicleId && !empty($meta->category_id)) {
            $vehicleId = Vehicle::where('category_id', $meta->category_id)
                ->where('available', 1)
                ->where('active', 1)
                ->value('id');
        }
        if (!$vehicleId) return null;

        try {
            $renta = Renta::create([
                'vehicle_id'        => $vehicleId,
                'nombre_completo'   => $meta->nombre_completo,
                'telefono'          => $meta->telefono,
                'correo'            => $meta->correo,
                'ciudad'            => $meta->ciudad,
                'fecha_entrega'     => $meta->fecha_entrega,
                'hora_entrega'      => $meta->hora_entrega,
                'lugar_entrega'     => $meta->lugar_entrega,
                'fecha_devolucion'  => $meta->fecha_devolucion,
                'hora_devolucion'   => $meta->hora_devolucion,
                'lugar_devolucion'  => $meta->lugar_devolucion,
                'num_pasajeros'     => (int) $meta->num_pasajeros,
                'total_dias'        => (int) $meta->total_dias,
                'costo_total'       => (float) $meta->costo_total,
                'metodo_pago'       => 'tarjeta',
                'payment_intent_id' => $intent->id,
                'monto_anticipo'    => ($intent->amount ?? 0) / 100,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Stripe webhook: fallo al recuperar renta desde metadata', [
                'payment_intent_id' => $intent->id,
                'error'             => $e->getMessage(),
            ]);
            return null;
        }

        \Log::info('Stripe webhook: renta #' . $renta->id . ' creada automáticamente desde metadata (formulario incompleto)', [
            'payment_intent_id' => $intent->id,
        ]);

        $renta->load('vehicle');
        SiteStat::addOne('total_reservations');

        try {
            Mail::to($renta->correo)->send(new RentaSolicitada($renta));
            $renta->update(['mail_enviado' => true, 'mail_enviado_at' => now()]);
        } catch (\Throwable $e) {
            \Log::error('Stripe webhook: error enviando correo de renta recuperada #' . $renta->id . ': ' . $e->getMessage());
        }

        try {
            Mail::to(SiteSetting::get('admin_notification_email', 'flashcarental@gmail.com'))->send(new NuevaRentaAdmin($renta));
        } catch (\Throwable $e) {
            \Log::error('Stripe webhook: error enviando correo admin de renta recuperada #' . $renta->id . ': ' . $e->getMessage());
        }

        return $renta;
    }

    private function onPaymentFailed($intent): void
    {
        $error = $intent->last_payment_error?->message ?? 'Error desconocido';

        \Log::error('Stripe webhook: pago fallido', [
            'payment_intent_id' => $intent->id,
            'error'             => $error,
            'nombre'            => $intent->metadata->nombre_completo ?? '—',
        ]);
    }

    private function onChargeRefunded($charge): void
    {
        // Un cargo fue reembolsado — buscar la renta por payment_intent y marcarla cancelada
        $intentId = $charge->payment_intent ?? null;
        if (!$intentId) return;

        $renta = Renta::where('payment_intent_id', $intentId)->first();
        if ($renta && $renta->estado !== 'cancelada') {
            $renta->update(['estado' => 'cancelada']);
            \Log::info('Stripe webhook: renta #' . $renta->id . ' cancelada por reembolso (' . $intentId . ')');
        }
    }
}
