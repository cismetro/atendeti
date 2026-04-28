// Lógica de notificações, sons e configurações de interface
function tocarSom(prioridade) {
    const soundToggle = document.getElementById('sound-toggle');
    if (soundToggle && !soundToggle.checked) return;
    
    let audioId = 'notification-sound-baixa';
    if (prioridade === 'Urgente') audioId = 'notification-sound-urgente';
    else if (prioridade === 'Alta') audioId = 'notification-sound-alta';
    else if (prioridade === 'Média' || prioridade === 'Media') audioId = 'notification-sound-media';
    
    const audio = document.getElementById(audioId);
    if (audio) {
        const volumeSlider = document.getElementById('volume-slider');
        if (volumeSlider) {
            audio.volume = volumeSlider.value / 100;
        }
        audio.play().catch(e => console.log("Erro ao tocar som:", e));
    }
}

function atualizarNotificacoes() {
    // Esta função pode ser expandida para buscar notificações reais do servidor
    console.log("Atualizando notificações...");
}

// Tornar globais
window.tocarSom = tocarSom;
window.atualizarNotificacoes = atualizarNotificacoes;

document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notification-bell');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const settingsBtn = document.getElementById('settings-btn');
    const settingsModal = document.getElementById('settings-modal');
    
    if (notificationBell) {
        notificationBell.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
    }
    
    if (settingsBtn) {
        settingsBtn.addEventListener('click', () => {
            settingsModal.classList.remove('hidden');
            notificationDropdown.classList.add('hidden');
        });
    }
    
    // Fechar dropdown ao clicar fora
    document.addEventListener('click', (e) => {
        if (notificationDropdown && !notificationDropdown.contains(e.target) && e.target !== notificationBell) {
            notificationDropdown.classList.add('hidden');
        }
    });
});
