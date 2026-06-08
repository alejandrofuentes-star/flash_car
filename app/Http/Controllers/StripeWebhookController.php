<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Illuminate\Http\Request;
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
        \Log::warning('Stripe webhook: pago sin renta registrada', [
            'payment_intent_id' => $intent->id,
            'amount'            => ($intent->amount ?? 0) / 100,
            'currency'          => $intent->currency ?? 'mxn',
            'nombre'            => $intent->metadata->nombre ?? '—',
        ]);
    }

    private function onPaymentFailed($intent): void
    {
        $error = $intent->last_payment_error?->message ?? 'Error desconocido';

        \Log::error('Stripe webhook: pago fallido', [
            'payment_intent_id' => $intent->id,
            'error'             => $error,
            'nombre'            => $intent->metadata->nombre ?? '—',
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
