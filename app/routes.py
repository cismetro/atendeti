from datetime import datetime
from flask import Blueprint, render_template, redirect, url_for, request, jsonify
from flask_login import LoginManager, login_user, logout_user, login_required, current_user
from app.models import User, Ticket, TicketHistory, Notification, Visit
from app import db, socketio
from flask_socketio import emit

main_bp = Blueprint('main', __name__)
auth_bp = Blueprint('auth', __name__)
api_bp = Blueprint('api', __name__, url_prefix='/api')

login_manager = LoginManager()
login_manager.login_view = 'auth.login'
login_manager.login_message = "Por favor, faça login para acessar esta página."

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('main.dashboard'))
    
    if request.method == 'POST':
        # Suporta tanto Form submission quanto JSON (AJAX)
        if request.is_json:
            data = request.get_json()
            email = data.get('email')
            senha = data.get('senha')
        else:
            email = request.form.get('email')
            senha = request.form.get('senha')
            
        user = User.query.filter_by(email=email).first()
        if user and user.check_password(senha):
            login_user(user)
            if request.is_json:
                return jsonify({"success": True, "redirect": url_for('main.dashboard')})
            return redirect(url_for('main.dashboard'))
            
        if request.is_json:
            return jsonify({"success": False, "message": "Email ou senha inválidos"}), 401
        return render_template('login.html', error="Email ou senha inválidos")
    
    return render_template('login.html')

@auth_bp.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('auth.login'))

@auth_bp.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        nome = request.form.get('nome')
        email = request.form.get('email')
        senha = request.form.get('senha')
        setor = request.form.get('setor')
        
        if User.query.filter_by(email=email).first():
            return render_template('register.html', error="Este e-mail já está em uso.")
            
        new_user = User(nome=nome, email=email, setor=setor, is_ti=False)
        new_user.set_password(senha)
        db.session.add(new_user)
        db.session.commit()
        return redirect(url_for('auth.login'))
        
    return render_template('register.html')

@main_bp.route('/')
@main_bp.route('/<secao>')
@login_required
def dashboard(secao='novo-chamado'):
    # Lista de seções válidas para evitar erros
    secoes_validas = ['novo-chamado', 'meus-chamados', 'atendimento', 'visitas', 'config-email', 'usuarios', 'dashboard']
    if secao not in secoes_validas:
        secao = 'novo-chamado'
    
    # Busca os chamados para renderização via Jinja2
    if current_user.is_ti:
        tickets = Ticket.query.order_by(Ticket.data_criacao.desc()).all()
    else:
        tickets = Ticket.query.filter_by(criador_id=current_user.id).order_by(Ticket.data_criacao.desc()).all()
        
    return render_template('dashboard.html', current_user=current_user, active_tab=secao, tickets=tickets)

@main_bp.route('/abrir-chamado', methods=['POST'])
@login_required
def abrir_chamado():
    titulo = request.form.get('assunto')
    prioridade = request.form.get('prioridade', 'Baixa')
    descricao = request.form.get('descricao')
    tipo = request.form.get('tipo-chamado')
    
    # Concatena informações extras na descrição
    meta_info = f"\n\n--- Informações Adicionais ---\nTipo: {tipo}\nSetor Origem: {current_user.setor}\nSolicitante: {current_user.nome}"
    descricao_completa = f"{descricao}{meta_info}"
    
    new_ticket = Ticket(
        titulo=titulo,
        descricao=descricao_completa,
        prioridade=prioridade,
        criador_id=current_user.id
    )
    
    db.session.add(new_ticket)
    db.session.commit()
    
    # Adiciona histórico
    historico = TicketHistory(
        ticket_id=new_ticket.id,
        user_id=current_user.id,
        acao='Abertura de Chamado'
    )
    db.session.add(historico)
    db.session.commit()
    
    # Notificação WebSocket
    socketio.emit('new_ticket', {
        'id': new_ticket.id,
        'titulo': new_ticket.titulo,
        'criador': current_user.nome,
        'prioridade': new_ticket.prioridade
    })
    
    # Notificação por E-mail (Opcional - se configurado)
    try:
        from app.services.mail_service import send_notification_email
        from app.models import User
        
        # Busca todos os usuários da TI para notificar
        ti_users = User.query.filter_by(is_ti=True).all()
        emails = [u.email for u in ti_users if u.email]
        
        if emails:
            subject = f"Novo Chamado #{new_ticket.id} - {new_ticket.prioridade}"
            body = f"""
            <h2>Novo Chamado Aberto</h2>
            <p><strong>ID:</strong> #{new_ticket.id}</p>
            <p><strong>Assunto:</strong> {new_ticket.titulo}</p>
            <p><strong>Solicitante:</strong> {current_user.nome} ({current_user.setor})</p>
            <p><strong>Prioridade:</strong> {new_ticket.prioridade}</p>
            <hr>
            <p>Acesse o painel para atender: <a href="http://localhost:5000/atendimento">Atender TI</a></p>
            """
            for email in emails:
                send_notification_email(email, subject, body)
    except Exception as e:
        print(f"Erro ao disparar notificações de e-mail: {e}")

    return redirect(url_for('main.dashboard', secao='meus-chamados'))

