document.addEventListener('DOMContentLoaded', () => {
    mostrarMensajesURL();
    cargarSprints();
});

function mostrarAlerta(tipo, texto) {
    const contenedor = document.getElementById('mensajes');
    const clase = tipo === 'ok' ? 'alerta alerta--ok' : 'alerta alerta--error';
    contenedor.innerHTML = `<p class="${clase}">${Api.escapeHtml(texto)}</p>`;
}

function mostrarMensajesURL() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('ok')) {
        mostrarAlerta('ok', 'Cambios guardados correctamente.');
    } else if (params.get('error')) {
        mostrarAlerta('error', 'No se pudo completar la operación.');
    }
}

async function cargarSprints() {
    const contenedor = document.getElementById('contenido-sprints');

    try {
        const sprints = await Api.getSprints();

        if (!Array.isArray(sprints) || sprints.length === 0) {
            contenedor.innerHTML = '<p class="vacio">Todavía no hay sprints. Crea el primero con el botón de arriba.</p>';
            return;
        }

        const filas = sprints.map((sprint) => `
            <tr>
                <td>${Api.escapeHtml(sprint.id)}</td>
                <td>${Api.escapeHtml(sprint.nombre)}</td>
                <td>${Api.escapeHtml(sprint.fecha_inicio)}</td>
                <td>${Api.escapeHtml(sprint.fecha_fin)}</td>
                <td>
                    <a class="btn btn--secundario btn--chico" href="retrospectiva.html?sprint_id=${Api.escapeHtml(sprint.id)}">Ver retrospectiva</a>
                    <a class="btn btn--secundario btn--chico" href="sprint_form.html?id=${Api.escapeHtml(sprint.id)}">Editar</a>
                    <button type="button" class="btn btn--peligro btn--chico btn-eliminar-sprint" data-id="${Api.escapeHtml(sprint.id)}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        contenedor.innerHTML = `
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        `;

        contenedor.querySelectorAll('.btn-eliminar-sprint').forEach((btn) => {
            btn.addEventListener('click', () => eliminarSprint(btn.dataset.id));
        });
    } catch (error) {
        contenedor.innerHTML = '';
        mostrarAlerta('error', `Error al cargar sprints: ${error.message}`);
    }
}

async function eliminarSprint(id) {
    if (!confirm('¿Seguro que deseas eliminar este sprint y todos sus ítems de retrospectiva?')) {
        return;
    }

    try {
        await Api.deleteSprint(id);
        mostrarAlerta('ok', 'Sprint eliminado correctamente.');
        cargarSprints();
    } catch (error) {
        mostrarAlerta('error', `No se pudo eliminar el sprint: ${error.message}`);
    }
}