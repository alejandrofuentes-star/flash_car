function actualizarContador() {
    const mundial = new Date('2026-06-11T13:00:00');
    const ahora   = new Date();
    const diff    = mundial - ahora;

    if (diff <= 0) {
        document.getElementById('dias').textContent    = '0';
        document.getElementById('horas').textContent   = '0';
        document.getElementById('minutos').textContent = '0';
        return;
    }

    const dias    = Math.floor(diff / (1000 * 60 * 60 * 24));
    const horas   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    document.getElementById('dias').textContent    = dias;
    document.getElementById('horas').textContent   = horas;
    document.getElementById('minutos').textContent = minutos;
}

actualizarContador();
setInterval(actualizarContador, 1000 * 60); // actualiza cada minuto
