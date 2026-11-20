
// Preview de imagen al seleccionar archivo
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}


function calcularCosto() {
    const precioDia    = parseFloat(document.getElementById('precio_dia')?.value || 0);
    const precioSemana = parseFloat(document.getElementById('precio_semana')?.value || 0);
    const precioMes    = parseFloat(document.getElementById('precio_mes')?.value || 0);
    const entrega      = document.getElementById('fecha_entrega')?.value;
    const devolucion   = document.getElementById('fecha_devolucion')?.value;
    const horaEntrega  = document.querySelector('input[name="hora_entrega"]')?.value;
    const horaDevol    = document.querySelector('input[name="hora_devolucion"]')?.value;
    const rowExtra     = document.getElementById('row_cargo_extra');

    if (!entrega || !devolucion || !horaEntrega || !horaDevol) {
        if (rowExtra) rowExtra.style.setProperty('display', 'none', 'important');
        return;
    }

    const d1   = new Date(entrega + 'T' + horaEntrega + ':00');
    const d2   = new Date(devolucion + 'T' + horaDevol + ':00');
    const diff = d2 - d1;

    if (diff <= 0) {
        document.getElementById('resumen_dias').textContent  = 'La fecha de devolución debe ser mayor';
        document.getElementById('resumen_costo').textContent = '$0.00';
        document.getElementById('total_dias').value          = 0;
        document.getElementById('costo_total_input').value   = 0;
        if (rowExtra) rowExtra.style.setProperty('display', 'none', 'important');
        return;
    }

    const d1Date   = new Date(entrega + 'T00:00:00');
    const d2Date   = new Date(devolucion + 'T00:00:00');
    const diasBase = Math.round((d2Date - d1Date) / (1000 * 60 * 60 * 24));

    // Comparar solo horas para determinar cargo extra.
    // Regla: si la hora de devolución supera la hora de entrega por más de 2 horas
    // (tolerancia de 2h), se cobra un día completo adicional.
    const minutosEntrega = parseInt(horaEntrega.split(':')[0]) * 60 + parseInt(horaEntrega.split(':')[1]);
    const minutosDevol   = parseInt(horaDevol.split(':')[0]) * 60 + parseInt(horaDevol.split(':')[1]);
    const difMinutos     = minutosDevol - minutosEntrega;
    const cargoExtra     = difMinutos > 120;

    const dias = diasBase === 0 ? 1 : (cargoExtra ? diasBase + 1 : diasBase);

    // Calcular costo según rango
    let total    = 0;
    let desglose = '';

    if (dias >= 30) {
        const meses         = Math.floor(dias / 30);
        const diasRestantes = dias % 30;
        const semanasRest   = Math.floor(diasRestantes / 7);
        const diasSueltos   = diasRestantes % 7;
        total    = (meses * precioMes) + (semanasRest * precioSemana) + (diasSueltos * precioDia);
        desglose = `${meses} mes(es) + ${semanasRest} sem + ${diasSueltos} día(s)`;
    } else if (dias >= 7) {
        const semanas     = Math.floor(dias / 7);
        const diasSueltos = dias % 7;
        total    = (semanas * precioSemana) + (diasSueltos * precioDia);
        desglose = `${semanas} semana(s) + ${diasSueltos} día(s)`;
    } else {
        total    = dias * precioDia;
        desglose = `${dias} día(s)`;
    }

    document.getElementById('resumen_dias').textContent  = desglose;
    document.getElementById('resumen_costo').textContent = '$' + total.toFixed(2);
    document.getElementById('total_dias').value          = dias;
    document.getElementById('costo_total_input').value   = total.toFixed(2);

    if (rowExtra) {
        if (cargoExtra) {
            rowExtra.style.setProperty('display', 'flex', 'important');
            document.getElementById('texto_cargo_extra').textContent = '+1 día por hora extra';
        } else {
            rowExtra.style.setProperty('display', 'none', 'important');
        }
    }
}

// Horas mínimas de anticipación para la entrega (las define la vista con Renta::HORAS_ANTICIPACION;
// el servidor vuelve a validar, esto solo evita que el cliente avance con una hora inválida)
function horasAnticipacion() {
    return (window.flashCarAnticipacion && window.flashCarAnticipacion.horas) || 8;
}

