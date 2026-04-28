from app import create_app, db
from app.models import User

app = create_app()
with app.app_context():
    db.create_all()
    # Adiciona usuário administrador se não existir
    if not User.query.filter_by(email='admin@atendeti.com').first():
        admin = User(nome='Administrador', email='admin@atendeti.com', setor='TI', is_ti=True)
        admin.set_password('admin123')
        db.session.add(admin)
        db.session.commit()
    print('Banco de dados inicializado com sucesso!')
