import sqlite3

def fix_visit_table():
    conn = sqlite3.connect('app.db')
    cursor = conn.cursor()
    
    try:
        # No SQLite, para mudar NULLABLE de uma coluna, precisamos recriar a tabela
        # Mas vamos primeiro tentar ver se podemos simplesmente adicionar as novas colunas
        cursor.execute("PRAGMA table_info(visit)")
        columns = [c[1] for c in cursor.fetchall()]
        
        if 'setor' not in columns:
            print("Adicionando coluna 'setor' à tabela 'visit'...")
            cursor.execute("ALTER TABLE visit ADD COLUMN setor VARCHAR(100)")
            
        # Para tornar ticket_id opcional no SQLite, o jeito mais seguro é recriar
        # Mas por enquanto, vamos apenas permitir que seja NULL se o SQLite permitir via ALTER (não permite geralmente)
        # Então vamos apenas adicionar a coluna 'assunto' também
        if 'assunto' not in columns:
            print("Adicionando coluna 'assunto' à tabela 'visit'...")
            cursor.execute("ALTER TABLE visit ADD COLUMN assunto VARCHAR(255)")
            
        conn.commit()
        print("Tabela 'visit' atualizada com sucesso.")
    except Exception as e:
        print(f"Erro ao atualizar tabela 'visit': {e}")
    finally:
        conn.close()

if __name__ == "__main__":
    fix_visit_table()
