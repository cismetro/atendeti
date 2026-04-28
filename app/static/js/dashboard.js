// Lógica do Dashboard e estatísticas
async function atualizarDashboard() {
    try {
        const response = await fetch('/api/stats/dashboard');
        const stats = await response.json();
        
        const totalElem = document.getElementById('total-chamados');
        const abertosElem = document.getElementById('em-aberto');
        const andamentoElem = document.getElementById('em-andamento');
        const resolvidosElem = document.getElementById('resolvidos');
        
        if (totalElem) totalElem.textContent = stats.total;
        if (abertosElem) abertosElem.textContent = stats.abertos;
        if (andamentoElem) andamentoElem.textContent = stats.em_andamento;
        if (resolvidosElem) resolvidosElem.textContent = stats.concluidos;
    } catch (error) {
        console.error("Erro ao atualizar dashboard:", error);
    }
}

window.atualizarDashboard = atualizarDashboard;

document.addEventListener('DOMContentLoaded', function() {
    if (window.usuarioAtual) {
        atualizarDashboard();
        // Atualiza a cada 5 minutos
        setInterval(atualizarDashboard, 300000);
    }
});
