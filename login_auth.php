<?php
require_once("bin/errors.php");
require_once("config.php");
require_once("bin/functions.php");
//inclui verificação se o usuário está online ou não
require_once("usuario_com_internet.php");

//se usuário tiver online faz verificação com CAPTCHA
if($usuario_com_internet){
	if(isset($_POST["g-recaptcha-response"]) && !empty($_POST["g-recaptcha-response"])){
		$captcha=$_POST["g-recaptcha-response"];
	}else{
		$captcha=NULL;
	}	
//se o usuário não estiver online NÃO faz a verificação com captcha
}else{
	$captcha="não_sera_validado";
}

//se teve alguma página de redirecionamento enviada
if(isset($_POST['page']) && !empty($_POST['page'])){
	$page=$_POST['page'];
}else{
	$page=false;
}


if(!empty($_POST['login']) && !empty($_POST['senha'])){

	//inicializa variavel que define se o captcha enviado está correto
	$captcha_ok=false;
	//se o usuário tem internet, CAPTCHA obrigatório
	if($usuario_com_internet){
		//envia requisição para o Google com os dados enviados pelo usuário e as configurações da API
		$recaptcha = @file_get_contents(APP_CAPTCHA_SITE.APP_CAPTCHA_SECRET_KEY."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
		//se ele encontrar na resposta do Google algum "success": true   é pq o captcha está certo!
		if(strpos($recaptcha, "\"success\": true") !== false) {
			//o IF abaixo deve ser removido se deixarmos o google verificar os domínios sozinho:
			if(strpos($recaptcha, "\"hostname\": \"".APP_CAPTCHA_HOSTNAME."\"") !== false){
				$captcha_ok=true;	
			}
		}
	//se não tiver internet, pula o captcha
	}else{
		$captcha_ok=true;
	}

	//se o captcha informado for inválido
	if(!$captcha_ok){
		echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=4\">";
		exit();
	}
	
	$login   =   sqlTrataString(strtolower($_POST['login']));
	$senha   =   sqlTrataString(codifica($_POST['senha']));
	
	if($captcha_ok!==false && $login!==false && $senha!==false){
	
		//carrega as bibliotecas para recuperar informações do BD sobre o usuário
		require_once("conexao.php");
		require_once('model/Registry.php');
		require_once('dao/UsuarioDAO.php');
		require_once('model/Usuario.php');		
		// Armazenar essa instância (conexão) no Registry - conecta uma só vez
		$registry = Registry::getInstance();
		$registry->set('Connection', $myBD);		
		//cria um Usuario de acordo com o passado pelo usuário
		$usuario = new Usuario();
		$usuario->setLogin($login);
		$usuario->setSenha($senha);	
		// Instanciar o DAO e retornar o Usuario da base
		$UsuarioDAO = new UsuarioDAO();
		$result = $UsuarioDAO->getLogin($usuario);

		$bloqueado = $UsuarioDAO->isBlocked($usuario);

		//se o usuario estiver bloqueado
		if($bloqueado!==false){

			//interrompe o script e redireciona para a tela de login avisando que as credenciais estão bloqueadas
			echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=10&d=".$bloqueado["tentativas_time"]."\">";
			exit();

		}else{

			if($result === false){
				
				//registra tentativa de login inválido
				$UsuarioDAO->registerLoginAttempt($usuario);
				//bloqueia o login caso tenha ultrapassado o limite de tentativas
				$UsuarioDAO->blockUser($usuario);
				echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=1\">";
				exit();
			
			} else {
			//se resultado diferente de false, é porque o usuário/senha estão corretos
				
				//verifica se o usuário foi removido do sistema
				if($result["usuarioflag"]!=1){

					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=2\">";
					exit();

				//verifica se o perfil do usuário foi removido
				}elseif($result["perfilflag"]!=1){

					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=3\">";
					exit();

				//verifica se o login expirou
				}elseif($result["dtexpiracao"]!=0 && $result["dtexpiracao"]<date("Ymd")){
					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=".$result["dtexpiracao"]."\">";
					exit();
					
				}else{
				//se chegar aqui é porque não expira ou não expirou ainda
				//configura as permissões dos usuários
					
					$idusuario = $result["idusuario"];
					
					//funcoes
					$funcoes = $UsuarioDAO->getFuncoes($idusuario);
					$arrayfuncoes = array();
					if($funcoes!==false){
						for($i=0;$i<sizeof($funcoes);$i++){
							$arrayfuncoes[]=$funcoes[$i]["idfuncao"];
						}
					}
					if(sizeof($arrayfuncoes)>0){
						//adiciona ao array de resultados as funções do usuário
						$result["funcoes"] = $arrayfuncoes;
					}

					//verificação de restrição de acesso (só visualiza determinados processos)
					//Se o usuário é do perfil Membro Comissão de Ética só vê os processos que é responsável
					if($result["idperfil"]==PERFIL_IDRESPONSAVEL){
						
						$processos = $UsuarioDAO->getProcessosMembroCE($idusuario);
						$arrayprocessos = array();
						$arrayprocessos[]=0;
						if($processos!==false){
							for($i=0;$i<sizeof($processos);$i++){
								$arrayprocessos[]=$processos[$i]["idprocesso"];
							}
						}
						if(sizeof($arrayprocessos)>0){
							//adiciona ao array de resultados os processos visualizáveis
							$result["processos"] = $arrayprocessos;
						}	

					//se não for um "IDRESPONSAVEL" trata normalmente
					}else{
						
						//processos
						$processos = $UsuarioDAO->getProcessos($idusuario);
						$arrayprocessos = array();
						if($processos!==false){
							for($i=0;$i<sizeof($processos);$i++){
								$arrayprocessos[]=$processos[$i]["idprocesso"];
							}
						}				
						if(sizeof($arrayprocessos)>0){
							//adiciona ao array de resultados os processos visualizáveis
							$result["processos"] = $arrayprocessos;
						}

					}
					
					//infos de sessao
					ini_set("session.save_path", APP_URL_UPLOAD.'sessions');					
					ini_set('session.gc_maxlifetime', APP_SESSION_LIFETIME);
					session_cache_expire(APP_SESSION_LIFETIME);
					session_set_cookie_params(APP_SESSION_LIFETIME);
					ini_set("session.name", APP_SESSION_NAME);
					session_start();
					
					$_SESSION['USUARIO'] = $result;
					if(!empty($_SESSION['USUARIO'])){
						
						$obs="Navegador utilizado: ";
						//http://mobiledetect.net/
						require_once('bin/Mobile_Detect.php');
						$detect = new Mobile_Detect;						 
						// Any mobile device (phones or tablets).
						if ( $detect->isMobile() ) {
							if( $detect->isTablet() ){
								$obs.="Tablet.";
							}else{
								$obs.="Celular.";
							}										
						}else{
							$obs.="PC.";
						}
						//registra ação no Historico
						require_once("dao/HistoricoDAO.php");
						require_once("model/Historico.php");
						$Historico = new Historico();
						$Historico->setAcao(LOG_LOGIN_USER);
						$Historico->setObs($obs);
						$HistoricoDAO = new HistoricoDAO();
						$HistoricoDAO->insert($Historico);

					}

					//registra este como o último login efetuado pelo usuário
					$UsuarioDAO->registerLogin($idusuario);

					//SE logou no sistema, verifica se já trocou a senha alguma vez, se não, exige troca
					if($result["trocousenha"]==2){
						echo "<meta http-equiv=\"refresh\" content=\"0; url=control/change_password.php\">";
						exit();
					}

					//se tiver página de redirecionamento vá para ela, caso contrário exiba index_pro
					if($page!==false){
						//trata página para só pegar o caminho após /control/
						$aux = explode("/control/",$page);
						if(isset($aux[1]) && !empty($aux[1])){
							echo "<meta http-equiv=\"refresh\" content=\"0; url=control/".$aux[1]."\">";
							exit();
						}else{
							echo "<meta http-equiv=\"refresh\" content=\"0; url=control/index.php\">";
							exit();
						}
					}else{
						echo "<meta http-equiv=\"refresh\" content=\"0; url=control/index.php\">";
						exit();
					}
				}			
			}
		}//fim do else do isBlocked	
	}

}else{
	
	echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=1\">";
	exit();
}

?>