# --- Rotas de API ---

@api_bp.route('/tickets', methods=['GET'])
@login_required
def get_tickets():
    if current_user.is_ti:
        tickets = Ticket.query.all()
    else:
        tickets = Ticket.query.filter_by(criador_id=current_user.id).all()
    
    return jsonify([{
        'id': t.id,
        'titulo': t.titulo,
        'descricao': t.descricao,
        'status': t.status,
        'prioridade': t.prioridade,
        'setor_origem': t.criador.setor,
        'data_criacao': t.data_criacao.isoformat(),
        'criador_nome': t.criador.nome
    } for t in tickets])

@api_bp.route('/tickets', methods=['POST'])
@login_required
def create_ticket():
    data = request.get_json()
    
    descricao = data.get('descricao')
    # Se o usuário não for TI, anexa metadados de identificação na descrição para facilitar o atendimento
    if not current_user.is_ti:
        meta_info = f"\n\n--- Informações do Solicitante ---\nNome: {current_user.nome}\nSetor: {current_user.setor}"
        descricao += meta_info

    new_ticket = Ticket(
        titulo=data.get('titulo'),
        descricao=descricao,
        prioridade=data.get('prioridade', 'Baixa'),
        criador_id=current_user.id
    )
    db.session.add(new_ticket)
    db.session.commit()
    
    # Adiciona ao histórico
    history = TicketHistory(ticket_id=new_ticket.id, user_id=current_user.id, acao="Chamado criado")
    db.session.add(history)
    db.session.commit()
    
    # Notifica via WebSocket
    socketio.emit('new_ticket', {
        'id': new_ticket.id,
        'titulo': new_ticket.titulo,
        'criador': current_user.nome,
        'setor': current_user.setor
    }, broadcast=True)
    
    return jsonify({'success': True, 'id': new_ticket.id}), 201

@api_bp.route('/tickets/<int:id>/update', methods=['POST'])
@login_required
def update_ticket(id):
    try:
        ticket = Ticket.query.get_or_404(id)
        data = request.get_json() or {}
        
        old_status = ticket.status
        ticket.status = data.get('status', ticket.status)
        
        print(f"Atualizando chamado {id}: {old_status} -> {ticket.status}")
        
        if old_status != ticket.status:
            history = TicketHistory(
                ticket_id=ticket.id, 
                user_id=current_user.id, 
                acao=f"Status alterado para {ticket.status}"
            )
            db.session.add(history)
        
        resposta = data.get('resposta')
        if resposta:
            history = TicketHistory(
                ticket_id=ticket.id, 
                user_id=current_user.id, 
                acao="Resposta enviada", 
                detalhes=resposta
            )
            db.session.add(history)
        
        db.session.commit()
        
        try:
            socketio.emit('update_ticket', {
                'id': ticket.id,
                'status': ticket.status,
                'resposta': resposta
            }, broadcast=True)
        except Exception as se:
            print(f"Erro ao emitir socket: {str(se)}")
            # Não falha a rota por causa do socket
        
        return jsonify({'success': True})
    except Exception as e:
        db.session.rollback()
        import traceback
        error_details = traceback.format_exc()
        print(f"Erro ao atualizar chamado {id}:")
        print(error_details)
        return jsonify({
            'success': False, 
            'error': str(e),
            'details': error_details
        }), 500

@api_bp.route('/tickets/<int:id>', methods=['GET'])
@login_required
def get_ticket_details(id):
    ticket = Ticket.query.get_or_404(id)
    
    # Verifica permissão
    if not current_user.is_ti and ticket.criador_id != current_user.id:
        return jsonify({'error': 'Unauthorized'}), 403
    
    return jsonify({
        'id': ticket.id,
        'titulo': ticket.titulo,
        'descricao': ticket.descricao,
        'status': ticket.status,
        'prioridade': ticket.prioridade,
        'data_criacao': ticket.data_criacao.isoformat(),
        'criador_nome': ticket.criador.nome,
        'criador_email': ticket.criador.email,
        'setor_origem': ticket.criador.setor,
        'setor_destino': 'TI', # Mock por enquanto, pois só temos um setor de TI
        'historico': [{
            'data': h.data.isoformat(),
            'acao': h.acao,
            'responsavelNome': h.user.nome,
            'responsavelSetor': h.user.setor,
            'detalhes': h.detalhes or ''
        } for h in ticket.historico]
    })

