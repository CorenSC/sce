<?php
require_once("bin/errors.php");
require_once("config.php");
require_once("bin/functions.php");
//inclui verifica��o se o usu�rio est� online ou n�o
require_once("usuario_com_internet.php");

//se usu�rio tiver online faz verifica��o com CAPTCHA
if($usuario_com_internet){
	if(isset($_POST["g-recaptcha-response"]) && !empty($_POST["g-recaptcha-response"])){
		$captcha=$_POST["g-recaptcha-response"];
	}else{
		$captcha=NULL;
	}	
//se o usu�rio n�o estiver online N�O faz a verifica��o com captcha
}else{
	$captcha="n�o_sera_validado";
}

//se teve alguma p�gina de redirecionamento enviada
if(isset($_POST['page']) && !empty($_POST['page'])){
	$page=$_POST['page'];
}else{
	$page=false;
}


if(!empty($_POST['login']) && !empty($_POST['senha'])){

	//inicializa variavel que define se o captcha enviado est� correto
	$captcha_ok=false;
	//se o usu�rio tem internet, CAPTCHA obrigat�rio
	if($usuario_com_internet){
		//envia requisi��o para o Google com os dados enviados pelo usu�rio e as configura��es da API
		$recaptcha = @file_get_contents(APP_CAPTCHA_SITE.APP_CAPTCHA_SECRET_KEY."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
		//se ele encontrar na resposta do Google algum "success": true   � pq o captcha est� certo!
		if(strpos($recaptcha, "\"success\": true") !== false) {
			//o IF abaixo deve ser removido se deixarmos o google verificar os dom�nios sozinho:
			if(strpos($recaptcha, "\"hostname\": \"".APP_CAPTCHA_HOSTNAME."\"") !== false){
				$captcha_ok=true;	
			}
		}
	//se n�o tiver internet, pula o captcha
	}else{
		$captcha_ok=true;
	}

	//se o captcha informado for inv�lido
	if(!$captcha_ok){
		echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=4\">";
		exit();
	}
	
	$login   =   sqlTrataString(strtolower($_POST['login']));
	$senha   =   sqlTrataString(codifica($_POST['senha']));
	
	if($captcha_ok!==false && $login!==false && $senha!==false){
	
		//carrega as bibliotecas para recuperar informa��es do BD sobre o usu�rio
		require_once("conexao.php");
		require_once('model/Registry.php');
		require_once('dao/UsuarioDAO.php');
		require_once('model/Usuario.php');		
		// Armazenar essa inst�ncia (conex�o) no Registry - conecta uma s� vez
		$registry = Registry::getInstance();
		$registry->set('Connection', $myBD);		
		//cria um Usuario de acordo com o passado pelo usu�rio
		$usuario = new Usuario();
		$usuario->setLogin($login);
		$usuario->setSenha($senha);	
		// Instanciar o DAO e retornar o Usuario da base
		$UsuarioDAO = new UsuarioDAO();
		$result = $UsuarioDAO->getLogin($usuario);

		$bloqueado = $UsuarioDAO->isBlocked($usuario);

		//se o usuario estiver bloqueado
		if($bloqueado!==false){

			//interrompe o script e redireciona para a tela de login avisando que as credenciais est�o bloqueadas
			echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=10&d=".$bloqueado["tentativas_time"]."\">";
			exit();

		}else{

			if($result === false){
				
				//registra tentativa de login inv�lido
				$UsuarioDAO->registerLoginAttempt($usuario);
				//bloqueia o login caso tenha ultrapassado o limite de tentativas
				$UsuarioDAO->blockUser($usuario);
				echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=1\">";
				exit();
			
			} else {
			//se resultado diferente de false, � porque o usu�rio/senha est�o corretos
				
				//verifica se o usu�rio foi removido do sistema
				if($result["usuarioflag"]!=1){

					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=2\">";
					exit();

				//verifica se o perfil do usu�rio foi removido
				}elseif($result["perfilflag"]!=1){

					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=3\">";
					exit();

				//verifica se o login expirou
				}elseif($result["dtexpiracao"]!=0 && $result["dtexpiracao"]<date("Ymd")){
					echo "<meta http-equiv=\"refresh\" content=\"0; url=login.php?s=".$result["dtexpiracao"]."\">";
					exit();
					
				}else{
				//se chegar aqui � porque n�o expira ou n�o expirou ainda
				//configura as permiss�es dos usu�rios
					
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
						//adiciona ao array de resultados as fun��es do usu�rio
						$result["funcoes"] = $arrayfuncoes;
					}

					//verifica��o de restri��o de acesso (s� visualiza determinados processos)
					//Se o usu�rio � do perfil Membro Comiss�o de �tica s� v� os processos que � respons�vel
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
							//adiciona ao array de resultados os processos visualiz�veis
							$result["processos"] = $arrayprocessos;
						}	

					//se n�o for um "IDRESPONSAVEL" trata normalmente
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
							//adiciona ao array de resultados os processos visualiz�veis
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
						//registra a��o no Historico
						require_once("dao/HistoricoDAO.php");
						require_once("model/Historico.php");
						$Historico = new Historico();
						$Historico->setAcao(LOG_LOGIN_USER);
						$Historico->setObs($obs);
						$HistoricoDAO = new HistoricoDAO();
						$HistoricoDAO->insert($Historico);

					}

					//registra este como o �ltimo login efetuado pelo usu�rio
					$UsuarioDAO->registerLogin($idusuario);

					//SE logou no sistema, verifica se j� trocou a senha alguma vez, se n�o, exige troca
					if($result["trocousenha"]==2){
						echo "<meta http-equiv=\"refresh\" content=\"0; url=control/change_password.php\">";
						exit();
					}

					//se tiver p�gina de redirecionamento v� para ela, caso contr�rio exiba index_pro
					if($page!==false){
						//trata p�gina para s� pegar o caminho ap�s /control/
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