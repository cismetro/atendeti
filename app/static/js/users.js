// Lógica de gerenciamento de usuários
async function carregarUsuarios() {
    if (!window.usuarioAtual || !window.usuarioAtual.is_ti) return;
    
    try {
        const response = await fetch('/api/users');
        const users = await response.json();
        const tbody = document.querySelector('#tabela-usuarios tbody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        users.forEach(u => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${u.nome}</td>
                <td>${u.email}</td>
                <td>${u.setor}</td>
                <td>${u.is_ti ? 'TI' : 'Usuário'}</td>
                <td><span class="badge badge-success">Ativo</span></td>
                <td>
                    <button class="btn btn-sm btn-info btn-editar-user" data-user='${JSON.stringify(u)}'>Editar</button>
                    <button class="btn btn-sm btn-danger btn-excluir-user" data-id="${u.id}">Excluir</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Listeners para botões recém-criados
        document.querySelectorAll('.btn-editar-user').forEach(btn => {
            btn.addEventListener('click', () => {
                const user = JSON.parse(btn.dataset.user);
                const formUsuarioContainer = document.getElementById('form-usuario-container');
                const formUsuarioTitulo = document.getElementById('form-usuario-titulo');
                const usuarioId = document.getElementById('usuario-id');
                const usuarioNome = document.getElementById('usuario-nome');
                const usuarioEmail = document.getElementById('usuario-email');
                const usuarioSetor = document.getElementById('usuario-setor');
                
                formUsuarioTitulo.textContent = 'Editar Usuário';
                usuarioId.value = user.id;
                usuarioNome.value = user.nome;
                usuarioEmail.value = user.email;
                usuarioSetor.value = user.setor;
                formUsuarioContainer.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.btn-excluir-user').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('Tem certeza que deseja excluir este usuário?')) {
                    const response = await fetch(`/api/users/${btn.dataset.id}`, { method: 'DELETE' });
                    if (response.ok) {
                        showToast('success', 'Sucesso', 'Usuário excluído!');
                        carregarUsuarios();
                    }
                }
            });
        });
    } catch (error) {
        console.error("Erro ao carregar usuários:", error);
    }
}

window.carregarUsuarios = carregarUsuarios;

document.addEventListener('DOMContentLoaded', function() {
    const formUsuario = document.getElementById('form-usuario');
    const btnNovoUsuario = document.getElementById('btn-novo-usuario');
    const formUsuarioContainer = document.getElementById('form-usuario-container');
    const btnCancelarUsuario = document.getElementById('btn-cancelar-usuario');

    if (btnNovoUsuario) {
        btnNovoUsuario.addEventListener('click', () => {
            formUsuario.reset();
            document.getElementById('form-usuario-titulo').textContent = 'Novo Usuário';
            document.getElementById('usuario-id').value = '';
            formUsuarioContainer.classList.remove('hidden');
        });
    }

    if (btnCancelarUsuario) {
        btnCancelarUsuario.addEventListener('click', () => {
            formUsuarioContainer.classList.add('hidden');
        });
    }

    if (formUsuario) {
        formUsuario.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('usuario-id').value;
            const data = {
                nome: document.getElementById('usuario-nome').value,
                email: document.getElementById('usuario-email').value,
                setor: document.getElementById('usuario-setor').value,
                senha: document.getElementById('usuario-senha').value,
                is_ti: document.getElementById('usuario-setor').value === 'TI'
            };
            
            const url = id ? `/api/users/${id}` : '/api/users';
            const method = id ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    showToast('success', 'Sucesso', id ? 'Usuário atualizado!' : 'Usuário criado!');
                    formUsuarioContainer.classList.add('hidden');
                    carregarUsuarios();
                }
            } catch (error) {
                showToast('danger', 'Erro', 'Falha ao salvar usuário.');
            }
        });
    }
});