@api_bp.route('/stats/dashboard', methods=['GET'])
@login_required
def get_dashboard_stats():
    # Contadores básicos
    total_tickets = Ticket.query.count()
    open_tickets = Ticket.query.filter_by(status='Aberto').count()
    in_progress_tickets = Ticket.query.filter_by(status='Em Andamento').count()
    completed_tickets = Ticket.query.filter(Ticket.status.in_(['Concluido', 'Resolvido'])).count()
    
    # Estatísticas por setor (Mockado por enquanto, pode ser expandido com GROUP BY)
    # Em uma implementação real, faríamos query no User.setor e Ticket.criador
    
    return jsonify({
        'total': total_tickets,
        'abertos': open_tickets,
        'em_andamento': in_progress_tickets,
        'concluidos': completed_tickets,
        'visitas': {
            'total': Visit.query.count(),
            'pendentes': Visit.query.filter_by(status='Agendada').count(),
            'concluidas': Visit.query.filter_by(status='Concluída').count()
        }
    })

# --- API Usuários ---

@api_bp.route('/users', methods=['GET'])
@login_required
def get_users():
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    users = User.query.all()
    return jsonify([{
        'id': u.id,
        'nome': u.nome,
        'email': u.email,
        'setor': u.setor,
        'is_ti': u.is_ti
    } for u in users])

@api_bp.route('/users', methods=['POST'])
@login_required
def create_user():
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    data = request.get_json()
    user = User(
        nome=data.get('nome'),
        email=data.get('email'),
        setor=data.get('setor'),
        is_ti=data.get('is_ti', False)
    )
    user.set_password(data.get('senha'))
    db.session.add(user)
    db.session.commit()
    return jsonify({'success': True, 'id': user.id}), 201

@api_bp.route('/users/<int:id>', methods=['PUT'])
@login_required
def update_user(id):
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    user = User.query.get_or_404(id)
    data = request.get_json()
    user.nome = data.get('nome')
    user.email = data.get('email')
    user.setor = data.get('setor')
    user.is_ti = data.get('is_ti', False)
    if data.get('senha'):
        user.set_password(data.get('senha'))
    db.session.commit()
    return jsonify({'success': True})

@api_bp.route('/users/<int:id>', methods=['DELETE'])
@login_required
def delete_user(id):
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    user = User.query.get_or_404(id)
    if user.id == current_user.id:
        return jsonify({'error': 'Cannot delete yourself'}), 400
    db.session.delete(user)
    db.session.commit()
    return jsonify({'success': True})

# --- API Visitas ---

@api_bp.route('/visits', methods=['GET'])
@login_required
def get_visits():
    visits = Visit.query.order_by(Visit.data_agendada.desc()).all()
    return jsonify([{
        'id': v.id,
        'ticket_id': v.ticket_id,
        'data': v.data_agendada.strftime('%Y-%m-%d'),
        'hora': v.data_agendada.strftime('%H:%M'),
        'tecnico': v.tecnico,
        'status': v.status,
        'observacoes': v.observacoes,
        'hora_retorno': v.hora_retorno,
        'assunto': v.assunto or (v.ticket.titulo if v.ticket else 'N/A'),
        'setor_nome': v.setor or (v.ticket.criador.setor if v.ticket else 'N/A')
    } for v in visits])

@api_bp.route('/visits', methods=['POST'])
@login_required
def create_visit():
    try:
        if not current_user.is_ti:
            return jsonify({'error': 'Unauthorized'}), 403
        
        data = request.get_json() or {}
        
        # Validação de data/hora
        data_val = data.get('data')
        hora_val = data.get('hora')
        if not data_val or not hora_val:
            return jsonify({'error': 'Data e hora são obrigatórias'}), 400
            
        dt_str = f"{data_val} {hora_val}"
        try:
            dt = datetime.strptime(dt_str, '%Y-%m-%d %H:%M')
        except ValueError:
            # Tenta com segundos se por acaso o navegador enviar
            dt = datetime.strptime(dt_str, '%Y-%m-%d %H:%M:%S')
        
        ticket_id = data.get('ticket_id')
        # Converte para int ou None se for string vazia
        if ticket_id == '' or ticket_id == 'undefined':
            ticket_id = None
        elif ticket_id is not None:
            ticket_id = int(ticket_id)
            
        visit = Visit(
            ticket_id=ticket_id,
            data_agendada=dt,
            tecnico=data.get('tecnico'),
            observacoes=data.get('observacoes'),
            status='Agendada',
            setor=data.get('setor'),
            assunto=data.get('assunto')
        )
        db.session.add(visit)
        
        if visit.ticket_id:
            history = TicketHistory(
                ticket_id=visit.ticket_id, 
                user_id=current_user.id, 
                acao=f"Visita técnica agendada para {dt_str} com {visit.tecnico}"
            )
            db.session.add(history)
        
        db.session.commit()
        return jsonify({'success': True, 'id': visit.id}), 201
    except Exception as e:
        db.session.rollback()
        import traceback
        print(traceback.format_exc())
        return jsonify({'success': False, 'error': str(e)}), 500

