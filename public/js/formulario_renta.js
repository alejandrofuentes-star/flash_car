
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

    const diasBase = Math.floor(diff / (1000 * 60 * 60 * 24));

    // Comparar solo horas para determinar cargo extra
    const minutosEntrega = parseInt(horaEntrega.split(':')[0]) * 60 + parseInt(horaEntrega.split(':')[1]);
    const minutosDevol   = parseInt(horaDevol.split(':')[0]) * 60 + parseInt(horaDevol.split(':')[1]);
    const difMinutos     = minutosDevol - minutosEntrega;
    const cargoExtra     = difMinutos >= 60;

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

// Configurar mínimo de 8 horas de anticipación para fecha y hora de entrega
function configurarMinEntrega() {
    const fechaInput = document.getElementById('fecha_entrega');
    const horaInput  = document.querySelector('input[name="hora_entrega"]');

    if (!fechaInput || !horaInput) return;

    const ahora  = new Date();
    const minima = new Date(ahora.getTime() + 8 * 60 * 60 * 1000);

    const fechaMin = minima.toISOString().split('T')[0];
    const horaMin  = String(minima.getHours()).padStart(2, '0') + ':' + String(minima.getMinutes()).padStart(2, '0');

    // Establecer fecha mínima
    fechaInput.min = fechaMin;

    // Si ya tiene una fecha seleccionada menor a la mínima, limpiarla
    if (fechaInput.value && fechaInput.value < fechaMin) {
        fechaInput.value = '';
        horaInput.value  = '';
    }

    // Al cambiar fecha de entrega, validar hora mínima
    fechaInput.addEventListener('change', function() {
        if (this.value === fechaMin) {
            horaInput.min = horaMin;
            // Si la hora elegida es menor a la mínima, limpiarla
            if (horaInput.value && horaInput.value < horaMin) {
                horaInput.value = '';
            }
        } else {
            horaInput.min = '';
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

    // Validar etapa 2 antes de enviar
    const formRenta = document.getElementById('formRenta');
    if (formRenta) {
        formRenta.addEventListener('submit', function (e) {
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
    if (!pasajeros?.value || parseInt(pasajeros.value) < 1) { marcarError(pasajeros); valido = false; } else limpiarError(pasajeros);

    const ciudad = document.getElementById('select_estado');
    if (!ciudad?.value) { marcarError(ciudad); valido = false; } else limpiarError(ciudad);

    const numTel = document.getElementById('numero_tel');
    const itiEl  = numTel?.closest('.iti');
    const telOk  = typeof validarTelefono === 'function' ? validarTelefono() : !!numTel?.value.trim();
    if (!telOk) { marcarError(itiEl); valido = false; } else limpiarError(itiEl);

    return valido;
}

function irEtapa(n) {
    if (n === 2 && etapaActual === 1 && !validarEtapa1()) return;

    document.getElementById('etapa_' + etapaActual).style.display = 'none';
    document.getElementById('etapa_' + n).style.display = 'block';

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
    if (btnConfirmar) btnConfirmar.style.display = n === 2 ? 'block' : 'none';
}