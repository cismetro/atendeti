import sqlite3

def add_column():
    conn = sqlite3.connect('app.db')
    cursor = conn.cursor()
    try:
        cursor.execute("ALTER TABLE ticket_history ADD COLUMN detalhes TEXT")
        conn.commit()
        print("Column 'detalhes' added successfully to 'ticket_history' table.")
    except sqlite3.OperationalError as e:
        print(f"Error: {e}")
    finally:
        conn.close()

if __name__ == "__main__":
    add_column()
