const API_BASE_URL = 'http://127.0.0.1:8000';

async function apiRequest(endpoint, options = {}) {
    const config = {
        headers: {
            'Accept': 'application/json',
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...(options.headers || {}),
        },
        ...options,
    };

    const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
    const text = await response.text();
    let data = null;

    try {
        data = text ? JSON.parse(text) : null;
    } catch {
        data = null;
    }

    if (!response.ok) {
        const message = (data && data.error) ? data.error : `Error HTTP ${response.status}`;
        throw new Error(message);
    }

    return data;
}

function escapeHtml(valor) {
    return String(valor ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function nl2br(texto) {
    return escapeHtml(texto).replace(/\r?\n/g, '<br>');
}

window.Api = {
    // --- Sprints ---
    getSprints: () => apiRequest('/sprints'),
    getSprint: (id) => apiRequest(`/sprints/${id}`),
    getPreviousSprint: (id) => apiRequest(`/sprints/${id}/anterior`),
    createSprint: (payload) => apiRequest('/sprints', {
        method: 'POST',
        body: JSON.stringify(payload),
    }),
    updateSprint: (id, payload) => apiRequest(`/sprints/${id}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
    }),
    deleteSprint: (id) => apiRequest(`/sprints/${id}`, {
        method: 'DELETE',
    }),

    // --- Retro Items ---
    getRetros: () => apiRequest('/retros'),
    getRetrosBySprint: (sprintId) => apiRequest(`/sprints/${sprintId}/retros`),
    getAccionesBySprint: (sprintId) => apiRequest(`/sprints/${sprintId}/acciones`),
    getRetro: (id) => apiRequest(`/retros/${id}`),
    createRetro: (payload) => apiRequest('/retros', {
        method: 'POST',
        body: JSON.stringify(payload),
    }),
    updateRetro: (id, payload) => apiRequest(`/retros/${id}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
    }),
    evaluateAccion: (id, cumplida, fecha_revision = null) => {
        const payload = { cumplida };
        if (fecha_revision) {
            payload.fecha_revision = fecha_revision;
        }
        return apiRequest(`/retros/${id}/evaluar`, {
            method: 'PATCH',
            body: JSON.stringify(payload),
        });
    },
    deleteRetro: (id) => apiRequest(`/retros/${id}`, {
        method: 'DELETE',
    }),

    // --- Utilidades ---
    escapeHtml,
    nl2br,
};