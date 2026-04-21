¡Hola, {{ $renta->nombre_completo }}!

Hemos recibido tu solicitud de renta correctamente. En breve uno de nuestros agentes se pondrá en contacto contigo para confirmar los detalles.

========================================
VEHÍCULO SOLICITADO
========================================
{{ $renta->vehicle->name ?? 'Vehículo' }}
@if($renta->vehicle){{ $renta->vehicle->brand }} {{ $renta->vehicle->model }}@if($renta->vehicle->year) · {{ $renta->vehicle->year }}@endif — o similar
@endif

========================================
DETALLES DE LA RENTA
========================================
Fecha de entrega:     {{ \Carbon\Carbon::parse($renta->fecha_entrega)->format('d/m/Y') }} · {{ substr($renta->hora_entrega, 0, 5) }}
Lugar de entrega:     {{ $renta->lugar_entrega }}
Fecha de devolución:  {{ \Carbon\Carbon::parse($renta->fecha_devolucion)->format('d/m/Y') }} · {{ substr($renta->hora_devolucion, 0, 5) }}
Lugar de devolución:  {{ $renta->lugar_devolucion }}
Pasajeros:            {{ $renta->num_pasajeros }}
Total de días:        {{ $renta->total_dias }} día{{ $renta->total_dias > 1 ? 's' : '' }}
Costo estimado:       ${{ number_format($renta->costo_total, 2) }} MXN

========================================
AVISO IMPORTANTE
========================================
Esta es una confirmación de solicitud, no una reserva definitiva.
Un asesor de Flash Car te contactará en las próximas horas para
confirmar disponibilidad y coordinar los detalles finales.

========================================
¿Tienes dudas? Escríbenos a flashcar@rentadeautos.site
© {{ date('Y') }} Flash Car · Querétaro, México
