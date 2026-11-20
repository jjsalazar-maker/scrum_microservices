const params = new URLSearchParams(window.location.search);
const itemId = Number(params.get('id') || 0);
let sprintId = Number(params.get('sprint_id') || 0);
const categoriaParam = params.get('categoria') || 'logro';
const CATEGORIAS_VALIDAS = ['logro', 'impedimento', 'accion', 'comentario', 'otro'];
let cumplidaActual = null;

document.addEventListener('DOMContentLoaded', async () => {
    const selectCategoria = document.getElementById('categoria');
    if (CATEGORIAS_VALIDAS.includes(categoriaParam)) {
        selectCategoria.value = categoriaParam;
    }

    if (itemId > 0) {
        document.getElementById('page-title').textContent = 'Editar ítem';
        document.getElementById('titulo-formulario').textContent = 'Editar ítem';
        await cargarItem(itemId);
    }

    if (sprintId <= 0) {
        window.location.href = 'index.html?error=1';
        return;
    }

    const urlVolver = `retrospectiva.html?sprint_id=${sprintId}`;
    document.getElementById('link-volver').href = urlVolver;
    document.getElementById('link-cancelar').href = urlVolver;

    document.getElementById('form-item').addEventListener('submit', guardarItem);
});

function mostrarError(mensaje) {
    document.getElementById('mensajes').innerHTML =
        `<p class="alerta alerta--error">${Api.escapeHtml(mensaje)}</p>`;
}

async function cargarItem(id) {
    try {
        const item = await Api.getRetro(id);
        sprintId = Number(item.sprint_id || sprintId);
        document.getElementById('categoria').value = item.categoria || 'logro';
        document.getElementById('descripcion').value = item.descripcion || '';
        document.getElementById('fecha_revision').value = item.fecha_revision || '';
        cumplidaActual = item.cumplida ?? null;
    } catch {
        window.location.href = 'index.html?error=1';
    }
}

async function guardarItem(event) {
    event.preventDefault();

    const categoria = document.getElementById('categoria').value;
    const descripcion = document.getElementById('descripcion').value.trim();
    const fecha_revision = document.getElementById('fecha_revision').value.trim() || null;

    if (!CATEGORIAS_VALIDAS.includes(categoria) || !descripcion) {
        mostrarError('Faltan datos o alguno no es válido.');
        return;
    }

    const payload = {
        sprint_id: sprintId,
        categoria,
        descripcion,
        fecha_revision,
        ...(itemId > 0 && categoria === 'accion' ? { cumplida: cumplidaActual } : {}),
    };

    try {
        if (itemId > 0) {
            await Api.updateRetro(itemId, payload);
        } else {
            await Api.createRetro(payload);
        }
        window.location.href = `retrospectiva.html?sprint_id=${sprintId}&ok=1`;
    } catch (error) {
        mostrarError(error.message);
    }
}
