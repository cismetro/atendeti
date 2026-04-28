import sqlite3
import os

def check_tables():
    if not os.path.exists('app.db'):
        print("Database file not found.")
        return

    conn = sqlite3.connect('app.db')
    cursor = conn.cursor()
    
    tables = ['user', 'ticket', 'ticket_history', 'notification', 'visit', 'settings']
    
    for table in tables:
        print(f"\nTable: {table}")
        try:
            cursor.execute(f"PRAGMA table_info({table})")
            columns = cursor.fetchall()
            for col in columns:
                print(f"  {col[1]} ({col[2]})")
        except sqlite3.OperationalError as e:
            print(f"  Error accessing table: {e}")
            
    conn.close()

if __name__ == "__main__":
    check_tables()
