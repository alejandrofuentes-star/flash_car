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
                const headerH = Math.max(12, Math.round(cardH * 0.078));
                const header = document.createElement('div');
                header.style.cssText = `position:absolute; top:0; left:0; width:100%; height:${headerH}px; background:rgba(255,255,255,0.94); display:flex; align-items:center; justify-content:space-between; padding:0 ${Math.max(5, Math.round(cardW * 0.035))}px; box-shadow:0 1px 4px rgba(0,0,0,0.18); box-sizing:border-box;`;
                header.innerHTML = `
                    <div style="width:${Math.max(34, Math.round(cardW * 0.13))}px;height:${Math.max(8, Math.round(headerH * 0.42))}px;background:#111;border-radius:2px;position:relative;">
                        <div style="position:absolute;left:0;top:0;width:28%;height:100%;background:#f5c400;border-radius:2px 0 0 2px;"></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:${Math.max(3, Math.round(cardW * 0.01))}px;">
                        <div style="width:${Math.max(22, Math.round(cardW * 0.08))}px;height:${Math.max(3, Math.round(headerH * 0.14))}px;background:#d8d8d8;border-radius:999px;"></div>
                        <div style="width:${Math.max(9, Math.round(headerH * 0.42))}px;height:${Math.max(9, Math.round(headerH * 0.42))}px;background:#f5c400;border-radius:50%;"></div>
                    </div>
                `;

                // Overlay: formulario de búsqueda
                // Proporciones reales del hero form (.hero_form_card) en pantalla 1440×900:
                //   Desktop → col-lg-4: ~29% de vw, padding 18px, inputs 36px, btn 38px
                //             margen izq container: ~7% del card en preview
                //   Mobile  → col-12: full width, centrado, posición bottom del slider
                const formBox = document.createElement('div');
                const isMobile = type === 'mobile';

                const formW      = isMobile ? Math.round(cardW * 0.82) : Math.round(cardW * 0.64);
                const formH      = isMobile ? Math.round(cardH * 0.30) : Math.round(cardH * 0.34);
                const formLeft   = Math.round((cardW - formW) / 2);
                const formBottom = Math.max(6, Math.round(cardH * 0.045));

                const s          = Math.max(0.45, Math.min(1, formW / 930));
                const pad        = Math.max(5,  Math.round(18 * s));
                const titleH     = Math.max(6,  Math.round(24 * s));
                const subH       = Math.max(3,  Math.round(10 * s));
                const inputH     = Math.max(7,  Math.round(34 * s));
                const btnH       = inputH;
                const badgeH     = Math.max(5,  Math.round(18 * s));
                const gapPx      = Math.max(3,  Math.round(8  * s));
                const bdrPx      = Math.max(3,  Math.round(8  * s));
                const smallFont  = Math.max(4,  Math.round(10 * s));
                const buttonFont = Math.max(4,  Math.round(11 * s));
                const dotSize    = Math.max(3,  Math.round(8  * s));
                const formGrid   = isMobile
                    ? `display:grid;grid-template-columns:1fr;gap:${gapPx}px;`
                    : `display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,1fr) minmax(0,1fr) minmax(0,0.95fr);gap:${gapPx}px;`;

                formBox.style.cssText = `
                    position:absolute;
                    bottom:${formBottom}px;
                    left:${formLeft}px;
                    width:${formW}px;
                    height:${formH}px;
                    background:rgba(0,0,0,0.60);
                    backdrop-filter:blur(4px);
                    border-radius:${bdrPx * 2}px;
                    padding:${pad}px;
                    display:flex;
                    flex-direction:column;
                    justify-content:space-between;
                    box-sizing:border-box;
                `;
                formBox.innerHTML = `
                    <div style="background:rgba(255,255,255,0.92);border-radius:${bdrPx}px;height:${titleH}px;width:42%;"></div>
                    <div style="background:rgba(255,255,255,0.45);border-radius:${bdrPx}px;height:${subH}px;width:28%;"></div>
                    <div style="${formGrid}">
                        <div style="height:${inputH}px;background:#fff;border-radius:${bdrPx}px;position:relative;box-sizing:border-box;padding-left:${Math.max(8, Math.round(30 * s))}px;">
                            <div style="position:absolute;left:${Math.max(3, Math.round(10 * s))}px;top:50%;transform:translateY(-50%);width:${Math.max(4, Math.round(10 * s))}px;height:${Math.max(4, Math.round(10 * s))}px;border-radius:50%;border:1px solid #777;"></div>
                        </div>
                        <div style="height:${inputH}px;background:#fff;border-radius:${bdrPx}px;box-sizing:border-box;padding:${Math.max(2, Math.round(3 * s))}px ${Math.max(3, Math.round(8 * s))}px ${Math.max(2, Math.round(3 * s))}px ${Math.max(8, Math.round(30 * s))}px;">
                            <div style="height:${Math.max(2, Math.round(7 * s))}px;width:55%;background:#9b9b9b;border-radius:999px;margin-bottom:${Math.max(1, Math.round(2 * s))}px;"></div>
                            <div style="height:${Math.max(2, Math.round(9 * s))}px;width:76%;background:#222;border-radius:999px;"></div>
                        </div>
                        <div style="height:${inputH}px;background:#fff;border-radius:${bdrPx}px;box-sizing:border-box;padding:${Math.max(2, Math.round(3 * s))}px ${Math.max(3, Math.round(8 * s))}px ${Math.max(2, Math.round(3 * s))}px ${Math.max(8, Math.round(30 * s))}px;">
                            <div style="height:${Math.max(2, Math.round(7 * s))}px;width:62%;background:#9b9b9b;border-radius:999px;margin-bottom:${Math.max(1, Math.round(2 * s))}px;"></div>
                            <div style="height:${Math.max(2, Math.round(9 * s))}px;width:82%;background:#222;border-radius:999px;"></div>
                        </div>
                        <div style="height:${btnH}px;background:#f5c400;border-radius:${bdrPx}px;display:flex;align-items:center;justify-content:center;gap:${Math.max(2, Math.round(5 * s))}px;font-size:${buttonFont}px;font-weight:800;color:#111;letter-spacing:0;white-space:nowrap;">
                            <span style="width:${Math.max(4, Math.round(9 * s))}px;height:${Math.max(4, Math.round(9 * s))}px;border:1px solid #111;border-radius:50%;display:inline-block;"></span>
                            <span>RESERVAR</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:${gapPx}px;flex-wrap:wrap;align-items:center;font-size:${smallFont}px;color:rgba(255,255,255,0.85);line-height:1;">
                        <div style="height:${badgeH}px;width:${Math.round(formW*0.18)}px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);border-radius:${badgeH}px;"></div>
                        <div style="height:${badgeH}px;width:${Math.round(formW*0.16)}px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);border-radius:${badgeH}px;"></div>
                        <div style="height:${badgeH}px;width:${Math.round(formW*0.15)}px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);border-radius:${badgeH}px;"></div>
                        <div style="display:flex;align-items:center;gap:${Math.max(2, Math.round(5 * s))}px;height:${badgeH}px;">
                            <span style="width:${dotSize}px;height:${dotSize}px;border-radius:50%;background:#28a745;display:inline-block;"></span>
                            <span style="width:${Math.round(formW*0.16)}px;height:${Math.max(2, Math.round(7 * s))}px;background:rgba(255,255,255,0.72);border-radius:999px;display:inline-block;"></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:${Math.max(2, Math.round(5 * s))}px;height:${badgeH}px;">
                            <span style="width:${dotSize}px;height:${dotSize}px;border-radius:50%;border:1px solid rgba(255,255,255,0.7);display:inline-block;"></span>
                            <span style="width:${Math.round(formW*0.17)}px;height:${Math.max(2, Math.round(7 * s))}px;background:rgba(255,255,255,0.72);border-radius:999px;display:inline-block;"></span>
                        </div>
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
