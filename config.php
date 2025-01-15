<?php
/*	

Sistema Desenvolvido pelo DTI - COREN / SC - 2017

NESTE ARQUIVO CONFIGURAÇÕES GERAIS SÃO DEFINIDAS	
* Há também configurações na parte inicial do arquivo /common/js/app.js
** Há também a configuração do banco de dados no arquivo /conexao.php

MODELOS DE DOCUMENTOS FIXOS (POIS TEM REFERENCIA EM ALGUM LOCAL DO SISTEMA)
id 1 (regimentoInterno) -> pagina iniciar.php
id 9 (roteiro) -> site do Coren/SC

*/

//título para todas as páginas do sistema
define('APP_TITLE',	'Sistema de Comissões de Ética - Coren/SC');
//contração sem acentos do título do sistema
define('APP_TITLE2','Coren/SC - SCE');
//pasta de uploads
define('APP_UPLOAD_FOLDER','/uploads/');
//
/*	//SERVIDOR   				    */
//exibicao de erros
error_reporting(0);
ini_set("display_errors", 0 );
define('APP_SHOW_SQL_ERRORS',false);
//configuracoes da URL
define('APP_URL','https://jurere-homolog.corensc.gov.br/sistemas/sce');
define('APP_URL_UPLOAD','/usr/local/apache2/htdocs/corensc.gov.br/sistemas/sce'.APP_UPLOAD_FOLDER);
define('APP_FILE_ERROR_LOG','/usr/local/apache2/htdocs/corensc.gov.br/sistemas/sce'.APP_UPLOAD_FOLDER.'/logs/');
define('APP_URL_BACKUP','/var/adm/backup/sistemas/sce/');
define('APP_CAPTCHA_HOSTNAME','jurere-homolog.corensc.gov.br');
/*/CONFIGS E-MAIL - SERVIDOR :::
define('EMAIL_HOST','smtp.gmail.com');
define('EMAIL_USER','sistemas.coren@gmail.com');
define('EMAIL_PASS','7@.M3*z+WX');
define('EMAIL_SENDER','sistemas.coren@gmail.com');
define('EMAIL_PORT',465);
define('EMAIL_SMTP_SECURE',true);
define('EMAIL_SMTP_AUTH','ssl');
//   				    */
define('EMAIL_HOST','192.168.1.16');
define('EMAIL_USER','website@corensc.gov.br');
define('EMAIL_PASS','WE*$53by');
define('EMAIL_SENDER','website@corensc.gov.br');
define('EMAIL_PORT',465);
define('EMAIL_SMTP_SECURE',true);
define('EMAIL_SMTP_AUTH','ssl');


/*	//LOCAL
//exibicao de erros
error_reporting(E_ALL);
ini_set("display_errors", 1 );
define('APP_SHOW_SQL_ERRORS',true);
//configuracoes da URL
define('APP_URL','http://192.168.4.232/sce');
define('APP_URL_UPLOAD','C:/Apache24/htdocs/sce'.APP_UPLOAD_FOLDER);
define('APP_FILE_ERROR_LOG','C:/Apache24/htdocs/sce'.APP_UPLOAD_FOLDER.'/logs/');
define('APP_URL_BACKUP','C:/Apache24/htdocs/sce/@admin/');
//configuracoes do reCaptcha
define('APP_CAPTCHA_HOSTNAME','192.168.4.232');
//CONFIGS E-MAIL - LOCALHOST :::
define('EMAIL_HOST','192.168.1.16');
define('EMAIL_USER','website@corensc.gov.br');
define('EMAIL_PASS','WE*$53by');
define('EMAIL_SENDER','website@corensc.gov.br');
define('EMAIL_PORT',465);
define('EMAIL_SMTP_SECURE',true);
define('EMAIL_SMTP_AUTH','ssl');
//   				    */

