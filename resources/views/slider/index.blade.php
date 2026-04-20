@extends('layout.layouts')

@section('title', 'Slider Principal - Flash Car')

@section('content')
@include('layout.header')
<div class="bg-light">
    <div class="main_principal">
        <div class="space_principal">
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        <div class="col-12 d-flex align-items-start justify-content-start flex-wrap">

            {{-- ===== COLUMNA IZQUIERDA: VISTA PREVIA ===== --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-5 p-1">
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base">
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h1 class="fs-6 text_uppcase m-0">Vista Previa</h1>
                    </div>
                    <div class="col-12 p-2">
                        <div id="preview_container" style="display:none;">
                            <p id="preview_label" class="text-muted mb-1 text-center" style="font-size:0.75rem;"></p>
                            <div id="preview_grid" style="display:flex; flex-wrap:wrap; gap:8px; justify-content:center;"></div>
                        </div>
                        <p id="preview_empty" class="text-muted fs-6 p-2 m-0">Selecciona una imagen para ver la vista previa.</p>
                    </div>
                </div>
            </div>

            {{-- ===== COLUMNA DERECHA: FORMULARIO + IMÁGENES CARGADAS ===== --}}
            <div class="col-12 col-sm-12 col-md-12 col-lg-7 p-1">

                {{-- FORMULARIO --}}
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-1">
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h2 class="fs-6 text_uppcase m-0">Slider Principal</h2>
                    </div>
                    <div class="col-12 bg_amarillo d-flex align-items-center justify-content-start p-2">
                        <p class="text-dark fs-6 m-0"><b>Subir nuevas imágenes</b></p>
                    </div>
                    <form method="POST" action="{{ route('slider.store') }}" enctype="multipart/form-data"
                          class="col-12 d-flex align-items-end justify-content-start flex-wrap p-2">
                        @csrf
                        <div class="col-12 col-md-4 fila_form_f_b py-2 px-1">
                            <label class="label_form_f_b fs-6 p-1"><b>Tipo *</b></label>
                            <select class="input_form_f_b fs-6 p-1" name="type" id="select_type" required>
                                <option value="desktop">Escritorio (2000×1100)</option>
                                <option value="mobile">Móvil (1200×2000)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-5 fila_form_f_b py-2 px-1">
                            <label class="label_form_f_b fs-6 p-1"><b>Imagen(es) *</b></label>
                            <input class="input_form_f_b fs-6 p-1" type="file" id="input_images" name="images[]" accept="image/*" multiple required>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end py-2 px-1">
                            <button type="submit" class="boton_link_xxl b_sm rounded">+ Subir</button>
                        </div>
                        @error('images.*')
                            <div class="col-12 px-1"><small class="text-danger">{{ $message }}</small></div>
                        @enderror
                    </form>
                </div>

                {{-- IMÁGENES ESCRITORIO --}}
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-1">
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h2 class="fs-6 text_uppcase m-0">Escritorio <span class="badge bg-secondary">{{ $desktop->count() }}</span></h2>
                    </div>
                    @forelse($desktop as $img)
                    <div class="col-6 col-md-4 p-2">
                        <div class="cont_base rounded shadow-sm overflow-hidden">
                            <img src="{{ Storage::url($img->image_path) }}" alt="Slide escritorio" class="w-100" style="height:90px; object-fit:cover;">
                            <div class="p-1 d-flex align-items-center justify-content-between">
                                <span class="text-muted" style="font-size:0.75rem;">N° {{ $img->orden + 1 }}</span>
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('slider.toggle', $img->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="boton_link_sm b_sm rounded {{ $img->active ? '' : 'opacity-50' }}" title="{{ $img->active ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi {{ $img->active ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('slider.destroy', $img->id) }}" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="boton_link_sm b_sm bg_rojo_2 rounded" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @if(!$img->active)
                                <div class="px-1 pb-1"><span class="badge bg-secondary w-100" style="font-size:0.7rem;">Inactiva</span></div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-12 p-3 text-muted fs-6">No hay imágenes de escritorio.</div>
                    @endforelse
                </div>

                {{-- IMÁGENES MÓVIL --}}
                <div class="col-12 d-flex align-items-center justify-content-start flex-wrap rounded cont_base my-1">
                    <div class="col-12 d-flex align-items-center justify-content-between p-2 bg_gris_8">
                        <h2 class="fs-6 text_uppcase m-0">Móvil <span class="badge bg-secondary">{{ $mobile->count() }}</span></h2>
                    </div>
                    @forelse($mobile as $img)
                    <div class="col-6 col-md-4 p-2">
                        <div class="cont_base rounded shadow-sm overflow-hidden">
                            <img src="{{ Storage::url($img->image_path) }}" alt="Slide móvil" class="w-100" style="height:130px; object-fit:cover;">
                            <div class="p-1 d-flex align-items-center justify-content-between">
                                <span class="text-muted" style="font-size:0.75rem;">N° {{ $img->orden + 1 }}</span>
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('slider.toggle', $img->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="boton_link_sm b_sm rounded {{ $img->active ? '' : 'opacity-50' }}" title="{{ $img->active ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi {{ $img->active ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('slider.destroy', $img->id) }}" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="boton_link_sm b_sm bg_rojo_2 rounded" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @if(!$img->active)
                                <div class="px-1 pb-1"><span class="badge bg-secondary w-100" style="font-size:0.7rem;">Inactiva</span></div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-12 p-3 text-muted fs-6">No hay imágenes móvil.</div>
                    @endforelse
                </div>

            </div>

        </div>
        <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        </div>
    </div>
</div>

<script>
(function () {
    const selectType  = document.getElementById('select_type');
    const inputFiles  = document.getElementById('input_images');
    const container   = document.getElementById('preview_container');
    const empty       = document.getElementById('preview_empty');
    const grid        = document.getElementById('preview_grid');
    const label       = document.getElementById('preview_label');

    const ratios = {
        desktop: { w: 2000, h: 1100, texto: 'Escritorio — proporción 2000×1100' },
        mobile:  { w: 1200, h: 2000, texto: 'Móvil — proporción 1200×2000'      },
    };

    function renderPreviews(files) {
        grid.innerHTML = '';
        if (!files.length) {
            container.style.display = 'none';
            empty.style.display     = 'block';
            return;
        }

        const type  = selectType.value;
        const r     = ratios[type];
        const colW  = document.getElementById('preview_container').parentElement.offsetWidth;
        const cardW = type === 'mobile' ? Math.round(colW * 0.52) : Math.round(colW * 0.9);
        const cardH = Math.round(cardW * r.h / r.w);

        label.textContent       = r.texto + ' — object-fit: cover (igual que el servidor)';
        empty.style.display     = 'none';
        container.style.display = 'block';

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const card = document.createElement('div');
                card.style.cssText = `position:relative; width:${cardW}px; height:${cardH}px; overflow:hidden; border-radius:6px; flex-shrink:0;`;

                const img = document.createElement('img');
                img.src           = e.target.result;
                img.style.cssText = 'width:100%; height:100%; object-fit:cover; display:block;';

                // Overlay: header — proporción real 70px / viewport
                const headerH = Math.round((70 / window.innerHeight) * cardH);
                const header = document.createElement('div');
                header.style.cssText = `position:absolute; top:0; left:0; width:100%; height:${headerH}px; background:rgba(255,255,255,0.92); display:flex; align-items:center; padding:0 8px; box-shadow:0 1px 4px rgba(0,0,0,0.2);`;
                header.innerHTML = `<div style="width:28px;height:28px;background:#f5c400;border-radius:50%;margin-right:6px;"></div><div style="flex:1;height:4px;background:#ddd;border-radius:2px;"></div><div style="width:20px;height:20px;background:#eee;border-radius:50%;margin-left:6px;"></div>`;

                // Overlay: formulario de búsqueda
                // Proporciones reales del hero form (.hero_form_card) en pantalla 1440×900:
                //   Desktop → col-lg-5: ~475px wide (33% de vw), ~342px tall (38% de vh)
                //             margen izq container: ~150px (10.4% de vw) → 7% del card en preview
                //   Mobile  → col-12: full width, centrado, posición bottom del slider
                const formBox = document.createElement('div');
                const isMobile = type === 'mobile';

                const formW = isMobile ? Math.round(cardW * 0.82) : Math.round(cardW * 0.34);
                const formH = isMobile ? Math.round(cardH * 0.32) : Math.round(cardH * 0.40);
                const formLeft = isMobile ? Math.round((cardW - formW) / 2) : Math.round(cardW * 0.07);
                const formTop  = isMobile ? null : Math.round((cardH - formH) / 2 + headerH / 2);
                const formBottom = isMobile ? Math.round(cardH * 0.05) : null;

                // Factor de escala para elementos internos (base: form real ~342px alto)
                const s        = formH / 342;
                const pad      = Math.max(4, Math.round(14 * s));
                const titleH   = Math.max(6,  Math.round(34 * s));
                const subH     = Math.max(4,  Math.round(12 * s));
                const inputH   = Math.max(6,  Math.round(44 * s));
                const btnH     = Math.max(6,  Math.round(46 * s));
                const badgeH   = Math.max(4,  Math.round(16 * s));
                const gapPx    = Math.max(2,  Math.round(6  * s));
                const bdrPx    = Math.max(2,  Math.round(6  * s));

                formBox.style.cssText = `
                    position:absolute;
                    ${isMobile ? `bottom:${formBottom}px;` : `top:${formTop}px;`}
                    left:${formLeft}px;
                    width:${formW}px;
                    height:${formH}px;
                    background:rgba(0,0,0,0.65);
                    backdrop-filter:blur(3px);
                    border-radius:${bdrPx * 2}px;
                    padding:${pad}px;
                    display:flex;
                    flex-direction:column;
                    justify-content:space-between;
                    box-sizing:border-box;
                `;
                formBox.innerHTML = `
                    <div style="background:rgba(255,255,255,0.92);border-radius:${bdrPx}px;height:${titleH}px;width:72%;"></div>
                    <div style="background:rgba(255,255,255,0.45);border-radius:${bdrPx}px;height:${subH}px;width:52%;"></div>
                    <div style="background:#fff;border-radius:${bdrPx}px;height:${inputH}px;"></div>
                    <div style="display:flex;gap:${gapPx}px;">
                        <div style="flex:1;background:#fff;border-radius:${bdrPx}px;height:${inputH}px;"></div>
                        <div style="flex:1;background:#fff;border-radius:${bdrPx}px;height:${inputH}px;"></div>
                    </div>
                    <div style="background:#f5c400;border-radius:${bdrPx}px;height:${btnH}px;"></div>
                    <div style="display:flex;gap:${gapPx}px;flex-wrap:wrap;">
                        <div style="height:${badgeH}px;width:${Math.round(formW*0.28)}px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.35);border-radius:${badgeH}px;"></div>
                        <div style="height:${badgeH}px;width:${Math.round(formW*0.21)}px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.35);border-radius:${badgeH}px;"></div>
                        <div style="height:${badgeH}px;width:${Math.round(formW*0.24)}px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.35);border-radius:${badgeH}px;"></div>
                    </div>
                `;

                card.appendChild(img);
                card.appendChild(header);
                card.appendChild(formBox);
                grid.appendChild(card);
            };
            reader.readAsDataURL(file);
        });
    }

    inputFiles.addEventListener('change', () => renderPreviews(inputFiles.files));
    selectType.addEventListener('change', () => { if (inputFiles.files.length) renderPreviews(inputFiles.files); });
})();
</script>

@if(session('success'))
    <div class="messenger_alert">
        <div class="dialog_alert messenger py-2 px-3 rounded">
            <div class="fs-6 text-white"><b>{{ session('success') }}</b><i id="close_messenger" class="m_izq bi bi-x-lg"></i></div>
        </div>
    </div>
@endif
@endsection
