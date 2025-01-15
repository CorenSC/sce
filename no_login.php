<?php
require_once("config.php");
require_once("bin/errors.php");
require_once("bin/functions.php");
require_once("bin/js-css.php");
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

//1 POST - quando ele ainda precisa provar que é o responsável pela instituição
if(isset($_POST["idprocessotipo"]) && !empty($_POST["idprocessotipo"])){

	//atribui os valores do POST a sessão pois caso dê problemas no envio, retoma a página com informações
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]=$value;
	}	

	if(!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])){
		enviaMsg("erro","Nem todos os dados para recuperação do acesso foram preenchidos","Você precisa clicar no quadrado \"Não sou um robô\" antes de enviar o formulário");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
		exit();
	}

	//TRATAMENTO DO CAPTCHA
		//https://www.google.com/recaptcha/admin#site/319211787
		//variável para controle do Captcha
		$captcha_ok = false;
		//recebe dados do post
		$captcha_data = $_POST['g-recaptcha-response'];
		//verifica se o usuário NÃO enviou dados
		if (!$captcha_data) {
		    enviaMsg("erro","Login não verificado","Por favor confirme que você não é um robô antes de enviar o formulário");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
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

	//se passou na validação do captcha, dá prosseguimento ao POST
	if($captcha_ok){

		//verifica cada campo obrigatório
		$idprocessotipo		=	sqlTrataInteiro(validaInteiro($_POST["idprocessotipo"],PROCESSOTIPO_ID_SIZE));
		$nome_instituicao	=	sqlTrataString(validaLiteral($_POST["nome_instituicao"],USUARIO_NOMEINSTITUICAO_SIZE));		
		$idmunicipio 		=	sqlTrataInteiro(validaInteiro($_POST["idmunicipio"],MUNICIPIO_ID_SIZE));
		$email1 			=	sqlTrataString(validaLiteral($_POST["email1"],USUARIO_EMAIL_SIZE));
		$dtcriacao 			=	sqlTrataInteiro(validaInteiro($_POST["dtcriacao"],USUARIO_DTEXPIRACAO_SIZE));
		
		//verifica se todos campos obrigatórios foram preenchidos
		if( $idprocessotipo!==false && $nome_instituicao!==false && $idmunicipio!==false  
			&& $email1!==false && $dtcriacao!==false){

			//cria dados do usuário para encontrar a instituição
				$Usuario = new Usuario();
				$Usuario->setPerfil(PERFIL_IDINSTITUICAO);
				$Usuario->setNomeInstituicao($nome_instituicao);
				$Usuario->setMunicipio($idmunicipio);
				$Usuario->setEmail1($email1);
				//os atributos abaixo são usados para a data de início (dtcriacao) e último dia (dtfim) que o usuário acredita ter iniciado o processo
				$Usuario->setDtCriacao(substr($dtcriacao, 0,5).'01');
				$Usuario->setDtExpiracao($dtcriacao);

			// Instanciar as infos do processo
				$Processo = new Processo();						
				$Processo->setProcessoTipo($idprocessotipo);

			//verifica se encontra os dados informados pelo usuário
				$UsuarioDAO = new UsuarioDAO();
				$dados = $UsuarioDAO->checkLogin($Usuario, $Processo);
			//se encontrou os dados, passa a segunda etapa de verificação
			if(sizeof($dados)>0 && (!empty($dados["idusuario"]) || !empty($dados["idprocesso"])) ){

				//armazenamos em variáveis as informações corretas
				$nomecerto = $dados["nome"];
				$idcerto = $dados["idusuario"];
				$idprocesso = $dados["idprocesso"];
				//aqui listamos vários nomes de responsável, inclusive o correto, e pedimos pro usuário acertar quem é o responsável que fora definido
				//criamos uma lista de 60 nomes falsos
				$nomes = [	"Roberto de Oliveira Silva","João Martins","Robson Antunes","Márcia Guimarães","Cléber da Luz Pinto","Gabriela Farias","João Pedro Correia","Bruna Alves do Nascimento","Ana Strodieck","Márcio Fernandes Gozman",
							"Luiz Pereira Vegh","Heitor Silva Costa","Amadeu","Gustavo Zortman","Marcos Torres","Aline Yaman","Walter de Araújo Souza","Vinícius","Franciso de Cardoso Perez","Daniel Magno",
							"Sandra Welter Moritz","Lara Bonin Silva","Michel K Rolling","César Marques Toniollo","Jonas Albuquerque","Roberto Souza","Márcia Cristina Filho","Gustavo Vets","Kelly Fortes","Eduardo",
							"Fernando Querubim","Leonardo Versilo Pereira","André de Souza","Priscila Marques","Júlia Medeiros","Ronaldo Jelt Nunes","Renata Bruna Zelter","Pedro Henrique Costa","Júnior Richard","Felipe Reppini",
							"Karen","Daiane Karlson","Thayse Mineiro","Camila Rodes Talharin","Lívia Martini","Rubens Mattos","Jorge Venâncio de Oliveira","Aline Jhon Turmann","Bernardo Helter","Nora K Carvalho",
							"Acássio Tybaux","Nélio","Therezinha Venina Pereira da Costa","Geovana Zipper","Givanildo Torres da Silva","Manoel Rezende","Bruno Arantes","Bruno Lobos","Norberto Espinhosa Ferreira","Sandra Santana"];
				$nomes_lista=array();
				$nomes_lista[0]["nome"]=$nomecerto;
				$nomes_lista[0]["id"]=$idcerto;
				$nomes_lista[1]["nome"]=$nomes[mt_rand(6,10)];
				$nomes_lista[1]["id"]=$idcerto+1;
				$nomes_lista[2]["nome"]=$nomes[mt_rand(11,15)];
				$nomes_lista[2]["id"]=$idcerto+2;
				$nomes_lista[3]["nome"]=$nomes[mt_rand(16,20)];
				$nomes_lista[3]["id"]=$idcerto+3;
				$nomes_lista[4]["nome"]=$nomes[mt_rand(21,25)];
				$nomes_lista[4]["id"]=$idcerto+4;
				$nomes_lista[5]["nome"]=$nomes[mt_rand(26,30)];
				$nomes_lista[5]["id"]=$idcerto-1;
				$nomes_lista[6]["nome"]=$nomes[mt_rand(31,35)];
				$nomes_lista[6]["id"]=$idcerto-2;
				$nomes_lista[7]["nome"]=$nomes[mt_rand(36,40)];
				$nomes_lista[7]["id"]=$idcerto-3;
				$nomes_lista[8]["nome"]=$nomes[mt_rand(41,45)];
				$nomes_lista[8]["id"]=$idcerto+6;
				$nomes_lista[9]["nome"]=$nomes[mt_rand(46,49)];
				$nomes_lista[9]["id"]=$idcerto+9;
				$nomes_lista[10]["nome"]=$nomes[mt_rand(50,53)];
				$nomes_lista[10]["id"]=$idcerto+10;
				$nomes_lista[11]["nome"]=$nomes[mt_rand(54,56)];
				$nomes_lista[11]["id"]=$idcerto+11;
				$nomes_lista[12]["nome"]=$nomes[mt_rand(57,59)];
				$nomes_lista[12]["id"]=$idcerto+12;
				$nomes_lista[13]["nome"]=$nomes[mt_rand(0,5)];
				$nomes_lista[13]["id"]=$idcerto-4;
				//embaralha a lista de 14 nomes
				shuffle($nomes_lista);
				//pede pro usuário escolher na lista qual dos nomes é o nome correto do responsável eleito para criar o processo no sistema:
				
//				<form enctype=\"multipart/form-data\" name=\"no_login\" action=\"no_login.php\" method=\"post\" class=\"form-horizontal\" onSubmit=\"return validaForm('no_login','idusuario');\">

				echo "
				<script src='https://www.google.com/recaptcha/api.js'></script>
				<div id=\"conteudo_borda\">
				<p class=\"iniciar_titulo_sistema\">&nbsp;</p>
				<div  id=\"conteudo\"><h5 class='onlyprint'>Recuperação do login/senha</h5>
					<form id=\"no_login\" enctype=\"multipart/form-data\" name=\"no_login\" action=\"no_login.php\" method=\"post\" class=\"form-horizontal\"\">
					<div class=\"form-group\">
		                <label class=\"col-sm-12 control-label\">1) Marque o nome mais completo ou exato do responsável por este processo</label>
		                <div class=\"col-lg-12\">";
		                	for ($i=0; $i < sizeof($nomes_lista); $i++) {
		                		echo "<input type=\"radio\" id=\"idusuario\" name=\"idusuario\" value=\"".$nomes_lista[$i]["id"]."\"> ".textoMaiusculo($nomes_lista[$i]["nome"])."<br>";
		                	}
		        echo "	<span id=\"helpBlock\" class=\"help-block\">".APP_MSG_REQUIRED."</span>
		                </div>
		            </div>
		            <div class=\"form-group\">
		            	<label class=\"col-sm-10 control-label\">2) Confirme que você não é um robô</label>
		                <div class=\"col-lg-8\">
		            		<div class=\"g-recaptcha\" data-sitekey=\"".APP_CAPTCHA_SITE_KEY."\"></div>
		            		<span id=\"helpBlock\" class=\"help-block\"".APP_MSG_REQUIRED."</span>
		            	</div>
		        	</div>
		            <div class=\"form-group\" style=\"margin-top:35px;\">
		              <div class=\"col-lg-10\">
		              	<button type=\"reset\" id=\"cancelar\" class=\"btn btn-default no_login.php\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cancelar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		                <button type=\"submit\" class=\"btn btn-primary\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reaver os dados de acesso&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		              </div>
		            </div>	
					</form>
				</div>
				</div>";

				//zera os valores das variáveis de sessão 
				foreach ($_POST as $key => $value) {
					$_SESSION[$key]=NULL;
				}

				//seta o ID do processo na sessão (para o usuário não interferir neste valor)
				$_SESSION["idprocesso"]=$idprocesso;

				exit();
				
			}else{
				enviaMsg("erro","Os dados fornecidos não foram encontrados","Por favor esteja certo de que usou os dados corretos e tente novamente");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
				exit();
			}

		//cai aqui caso nem todos campos obrigatórios não tenham sido preenchidos
		}else{
			enviaMsg("erro","Usuário não encontrado","Um ou mais dados informados estão inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
			exit();
		}
	}else{
		enviaMsg("erro","Usuário não encontrado","Você precisa clicar no quadrado \"Não sou um robô\" antes de enviar o formulário");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
		exit();
	}
}

//2 POST - quando efetua o último passo p/ provar quem é e redefinimos a senha
if(isset($_POST["idusuario"]) && !empty($_POST["idusuario"])){

	if(!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])){
		enviaMsg("erro","Nem todos os dados para recuperação do acesso foram preenchidos, refaça todos os passos","Você precisa clicar no quadrado \"Não sou um robô\" antes de enviar o formulário");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
		exit();
	}

	//TRATAMENTO DO CAPTCHA
		//https://www.google.com/recaptcha/admin#site/319211787
		//variável para controle do Captcha
		$captcha_ok = false;
		//recebe dados do post
		$captcha_data = $_POST['g-recaptcha-response'];
		//verifica se o usuário NÃO enviou dados
		if (!$captcha_data) {
		    enviaMsg("erro","Falha na verificação de usuário não robô","Por favor confirme que você não é um robô antes de enviar o formulário");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
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

	//se passou na validação do captcha, dá prosseguimento ao POST
	if($captcha_ok){

		//verifica campos obrigatórios
		$idusuario		=	sqlTrataInteiro(validaInteiro($_POST["idusuario"],USUARIO_ID_SIZE));
		$idprocesso		=	sqlTrataInteiro(validaInteiro($_SESSION["idprocesso"],PROCESSO_ID_SIZE));

		//verifica se todos campos obrigatórios foram preenchidos
		if( $idusuario!==false && $idprocesso!==false){

				//instancia dados do usuário
					$Usuario = new Usuario();
					$Usuario->setId($idusuario);
				// Instanciar as infos do processo
					$Processo = new Processo();						
					$Processo->setId($idprocesso);

				//Instancia DAO para efetuar consultas/atualizações no banco
					$UsuarioDAO = new UsuarioDAO();
					$ProcessoDAO = new ProcessoDAO();

				//pega dados do PAD em que a instituição está atrelada
					$dadosPad = $ProcessoDAO->getInfosCapa($Processo);

				//verifica se encontra os dados informados pelo usuário					
					$dados = $UsuarioDAO->checkLogin2($Usuario, $Processo);

				//se encontrou os dados 
				if(sizeof($dados)>0 && (!empty($dados["email1"]) || !empty($dados["email2"]) ) ){

					if(isset($dados["numlosts"]) && $dados["numlosts"]>5){
						enviaMsg("erro","Pedido negado","Sua instituição já tentou recuperar o acesso com dados inválidos por mais de 5 vezes e, por questões de segurança, é preciso entrar em contato com o Coren/SC para reaver seu login");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
						exit();
					}

					//atribui dados que vieram do banco
					if(isset($dados["email1"]) && !empty($dados["email1"])){
						$Usuario->setEmail1($dados["email1"]);
					}else{
						$Usuario->setEmail1(NULL);
					}
					if(isset($dados["email2"]) && !empty($dados["email2"])){
						$Usuario->setEmail2($dados["email2"]);
					}else{
						$Usuario->setEmail2(NULL);
					}
					$Usuario->setLogin($dados["login"]);
					$Usuario->setNome($dados["nome"]);
					$Usuario->setNomeInstituicao($dados["nome_instituicao"]);
					$Processo->setNumero($dados["numero"]);

					//cria nova SENHA
					$senha=mt_rand(14,98).key_encrypt(mt_rand(3561,7892)).mt_rand(102,987);
					$Usuario->setSenha(codifica($senha));

					// Chama a função que atualiza a senha do usuário/instituição
					$atualizou = $UsuarioDAO->updateSenhaPerdida($Usuario);

					//redefine o valor da senha para a não codificada, pois essa informação será enviada por e-mail
					$Usuario->setSenha($senha);

					//zera os valores das variáveis de sessão 
					foreach ($_POST as $key => $value) {
						$_SESSION[$key]=NULL;
					}

					if($atualizou){

						//MANDA EMAILS
						$tipoajaxmail="no_login";
						require_once("control/mail.php");

						//SALVA HISTÓRICO
						$Historico = new Historico();
						$Historico->setAcao(LOG_RECOVER_USER);
						$Historico->setProcesso($Processo->getId());
						$Historico->setUsuario($Usuario->getId());
						$Historico->setDocumento(0);
						$Historico->setObs("Senha redefinida e enviada para o(s) email(s) cadastrado(s)");
						$HistoricoDAO = new HistoricoDAO();
						$inseriuLog=$HistoricoDAO->insert($Historico);
						if(!$inseriuLog){
							enviaMsg("erro","Recuperação efetuada com erros","O histórico não pôde ser salvo");
						}

						//apos enviar o e-mail redireciona o usuário para tela de login
						echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=6&e1=".$Usuario->getEmail1()."&e2=".$Usuario->getEmail2()."\">";
						exit();

					//não inseriu usuário
					}else{
						enviaMsg("erro","Problemas ao atualizar dados de acesso da instituição","Por favor tente novamente mais tarde.");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
						exit();
					}



				}else{

					//define o dado correto de IDUSUARIO
					$Usuario->setId($dadosPad["idusuario"]);

					//registra nova tentativa para o usuário/instituição (limite é 5)
					$UsuarioDAO->updateTentativaSenhaPerdida($Usuario);

					enviaMsg("erro","O nome do responsável escolhido está incorreto","Lembre-se de que após 5 tentativas/recuperações de senha o sistema bloqueia a recuperação por e-mail.");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
					exit();

				}



				
		//cai aqui caso nem todos campos obrigatórios não tenham sido preenchidos
		}else{
			enviaMsg("erro","Processo não iniciado","Um ou mais dados informados estão inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
			exit();
		}
	}else{
		enviaMsg("erro","Processo não iniciado","Você precisa clicar no quadrado \"Não sou um robô\" antes de enviar o formulário");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=no_login.php\">";
		exit();
	}
}

//inicializa as variaveis com valor para não precisar fazer IF's mais elaborados no form
$nome_instituicao = false;
$idmunicipio = false;
$idprocessotipo = false;
$email1 = false;
$dtcriacao=false;

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
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<div id="msg_erro" class="alert alert-danger noprint" role="alert"></div>
<div id="msg_sucesso" class="alert alert-success noprint" role="alert"></div>
<div id="msg_atencao" class="alert alert-warning noprint" role="alert"></div>
<?php
  //se houver mensagens exibe as mesmas aqui (veja arquivo \bin\functions)
  if(temMsg()){
    exibeMsg();
  }
?>


		<div id="conteudo_borda">
			<p class="iniciar_titulo_sistema">&nbsp;</p>
			<div class="well bg-msg">
				<p><strong>Atenção: área dedicada apenas às instituições que esqueceram seus dados de acesso, outros tipos de usuários precisam entrar em contato com o Coren/SC.</strong>
					<br><br>Insira abaixo as informações usadas no momento de iniciar o processo de renovação/implantação para receber por e-mail uma nova senha de acesso*.
					<br>Lembre-se de que um usuário e senha já havia sido enviado para os dois e-mails informados no momento de iniciar um novo processo de renovação/implantação, portanto, se você não trocou a senha, ela ainda deve estar disponível na caixa de entrada (ou de spam) dos e-mails que foram utilizados.
					<br><br>* Cada instituição poderá efetuar 5 tentativas/recuperações de login, após esse número somente entrando em contato com a Comissão de Ética do Coren/SC (CEC) para reaver o login/senha.</p>
			</div>
			<div  id="conteudo"><h5 class='onlyprint'>Iniciar Processo de Implantação/Renovação da Comissão de Ética de Enfermagem</h5>
				<!-- <form enctype="multipart/form-data" name="no_login" action="no_login.php" method="post" class="form-horizontal" onSubmit="return validaForm('no_login','idprocessotipo','dtcriacao','idmunicipio','nome_instituicao','nome','email1');"> -->
				<form enctype="multipart/form-data" name="no_login" action="no_login.php" method="post" class="form-horizontal">
					
					<div class="form-group">
		                <label class="col-sm-10 control-label">1) Trata-se da implantação da Comissão de Ética da sua instituição ou de uma renovação da Comissão?</label>
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
		                <label class="col-sm-12 control-label">2) Em qual dos períodos abaixo iniciou o processo de renovação/implantação no sistema?</label>
		                <div class="col-lg-12">
		                <?php 
		                	//array de periodos
		                	$array_dtcriacao = array();

							$ultimo_dia = date("t", mktime(0,0,0,date("m"),'01',date("Y")));
		                	$array_dtcriacao[0]["de"]=date("Ym")."01";
		                	$array_dtcriacao[0]["ate"]=date("Ymd");

		                	$timestamp = strtotime(date("Ym") . "-1 month");
                			$mes = date("m", $timestamp);
							$ano = date("Y", $timestamp);
							$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
		                	$array_dtcriacao[1]["de"]=$ano.$mes."01";
		                	$array_dtcriacao[1]["ate"]="$ano$mes$ultimo_dia";

		                	$timestamp = strtotime(date("Ym") . "-2 month");
                			$mes = date("m", $timestamp);
							$ano = date("Y", $timestamp);
							$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
		                	$array_dtcriacao[2]["de"]=$ano.$mes."01";
		                	$array_dtcriacao[2]["ate"]="$ano$mes$ultimo_dia";

		                	$timestamp = strtotime(date("Ym") . "-3 month");
                			$mes = date("m", $timestamp);
							$ano = date("Y", $timestamp);
							$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
		                	$array_dtcriacao[3]["de"]=$ano.$mes."01";
		                	$array_dtcriacao[3]["ate"]="$ano$mes$ultimo_dia";

		                	$timestamp = strtotime(date("Ym") . "-4 month");
                			$mes = date("m", $timestamp);
							$ano = date("Y", $timestamp);
							$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
		                	$array_dtcriacao[4]["de"]=$ano.$mes."01";
		                	$array_dtcriacao[4]["ate"]="$ano$mes$ultimo_dia";

		                	$timestamp = strtotime(date("Ym") . "-5 month");
                			$mes = date("m", $timestamp);
							$ano = date("Y", $timestamp);
							$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
		                	$array_dtcriacao[5]["de"]=$ano.$mes."01";
		                	$array_dtcriacao[5]["ate"]="$ano$mes$ultimo_dia";

		                	for ($i=0; $i < sizeof($array_dtcriacao); $i++) {
		                		$d_opcao = "";
								if($dtcriacao==$array_dtcriacao[$i]["ate"]){
									$d_opcao = " checked=\"checked\" ";
								}
		                		echo "<input ".$d_opcao." type=\"radio\" id=\"dtcriacao\" name=\"dtcriacao\" value=\"".$array_dtcriacao[$i]["ate"]."\">Entre ".exibeData($array_dtcriacao[$i]["de"])." e ".exibeData($array_dtcriacao[$i]["ate"])."<br>";
		                	}
		                	
		                	
		                ?>
		                <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">3) Município da instituição</label>
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
		                <label class="col-sm-10 control-label">4) Nome completo da instituição (sem abreviações)</label>
		                <div class="col-lg-8">
		                    <input type="text" class="form-control" id="nome_instituicao" name="nome_instituicao" value="<?php if($nome_instituicao) echo $nome_instituicao; ?>" placeholder="Nome completo da instituição" maxlength="<?php echo USUARIO_NOMEINSTITUICAO_SIZE-10; ?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">5) E-mail do responsável ou da instituição usados para iniciar o processo</label>
		                <div class="col-lg-8">
		                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email1" name="email1" placeholder="E-mail do responsável ou da instituição" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php if($email1) echo $email1;?>">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?> (fora usado para enviar o login/senha e atualizações do processo)</span>
		                </div>
		            </div>

		            <div class="form-group">
		            	<label class="col-sm-10 control-label">6) Confirme que você não é um robô</label>
		                <div class="col-lg-8">
		            		<div class="g-recaptcha" data-sitekey="<?php echo APP_CAPTCHA_SITE_KEY; ?>"></div>
		            		<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		            	</div>
		        	</div>

		            <div class="form-group" style="margin-top:20px;">
		              <div class="col-lg-10">
		              	<button type="reset" id="cancelar" class="btn btn-default login.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cancelar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seguir para o próximo passo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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