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

// Calcular costo de renta según fechas
function calcularCosto() {
    const precioDia  = parseFloat(document.getElementById('precio_dia')?.value || 0);
    const entrega    = document.getElementById('fecha_entrega')?.value;
    const devolucion = document.getElementById('fecha_devolucion')?.value;

    if (entrega && devolucion) {
        const d1   = new Date(entrega + 'T00:00:00');
        const d2   = new Date(devolucion + 'T00:00:00');
        const diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));

        if (diff > 0) {
            const total = diff * precioDia;
            document.getElementById('resumen_dias').textContent  = diff + ' días';
            document.getElementById('resumen_costo').textContent = '$' + total.toFixed(2);
            document.getElementById('total_dias').value          = diff;
            document.getElementById('costo_total_input').value   = total.toFixed(2);
        } else {
            document.getElementById('resumen_dias').textContent  = 'La fecha de devolución debe ser mayor';
            document.getElementById('resumen_costo').textContent = '$0.00';
            document.getElementById('total_dias').value          = 0;
            document.getElementById('costo_total_input').value   = 0;
        }
    }
}

// Eventos
window.addEventListener('load', function () {
    // Preview de imagen
    const inputImagen = document.getElementById('image');
    if (inputImagen) {
        inputImagen.addEventListener('change', previewImage);
    }

    // Calcular costo
    const fechaEntrega    = document.getElementById('fecha_entrega');
    const fechaDevolucion = document.getElementById('fecha_devolucion');
    if (fechaEntrega && fechaDevolucion) {
        fechaEntrega.addEventListener('change', calcularCosto);
        fechaDevolucion.addEventListener('change', calcularCosto);
    }
});