//timezone padrão da aplicação
date_default_timezone_set('America/Sao_Paulo');
//nome específico das sessões no app
define('APP_SESSION_NAME','corenscsicee');
//reCaptcha - google
define('APP_CAPTCHA_SITE','https://www.google.com/recaptcha/api/siteverify?secret=');
define('APP_CAPTCHA_SITE_KEY','6LcLyQYTAAAAAHMdEubxQ9ShDo6CPuWpAa-LTGNB');
define('APP_CAPTCHA_SECRET_KEY','6LcLyQYTAAAAANjahnDBeAWuSp5hBYNpA6hTKnxB');
//logo
define('APP_SYS_LOGO','');
//charset padrao
define('APP_CHARSET','utf-8');
//PAGINACAO: numero de registros por pagina
define('APP_MAX_PAGE_ROWS',8);
//Tempo de sessão após o login/navegação entre páginas (10800 segs = 3 horas de sessão)
define('APP_SESSION_LIFETIME',10800);
//Número máximo de tentativas de acesso com credenciais erradas
define('APP_MAX_LOGIN_ATTEMPTS',10);
//Caracteres para quebra de linha no LOG OU separação de argumentos: (se mudar aqui deve-se mudar as do topo do "app.js" também)
define('APP_LINE_BREAK','@@@');
//Número Inteiro que define os registros ativos
define('APP_FLAG_ACTIVE',1);
//Número Inteiro que define os registros inativos
define('APP_FLAG_INACTIVE',2);
//Mensagem padrão para "campo obrigatório"
define('APP_MSG_REQUIRED','* Campo obrigatório');

