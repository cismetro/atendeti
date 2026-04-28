import smtplib
import json
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from app.models import Settings

def send_notification_email(to_email, subject, body):
    """
    Envia um e-mail de notificação usando as configurações SMTP salvas no banco de dados.
    """
    try:
        # Busca a configuração no banco
        config_record = Settings.query.filter_by(chave='smtp_config').first()
        if not config_record:
            print("Erro: Configurações de SMTP não encontradas no banco de dados.")
            return False
            
        config = json.loads(config_record.valor)
        
        # Prepara a mensagem
        msg = MIMEMultipart()
        msg['From'] = f"{config.get('nome_remetente')} <{config.get('email_remetente')}>"
        msg['To'] = to_email
        msg['Subject'] = subject
        
        msg.attach(MIMEText(body, 'html'))
        
        # Conecta e envia
        server = smtplib.SMTP(config.get('servidor'), int(config.get('porta')))
        if config.get('use_tls', True):
            server.starttls()
            
        server.login(config.get('usuario'), config.get('senha'))
        server.send_message(msg)
        server.quit()
        
        print(f"E-mail enviado com sucesso para {to_email}")
        return True
    except Exception as e:
        print(f"Erro ao enviar e-mail: {e}")
        return False
