{{--
    Tarjeta de "Resumen de Costo" con edición rápida (lápiz → input → PATCH rentas.campoRapido).
    Parámetros: $campo (columna de rentas, ver RentaController::CAMPOS_RAPIDOS), $titulo,
    $valor (monto a mostrar, puede venir de otro lado), $valorEditar (valor inicial del input),
    $nota (texto chico bajo el monto, opcional), $vacio (texto si $valor es null).
--}}
@php $editando = $errors->has($campo); @endphp
<div class="col-12 col-sm-12 col-md-4 col-lg-4 p-1" data-tarjeta-editable>
<div class="h-100 rounded p-3 text-center position-relative" style="background:#f5f6f8; border:1px solid #e0e3e8;">
    <button type="button" class="boton_editar_rapido position-absolute top-0 end-0 m-1 {{ $editando ? 'd-none' : '' }}" data-editar title="Editar {{ mb_strtolower($titulo) }}">
        <i class="bi bi-pencil-fill"></i>
    </button>
    <p class="text-muted fs-6 mb-1">{{ $titulo }}</p>
    <div class="{{ $editando ? 'd-none' : '' }}" data-vista>
        @if($valor !== null)
            <b class="fs-4" style="color:var(--primary);">${{ number_format($valor, 2) }}</b>
            @if(!empty($nota))
                <p class="mb-0 mt-1" style="font-size:0.72rem; color:#6b7280;">{{ $nota }}</p>
            @endif
        @else
            <b class="fs-6" style="color:#9ca3af;">{{ $vacio ?? 'No especificado' }}</b>
        @endif
    </div>
    <form method="POST" action="{{ route('rentas.campoRapido', [$renta->id, $campo]) }}" class="{{ $editando ? '' : 'd-none' }}" data-form>
        @csrf
        @method('PATCH')
        <input class="input_form_f_b fs-6 p-1 w-100 text-center" type="number" step="0.01" min="0" name="{{ $campo }}"
               value="{{ old($campo, $valorEditar) }}" placeholder="0.00" data-input>
        @error($campo)
            <small class="text-danger d-block">{{ $message }}</small>
        @enderror
        <div class="d-flex justify-content-center mt-2">
            <button type="submit" class="boton_link_sm b_sm rounded border-0 me-1" title="Guardar"><i class="bi bi-check-lg"></i></button>
            <button type="button" class="boton_link_sm b_sm rounded border-0 bg-secondary text-white" data-cancelar title="Cancelar"><i class="bi bi-x-lg"></i></button>
        </div>
    </form>
</div>
</div>
