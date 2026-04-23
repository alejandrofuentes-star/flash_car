let currentSlide = 0;
const totalSlides = document.querySelector('.slider_track').querySelectorAll('.slide').length;
let autoSlide = setInterval(() => moveSlide(1), 5000);

// Generar dots automáticamente
const dotsContainer = document.querySelector('.slider_dots');
for (let i = 0; i < totalSlides; i++) {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (i === 0) dot.classList.add('active');
    dot.onclick = () => goToSlide(i);
    dotsContainer.appendChild(dot);
}

function getActiveTrack() {
    return Array.from(document.querySelectorAll('.slider_track'))
                .find(t => t.offsetParent !== null);
}

function updateSlider() {
    const track = getActiveTrack();
    if (!track) return;

    track.querySelectorAll('.slide').forEach((s, i) => {
        s.classList.toggle('active', i === currentSlide);
    });

    document.querySelectorAll('.dot').forEach((d, i) => {
        d.classList.toggle('active', i === currentSlide);
    });
}

function moveSlide(dir) {
    currentSlide = (currentSlide + dir + totalSlides) % totalSlides;
    updateSlider();
    resetTimer();
}

function goToSlide(index) {
    currentSlide = index;
    updateSlider();
    resetTimer();
}

function resetTimer() {
    clearInterval(autoSlide);
    autoSlide = setInterval(() => moveSlide(1), 5000);
}

//scripts fechas busqueda
const hoy = new Date().toISOString().split('T')[0];
const fechaEntrega = document.querySelector('input[name="fecha_entrega"]');
const fechaDevolucion = document.querySelector('input[name="fecha_devolucion"]');

// No permitir fechas pasadas
fechaEntrega.min = hoy;
fechaDevolucion.min = hoy;

// Al cambiar fecha entrega, la devolución debe ser mayor
fechaEntrega.addEventListener('change', function() {
    const nextDay = new Date(this.value);
    nextDay.setDate(nextDay.getDate() + 1);
    fechaDevolucion.min = nextDay.toISOString().split('T')[0];
    
    // Limpiar devolución si es menor o igual a entrega
    if (fechaDevolucion.value && fechaDevolucion.value <= this.value) {
        fechaDevolucion.value = '';
    }
});

//filtro en el catalogo
function normalizar(str) {
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
}

const filtroCategoria = document.getElementById('filtro_category');
const filtroPassengers = document.getElementById('filtro_passengers');

function filtrarVehiculos() {
    const category = normalizar(filtroCategoria.value);
    const passengers = filtroPassengers.value;

    document.querySelectorAll('.tarjeta_vehiculo').forEach(card => {
        const matchCategory = !category || normalizar(card.dataset.category) === category;
        const matchPassengers = !passengers || card.dataset.passengers === passengers;
        card.style.display = matchCategory && matchPassengers ? '' : 'none';
    });
}

filtroCategoria.addEventListener('change', filtrarVehiculos);
filtroPassengers.addEventListener('change', filtrarVehiculos);