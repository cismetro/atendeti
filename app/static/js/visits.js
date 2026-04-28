// Lógica de Visitas Técnicas
async function carregarVisitasTecnicas() {
    console.log("Chamando carregarVisitasTecnicas...");
    try {
        const container = document.getElementById('lista-visitas-agendadas');
        const semVisitas = document.getElementById('sem-visitas');
        const filtroElem = document.getElementById('filtro-visitas');
        
        if (!container || !filtroElem) return;
        
        const filtro = filtroElem.value;
        console.log("Filtro atual:", filtro);
        
        const response = await fetch('/api/visits');
        let visits = await response.json();
        console.log("Visitas carregadas:", visits.length);
        
        // Aplicar Filtros
        if (filtro === 'pendentes') {
            visits = visits.filter(v => v.status === 'Agendada');
        } else if (filtro === 'concluidas') {
            visits = visits.filter(v => ['Concluída', 'Concluido', 'Realizada'].includes(v.status));
        } else if (filtro === 'hoje') {
            const hoje = new Date().toISOString().split('T')[0];
            visits = visits.filter(v => v.data === hoje);
        }
        
        // Limpa lista antiga e prepara a estrutura
        container.innerHTML = `
            <div class="responsive-table">
                <table class="table-visitas">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Assunto</th>
                            <th>Setor</th>
                            <th>Agendamento</th>
                            <th>Técnico</th>
                            <th>Retorno</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <p id="sem-visitas" class="empty-state hidden">Nenhuma visita técnica encontrada para este filtro.</p>
        `;
        
        const tbody = container.querySelector('tbody');
        const semVisitasNova = container.querySelector('#sem-visitas');
        const tableContainer = container.querySelector('.responsive-table');
        
        if (visits.length === 0) {
            semVisitasNova.classList.remove('hidden');
            tableContainer.classList.add('hidden');
            return;
        }
        
        semVisitasNova.classList.add('hidden');
        tableContainer.classList.remove('hidden');
        
        visits.forEach(v => {
            const tr = document.createElement('tr');
            tr.className = v.status === 'Concluída' ? 'visita-concluida' : '';
            tr.innerHTML = `
                <td>#${v.id}</td>
                <td style="font-weight: bold;">${v.assunto}</td>
                <td>${v.setor_nome}</td>
                <td>${formatarData(new Date(v.data))} às ${v.hora}</td>
                <td>${v.tecnico}</td>
                <td><span class="retorno-text">${v.hora_retorno || '-'}</span></td>
                <td><span class="badge ${getStatusClass(v.status)}">${v.status}</span></td>
                <td class="user-actions-cell">
                    ${v.status === 'Agendada' ? `<button class="btn btn-xs btn-success" title="Concluir" onclick="concluirVisita(${v.id})"><i class="fas fa-check"></i></button>` : ''}
                    <button class="btn btn-xs btn-danger" title="Excluir" onclick="cancelarVisita(${v.id})"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error("Erro ao carregar visitas:", error);
    }
}

async function concluirVisita(id) {
    const horaRetorno = prompt('Informe o horário de retorno (Ex: 15:30):');
    if (horaRetorno === null) return;
    
    try {
        const response = await fetch(`/api/visits/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'Concluída', hora_retorno: horaRetorno })
        });
        if (response.ok) {
            showToast('success', 'Sucesso', 'Visita concluída com horário de retorno registrado!');
            carregarVisitasTecnicas();
        }
    } catch (error) {
        console.error("Erro ao concluir visita:", error);
    }
}

async function cancelarVisita(id) {
    if (!confirm('Tem certeza que deseja excluir esta visita?')) return;
    try {
        const response = await fetch(`/api/visits/${id}`, { method: 'DELETE' });
        if (response.ok) {
            showToast('success', 'Removido', 'Visita excluída com sucesso.');
            carregarVisitasTecnicas();
        }
    } catch (error) {
        console.error("Erro ao excluir visita:", error);
    }
}

window.carregarVisitasTecnicas = carregarVisitasTecnicas;
window.concluirVisita = concluirVisita;
window.cancelarVisita = cancelarVisita;

document.addEventListener('DOMContentLoaded', function() {
    const btnNovaVisita = document.getElementById('nova-visita-manual');
    const modalVisita = document.getElementById('modal-visita');
    const formVisita = document.getElementById('form-visita-tecnica');
    const fecharModalVisita = document.getElementById('fechar-modal-visita');
    const btnCancelarVisita = document.getElementById('btn-cancelar-visita');
    const btnAplicarFiltro = document.getElementById('btn-aplicar-filtro-visitas');

    if (btnNovaVisita) {
        btnNovaVisita.addEventListener('click', () => {
            formVisita.reset();
            document.getElementById('visita-id').value = '';
            document.getElementById('modal-visita-titulo').textContent = 'Agendar Visita Técnica Direta';
            modalVisita.classList.remove('hidden');
        });
    }

    if (btnAplicarFiltro) {
        btnAplicarFiltro.addEventListener('click', carregarVisitasTecnicas);
    }

    if (fecharModalVisita) fecharModalVisita.addEventListener('click', () => modalVisita.classList.add('hidden'));
    if (btnCancelarVisita) btnCancelarVisita.addEventListener('click', () => modalVisita.classList.add('hidden'));

    if (formVisita) {
        formVisita.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const data = {
                setor: document.getElementById('visita-setor').value,
                data: document.getElementById('visita-data-manual').value,
                hora: document.getElementById('visita-hora-manual').value,
                tecnico: document.getElementById('visita-tecnico-manual').value,
                assunto: document.getElementById('visita-assunto').value,
                observacoes: document.getElementById('visita-obs').value
            };
            
            try {
                const response = await fetch('/api/visits', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    showToast('success', 'Sucesso', 'Visita agendada com sucesso!');
                    modalVisita.classList.add('hidden');
                    carregarVisitasTecnicas();
                }
            } catch (error) {
                console.error("Erro ao salvar visita:", error);
            }
        });
    }
    
    // Lógica para o formulário de agendamento dentro do modal de chamado
    const formAgendarNoChamado = document.getElementById('form-agendar-visita');
    if (formAgendarNoChamado) {
        formAgendarNoChamado.addEventListener('submit', async function(e) {
            e.preventDefault();
            const ticketId = document.getElementById('visita-chamado-id').value;
            const data = {
                ticket_id: ticketId,
                data: document.getElementById('visita-data').value,
                hora: document.getElementById('visita-hora').value,
                tecnico: document.getElementById('visita-tecnico').value,
                observacoes: document.getElementById('visita-observacoes').value
            };
            
            try {
                const response = await fetch('/api/visits', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (response.ok) {
                    showToast('success', 'Agendado', 'Visita técnica agendada para o chamado.');
                    document.getElementById('modal-detalhes').classList.add('hidden');
                    if (typeof carregarVisitasTecnicas === 'function') carregarVisitasTecnicas();
                }
            } catch (error) {
                console.error("Erro ao agendar visita via chamado:", error);
            }
        });
    }
});
