// AtendeTI - Script Principal de Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log("Iniciando AtendeTI...");

    // Garante que os modais estão ocultos na inicialização
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.add('hidden');
    });

    // Se estivermos na dashboard (Flask), o usuário já foi injetado pelo template
    if (window.usuarioAtual) {
        console.log("Usuário autenticado:", window.usuarioAtual.nome);
        
        // Inicializa Socket.IO
        const socket = io();
        
        socket.on('connect', () => {
            console.log('Conectado ao servidor via WebSocket');
        });
        
        socket.on('new_ticket', (data) => {
            console.log('Novo chamado recebido:', data);
            showToast('info', 'Novo Chamado', `Chamado #${data.id} aberto por ${data.criador}`);
            
            if (window.usuarioAtual.is_ti) {
                if (typeof carregarChamadosParaAtendimento === 'function') {
                    carregarChamadosParaAtendimento();
                }
                if (typeof tocarSom === 'function') {
                    tocarSom('Alta');
                }
                if (typeof atualizarDashboard === 'function') {
                    atualizarDashboard();
                }
            }
        });
        
        socket.on('update_ticket', (data) => {
            console.log('Chamado atualizado:', data);
            if (typeof carregarMeusChamados === 'function') carregarMeusChamados();
            if (typeof carregarChamadosParaAtendimento === 'function') carregarChamadosParaAtendimento();
            if (typeof atualizarDashboard === 'function') atualizarDashboard();
        });
    }

    // Navegação entre seções (Apenas se o menu de navegação existir)
    const mainNav = document.getElementById('main-nav');
    
    function mostrarSecao(secao) {
        if (!mainNav) return;
        
        const secoes = ['novo-chamado', 'meus-chamados', 'atendimento', 'visitas', 'config-email', 'usuarios', 'dashboard'];
        const correspondencia = {
            'atendimento': 'atender-chamados',
            'usuarios': 'gerenciar-usuarios',
            'visitas': 'visitas-tecnicas'
        };

        const targetId = correspondencia[secao] || secao;

        // Esconde todas as seções e remove classe active dos botões
        document.querySelectorAll('.section').forEach(s => {
            // Não esconde seções que não fazem parte da navegação do dashboard
            if (secoes.includes(s.id) || Object.values(correspondencia).includes(s.id)) {
                s.classList.add('hidden');
            }
        });
        document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));

        // Mostra a seção alvo
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            targetSection.classList.remove('hidden');
            const targetBtn = document.getElementById(`btn-${secao}`);
            if (targetBtn) targetBtn.classList.add('active');
        }

        // Carregamento específico de cada seção
        if (secao === 'meus-chamados') carregarMeusChamados();
        if (secao === 'atendimento') carregarChamadosParaAtendimento();
        if (secao === 'usuarios') carregarUsuarios();
        if (secao === 'dashboard') atualizarDashboard();
        if (secao === 'visitas' && typeof carregarVisitasTecnicas === 'function') carregarVisitasTecnicas();
        if (secao === 'config-email' && typeof carregarConfigEmail === 'function') carregarConfigEmail();
    }

    window.mostrarSecao = mostrarSecao;

    if (mainNav) {
        // Listeners de navegação
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const secao = this.id.replace('btn-', '');
                mostrarSecao(secao);
            });
        });

        // Inicializa a seção ativa vinda do Flask/URL
        if (window.ACTIVE_TAB) {
            mostrarSecao(window.ACTIVE_TAB);
        } else {
            mostrarSecao('novo-chamado');
        }
    }

    // Fechar modais ao clicar no botão fechar ou fora
    const btnFecharModal = document.getElementById('btn-fechar-modal');
    const fecharModalX = document.getElementById('fechar-modal');
    
    if (btnFecharModal) btnFecharModal.addEventListener('click', () => {
        document.getElementById('modal-detalhes').classList.add('hidden');
    });
    
    if (fecharModalX) fecharModalX.addEventListener('click', () => {
        document.getElementById('modal-detalhes').classList.add('hidden');
    });

    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.add('hidden');
        }
    });

    // ESC para fechar modais
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
        }
    });
});