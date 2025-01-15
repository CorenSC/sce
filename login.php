<?php
	require_once("config.php");	
	require_once("bin/errors.php");
	require_once("bin/js-css.php");
	require_once("bin/functions.php");
	//inclui verificação se o usuário está online ou não
	require_once("usuario_com_internet.php");

	//verificacao de compatibilidade do navegador - o usuário "DEVERIA" usar o navegador CHROME
	if(getUserBrowser()!="chrome"){
	  echo '<script>
	          showMsgAtencao(\'\',\'Sistema desenvolvido <strong>preferencialmente</strong> para o navegador Google Chrome. Caso não o tenha, <a target="_blank" href="https://www.google.com/chrome">clique aqui para baixá-lo!</a>\',60000);
	        </script>';
	}

	if(isset($_GET["page"]) && !empty($_GET["page"])){		
		$page=$_GET["page"];
	}else{
		$page="";
	}
	
	if(isset($_GET["s"]) && !empty($_GET["s"])){
		echo "	<script>";

		//se o login/senha digitado não conferem, exibe mensagem de erro
		if($_GET["s"]==1){
			echo "showMsgErro('Login ou Senha inválidos','Certifique-se de que digitou corretamente e tente novamente.');";
		}else{
			if($_GET["s"]==2){
				echo "showMsgErro('Acesso Negado','Você não possui mais acesso ao sistema');";
			}elseif($_GET["s"]==3){
				echo "showMsgErro('Acesso Negado','Este perfil de usuário foi removido');";
			}elseif($_GET["s"]==4){
				echo "showMsgErro('Tentativa Inválida','É preciso comprovar que não é um robô antes de enviar o formulário');";
			}elseif($_GET["s"]==5){
				if(isset($_GET["n"]) && !empty($_GET["n"])){
					$tipo=$_GET["n"];
				}else{
					$tipo="Demanda";
				}
				echo "showMsgSucesso('$tipo iniciada com sucesso','Insira o login e senha que acaba de ser enviado para o(s) e-mail(s) abaixo:<br>";
				if(isset($_GET["e1"]) && !empty($_GET["e1"]) && $_GET["e1"]!="NULL"){
					echo "<em>".$_GET["e1"]."</em>";
				}
				if(isset($_GET["e2"]) && !empty($_GET["e2"]) && $_GET["e2"]!="NULL"){
					echo " e <em>".$_GET["e2"]."</em>";
				}
				echo "');";
			}elseif($_GET["s"]==6){
				echo "showMsgSucesso('Senha de acesso alterada com sucesso','Insira o login e senha que acaba de ser enviado para o(s) e-mail(s) abaixo:<br>";
				if(isset($_GET["e1"]) && !empty($_GET["e1"]) && $_GET["e1"]!="NULL"){
					echo "<em>".$_GET["e1"]."</em>";
				}
				if(isset($_GET["e2"]) && !empty($_GET["e2"]) && $_GET["e2"]!="NULL"){
					echo " e <em>".$_GET["e2"]."</em>";
				}
				echo "');";
			}elseif($_GET["s"]==9){
				echo "showMsgSucesso('Deslogado com sucesso','');";
			}elseif($_GET["s"]==10){
				if(!empty($_GET["d"])){
					$dataBloqueio=$_GET["d"];
				}				
				echo "showMsgErro('Usuário bloqueado','Tente acessar novamente após o dia ".exibeData($dataBloqueio).". Limite diário de tentativas erradas de login excedido.');";
			}else{
				$dataexpiracao=exibeData($_GET["s"]);
				echo "showMsgErro('Acesso negado','Seu login expirou em ".$dataexpiracao."');";
			}
		}
		echo "	</script>";
	}
	?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" http-equiv="Content-Type" content="text/html; charset=<?php echo APP_CHARSET; ?>" />
<title><?php echo APP_TITLE; ?></title>
<link rel="shortcut icon" href="<?php echo APP_URL.'/favicon.ico'; ?>" />
<?php 
//se o usuário estiver ONLINE, faz a verificação com Captcha, se não não
if ($usuario_com_internet){ echo "<script src='https://www.google.com/recaptcha/api.js'></script>"; }
?>
</head>
<body onLoad="document.forms[0].login.focus();">
<!-- MENSAGEM DE JAVASCRIPT NÃO ATIVADO -->
<noscript>
<div style="background-color: #337ab7; color: white;"><center><br /><br />
<h3 style="background-color: white; color: black;">ATENÇÃO!</h3><br /><br />
	Este sistema utiliza JavaScript para interagir entre as páginas.<br />
	Você <strong>não</strong> está com o JavaScript habilitado, por favor, utilize outro navegador da Internet ou ative o JavaScript neste navegador.<br />
	Visite o site <strong><a style="color: black;" href="http://www.enable-javascript.com/pt/" target="_blank">http://www.enable-javascript.com</a></strong>
	 para conhecer o passo-a-passo da ativação do JavaScript neste navegador.
	<br />Caso já tenha seguido os passos do site acima, é só atualizar esta página (ou apertar a tecla F5) que esta mensagem desaparecerá! 
	<br /><h3>Obrigado</h3></strong><?php echo APP_TITLE; ?><br /><br /><br /><br /><br /><br /><br /> <br /><br /><br /><br /><br /> <br /><br /><br /><br /><br /><br /><br /><br /></center>
</div>
</noscript>
<!-- FIM MENSAGEM DE JAVASCRIPT NÃO ATIVADO -->	
<div id="msg_erro" class="alert alert-danger" role="alert"></div>
<div id="msg_sucesso" class="alert alert-success" role="alert"></div>
<div id="msg_atencao" class="alert alert-warning" role="alert"></div>

<div class="well corensys_sce">
<p></p><center><h4 class="panel-title"><?php echo APP_TITLE; ?></h4></center><p></p>
</div>

<!--<div class="quadro-login panel panel-primary" style="height:338px;width:300px;">-->
<center>
<div class="quadro-login panel panel-primary" style="height:448px;width:335px;">
	<center><div id="syslogo_login"></div></center>
    <div class="panel-heading">
      <center><h3 class="panel-title">Autenticação Obrigatória</h3></center>
    </div>
    <div class="panel-body">
    	<!-- <form method="post" name="login" action="login_auth.php" onSubmit="return validaForm('login','login','senha');"  autocomplete="off"> -->
        <form method="post"  class="form-horizontal" id="login_php" name="login_php" action="login_auth.php">
        <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
        <div class="form-group">
	        <label class="col-lg-2 control-label">Usuário</label>
	        <div class="col-lg-12">
	        	<!-- tipos: text, password, datetime, datetime-local, date, month, time, week, number, email, url, search, tel, color. -->
	            <input name="login" id="login" type="text" placeholder="Seu usuário/login" class="form-control camposomente09AZponto<?php echo USUARIO_LOGIN_SIZE; ?>" maxlength="<?php echo USUARIO_LOGIN_SIZE; ?>" autocomplete="username" />
	        </div>
	        <label class="col-lg-2 control-label">Senha</label>
	        <div class="col-lg-12">
	        	<!-- tipos: text, password, datetime, datetime-local, date, month, time, week, number, email, url, search, tel, color. -->
	        	<input name="senha" id="senha" type="password" placeholder="Sua senha" class="form-control camposenha" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>" autocomplete="current-password" />
	        </div>
	        <?php
	        //se o usuário estiver online faz a verificação com Captcha
	        if($usuario_com_internet){
	        	echo "	<label class=\"col-lg-10 control-label\">Confirme que você não é um robô</label>
				        <div class=\"col-lg-12\">
				        	<div class=\"g-recaptcha\" data-sitekey=\"".APP_CAPTCHA_SITE_KEY."\"></div>
				        </div>";
	        }
	        ?>
	        
	        <div class="col-lg-12" style="padding-top: 10px;">
	        	<center><button title="Clique aqui para efetuar o login no sistema" type="submit" class="btn btn-primary">Entrar</button>&nbsp;&nbsp;<button type="reset" id="cancelar" class="btn btn-default no_login.php">Esqueci a senha</button></center>
	        </div>
	        <div class="col-lg-12" style="padding-top: 5px;">
	        	<center><button type="reset" id="semusuario" class="btn btn-danger" value="../../comissoes-de-etica/">&nbsp;Não tenho usuário e senha&nbsp;</button></center>
	        </div>
        </div>
        </form>
    </div>    
	<br/><center><a href="../index.php" title="Clique aqui para retornar a listagem de sistemas"><div id="logo_dti"></div></a></center>
	</div>
</div>
</center>