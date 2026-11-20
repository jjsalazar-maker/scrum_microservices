const params = new URLSearchParams(window.location.search);
const sprintId = Number(params.get('id') || 0);

document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('form-sprint');

    if (sprintId > 0) {
        document.getElementById('page-title').textContent = 'Editar sprint';
        document.getElementById('titulo-formulario').textContent = 'Editar sprint';
        await cargarSprint(sprintId);
    }

    form.addEventListener('submit', guardarSprint);
});

function mostrarError(mensaje) {
    document.getElementById('mensajes').innerHTML =
        `<p class="alerta alerta--error">${Api.escapeHtml(mensaje)}</p>`;
}

async function cargarSprint(id) {
    try {
        const sprint = await Api.getSprint(id);
        document.getElementById('nombre').value = sprint.nombre || '';
        document.getElementById('fecha_inicio').value = sprint.fecha_inicio || '';
        document.getElementById('fecha_fin').value = sprint.fecha_fin || '';
    } catch {
        window.location.href = 'index.html?error=1';
    }
}

async function guardarSprint(event) {
    event.preventDefault();

    const nombre = document.getElementById('nombre').value.trim();
    const fecha_inicio = document.getElementById('fecha_inicio').value.trim();
    const fecha_fin = document.getElementById('fecha_fin').value.trim();

    if (!nombre || !fecha_inicio || !fecha_fin) {
        mostrarError('Faltan datos o alguno no es válido.');
        return;
    }

    if (fecha_fin < fecha_inicio) {
        mostrarError('La fecha de fin no puede ser anterior a la de inicio.');
        return;
    }

    const payload = { nombre, fecha_inicio, fecha_fin };

    try {
        if (sprintId > 0) {
            await Api.updateSprint(sprintId, payload);
        } else {
            await Api.createSprint(payload);
        }
        window.location.href = 'index.html?ok=1';
    } catch (error) {
        mostrarError(error.message);
    }
}