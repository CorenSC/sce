<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");
?>
<script>
$(document).ready(function(){
	//remove menu
	$(".navbar-default").remove();
});
</script>
<?php
//exibe mensagens de erro caso tenha
if(isset($_GET["s"]) && !empty($_GET["s"])){
	echo "<script>";
	if($_GET["s"]==2){
		echo "showMsgErro('A senha informada é igual a anterior, por favor, insira uma senha diferente.','');";
	}
	if($_GET["s"]==3 && isset($_GET["l"]) && !empty($_GET["l"])){
		echo "showMsgErro('O login desejado (<strong>".$_GET["l"]."</strong>) já foi atribuido à outro usuário', 'Por favor, repita o procedimento de troca de login/senha.');";
	}	
	echo "</script>";
}


//pega infos do Usuário
//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's p/ retornar infos
require_once('../dao/UsuarioDAO.php');
require_once('../model/Usuario.php');

//se enviou o formulário
if(isset($_POST) && !empty($_POST)){

	//valida informações enviadas
	$login=sqlTrataString(validaLiteral($_POST["login"],USUARIO_LOGIN_SIZE));
	$senha=		sqlTrataString(validaLiteral($_POST["senha"],USUARIO_SENHA_SIZE+30));//+30 pois especiais ocupam mais espaço
	$rsenha=	sqlTrataString(validaLiteral($_POST["rsenha"],USUARIO_SENHA_SIZE+30));//+30 pois especiais ocupam mais espaço
	$email=		sqlTrataString(validaLiteral($_POST["email"],USUARIO_EMAIL_SIZE));

	//se os dados forem informados corretamente
	if(($senha==$rsenha) && $senha!==false && $rsenha!==false && $email!==false && $login!==false){

		//primeiras 2 letras da senha
		$first=substr($senha, 0,2);
		//ultimas 2 letras da senha
		$last=substr($senha, strlen($senha)-2,2);

		// Instanciar as infos do usuario
		$Usuario = new Usuario();
		$Usuario->setId($_SESSION["USUARIO"]["idusuario"]);
		$Usuario->setLogin($_SESSION["USUARIO"]["login"]);
		$Usuario->setSenha(codifica($senha));
		$Usuario->setEmail1($email);
		// Instanciar o DAO para atualizar infos do usuario na base
		$UsuarioDAO = new UsuarioDAO();

			//verificando se o usuário realmente alterou a senha:
			$result = $UsuarioDAO->getLogin($Usuario);
			//se for diferente de FALSE é pq o usuário não alterou a senha
			if($result !== false){
				//deu pau - senha idêntica a anterior
				echo "<meta http-equiv=\"refresh\" content=\"0; url=change_password.php?s=2\">";
				exit();

			//se a senha for realmente diferente, então prossegue
			}else{

				//adiciona novo login no update
				$Usuario->setLogin($login);
				// Chama a função de verificação de duplicidade que só resulta FALSE caso dê algum problema.
				$possuiDuplicidade = $UsuarioDAO->isDuplicatedEdit($Usuario);
				//se der erro, exibe na tela msg para falar com admin
				if($possuiDuplicidade!==false){
					echo "<meta http-equiv=\"refresh\" content=\"0; url=change_password.php?s=3&l=".$login."\">";
					exit();
				}
				
				//atualizou login e/ou senha
				$trocouLSenha=$UsuarioDAO->trocaLoginSenha($Usuario);
				
				//se não conseguiu dar update na tabela
				if(!$trocouLSenha){
					
					//não deu pra atualizar a senha no BD
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php?s=2\">";
					exit();

				}else{

					//tenta atualizar a senha no banco
					echo "<div class=\"well\"><p><center>Redirecionando, aguarde...</center></p></div>
					<script>
						var resposta = $.ajax({
							type: 'POST',
							url: 'ajax_mail.php',
							async: false,
							dataType: 'text',
							data: {tipo:\"change_password\",l:\"".$login."\",first:\"".htmlentities(urlencode($first))."\",last:\"".htmlentities(urlencode($last))."\",e:\"".$email."\"} }).responseText;
						if(resposta.indexOf(\"erro\")!=-1){
							alert(\"Sua senha foi trocada mas o e-mail para o endereço informado ($email) não pôde ser enviado, por favor, anote sua nova senha para não esquecê-la. Obrigado.\");
							location=('index.php');
						}else{
							alert(\"E-mail contendo as novas credenciais enviado com sucesso para o endereço: $email\");
							location=('index_pro.php');
						}
					</script>
					";

					/*
					//tenta atualizar a senha no banco
					echo "<div class=\"well\"><p><center>Redirecionando, aguarde...</center></p></div>
					<script>
						var resposta = $.ajax({
							type: 'POST',
							url: 'ajax_mail.php',
							async: false,
							dataType: 'text',
							data: {tipo:\"change_password\",l:\"".$login."\",s:\"".htmlentities(urlencode($senha))."\",e:\"".$email."\"} }).responseText;
						if(resposta.indexOf(\"erro\")!=-1){
							alert(\"Sua senha foi trocada mas o e-mail para o endereço informado ($email) não pôde ser enviado, por favor, anote sua nova senha para não esquecê-la. Obrigado.\");
							location=('index.php');
						}else{
							alert(\"E-mail contendo as novas credenciais enviado com sucesso para o endereço: $email\");
							location=('index_pro.php');
						}
					</script>
					";
					*/

				}

			}
	}

//se não foi enviado nada por POST, então:
}else{

	// Instanciar o DAO e retornar dados do banco
	$UsuarioDAO = new UsuarioDAO();
	$infos=$UsuarioDAO->getOne($_SESSION["USUARIO"]["idusuario"]);
	//cria novo login no padrão nome.sobrenome
	$novoLogin=strtolower(removeCaracteresEspeciais($infos["nome"]));
	$nome1 = trim(strstr($novoLogin, " ", true));
	$nome2 = trim(substr(strrchr($novoLogin, " "), 1));
	//se for diferente
	if($nome1!=$nome2){
		$novoLogin = $nome1.".".$nome2;
	}
	//verifica se novo login já existe, criando outro se necessário:
	$Usuario = new Usuario();
	$Usuario->setId($_SESSION["USUARIO"]["idusuario"]);
	$Usuario->setLogin($novoLogin);
	// Chama a função de verificação de duplicidade que só resulta FALSE caso dê algum problema.
	$possuiDuplicidade = $UsuarioDAO->isDuplicatedEdit($Usuario);
	//se der erro, tenta 50 vezes encontrar um disponível
	if($possuiDuplicidade!==false){
		for ($i=1; $i < 50; $i++) { 
			$aux=$novoLogin.$i;
			$Usuario->setLogin($aux);
			$possuiDuplicidade = $UsuarioDAO->isDuplicatedEdit($Usuario);
			//se achou um disponível, sai do lopping e atribui esse valor à variavel $novoLogin
			if($possuiDuplicidade==false){
				$novoLogin=$aux;
				$Usuario->setLogin($novoLogin);
				break;
			}
		}	
		//se após 50 tentativas continuar com duplicidade, exibe msg para falar com admin
		if($possuiDuplicidade!==false){
			echo "<div class='well'><br><br><br>Ops, temos um problema com seu login!<br><br>Não foi possível criar um <strong>novo login</strong> válido para você pois o login '<strong>".$novoLogin."</strong>' já está em uso.<br><br>Por favor, entre em contato com o Coren/SC informando a tentativa automática de criação do usuário <strong>".$novoLogin."</strong><br><br>Tente acessar novamente o ".APP_TITLE." somente após contato com o Departamento de Tecnologia de Informação (DTI) do Coren/SC.<br><br>Lamentamos o transtorno, obrigado.<br><br><br><h4><a href='../logout.php'>Clique aqui para sair do sistema</a></h4><br><br>&nbsp;</div>";
			exit();
		}	
	}

	echo "	<div class=\"well\">
				<h5 class='onlyprint'>Troca de senha obrigatória</h5>";

	echo "		<p>".saudacao('')."! Por questões de segurança, você precisa alterar sua senha e, caso seu login não esteja no padrão <em>nome.sobrenome</em>, ele será alterado também.</p>
				<p>Confira abaixo seu login no padrão correto, insira uma nova senha (não pode ser idêntica à anterior) e clique em \"Salvar\" para normalizar seu acesso ao sistema.</p>"; ?>

				<div class="well">
				<form id="change_password" name="change_password" action="change_password.php" method="post" class="form-horizontal">

					<div class="form-group">
		                <label class="col-sm-10 control-label">Seu Login</label>
		                <div class="col-lg-4">
		                    <input type="text" class="form-control" id="login" name="login" value="<?php echo $novoLogin; ?>" readonly="readonly">
		                    <span id="helpBlock" class="help-block"><?php echo '* Campo não modificável, fornecido pelo sistema'; ?></span>
		                </div>
		            </div>

					<div class="form-group">
		                <label class="col-sm-10 control-label">Nova Senha</label>
		                <div class="col-lg-4">
		                    <input type="password" class="form-control camposenha" id="senha" name="senha" placeholder="Digite sua nova senha" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>"  autocomplete="off">
		                    <span id="helpBlock" class="help-block"><?php echo '* Campo obrigatório - de 6 a '.USUARIO_SENHA_SIZE.' caracteres, pelo menos: 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caracter especial exceto aspas simples (\') ou aspas duplas (") '; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">Redigite a Nova Senha</label>
		                <div class="col-lg-4">
		                    <input type="password" class="form-control camposenha" id="rsenha" name="rsenha" placeholder="Redigite sua nova senha" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>"  autocomplete="off">
		                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		                </div>
		            </div>

		            <div class="form-group">
		                <label class="col-sm-10 control-label">E-mail que você receberá um lembrete com seus dados de acesso</label>
		                <div class="col-lg-5">
		                    <input value="<?php if(isset($infos["email1"]) && $infos["email1"]!=NULL){ echo $infos["email1"]; } ?>" type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email" name="email" placeholder="E-mail do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>">
		                    <span id="helpBlock" class="help-block">* Campo obrigatório</span>
		                </div>
		            </div>

			        <div class="form-group">
		              <div class="col-lg-10">
		                <button type="reset" id="cancelar" class="btn btn-default ../logout.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sair do sistema&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		              </div>
		            </div>

		        </form>
		    	</div>

	<?php
	echo "		
			</div>";

}


include_once("../menu_rodape.php");
?>