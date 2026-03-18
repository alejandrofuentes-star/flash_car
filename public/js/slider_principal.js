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