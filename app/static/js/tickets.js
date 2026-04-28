// Lógica de chamados e modais
async function abrirModalChamado(id, tabInicial = 'info', statusAlvo = null) {
    const modalDetalhes = document.getElementById('modal-detalhes');
    const modalTitulo = document.getElementById('modal-titulo');
    const inputChamadoId = document.getElementById('chamado-id');
    const tabResponder = document.getElementById('tab-responder');
    const tabAgendar = document.getElementById('tab-agendar');
    const btnComecar = document.getElementById('btn-comecar');
    
    if (!window.usuarioAtual) {
        showToast('warning', 'Erro', 'Você precisa estar logado para visualizar detalhes do chamado!');
        return;
    }
    
    try {
        const response = await fetch(`/api/tickets/${id}`);
        if (!response.ok) throw new Error('Falha ao buscar detalhes');
        const chamado = await response.json();
        
        modalTitulo.textContent = `Chamado #${chamado.id} - ${chamado.titulo}`;
        inputChamadoId.value = chamado.id;
        
        const novoStatusSelect = document.getElementById('novo-status');
        if (novoStatusSelect) {
            novoStatusSelect.value = statusAlvo || chamado.status;
        }
        
        const podeResponder = window.usuarioAtual.is_ti;
        if (podeResponder) {
            tabResponder.classList.remove('hidden');
            tabAgendar.classList.remove('hidden');
        } else {
            tabResponder.classList.add('hidden');
            tabAgendar.classList.add('hidden');
        }
        
        // Botão Começar
        if (btnComecar) {
            if (window.usuarioAtual.is_ti && chamado.status === 'Aberto') {
                btnComecar.classList.remove('hidden');
            } else {
                btnComecar.classList.add('hidden');
            }
        }
        
        const infoChamado = document.getElementById('info-chamado');
        infoChamado.innerHTML = `
            <div class="info-item">
                <div class="info-item-label">Setor de Origem</div>
                <div class="info-item-value">${chamado.setor_origem}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Setor de Destino</div>
                <div class="info-item-value">${chamado.setor_destino}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Prioridade</div>
                <div class="info-item-value">
                    <span class="badge ${getPrioridadeBadgeClass(chamado.prioridade)}">${chamado.prioridade}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Status</div>
                <div class="info-item-value">
                    <span class="badge ${getStatusClass(chamado.status)}">${chamado.status}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Data de Abertura</div>
                <div class="info-item-value">${formatarDataHora(chamado.data_criacao)}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Solicitante</div>
                <div class="info-item-value">${chamado.criador_nome}</div>
            </div>
        `;
        
        document.getElementById('descricao-chamado').textContent = chamado.descricao;
        
        const historicoContainer = document.getElementById('historico-chamado');
        historicoContainer.innerHTML = '';
        chamado.historico.forEach(item => {
            const historicoItem = document.createElement('div');
            historicoItem.className = 'timeline-item';
            historicoItem.innerHTML = `
                <div class="timeline-date">${formatarDataHora(item.data)}</div>
                <div class="timeline-title">
                    ${item.acao}
                    <small style="display: block; margin-top: 3px; color: #666;">
                        por ${item.responsavelNome} (${item.responsavelSetor})
                    </small>
                </div>
                ${item.detalhes ? `<div class="timeline-body">${item.detalhes}</div>` : ''}
            `;
            historicoContainer.appendChild(historicoItem);
        });
        
        // Tabs
        const modalTabs = document.querySelectorAll('.modal-tab');
        const tabContents = document.querySelectorAll('.tab-content');
        modalTabs.forEach(tab => tab.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        const tabInicialElement = document.querySelector(`.modal-tab[data-tab="${tabInicial}"]`);
        if (tabInicialElement && !tabInicialElement.classList.contains('hidden')) {
            tabInicialElement.classList.add('active');
            document.getElementById(`tab-${tabInicial}`).classList.add('active');
        } else {
            modalTabs[0].classList.add('active');
            tabContents[0].classList.add('active');
        }
        
        modalDetalhes.classList.remove('hidden');
    } catch (error) {
        console.error("Erro ao abrir chamado:", error);
        showToast('danger', 'Erro', 'Não foi possível carregar os detalhes do chamado.');
    }
}

async function carregarMeusChamados() {
    try {
        const response = await fetch('/api/tickets');
        const tickets = await response.json();
        const tbody = document.querySelector('#tabela-meus-chamados tbody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        if (tickets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Nenhum chamado encontrado.</td></tr>';
            return;
        }
        
        tickets.forEach(ticket => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${ticket.id}</td>
                <td>${ticket.titulo}</td>
                <td>TI SecSau</td>
                <td>${formatarDataHora(ticket.data_criacao)}</td>
                <td><span class="badge ${getPrioridadeBadgeClass(ticket.prioridade)}">${ticket.prioridade}</span></td>
                <td><span class="badge ${getStatusClass(ticket.status)}">${ticket.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="abrirModalChamado(${ticket.id})">Ver</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error("Erro ao carregar meus chamados:", error);
    }
}

async function carregarChamadosParaAtendimento() {
    try {
        const response = await fetch('/api/tickets');
        let tickets = await response.json();
        const tbody = document.querySelector('#tabela-atender-chamados tbody');
        if (!tbody) return;
        
        // Filtra estritamente por 'Em Andamento' conforme solicitado
        tickets = tickets.filter(t => t.status === 'Em Andamento');
        
        const filterPrioridade = document.getElementById('filter-prioridade-atendimento').value;
        if (filterPrioridade !== 'todas') {
            tickets = tickets.filter(t => t.prioridade === filterPrioridade);
        }
        
        tbody.innerHTML = '';
        tickets.forEach(ticket => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${ticket.id}</td>
                <td>${ticket.titulo}</td>
                <td>${ticket.setor_origem}</td>
                <td>${ticket.criador_nome}</td>
                <td>${formatarDataHora(ticket.data_criacao)}</td>
                <td><span class="badge ${getPrioridadeBadgeClass(ticket.prioridade)}">${ticket.prioridade}</span></td>
                <td><span class="badge ${getStatusClass(ticket.status)}">${ticket.status}</span></td>
                <td>
                    ${ticket.status === 'Em Andamento' ? 
                        `<button class="btn btn-sm btn-success" onclick="abrirModalChamado(${ticket.id}, 'response', 'Concluido')">Terminar</button>` :
                        `<button class="btn btn-sm btn-primary" onclick="abrirModalChamado(${ticket.id})">Atender</button>`
                    }
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error("Erro ao carregar chamados para atendimento:", error);
    }
}

// Inicialização dos event listeners de chamados
document.addEventListener('DOMContentLoaded', function() {
    const btnComecar = document.getElementById('btn-comecar');
    const formResposta = document.getElementById('form-resposta');
    const inputChamadoId = document.getElementById('chamado-id');
    const modalDetalhes = document.getElementById('modal-detalhes');

    if (btnComecar) {
        btnComecar.addEventListener('click', async function() {
            const id = inputChamadoId.value;
            if (!id) return;
            
            try {
                const response = await fetch(`/api/tickets/${id}/update`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: 'Em Andamento' })
                });
                
                if (response.ok) {
                    showToast('success', 'Atendimento Iniciado', 'O chamado agora está em atendimento.');
                    modalDetalhes.classList.add('hidden');
                    
                    if (window.usuarioAtual.is_ti) {
                        if (typeof mostrarSecao === 'function') mostrarSecao('atendimento');
                        carregarChamadosParaAtendimento();
                    } else {
                        carregarMeusChamados();
                    }
                }
            } catch (error) {
                console.error("Erro ao iniciar atendimento:", error);
                showToast('danger', 'Erro', 'Não foi possível iniciar o atendimento.');
            }
        });
    }

    if (formResposta) {
        formResposta.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!window.usuarioAtual || !window.usuarioAtual.is_ti) {
                showToast('warning', 'Erro', 'Sem permissão!');
                return;
            }
            
            const chamadoId = parseInt(inputChamadoId.value);
            const novoStatus = document.getElementById('novo-status').value;
            const resposta = document.getElementById('resposta-chamado').value;
            
            try {
                const response = await fetch(`/api/tickets/${chamadoId}/update`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: novoStatus, resposta: resposta })
                });
                
                if (response.ok) {
                    showToast('success', 'Atualizado', 'Chamado atualizado com sucesso!');
                    document.getElementById('resposta-chamado').value = '';
                    
                    // Fecha o modal
                    modalDetalhes.classList.add('hidden');
                    
                    if (window.usuarioAtual.is_ti) {
                        carregarChamadosParaAtendimento();
                    } else {
                        carregarMeusChamados();
                    }
                }
            } catch (error) {
                console.error("Erro ao atualizar:", error);
            }
        });
    }

    // Gerenciamento de abas do modal
    const modalTabs = document.querySelectorAll('.modal-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    modalTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active de todas as abas e conteúdos
            modalTabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Adiciona active na aba clicada e no conteúdo correspondente
            this.classList.add('active');
            const targetContent = document.getElementById(`tab-${tabId}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
});

// Tornar globais
window.abrirModalChamado = abrirModalChamado;
window.carregarMeusChamados = carregarMeusChamados;
window.carregarChamadosParaAtendimento = carregarChamadosParaAtendimento;
