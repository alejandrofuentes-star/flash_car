{{--
    Selector del motivo de cancelación. Se incluye dentro del mismo <form> que el select name="estado";
    solo se muestra (y es obligatorio) cuando el estado elegido es "cancelada".
    "Falta de disponibilidad" cuenta como demanda no atendida en Reportes.
--}}
@php
    $motivoActual = old('motivo_cancelacion', $renta->motivo_cancelacion);
    $esCancelada  = old('estado', $renta->estado) === 'cancelada';
@endphp
<div class="col-12 bloque_motivo_cancelacion" @unless($esCancelada) style="display:none;" @endunless>
    <label class="label_form_f_b fs-6 p-1"><b>Motivo de cancelación *</b></label>
    <select class="input_form_f_b fs-6 p-1 select_motivo_cancelacion" name="motivo_cancelacion" @if($esCancelada) required @endif>
        <option value="">Selecciona un motivo</option>
        @foreach(\App\Models\Renta::MOTIVOS_CANCELACION as $clave => $etiqueta)
            <option value="{{ $clave }}" {{ $motivoActual === $clave ? 'selected' : '' }}>{{ $etiqueta }}</option>
        @endforeach
    </select>
    @error('motivo_cancelacion')
        <span class="text-danger d-block px-1" style="font-size:0.8rem;">{{ $message }}</span>
    @enderror
</div>
@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.bloque_motivo_cancelacion').forEach(function (bloque) {
                const form   = bloque.closest('form');
                const estado = form ? form.querySelector('select[name="estado"]') : null;
                const motivo = bloque.querySelector('.select_motivo_cancelacion');
                if (!estado || !motivo) return;

                function actualizar() {
                    const cancelada = estado.value === 'cancelada';
                    bloque.style.display = cancelada ? '' : 'none';
                    motivo.required = cancelada;
                }
                estado.addEventListener('change', actualizar);
                actualizar();
            });
        });
    </script>
@endonce
