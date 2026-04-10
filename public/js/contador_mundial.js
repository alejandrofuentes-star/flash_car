function actualizarContador() {
    const mundial = new Date('2026-06-11T13:00:00');
    const ahora   = new Date();
    const diff    = mundial - ahora;

    if (diff <= 0) {
        document.getElementById('meses').textContent = '0';
        document.getElementById('dias').textContent  = '0';
        document.getElementById('horas').textContent = '0';
        return;
    }

    const totalDias = Math.floor(diff / (1000 * 60 * 60 * 24));
    const meses     = Math.floor(totalDias / 30);
    const dias      = totalDias % 30;
    const horas     = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    document.getElementById('meses').textContent = meses;
    document.getElementById('dias').textContent  = dias;
    document.getElementById('horas').textContent = horas;
}

actualizarContador();
setInterval(actualizarContador, 1000 * 60); // actualiza cada minuto