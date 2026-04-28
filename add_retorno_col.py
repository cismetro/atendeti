import sqlite3

def add_retorno_column():
    conn = sqlite3.connect('app.db')
    cursor = conn.cursor()
    
    try:
        cursor.execute("PRAGMA table_info(visit)")
        columns = [c[1] for c in cursor.fetchall()]
        
        if 'hora_retorno' not in columns:
            print("Adicionando coluna 'hora_retorno' à tabela 'visit'...")
            cursor.execute("ALTER TABLE visit ADD COLUMN hora_retorno VARCHAR(10)")
            conn.commit()
            print("Coluna adicionada com sucesso.")
        else:
            print("Coluna 'hora_retorno' já existe.")
    except Exception as e:
        print(f"Erro ao adicionar coluna: {e}")
    finally:
        conn.close()

if __name__ == "__main__":
    add_retorno_column()