@api_bp.route('/visits/<int:id>', methods=['POST', 'PUT'])
@login_required
def update_visit(id):
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    visit = Visit.query.get_or_404(id)
    data = request.get_json()
    
    if data.get('data') and data.get('hora'):
        dt_str = f"{data.get('data')} {data.get('hora')}"
        visit.data_agendada = datetime.strptime(dt_str, '%Y-%m-%d %H:%M')
    
    visit.tecnico = data.get('tecnico', visit.tecnico)
    visit.status = data.get('status', visit.status)
    visit.observacoes = data.get('observacoes', visit.observacoes)
    visit.hora_retorno = data.get('hora_retorno', visit.hora_retorno)
    
    db.session.commit()
    return jsonify({'success': True})

@api_bp.route('/visits/<int:id>', methods=['DELETE'])
@login_required
def delete_visit(id):
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    visit = Visit.query.get_or_404(id)
    db.session.delete(visit)
    db.session.commit()
    return jsonify({'success': True})

# --- API Configurações ---

@api_bp.route('/settings/<string:chave>', methods=['GET'])
@login_required
def get_setting(chave):
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    setting = Settings.query.filter_by(chave=chave).first()
    if not setting:
        return jsonify({'value': None})
    return jsonify({'value': setting.valor})

@api_bp.route('/settings/<string:chave>', methods=['POST'])
@login_required
def set_setting(chave):
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
    data = request.get_json()
    valor = data.get('valor')
    
    setting = Settings.query.filter_by(chave=chave).first()
    if setting:
        setting.valor = valor
    else:
        setting = Settings(chave=chave, valor=valor)
        db.session.add(setting)
    
    db.session.commit()
    return jsonify({'success': True})

# --- Eventos SocketIO ---

@socketio.on('connect')
def handle_connect():
    print(f"Cliente conectado: {current_user.nome if current_user.is_authenticated else 'Anônimo'}")

@socketio.on('message')
def handle_message(msg):
    print(f"Mensagem recebida: {msg}")
@api_bp.route('/settings/email', methods=['GET', 'POST'])
@login_required
def manage_email_settings():
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
        
    if request.method == 'POST':
        data = request.get_json()
        # Salva como JSON na chave 'smtp_config'
        import json
        config_record = Settings.query.filter_by(chave='smtp_config').first()
        if not config_record:
            config_record = Settings(chave='smtp_config')
            db.session.add(config_record)
        
        config_record.valor = json.dumps(data)
        db.session.commit()
        return jsonify({'success': True})
    
    # GET: Retorna as configurações atuais (sem a senha por segurança)
    import json
    config_record = Settings.query.filter_by(chave='smtp_config').first()
    if config_record:
        config = json.loads(config_record.valor)
        if 'senha' in config:
            config['senha'] = '********'
        return jsonify(config)
    return jsonify({})

@api_bp.route('/settings/email/test', methods=['POST'])
@login_required
def test_email_settings():
    if not current_user.is_ti:
        return jsonify({'error': 'Unauthorized'}), 403
        
    data = request.get_json()
    import smtplib
    from email.mime.text import MIMEText
    from email.mime.multipart import MIMEMultipart
    
    try:
        # Se a senha vier como asteriscos, busca a senha real do banco
        if data.get('senha') == '********':
            import json
            config_record = Settings.query.filter_by(chave='smtp_config').first()
            if config_record:
                old_config = json.loads(config_record.valor)
                data['senha'] = old_config.get('senha')

        # Tenta conexão SMTP
        server = smtplib.SMTP(data.get('servidor'), int(data.get('porta')))
        if data.get('use_tls', True):
            server.starttls()
            
        server.login(data.get('usuario'), data.get('senha'))
        
        # Envia e-mail de teste
        msg = MIMEMultipart()
        msg['From'] = f"{data.get('nome_remetente')} <{data.get('email_remetente')}>"
        msg['To'] = data.get('usuario')
        msg['Subject'] = "Teste de Configuração - AtendeTI"
        
        body = "Este é um e-mail de teste para validar as configurações de SMTP do AtendeTI."
        msg.attach(MIMEText(body, 'plain'))
        
        server.send_message(msg)
        server.quit()
        
        return jsonify({'success': True, 'message': 'Conexão estabelecida e e-mail de teste enviado!'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500
