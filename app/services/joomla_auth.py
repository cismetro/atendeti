import pymysql
import logging
from flask import request, current_app
from app.config import Config

logger = logging.getLogger(__name__)

def _get_joomla_db():
    return pymysql.connect(
        host=current_app.config['JOOMLA_DB_HOST'],
        port=current_app.config['JOOMLA_DB_PORT'],
        user=current_app.config['JOOMLA_DB_USER'],
        password=current_app.config['JOOMLA_DB_PASS'],
        database=current_app.config['JOOMLA_DB_NAME'],
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )

def get_joomla_session():
    """
    Verifica se existe uma sessão ativa no Joomla baseada nos cookies da requisição.
    Retorna os dados do usuário se a sessão for válida.
    """
    try:
        # Joomla usa cookies com nomes de 32 caracteres hexadecimais para o session ID
        for cookie_name, cookie_val in request.cookies.items():
            if len(cookie_name) == 32:
                prefix = current_app.config['JOOMLA_TABLE_PREFIX']
                conn = _get_joomla_db()
                with conn.cursor() as cur:
                    # Busca a sessão e os dados do usuário associado
                    cur.execute(
                        f"SELECT s.userid, u.username, u.name, u.email "
                        f"FROM {prefix}session s "
                        f"JOIN {prefix}users u ON s.userid = u.id "
                        f"WHERE s.session_id=%s "
                        f"AND s.userid > 0 "
                        f"AND s.time > (UNIX_TIMESTAMP() - 7200)", # Sessão ativa nas últimas 2 horas
                        (cookie_val,)
                    )
                    sess = cur.fetchone()
                
                if sess:
                    # Busca os grupos do usuário para determinar permissões (ACL)
                    cur.execute(
                        f"SELECT g.title FROM {prefix}usergroups g "
                        f"JOIN {prefix}user_usergroup_map m ON g.id=m.group_id "
                        f"WHERE m.user_id=%s",
                        (sess['userid'],)
                    )
                    groups = [r['title'] for r in cur.fetchall()]
                    sess['groups'] = groups
                    
                    conn.close()
                    return sess
                conn.close()
    except Exception as e:
        logger.error(f"Erro ao verificar sessão Joomla: {e}")
    return None

def sync_joomla_user(joomla_sess):
    """
    Sincroniza um usuário do Joomla com o banco de dados local do AtendeTI.
    Se o usuário não existir, cria um novo.
    """
    from app.models import User
    from app import db
    
    user = User.query.filter_by(email=joomla_sess['email']).first()
    
    # Lista de grupos que definem acesso de Administrador/TI no Joomla
    ti_groups = ['Super Users', 'Administrator', 'TI', 'Suporte', 'Informatica']
    is_ti = any(group in ti_groups for group in joomla_sess['groups'])
    
    # Mapeamento do Setor: pega o primeiro grupo que não seja genérico
    generic_groups = ['Public', 'Registered', 'Guest', 'Author', 'Editor', 'Publisher', 'Super Users', 'Administrator']
    setor = "Geral"
    for group in joomla_sess['groups']:
        if group not in generic_groups:
            setor = group
            break

    if not user:
        user = User(
            nome=joomla_sess['name'],
            email=joomla_sess['email'],
            setor=setor,
            is_ti=is_ti
        )
        # Senha aleatória já que o login é via SSO
        user.set_password("SSO_JOOMLA_" + joomla_sess['username'])
        db.session.add(user)
        db.session.commit()
    else:
        # Atualiza informações se necessário
        changed = False
        if user.nome != joomla_sess['name']:
            user.nome = joomla_sess['name']
            changed = True
        if user.setor != setor:
            user.setor = setor
            changed = True
        if user.is_ti != is_ti:
            user.is_ti = is_ti
            changed = True
        
        if changed:
            db.session.commit()
            
    return user
