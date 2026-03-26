function abrirFoto(src) {
    document.getElementById('modalFotoImg').src = src;
    document.getElementById('modalFoto').style.display = 'flex';
}
function cerrarFoto() {
    document.getElementById('modalFoto').style.display = 'none';
}