// Fecha ('Y-m-d') y hora ('H:i') mínimas de entrega, en hora local del navegador.
// Se recalcula en cada uso porque el tiempo avanza mientras el cliente llena el formulario.
function minimoEntrega() {
    const minima = new Date(Date.now() + horasAnticipacion() * 60 * 60 * 1000);
    const fecha  = minima.getFullYear() + '-' +
        String(minima.getMonth() + 1).padStart(2, '0') + '-' +
        String(minima.getDate()).padStart(2, '0');
    const hora   = String(minima.getHours()).padStart(2, '0') + ':' + String(minima.getMinutes()).padStart(2, '0');
    return { fecha, hora };
}

// true si la fecha + hora de entrega elegidas cumplen la anticipación mínima
function entregaConAnticipacion() {
    const fecha = document.getElementById('fecha_entrega')?.value;
    const hora  = document.querySelector('input[name="hora_entrega"]')?.value;
    if (!fecha || !hora) return true; // los vacíos los marca validarEtapa2
    const min = minimoEntrega();
    return fecha > min.fecha || (fecha === min.fecha && hora.slice(0, 5) >= min.hora);
}

function avisarAnticipacion() {
    const msg = (window.flashCarAnticipacion && window.flashCarAnticipacion.mensaje)
        || ('La entrega debe ser con al menos ' + horasAnticipacion() + ' horas de anticipación.');
    if (typeof mostrarErrorFecha === 'function') mostrarErrorFecha(msg); else alert(msg);
}

// Configurar mínimo de horas de anticipación para fecha y hora de entrega
function configurarMinEntrega() {
    const fechaInput = document.getElementById('fecha_entrega');
    const horaInput  = document.querySelector('input[name="hora_entrega"]');

    if (!fechaInput || !horaInput) return;

    // Establecer fecha mínima
    fechaInput.min = minimoEntrega().fecha;

    // Si ya tiene una fecha seleccionada menor a la mínima, limpiarla
    if (fechaInput.value && fechaInput.value < fechaInput.min) {
        fechaInput.value = '';
        horaInput.value  = '';
    }

    // Al cambiar la hora, revisar la anticipación (antes solo se revisaba al cambiar la fecha)
    horaInput.addEventListener('change', function () {
        if (this.value && !entregaConAnticipacion()) {
            this.value = '';
            avisarAnticipacion();
        }
    });

    // Al cambiar fecha de entrega, validar hora mínima
    fechaInput.addEventListener('change', function() {
        const min = minimoEntrega();
        fechaInput.min = min.fecha;
        if (this.value && this.value < min.fecha) {
            this.value = '';
            avisarAnticipacion();
        }
        horaInput.min = this.value === min.fecha ? min.hora : '';
        if (horaInput.value && !entregaConAnticipacion()) {
            horaInput.value = '';
            avisarAnticipacion();
        }

        // Actualizar mínimo de fecha devolución
        if (this.value) {
            const siguienteDia = new Date(this.value + 'T00:00:00');
            siguienteDia.setDate(siguienteDia.getDate() + 1);
            const devInput = document.getElementById('fecha_devolucion');
            if (devInput) {
                devInput.min = siguienteDia.toISOString().split('T')[0];
                if (devInput.value && devInput.value <= this.value) {
                    devInput.value = '';
                }
            }
        }

        calcularCosto();
    });
}

function validarEtapa2() {
    let valido = true;

    function marcarError(el) {
        if (!el) return;
        el.style.borderColor = '#dc3545';
        el.style.boxShadow   = 'inset 0 1px 3px rgba(220,53,69,0.18)';
    }
    function limpiarError(el) {
        if (!el) return;
        el.style.borderColor = '';
        el.style.boxShadow   = '';
    }

    const campos = [
        document.getElementById('fecha_entrega'),
        document.querySelector('input[name="hora_entrega"]'),
        document.getElementById('select_entrega'),
        document.getElementById('fecha_devolucion'),
        document.querySelector('input[name="hora_devolucion"]'),
        document.getElementById('select_devolucion'),
    ];

    campos.forEach(campo => {
        if (!campo?.value.trim()) { marcarError(campo); valido = false; } else limpiarError(campo);
    });

    // El form tiene novalidate, así que el atributo min del input de hora no bloquea nada: se revisa aquí
    if (valido && !entregaConAnticipacion()) {
        marcarError(campos[1]);
        avisarAnticipacion();
        valido = false;
    }

    return valido;
}