//definição de valores para algumas especificidades (se mudar aqui deve-se mudar as do topo do "app.js" também, se houver configuração parecida lá)
//USUARIOS
//ID PERFIS
define('PERFIL_IDADMIN',1);
define('PERFIL_IDPRESIDENTE',2);
define('PERFIL_IDINSTITUICAO',3);
define('PERFIL_IDCOMISSAOETICA',4);
define('PERFIL_IDSECRETARIA',5);
define('PERFIL_IDFISCALIZACAO',6);
define('PERFIL_IDRESPONSAVEL',19);//também chamado de "Membro da CEC"
//PERFIS BLOQUEADOS PARA MANIPULAÇÃO (ADMIN E PRESIDENTE)
define('PERFIL_BLOCKED_SQL'," && p.idperfil <> '1'  && p.idperfil <> '2' ");
//ETAPAS
define('ETAPA_PRINCIPAL',0); 				//etapa do fluxo principal
define('ETAPA_ALTERNATIVA',1); 				//etapa do fluxo alternativo
define('ETAPA_NAO_APROVAVEL', 0); 			//etapa que não possui a caracteristica de ser aprovada ou não
define('ETAPA_AGUARDANDO_APROVACAO', 1); 	//etapa que precisa de aprovação
define('ETAPA_APROVADA', 2); 				//etapa aprovada
define('ETAPA_NAO_APROVADA', 3); 			//etapa não aprovada
define('ETAPA_NAO_ESCOLHE_DATA', 0); 		//etapa em que NÃO se escolhe uma data de posse
define('ETAPA_ESCOLHE_TIPO', 1); 			//etapa que escolhe (c/ ou s/ eleições)
define('ETAPA_ESCOLHE_RECURSO', 2); 		//etapa que escolhe (c/ ou s/ recursos ao pleito)
define('ETAPA_ESCOLHE_DATA', 1); 			//etapa em que se escolhe uma data de posse
define('ETAPA_NAO_BLOQUEIA_PROCESSO', 0); 	//etapa em que o processo NÃO fica bloqueado para qualquer modificação
define('ETAPA_BLOQUEIA_PROCESSO', 1); 		//etapa em que o processo fica bloqueado para qualquer modificação
define('ID_LAST_ETAPA', 19); 	//ID da última etapa
define('ETAPA_NAOMILITAR', 3);	//ID da etapa do tipo "NÃO MILITAR"
//PROCESSOS
define('PROCESSOETAPA_NORMAL', 0);	//ID do tipo "NORMAL"
define('PROCESSOETAPA_COMELEICOES', 1);	//ID do tipo "COM ELEIÇÕES"
define('PROCESSOETAPA_SEMELEICOES', 2);	//ID do tipo "SEM ELEIÇÕES"
define('PROCESSO_MILITAR', 1); //ID do tipo = MILITAR (processo)
//DOCUMENTOS
define('DOC_IDREGIMENTO', 1);		//ID do tipo de documento "Regimento Interno"
define('DOC_IDPARECER_TEC', 2);		//ID do tipo de documento "Parecer Técnico"
define('DOC_IDPARECER_TEC_H', 10);	//ID do tipo de documento "Parecer Técnico Homologado"
define('DOC_IDJUSTIFICATIVA', 11);	//ID do tipo de documento "Anexo da Justificativa de Não Aprovação"
define('DOC_IDLISTAINSCRITOS', 13);	//ID do tipo de documento "Lista de Pessoas Inscritas"
define('DOC_IDRECURSOS', 14);		//ID "Registro de recursos&respostas da Comissão Eleitoral"
//definição das funcoes do sistema
define('FUNCAO_PROCESSO_ADD',1);		#Adiciona Processo
define('FUNCAO_PROCESSO_EDIT',2);		#Edita Processo
define('FUNCAO_PROCESSO_DEL',3);		#Remove Processo
define('FUNCAO_DOCUMENTO_ADD',4);		#Adiciona documentos
define('FUNCAO_DOCUMENTO_EDIT',5);		#Edita documentos
define('FUNCAO_DOCUMENTO_DEL',6); 		#Remove documentos
define('FUNCAO_USUARIO_ADD',7);			#Adiciona usuários
define('FUNCAO_USUARIO_EDIT',8); 		#Edita usuários
define('FUNCAO_USUARIO_DEL',9);			#Remove usuários
define('FUNCAO_PERFIL_ADD',10);			#Adiciona perfis
define('FUNCAO_PERFIL_EDIT',11); 		#Edita perfis
define('FUNCAO_PERFIL_DEL',12);			#Remove perfis
define('FUNCAO_SUBSECAO_ADD',13);		#Adiciona subseções
define('FUNCAO_SUBSECAO_EDIT',14);		#Edita subseções
define('FUNCAO_SUBSECAO_DEL',15);		#Remove subseções
define('FUNCAO_HISTORICO_PRO',16);		#Visualiza histórico dos PADs
define('FUNCAO_HISTORICO_ALL',17); 		#Visualiza histórico geral do sistema
define('FUNCAO_MODELO_ADD',18);			#Adiciona modelo de documento
define('FUNCAO_MODELO_EDIT',19);		#Atualiza modelo de documento
define('FUNCAO_MODELO_DEL',20);			#Remove modelo de documento
define('FUNCAO_DOCUMENTOTIPO_ADD',21);	#Adiciona tipos de documento
define('FUNCAO_DOCUMENTOTIPO_EDIT',22);	#Edita tipos de documento
define('FUNCAO_DOCUMENTOTIPO_DEL',23);	#Remove tipos de documento
define('FUNCAO_ETAPA_ADD',24);			#Adiciona etapas
define('FUNCAO_ETAPA_EDIT',25);			#Edita etapas
define('FUNCAO_ETAPA_DEL',26);			#Remove etapas
//definicao das ações do log:
define('LOG_LOGIN_USER',1);		#Efetuou login
define('LOG_ADD_PRO',2);		#Inseriu Processo
define('LOG_EDIT_PRO',3);		#Atualizou Processo
define('LOG_DEL_PRO',4);		#Removeu Processo
define('LOG_ADD_DOC',5);		#Inseriu documento
define('LOG_EDIT_DOC',6);		#Atualizou documento
define('LOG_DEL_DOC',7);		#Removeu documento
define('LOG_VIEW_DOC',8);		#Visualizou documento
define('LOG_ADD_USER',9);		#Inseriu usuário
define('LOG_EDIT_USER',10);		#Atualizou usuário
define('LOG_DEL_USER',11);		#Removeu usuário
define('LOG_ADD_PERFIL',12);	#Inseriu perfil de usuário
define('LOG_EDIT_PERFIL',13);	#Atualizou perfil de usuário
define('LOG_DEL_PERFIL',14);	#Removeu perfil de usuário
define('LOG_ADD_TIPODOC',15);	#Inseriu tipo de documento
define('LOG_EDIT_TIPODOC',16);	#Atualizou tipo de documento
define('LOG_DEL_TIPODOC',17);	#Removeu tipo de documento
define('LOG_UPDATE_USER',18);	#Usuário atualizou seus dados
define('LOG_EMAIL',19);			#Enviou e-mail
define('LOG_ADD_MODELO',20);	#Adicionou modelo de documento
define('LOG_EDIT_MODELO',21);	#Atualizou modelo de documento
define('LOG_DEL_MODELO',22);	#Removeu modelo de documento
define('LOG_ADD_SUBSECAO',23);	#Adicionou subseção
define('LOG_EDIT_SUBSECAO',24);	#Atualizou subseção
define('LOG_DEL_SUBSECAO',25);	#Removeu subseção
define('LOG_ADD_ETAPA',26);		#Adicionou etapa
define('LOG_EDIT_ETAPA',27);	#Atualizou etapa
define('LOG_DEL_ETAPA',28);		#Removeu etapa
define('LOG_EMAIL_INVALIDO',29);#Não enviou e-mail
define('LOG_UPDATE_PRO',30);	#Atualizou etapa do processo
define('LOG_RECOVER_USER',31);	#Recuperou dados de acesso
define('LOG_DEL_UPLOAD',32);	#Removeu arquivo do FTP
define('LOG_UPDATE_SENHA',33);	#Trocou senha (changepassword)
//definições de tamanho dos campos no banco de dados = MAIS SEGURANÇA
//processo
define('PROCESSO_ID_SIZE',11);
define('PROCESSO_DTPOSSE1_SIZE',19);
define('PROCESSO_DTPOSSE2_SIZE',19);
define('PROCESSO_DTPOSSE3_SIZE',19);
define('PROCESSO_MILITAR_SIZE',1);
//usuario
define('USUARIO_ID_SIZE',10);
define('USUARIO_IDPERFIL_SIZE',10);
define('USUARIO_NOME_SIZE',200);
define('USUARIO_NOMEINSTITUICAO_SIZE',200);
define('USUARIO_LOGIN_SIZE',50);
define('USUARIO_SENHA_SIZE',50);
define('USUARIO_DTEXPIRACAO_SIZE',8);
define('USUARIO_FONE_SIZE',15);
define('USUARIO_EMAIL_SIZE',255);
//perfil
define('PERFIL_ID_SIZE',11);
define('PERFIL_NOME_SIZE',50);
//etapa
define('ETAPA_ID_SIZE',11);
define('ETAPA_NOME_SIZE',150);
define('ETAPA_ORDEM_SIZE',4);
define('ETAPA_FLUXO_SIZE',1);
define('ETAPA_APROVA_SIZE',1);
define('ETAPA_NUMDOCS_SIZE',1);
define('ETAPA_ESCOLHEDATA_SIZE',1);
define('ETAPA_BLOQUEAR_SIZE',1);
define('ETAPA_EXPIRA_SIZE',3);
define('ETAPA_PRAZO_SIZE',2);
define('ETAPA_MODO_SIZE',1);
define('ETAPA_ETAPATIPO_SIZE',1);
//processotipo
define('PROCESSOTIPO_ID_SIZE',10);
//documento
define('DOCUMENTO_ID_SIZE',10);
//documentotipo
define('DOCUMENTOTIPO_ID_SIZE',10);
define('DOCUMENTOTIPO_NOME_SIZE',200);
//municipio
define('MUNICIPIO_ID_SIZE',10);
//subsecao
define('SUBSECAO_ID_SIZE',10);
define('SUBSECAO_NOME_SIZE',200);
//modelo
define('MODELO_ID_SIZE',10);
define('MODELO_NOME_SIZE',200);
?>
