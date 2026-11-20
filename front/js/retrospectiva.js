const params = new URLSearchParams(window.location.search);
const sprintId = Number(params.get('sprint_id') || 0);
let sprintAnterior = null;

document.addEventListener('DOMContentLoaded', () => {
    if (sprintId <= 0) {
        window.location.href = 'index.html?error=1';
        return;
    }

    configurarEnlacesAgregar();
    mostrarMensajesURL();
    cargarTablero();

    document.getElementById('btn-cargar-anteriores').addEventListener('click', cargarAccionesAnteriores);
    document.getElementById('btn-ocultar-anteriores').addEventListener('click', () => {
        document.getElementById('panel-anteriores').style.display = 'none';
    });
});

function configurarEnlacesAgregar() {
    document.getElementById('link-agregar-logro').href = `item_form.html?sprint_id=${sprintId}&categoria=logro`;
    document.getElementById('link-agregar-impedimento').href = `item_form.html?sprint_id=${sprintId}&categoria=impedimento`;
    document.getElementById('link-agregar-accion').href = `item_form.html?sprint_id=${sprintId}&categoria=accion`;
    document.getElementById('link-agregar-comentario').href = `item_form.html?sprint_id=${sprintId}&categoria=comentario`;
}

function mostrarAlerta(tipo, texto) {
    const clase = tipo === 'ok' ? 'alerta alerta--ok' : 'alerta alerta--error';
    document.getElementById('mensajes').innerHTML = `<p class="${clase}">${Api.escapeHtml(texto)}</p>`;
}

function mostrarMensajesURL() {
    if (params.get('ok')) {
        mostrarAlerta('ok', 'Cambios guardados correctamente.');
    } else if (params.get('error')) {
        mostrarAlerta('error', 'No se pudo completar la operación.');
    }
}

function renderInsignia(cumplida) {
    if (cumplida === null || cumplida === undefined) {
        return '<span class="insignia insignia--pendiente">Pendiente</span>';
    }
    return cumplida
        ? '<span class="insignia insignia--ok">Cumplida</span>'
        : '<span class="insignia insignia--no">No cumplida</span>';
}

function renderTarjetaItem(item) {
    const metaAccion = item.categoria === 'accion'
        ? `<p class="item__meta">
                ${renderInsignia(item.cumplida)}
                ${item.fecha_revision ? ` Revisión: ${Api.escapeHtml(item.fecha_revision)}` : ''}
           </p>`
        : '';

    return `
        <article class="item">
            <p class="item__texto">${Api.nl2br(item.descripcion)}</p>
            ${metaAccion}
            <div class="item__botones">
                <a class="btn btn--secundario btn--chico" href="item_form.html?id=${Api.escapeHtml(item.id)}&sprint_id=${sprintId}">Editar</a>
                <button type="button" class="btn btn--peligro btn--chico btn-eliminar-item" data-id="${Api.escapeHtml(item.id)}">Eliminar</button>
            </div>
        </article>
    `;
}

async function cargarTablero() {
    try {
        const [sprint, items, anterior] = await Promise.all([
            Api.getSprint(sprintId),
            Api.getRetrosBySprint(sprintId),
            Api.getPreviousSprint(sprintId),
        ]);

        document.getElementById('page-title').textContent = `Retrospectiva · ${sprint.nombre}`;
        document.getElementById('titulo-retro').textContent = `Retrospectiva: ${sprint.nombre}`;

        // Configurar botón de sprint anterior si existe
        if (anterior && anterior.id) {
            sprintAnterior = anterior;
            const btnAnt = document.getElementById('btn-cargar-anteriores');
            btnAnt.textContent = `Cargar acciones de ${anterior.nombre}`;
            btnAnt.style.display = 'inline-block';
        }

        const grupos = { logro: [], impedimento: [], accion: [], otros: [] };
        items.forEach((item) => {
            if (grupos[item.categoria]) {
                grupos[item.categoria].push(item);
            } else {
                grupos.otros.push(item);
            }
        });

        pintarGrupo('grupo-logro', grupos.logro, 'Sin logros todavía.');
        pintarGrupo('grupo-impedimento', grupos.impedimento, 'Sin impedimentos todavía.');
        pintarGrupo('grupo-accion', grupos.accion, 'Sin acciones todavía.');
        pintarGrupo('grupo-otros', grupos.otros, 'Sin comentarios todavía.');
    } catch {
        window.location.href = 'index.html?error=1';
    }
}

function pintarGrupo(elementId, lista, textoVacio) {
    const contenedor = document.getElementById(elementId);
    if (lista.length === 0) {
        contenedor.innerHTML = `<p class="vacio">${Api.escapeHtml(textoVacio)}</p>`;
        return;
    }

    contenedor.innerHTML = lista.map(renderTarjetaItem).join('');
    contenedor.querySelectorAll('.btn-eliminar-item').forEach((btn) => {
        btn.addEventListener('click', () => eliminarItem(btn.dataset.id));
    });
}

async function eliminarItem(id) {
    if (!confirm('¿Seguro que deseas eliminar este ítem?')) {
        return;
    }

    try {
        await Api.deleteRetro(id);
        mostrarAlerta('ok', 'Ítem eliminado correctamente.');
        cargarTablero();
    } catch (error) {
        mostrarAlerta('error', `No se pudo eliminar el ítem: ${error.message}`);
    }
}

async function cargarAccionesAnteriores() {
    if (!sprintAnterior) return;

    const panel = document.getElementById('panel-anteriores');
    const lista = document.getElementById('lista-anteriores');
    document.getElementById('titulo-anteriores').textContent =
        `Seguimiento de Acciones · ${sprintAnterior.nombre}`;
    panel.style.display = 'block';

    try {
        const acciones = await Api.getAccionesBySprint(sprintAnterior.id);

        if (!Array.isArray(acciones) || acciones.length === 0) {
            lista.innerHTML = '<p class="vacio">El sprint anterior no tiene acciones registradas.</p>';
            return;
        }

        lista.innerHTML = acciones.map((acc) => `
            <article class="item">
                <p class="item__texto">${Api.nl2br(acc.descripcion)}</p>
                <p class="item__meta">
                    Estado actual: ${renderInsignia(acc.cumplida)}
                    ${acc.fecha_revision ? ` · Revisada el: ${Api.escapeHtml(acc.fecha_revision)}` : ''}
                </p>
                <div class="item__botones">
                    <button type="button" class="btn btn--primario btn--chico btn-evaluar" data-id="${Api.escapeHtml(acc.id)}" data-cumplida="true">Marcar Cumplida</button>
                    <button type="button" class="btn btn--peligro btn--chico btn-evaluar" data-id="${Api.escapeHtml(acc.id)}" data-cumplida="false">Marcar No Cumplida</button>
                </div>
            </article>
        `).join('');

        lista.querySelectorAll('.btn-evaluar').forEach((btn) => {
            btn.addEventListener('click', () => {
                const cumplida = btn.dataset.cumplida === 'true';
                evaluarAccionAnterior(btn.dataset.id, cumplida);
            });
        });
    } catch (error) {
        mostrarAlerta('error', `Error al cargar acciones anteriores: ${error.message}`);
    }
}

async function evaluarAccionAnterior(id, cumplida) {
    try {
        await Api.evaluateAccion(id, cumplida);
        mostrarAlerta('ok', 'Acción evaluada correctamente.');
        cargarAccionesAnteriores();
    } catch (error) {
        mostrarAlerta('error', `No se pudo evaluar la acción: ${error.message}`);
    }
}