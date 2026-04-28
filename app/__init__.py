from flask import Flask, request
from flask_sqlalchemy import SQLAlchemy
from flask_socketio import SocketIO
from app.config import Config

db = SQLAlchemy()
socketio = SocketIO(cors_allowed_origins="*")

def create_app(config_class=Config):
    app = Flask(__name__)
    app.config.from_object(config_class)

    db.init_app(app)
    socketio.init_app(app)

    from app.routes import main_bp, auth_bp, api_bp, login_manager
    login_manager.init_app(app)
    
    app.register_blueprint(auth_bp)
    app.register_blueprint(main_bp)
    app.register_blueprint(api_bp)

    @app.before_request
    def check_joomla_sso():
        from flask_login import current_user, login_user
        from app.services.joomla_auth import get_joomla_session, sync_joomla_user
        
        # Se o usuário já estiver autenticado no Flask, não faz nada
        if current_user.is_authenticated:
            return

        # Se for uma rota estática, ignora
        if request.endpoint == 'static':
            return

        # Verifica se há uma sessão ativa no Joomla (via cookies)
        joomla_sess = get_joomla_session()
        if joomla_sess:
            # Sincroniza o usuário Joomla com o DB local e loga no Flask-Login
            user = sync_joomla_user(joomla_sess)
            if user:
                login_user(user, remember=True)

    return app
