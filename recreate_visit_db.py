import sqlite3

def recreate_visit_table():
    conn = sqlite3.connect('app.db')
    cursor = conn.cursor()
    
    try:
        print("Recriando tabela 'visit' para permitir ticket_id nulo...")
        
        # 1. Renomeia a tabela antiga
        cursor.execute("ALTER TABLE visit RENAME TO visit_old")
        
        # 2. Cria a nova tabela com a estrutura correta
        cursor.execute("""
            CREATE TABLE visit (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ticket_id INTEGER,
                data_agendada DATETIME NOT NULL,
                tecnico VARCHAR(64),
                observacoes TEXT,
                status VARCHAR(20) DEFAULT 'Agendada',
                setor VARCHAR(100),
                assunto VARCHAR(255),
                FOREIGN KEY(ticket_id) REFERENCES ticket (id)
            )
        """)
        
        # 3. Copia os dados da antiga para a nova (se houver)
        cursor.execute("""
            INSERT INTO visit (id, ticket_id, data_agendada, tecnico, observacoes, status)
            SELECT id, ticket_id, data_agendada, tecnico, observacoes, status FROM visit_old
        """)
        
        # 4. Remove a tabela antiga
        cursor.execute("DROP TABLE visit_old")
        
        conn.commit()
        print("Tabela 'visit' recriada com sucesso.")
    except Exception as e:
        conn.rollback()
        print(f"Erro ao recriar tabela: {e}")
    finally:
        conn.close()

if __name__ == "__main__":
    recreate_visit_table()
