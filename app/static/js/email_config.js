// Lógica de Configuração de Email
async function carregarConfigEmail() {
    try {
        const response = await fetch('/api/settings/email');
        const config = await response.json();
        
        if (config.servidor) {
            document.getElementById('smtp-servidor').value = config.servidor;
            document.getElementById('smtp-porta').value = config.porta;
            document.getElementById('smtp-usuario').value = config.usuario;
            document.getElementById('smtp-senha').value = config.senha;
            document.getElementById('smtp-email').value = config.email_remetente;
            document.getElementById('smtp-remetente').value = config.nome_remetente;
            document.getElementById('smtp-seguranca').checked = config.use_tls;
        }
    } catch (error) {
        console.error("Erro ao carregar configurações de email:", error);
    }
}

async function salvarConfigEmail(e) {
    e.preventDefault();
    const data = {
        servidor: document.getElementById('smtp-servidor').value,
        porta: document.getElementById('smtp-porta').value,
        usuario: document.getElementById('smtp-usuario').value,
        senha: document.getElementById('smtp-senha').value,
        email_remetente: document.getElementById('smtp-email').value,
        nome_remetente: document.getElementById('smtp-remetente').value,
        use_tls: document.getElementById('smtp-seguranca').checked
    };
    
    try {
        const response = await fetch('/api/settings/email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            showToast('success', 'Salvo', 'Configurações de e-mail atualizadas com sucesso!');
        }
    } catch (error) {
        console.error("Erro ao salvar configurações de email:", error);
        showToast('danger', 'Erro', 'Falha ao salvar configurações.');
    }
}

async function testarEmail() {
    const btn = document.getElementById('btn-testar-email');
    const originalText = btn.textContent;
    btn.textContent = 'Testando...';
    btn.disabled = true;
    
    const data = {
        servidor: document.getElementById('smtp-servidor').value,
        porta: document.getElementById('smtp-porta').value,
        usuario: document.getElementById('smtp-usuario').value,
        senha: document.getElementById('smtp-senha').value,
        email_remetente: document.getElementById('smtp-email').value,
        nome_remetente: document.getElementById('smtp-remetente').value,
        use_tls: document.getElementById('smtp-seguranca').checked
    };
    
    try {
        const response = await fetch('/api/settings/email/test', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            showToast('success', 'Conectado', result.message);
        } else {
            showToast('warning', 'Falha no Teste', result.message);
        }
    } catch (error) {
        console.error("Erro ao testar email:", error);
        showToast('danger', 'Erro Crítico', 'Não foi possível contatar o servidor de teste.');
    } finally {
        btn.textContent = originalText;
        btn.disabled = false;
    }
}

window.carregarConfigEmail = carregarConfigEmail;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-config-smtp');
    if (form) {
        form.addEventListener('submit', salvarConfigEmail);
    }
    
    const btnTeste = document.getElementById('btn-testar-email');
    if (btnTeste) {
        btnTeste.addEventListener('click', testarEmail);
    }
});
