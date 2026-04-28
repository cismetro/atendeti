from datetime import datetime
from app import db
from werkzeug.security import generate_password_hash, check_password_hash

from flask_login import UserMixin

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(64), index=True, nullable=False)
    setor = db.Column(db.String(64), nullable=False)
    email = db.Column(db.String(120), index=True, unique=True, nullable=False)
    senha_hash = db.Column(db.String(256), nullable=False)
    is_ti = db.Column(db.Boolean, default=False)

    def set_password(self, password):
        self.senha_hash = generate_password_hash(password)

    def check_password(self, password):
        return check_password_hash(self.senha_hash, password)

    def __repr__(self):
        return f'<User {self.nome}>'

class Ticket(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    titulo = db.Column(db.String(100), nullable=False)
    descricao = db.Column(db.Text, nullable=False)
    status = db.Column(db.String(20), default='Aberto') # Aberto, Em Andamento, Concluido
    prioridade = db.Column(db.String(20), default='Baixa') # Baixa, Media, Alta, Urgente
    criador_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    responsavel_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=True)
    data_criacao = db.Column(db.DateTime, index=True, default=datetime.utcnow)
    data_atualizacao = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    criador = db.relationship('User', foreign_keys=[criador_id], backref='chamados_criados')
    responsavel = db.relationship('User', foreign_keys=[responsavel_id], backref='chamados_atribuidos')

    def __repr__(self):
        return f'<Ticket {self.id}: {self.titulo}>'

class TicketHistory(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    ticket_id = db.Column(db.Integer, db.ForeignKey('ticket.id'), nullable=False)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    acao = db.Column(db.String(255), nullable=False)
    detalhes = db.Column(db.Text, nullable=True)
    data = db.Column(db.DateTime, default=datetime.utcnow)

    ticket = db.relationship('Ticket', backref='historico')
    user = db.relationship('User')

class Notification(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    mensagem = db.Column(db.String(255), nullable=False)
    lida = db.Column(db.Boolean, default=False)
    data = db.Column(db.DateTime, default=datetime.utcnow)
    tipo = db.Column(db.String(20), default='Info') # Info, Success, Warning, Danger

    user = db.relationship('User', backref='notificacoes')

class Visit(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    ticket_id = db.Column(db.Integer, db.ForeignKey('ticket.id'), nullable=True)
    data_agendada = db.Column(db.DateTime, nullable=False)
    tecnico = db.Column(db.String(64), nullable=True)
    observacoes = db.Column(db.Text, nullable=True)
    status = db.Column(db.String(20), default='Agendada') # Agendada, Realizada, Concluída, Cancelada
    setor = db.Column(db.String(100), nullable=True)
    assunto = db.Column(db.String(255), nullable=True)
    hora_retorno = db.Column(db.String(10), nullable=True)

    ticket = db.relationship('Ticket', backref='visitas')

class Settings(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    chave = db.Column(db.String(50), unique=True, nullable=False)
    valor = db.Column(db.Text, nullable=False) # Mudado para Text para suportar templates e JSON de config SMTP
