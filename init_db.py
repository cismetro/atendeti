import os
from app import create_app, db
from app.models import User

def init_database():
    app = create_app()
    with app.app_context():
        print("Iniciando a criação do banco de dados SQLite...")
        
        # Cria as tabelas baseadas nos modelos definidos em app/models.py
        db.create_all()
        
        # Verifica se já existe um usuário administrador
        admin = User.query.filter_by(email='admin@admin.com').first()
        if not admin:
            print("Criando usuário administrador padrão...")
            admin = User(
                nome='Administrador',
                email='admin@admin.com',
                setor='TI',
                is_ti=True
            )
            admin.set_password('admin123')
            db.session.add(admin)
            db.session.commit()
            print("Usuário administrador criado: admin@admin.com / admin123")
        else:
            print("Usuário administrador já existe.")
            
        print("\nBanco de dados configurado com sucesso!")
        print(f"Localização do banco: {os.path.join(os.getcwd(), 'app.db')}")

if __name__ == '__main__':
    init_database()
