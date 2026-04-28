// Funções utilitárias e de formatação
function formatarDataHora(dataISO) {
    if (!dataISO) return '';
    const data = new Date(dataISO);
    return data.toLocaleString('pt-BR');
}

function formatarData(data) {
    if (!data) return '';
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR');
}

function formatarTempoRelativo(data) {
    const agora = new Date();
    const diff = Math.floor((agora - data) / 1000); // diferença em segundos
    
    if (diff < 60) return 'agora';
    if (diff < 3600) return `${Math.floor(diff / 60)} min atrás`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} h atrás`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)} dias atrás`;
    
    return `${data.getDate()}/${data.getMonth() + 1}/${data.getFullYear()}`;
}

function getPrioridadeBadgeClass(prioridade) {
    switch(prioridade) {
        case 'Urgente': return 'badge-danger';
        case 'Alta': return 'badge-warning';
        case 'Média':
        case 'Media': return 'badge-info';
        case 'Baixa': return 'badge-success';
        default: return 'badge-secondary';
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'Aberto': return 'badge-warning';
        case 'Em Andamento': return 'badge-info';
        case 'Concluido':
        case 'Concluída':
        case 'Resolvido': return 'badge-success';
        case 'Fechado': return 'badge-secondary';
        default: return 'badge-secondary';
    }
}

function showToast(type, title, message, duration = 5000) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = '';
    if (type === 'success') icon = '✓';
    else if (type === 'warning' || type === 'danger') icon = '⚠';
    else if (type === 'info') icon = 'ℹ';
    
    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode === toastContainer) {
                toastContainer.removeChild(toast);
            }
        }, 300);
    }, duration);
}
