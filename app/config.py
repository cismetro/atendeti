import os
basedir = os.path.abspath(os.path.dirname(__file__))

class Config:
    SECRET_KEY = os.environ.get('SECRET_KEY') or 'atendeti-chave-muito-secreta-123'
    SQLALCHEMY_DATABASE_URI = os.environ.get('DATABASE_URL') or \
        'sqlite:///' + os.path.join(basedir, '..', 'app.db')
    SQLALCHEMY_TRACK_MODIFICATIONS = False

    # Configurações do Joomla
    JOOMLA_DB_HOST = os.environ.get('JOOMLA_DB_HOST') or 'localhost'
    JOOMLA_DB_PORT = int(os.environ.get('JOOMLA_DB_PORT') or 3306)
    JOOMLA_DB_USER = os.environ.get('JOOMLA_DB_USER') or 'root'
    JOOMLA_DB_PASS = os.environ.get('JOOMLA_DB_PASS') or ''
    JOOMLA_DB_NAME = os.environ.get('JOOMLA_DB_NAME') or 'joomla'
    JOOMLA_TABLE_PREFIX = os.environ.get('JOOMLA_TABLE_PREFIX') or 'jos_'