// Eventos
window.addEventListener('load', function () {
    // Preview de imagen
    const inputImagen = document.getElementById('image');
    if (inputImagen) {
        inputImagen.addEventListener('change', previewImage);
    }

    // Configurar mínimo 8 horas anticipación
    configurarMinEntrega();

    // Calcular costo
    const fechaDevolucion = document.getElementById('fecha_devolucion');
    if (fechaDevolucion) {
        fechaDevolucion.addEventListener('change', calcularCosto);
    }

    // Listeners de hora
    const horaEntregaInput    = document.querySelector('input[name="hora_entrega"]');
    const horaDevolucionInput = document.querySelector('input[name="hora_devolucion"]');
    if (horaEntregaInput)    horaEntregaInput.addEventListener('change', calcularCosto);
    if (horaDevolucionInput) horaDevolucionInput.addEventListener('change', calcularCosto);

    // Validar etapa 3 antes de enviar
    const formRenta = document.getElementById('formRenta');
    if (formRenta) {
        formRenta.addEventListener('submit', function (e) {
            if (etapaActual === 3 && !validarEtapa3()) {
                e.preventDefault();
            }
            // Sin pago con tarjeta se envía desde la etapa 2: revisar fechas y anticipación antes
            if (etapaActual === 2 && !validarEtapa2()) {
                e.preventDefault();
            }
        });
    }

});

// Etapas del formulario
let etapaActual = 1;

function validarEtapa1() {
    let valido = true;

    function marcarError(el) {
        if (!el) return;
        el.style.borderColor = '#dc3545';
        el.style.boxShadow   = 'inset 0 1px 3px rgba(220,53,69,0.18)';
    }
    function limpiarError(el) {
        if (!el) return;
        el.style.borderColor = '';
        el.style.boxShadow   = '';
    }

    const nombre = document.querySelector('input[name="nombre_completo"]');
    if (!nombre?.value.trim()) { marcarError(nombre); valido = false; } else limpiarError(nombre);

    const correo = document.querySelector('input[name="correo"]');
    const emailOk = correo?.value.trim() && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value.trim());
    if (!emailOk) { marcarError(correo); valido = false; } else limpiarError(correo);

    const pasajeros = document.querySelector('input[name="num_pasajeros"]');
    // El max del input (plazas del auto, o del auto más grande de la categoría) no lo aplica el navegador
    // por el novalidate del form: se revisa aquí. El servidor vuelve a validar.
    const maxPasajeros = parseInt(pasajeros?.max);
    if (!pasajeros?.value || parseInt(pasajeros.value) < 1) { marcarError(pasajeros); valido = false; }
    else if (maxPasajeros > 0 && parseInt(pasajeros.value) > maxPasajeros) {
        marcarError(pasajeros);
        valido = false;
        const msg = (pasajeros.dataset.msgMax || 'Máximo :max pasajeros.').replace(':max', maxPasajeros);
        if (typeof mostrarErrorFecha === 'function') mostrarErrorFecha(msg); else alert(msg);
    }
    else limpiarError(pasajeros);

    const ciudad = document.getElementById('select_estado');
    if (!ciudad?.value) { marcarError(ciudad); valido = false; } else limpiarError(ciudad);

    const numTel = document.getElementById('numero_tel');
    const itiEl  = numTel?.closest('.iti');
    const telOk  = typeof validarTelefono === 'function' ? validarTelefono() : !!numTel?.value.trim();
    if (!telOk) { marcarError(itiEl); valido = false; } else limpiarError(itiEl);

    return valido;
}

function validarEtapa3() {
    return true;
}

function irEtapa(n) {
    if (n === 2 && etapaActual === 1 && !validarEtapa1()) return;
    if (n === 3 && etapaActual === 2 && !validarEtapa2()) return;

    document.getElementById('etapa_' + etapaActual).style.display = 'none';
    const nuevaEtapa = document.getElementById('etapa_' + n);
    nuevaEtapa.style.display = 'block';

    if (window.matchMedia('(max-width: 767.98px)').matches) {
        const anclaScroll = document.getElementById('indicador_etapas') || nuevaEtapa;
        anclaScroll.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    for (let i = 1; i <= 3; i++) {
        const c = document.getElementById('circulo_' + i);
        const l = document.getElementById('label_' + i);
        if (!c || !l) continue;
        c.classList.remove('activa', 'completada');
        l.classList.remove('fw-bold', 'text-muted');
        if (i < n)        { c.classList.add('completada'); c.innerHTML = '✓'; l.classList.add('text-muted'); }
        else if (i === n) { c.classList.add('activa'); c.innerHTML = i; l.classList.add('fw-bold'); }
        else              { c.innerHTML = i; l.classList.add('text-muted'); }
    }

    etapaActual = n;

    const btnConfirmar = document.getElementById('btn_confirmar');
    if (btnConfirmar) btnConfirmar.style.display = n === (window.flashCarUltimaEtapa || 3) ? 'block' : 'none';
}