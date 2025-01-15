<?php
require_once("config.php");
require_once("bin/errors.php");
require_once("bin/functions.php");
require_once("bin/js-css.php");
//inclui verificação se o usuário está online ou não
require_once("usuario_com_internet.php");
header("Content-Type: text/html; charset=".APP_CHARSET,true);
@session_start();

//pega infos do Usuário
//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("conexao.php");
require_once("model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's p/ retornar infos
require_once('dao/MunicipioDAO.php');
require_once('dao/ProcessoDAO.php');
require_once('dao/EtapaDAO.php');
require_once('dao/UsuarioDAO.php');
require_once('dao/DocumentoDAO.php');
require_once('dao/SubsecaoDAO.php');
require_once('dao/HistoricoDAO.php');
//carrega MODEL's 
require_once('model/Processo.php');
require_once('model/Usuario.php');
require_once('model/Historico.php');
require_once('model/Documento.php');
require_once('model/Etapa.php');

if(isset($_POST) && !empty($_POST)){

	if($usuario_com_internet && (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response']))){

		enviaMsg("erro","O processo não foi iniciado","Você precisa clicar no quadrado \"Não sou um robô\" antes de enviar o formulário");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
		exit();

	}

	//atribui os valores do POST a sessão pois caso dê problemas no envio, retoma a página com informações
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]=$value;
	}	

	//TRATAMENTO DO CAPTCHA
		//se o usuário tem internet, CAPTCHA obrigatório
		if($usuario_com_internet){
			//https://www.google.com/recaptcha/admin#site/319211787
			//variável para controle do Captcha
			$captcha_ok = false;
			//recebe dados do post
			$captcha_data = $_POST['g-recaptcha-response'];
			//verifica se o usuário NÃO enviou dados
			if (!$captcha_data) {
			    enviaMsg("erro","Processo não iniciado","Por favor confirme que você não é um robô antes de enviar o formulário");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
				exit();
			}
			//envia requisição para o Google com os dados enviados pelo usuário e as configurações da API
			$recaptcha = @file_get_contents(APP_CAPTCHA_SITE.APP_CAPTCHA_SECRET_KEY."&response=".$captcha_data."&remoteip=".$_SERVER['REMOTE_ADDR']);
			//se ele encontrar na resposta do Google algum "success": true   é pq o captcha está certo!
			if(strpos($recaptcha, "\"success\": true") !== false) {
				//o IF abaixo deve ser removido se deixarmos o google verificar os domínios sozinho:
				if(strpos($recaptcha, "\"hostname\": \"".APP_CAPTCHA_HOSTNAME."\"") !== false){
					$captcha_ok=true;	
				}
			}
		//fim if "user com internet" - se não tiver internet, pula o captcha
		}else{
			$captcha_ok=true;
		}
	//se passou na validação do captcha, dá prosseguimento ao POST
	if($captcha_ok){

		//verifica cada campo obrigatório
		$idprocessotipo		=	sqlTrataInteiro(validaInteiro($_POST["idprocessotipo"],PROCESSOTIPO_ID_SIZE));
		$nome_instituicao	=	sqlTrataString(validaLiteral($_POST["nome_instituicao"],USUARIO_NOMEINSTITUICAO_SIZE));		
		$idmunicipio 		=	sqlTrataInteiro(validaInteiro($_POST["idmunicipio"],MUNICIPIO_ID_SIZE));
		$nome 				=	sqlTrataString(validaLiteral($_POST["nome"],USUARIO_NOME_SIZE));
		$email1 			=	sqlTrataString(validaLiteral($_POST["email1"],USUARIO_EMAIL_SIZE));
		$email2 			=	sqlTrataString(validaLiteral($_POST["email2"],USUARIO_EMAIL_SIZE));
		$militar 			=	sqlTrataInteiro(validaInteiro($_POST["militar"],		PROCESSO_MILITAR_SIZE));
		//remove caracteres além do número do celular
		$celular=$_POST["celular"];
		$celular=str_replace("(", "", $celular);
		$celular=str_replace(")", "", $celular);
		$celular=str_replace("-", "", $celular);
		$celular=str_replace(" ", "", $celular);
		$celular=sqlTrataString(validaLiteral($celular,USUARIO_FONE_SIZE));
		//remove caracteres além do número do telefone
		$telefone=$_POST["telefone"];
		$telefone=sqlTrataString($telefone);
		$telefone=str_replace("(", "", $telefone);
		$telefone=str_replace(")", "", $telefone);
		$telefone=str_replace("-", "", $telefone);
		$telefone=str_replace(" ", "", $telefone);
		$telefone=sqlTrataString(validaLiteral($telefone,USUARIO_FONE_SIZE));

		if(strlen($telefone)<10 || strlen($celular)<10){
			enviaMsg("erro","Número de telefone ou celular inválido","Por favor informe o DDD com 2 dígitos seguido pelo número do telefone/celular com 8 ou 9 dígitos. Ex.: (48) 98765-4321");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
			exit();
		}

		//verifica se todos campos obrigatórios foram preenchidos
		if( $idprocessotipo!==false && $nome_instituicao!==false && $idmunicipio!==false  && $nome!==false && $email1!==false && $celular!==false  && $telefone!==false && $militar!==false){

			
			//cria usuário para a instituição
			$Usuario = new Usuario();
			$Usuario->setNome($nome);
			$Usuario->setPerfil(PERFIL_IDINSTITUICAO);
			$Usuario->setNomeInstituicao($nome_instituicao);
			$Usuario->setMunicipio($idmunicipio);				

			// Instanciar o DAO para inserir/consultar infos usuarios
			$UsuarioDAO = new UsuarioDAO();

			//cria novo login no padrão nome.sobrenome
			$login=strtolower(removeCaracteresEspeciais($nome));
			$nome1 = trim(strstr($login, " ", true));
			$nome2 = trim(substr(strrchr($login, " "), 1));
			//se for diferente
			if($nome1!=$nome2){
				$login = $nome1.".".$nome2;
			}
			$Usuario->setLogin($login);
			// Chama a função de verificação de duplicidade que só resulta FALSE caso dê algum problema.
			$possuiDuplicidade = $UsuarioDAO->isDuplicated($Usuario);
			//se der erro, tenta 50 vezes encontrar um disponível
			if($possuiDuplicidade!==false){
				for ($i=1; $i < 50; $i++) { 
					$aux=$login.$i;
					$Usuario->setLogin($aux);
					$possuiDuplicidade = $UsuarioDAO->isDuplicated($Usuario);
					//se achou um disponível, sai do lopping e atribui esse valor à variavel $novoLogin
					if($possuiDuplicidade==false){
						$login=$aux;
						$Usuario->setLogin($login);
						break;
					}
				}	
				//se após 50 tentativas continuar com duplicidade, exibe msg para falar com admin
				if($possuiDuplicidade!==false){
					echo "<div class='well'><br><br><br>Ops, temos um problema com seu login!<br><br>Não foi possível criar um <strong>novo login</strong> válido para você pois o login '<strong>".$login."</strong>' já está em uso.<br><br>Por favor, entre em contato com o Coren/SC informando a tentativa automática de criação do usuário <strong>".$login."</strong><br><br>Tente iniciar novamente o processo somente após contato com o Departamento de Tecnologia de Informação (DTI) do Coren/SC.<br><br>Lamentamos o transtorno, obrigado.</div>";
					exit();
				}	
			}

			// cria senha com caracteres obrigatórios de senha
			//1 letra maiúscula
			$senha_c_obg1="ABCDEFGHJKLMNPQRSTUVWXYZ";
			//1 letra minúscula
			$senha_c_obg2="abcdefghijkmnpqrstuvwxyz";
			//1 número
			$senha_c_obg3="23456789";
			//1 caracter especial
			$senha_c_obg4="!@#$%&*[]({})=:;,.?";
			$senha = $senha_c_obg1[mt_rand(0,23)].$senha_c_obg3[mt_rand(0,7)].$senha_c_obg1[mt_rand(0,23)].$senha_c_obg4[mt_rand(0,18)].$senha_c_obg2[mt_rand(0,23)].$senha_c_obg3[mt_rand(0,7)].$senha_c_obg2[mt_rand(0,23)];

			//atribui login e senha definidos
			$Usuario->setLogin($login);
			$Usuario->setSenha(codifica($senha));
		
			//pega Subseção correspondente do municipio:
			$SubsecaoDAO = new SubsecaoDAO();
			$idsubsecao = $SubsecaoDAO->getSubsecaoFromMunicipio($idmunicipio);

			//se tiver encontrado subseção atribui o valor, se não põe como nulo
			if($idsubsecao>0){
				$idsubsecao=$idsubsecao["idsubsecao"];
				$Usuario->setSubsecao($idsubsecao);
			}else{
				$idsubsecao=0;
				$Usuario->setSubsecao(0);
			}
			$Usuario->setEmail1($email1);
			if($email2){
				$Usuario->setEmail2($email2);
			}else{
				$Usuario->setEmail2(NULL);
			}
			$Usuario->setCelular($celular);
			if($telefone){
				$Usuario->setTelefone($telefone);
			}else{
				$Usuario->setTelefone(NULL);
			}
			//expira o login 90 dias após a abertura dele no sistema
			$timestamp = strtotime("+3 month");
			$dtexpiracao = date('Ymd', $timestamp);
			$Usuario->setDtExpiracao($dtexpiracao);
			
			// Chama a função que verifica duplicidade do login
			$possuiDuplicidade = $UsuarioDAO->isDuplicated($Usuario);
			if($possuiDuplicidade!==false){
				//como está duplicado, adiciona um número ao final do login
				$login.=mt_rand(10,999);
				$Usuario->setLogin($login);
				//verifica novamente se o login está duplicado
				$possuiDuplicidade = $UsuarioDAO->isDuplicated($Usuario);
				if($possuiDuplicidade!==false){
					enviaMsg("erro","Processo não iniciado","Houve falha na criação do seu usuário ($login), tente novamente");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php>";
					exit();
				}
			}
			// Chama a função de inserção que só resulta FALSE caso dê algum problema.
			//insere usuário passando parâmetro 1 para a troca de senha pois esse usuário não precisa trocar a senha
			$idusuario = $UsuarioDAO->insert($Usuario,1);

			if($idusuario){

				//define que o usuário já tem um processo iniciado
				$_SESSION["temProcesso"]=true;

				//atribui ID via método do Usuário
				$Usuario->setId($idusuario);

				//instancia objeto DAO para retornar dados sobre a próxima etapa
				$EtapaDAO = new EtapaDAO();				

				// Instanciar as infos do processo
				$Processo = new Processo();			
				// Instanciar o DAO para inserir na base
				$ProcessoDAO = new ProcessoDAO();
				$numero = $ProcessoDAO->getNumero();
				//recebe o próximo número a ser utilizado para o processo
				if(!empty($numero["numero"])){
					$numero = $numero["numero"];	
				//caso não haja processo seta como número 1
				}else{
					$numero = 1;
				}
				
				$Processo->setUsuario($idusuario);
				$Processo->setProcessoTipo($idprocessotipo);
				$Processo->setDtCriacao(date("Ymd"));
				$Processo->setNumero($numero);
				$Processo->setMilitar($militar);
				if($militar==PROCESSO_MILITAR){
					$Processo->setModo(PROCESSOETAPA_SEMELEICOES);
					//recupera segunda etapa do processo (pois a primeira está sendo feita aqui já), mas não qualquer uma, a primeira posterior a nº 1, que seja do modo "sem eleições"
					$result = $EtapaDAO->getSecond(PROCESSOETAPA_SEMELEICOES);
					//define essa como a etapa do processo
					$Processo->setEtapa($result["idetapa"]);
				}else{
					$Processo->setModo(PROCESSOETAPA_NORMAL);
					//recupera segunda etapa do processo (pois a primeira está sendo feita aqui já)
					$result = $EtapaDAO->getSecond();
					//define essa como a etapa do processo
					$Processo->setEtapa($result["idetapa"]);
				}

				$idprocesso=$ProcessoDAO->insert($Processo);
				if($idprocesso){		

					//recupera o nome do tipo de processo (implantação/renovação)
					$ProcessoDAO = new ProcessoDAO();
					$tipoprocesso = $ProcessoDAO->getOneTipo($idprocessotipo);

					//SALVA HISTÓRICO
					//se inseriu o documento com sucesso => SALVAR NO HISTÓRICO
					$Historico = new Historico();
					$Historico->setAcao(LOG_ADD_PRO);
					$Historico->setProcesso($idprocesso);
					$Historico->setUsuario($idusuario);
					$Historico->setDocumento(0);
					$Historico->setObs("Iniciado pela instituição");
					$HistoricoDAO = new HistoricoDAO();
					$inseriuLog=$HistoricoDAO->insert($Historico);
					if(!$inseriuLog){
						enviaMsg("erro","Processo iniciado com erros","O histórico do Processo ($idprocesso) não pôde ser salvo");
					}
					
					//MANDA EMAILS
					$tipoajaxmail="iniciar";
					require_once("control/mail.php");

					//salva etapa no histórico etapa_processo
					$Etapa = new Etapa();
					$Etapa->setId($result["idetapa"]);
					$Etapa->setUsuario1($idusuario);
					$Etapa->setProcesso($idprocesso);
					$Etapa->setAprova(ETAPA_AGUARDANDO_APROVACAO);
					$Etapa->setAprovaMsg(NULL);
					$inseriuEtapaProcesso = $EtapaDAO->insertEtapaProcesso($Etapa);

					//apos enviar o e-mail redireciona o usuário para tela de login
					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=5&n=".$tipoprocesso["nome"]."&e1=".$Usuario->getEmail1()."&e2=".$Usuario->getEmail2()."\">";
					exit();

				}else{
					enviaMsg("erro","Problemas ao criar um novo processo para sua instituição","Por favor tente novamente mais tarde.");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
					exit();
				}

			//não inseriu usuário
			}else{
				enviaMsg("erro","Problemas ao criar o usuário para sua instituição","Por favor tente novamente mais tarde.");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
				exit();
			}

		//cai aqui caso nem todos campos obrigatórios não tenham sido preenchidos
		}else{
			enviaMsg("erro","Processo não iniciado","Um ou mais dados informados estão inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
			exit();
		}

	}else{
		enviaMsg("erro","Processo não iniciado","Você precisa clicar no quadrado \"Não sou um robô\" antes de enviar o formulário");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=iniciar.php\">";
		exit();
	}

}

//inicializa as variaveis com valor para não precisar fazer IF's mais elaborados no form
$nome_instituicao = false;
$idmunicipio = false;
$idprocessotipo = false;
$nome = false;
$email1 = false;
$email2 = false;
$celular = "";
$telefone = false;

//verifica se foi atribuido valores por SESSION e atribui com o mesmo nome
foreach ($_SESSION as $key => $value) {
	$$key = $value;
}
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo APP_CHARSET; ?>" />
<title><?php echo APP_TITLE; ?></title>
<link rel="shortcut icon" href="<?php echo APP_URL.'/favicon.ico'; ?>" />
<?php 
//se o usuário estiver ONLINE, faz a verificação com Captcha, se não não
if ($usuario_com_internet){ echo "<script src='https://www.google.com/recaptcha/api.js'></script>"; }
?>
</head>
<body>
<div id="msg_erro" class="alert alert-danger noprint" role="alert"></div>
<div id="msg_sucesso" class="alert alert-success noprint" role="alert"></div>
<div id="msg_atencao" class="alert alert-warning noprint" role="alert"></div>
<?php
  //se houver mensagens as exibe aqui (veja arquivo \bin\functions)
  if(temMsg()){
    exibeMsg();
  }
?>
		<div id="conteudo_borda">
			<p class="iniciar_titulo_sistema">&nbsp;</p>
			<?php
				if(isset($_SESSION["temProcesso"]) && $_SESSION["temProcesso"]===true){
					echo "<div class=\"well bg-msg\"><h4 class=\"bg-danger\"><strong>(!) Atenção (!)</strong><br><br>Você já iniciou um novo processo hoje, tem certeza de que quer criar mais um agora?<br><br>Por favor, confira a caixa de entrada / caixa de spam e lixo eletrônico dos e-mails informados na criação do processo anterior antes de continuar pois você já deve ter recebido um login e senha para acesso ao sistema.<br>Caso tenha lembrado dos dados de acesso, <a href='".APP_URL."'>clique aqui</a>.<br>Se ainda assim tiver certeza de que quer criar um novo processo, preencha os campos abaixo.<br><br>Obrigado!<br>".APP_TITLE."</h4></div>";
				}
				?>
			<div class="well bg-msg">
				<p><strong>Bem vindo(a)! Você está prestes a iniciar o processo de Implantação/Renovação da Comissão de Ética de Enfermagem (CEE) na sua instituição</strong>!
				<br>Preencha os campos abaixo para gerar um novo usuário e senha que serão encaminhados para os e-mails informados no formulário.
				<br>Após receber <em>login</em> e senha, utilize o <strong>Sistema de Comissões de Ética</strong> para dar prosseguimento, seguindo as orientações descritas em cada etapa do processo de implantação/renovação da CEE.</p>
			</div>
			<div  id="conteudo"><h5 class='onlyprint'>Iniciar Processo de Implantação/Renovação da Comissão de Ética de Enfermagem</h5>
				<form enctype="multipart/form-data" id="iniciar" name="iniciar" action="iniciar.php" method="post" class="form-horizontal">
					
					<div class="form-group">
		                <label class="col-sm-10 control-label">1) É a primeira Comissão de Ética de Enfermagem da sua instituição (Implantação) ou trata-se de uma renovação da Comissão?</label>
		                <div class="col-lg-5">
		                  <select id="idprocessotipo"  name="idprocessotipo" class="form-control">
							<option value="-1" <?php if(!$idprocessotipo) echo "selected=\"selected\""; ?>>Selecione </option>
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $ProcessoDAO = new ProcessoDAO();
                    		$dados = $ProcessoDAO->getTipos();
		                    if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){
									$d_opcao = "";
									if($idprocessotipo==$dados[$i]["idprocessotipo"]){
										$d_opcao = " selected=\"selected\" ";
									}
									echo "<option ".$d_opcao." value=\"".$dados[$i]["idprocessotipo"]."\">".$dados[$i]["nome"]."</option>";
								}
							}
							?>
		                  </select>
		                  <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

					<div class="form-group">
		                <label class="col-sm-10 control-label">2) Nome completo da instituição (sem abreviações)</label>
		                <div class="col-lg-8">
		                    <input type="text" class="form-control" id="nome_instituicao" name="nome_instituicao" value="<?php if($nome_instituicao) echo $nome_instituicao; ?>" placeholder="Nome completo da instituição" maxlength="<?php echo USUARIO_NOMEINSTITUICAO_SIZE-20; ?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">3) Município onde a instituição está localizada</label>
		                <div class="col-lg-5">
		                  <select id="idmunicipio"  name="idmunicipio" class="form-control">
							<option value="-1" <?php if(!$idmunicipio) echo "selected=\"selected\""; ?>>Selecione o município em que a instituição está localizada</option>
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $MunicipioDAO = new MunicipioDAO();
		                    $dados = $MunicipioDAO->getAll();
		                    if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){
									$d_opcao = "";
									if($idmunicipio==$dados[$i]["idmunicipio"]){
										$d_opcao = " selected=\"selected\" ";
									}
									echo "<option ".$d_opcao." value=\"".$dados[$i]["idmunicipio"]."\">".$dados[$i]["nome"]."</option>";
								}
							}
							?>
		                  </select>
		                  <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">4) Nome completo do responsável pelo processo de implantação ou renovação da CEE no Sistema de Comissões de Ética</label>
		                <div class="col-lg-8">
		                    <input type="text" id="nome" name="nome" placeholder="Nome completo do responsável pela Comissão de Ética na instituição" maxlength="<?php echo USUARIO_NOME_SIZE-20; ?>" class="form-control camposenha" value="<?php if($nome) echo $nome;?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">5) E-mail principal do responsável</label>
		                <div class="col-lg-8">
		                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email1" name="email1" placeholder="E-mail principal do responsável" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php if($email1) echo $email1;?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?> (usado para enviar login/senha e atualizações do processo)</span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">6) E-mail secundário do responsável ou e-mail da instituição</label>
		                <div class="col-lg-8">
		                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email2" name="email2" placeholder="E-mail secundário do responsável ou e-mail da instituição" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php if($email2) echo $email2;?>">
		                    <span id="helpBlock" class="help-block">* Não obrigatório porém também usado para enviar login/senha e atualizações do processo</span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">7) Telefone do responsável ou da instituição (com DDD abreviado para 2 dígitos. Ex.: 48)</label>
		                <div class="col-lg-3">
		                    <input type="text" class="form-control campocelular" id="telefone" name="telefone" placeholder="Telefone do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE; ?>" value="<?php if($telefone) echo $telefone;?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">8) Celular do responsável (com DDD abreviado para 2 dígitos. Ex.: 48)</label>
		                <div class="col-lg-3">
		                    <input type="text" class="form-control campocelular" id="celular" name="celular" placeholder="Celular do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE; ?>" value="<?php if($celular) echo $celular;?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

			        <div class="form-group">

		            	<label for="militar" class="col-sm-12 control-label">9) Sua instituição é uma instituição militar?</label>
					    <div class="col-sm-2">
					      <select id="militar" name="militar" class="form-control">
							<option value="-1">Selecione uma opção</option>
							<?php
								echo "<option value=\"".PROCESSO_MILITAR."\">Sim</option>";
								echo "<option value=\"".PROCESSOETAPA_NORMAL."\">Não</option>";
							?>
		                  </select>
					      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
					    </div>
					    
		            </div>
			        <?php
			        //se o usuário estiver online faz a verificação com Captcha
			        if($usuario_com_internet){
			        ?>
		            <div class="form-group">
		            	<label class="col-sm-10 control-label">10) Confirme que você não é um robô</label>
		                <div class="col-lg-8">
		            		<div class="g-recaptcha" data-sitekey="<?php echo APP_CAPTCHA_SITE_KEY; ?>"></div>
		            		<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		            	</div>
		        	</div>
		        	<?php
		        	}
		        	?>

		            <div class="form-group" style="margin-top:20px;">
		              <div class="col-lg-10">
		                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iniciar Processo de Implantação/Renovação da Comissão de Ética&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		              </div>
		            </div>	
				</form>
			</div>
		</div>
<?php
	require_once("menu_rodape.php");
?>		
</body>
</html>