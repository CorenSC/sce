<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../login_verifica.php");
//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

if(isset($_POST["tipo"])){

	//se houver um periodo para as requisicoes
	if(isset($_POST["periodo_de"])){
		$periodo_de=$_POST["periodo_de"];	
	}else{
		$periodo_de=NULL;
	}
	if(isset($_POST["periodo_ate"])){
		$periodo_ate=$_POST["periodo_ate"];
	}else{
		$periodo_ate=NULL;
	}

	if($_POST["tipo"]=="dados_envio_email" && isset($_POST["idprocesso"]) && !empty($_POST["idprocesso"])){
		$idprocesso=sqlTrataInteiro(validaInteiro($_POST["idprocesso"],PROCESSO_ID_SIZE));
		$idinstituicao=sqlTrataInteiro(validaInteiro($_POST["idinstituicao"],USUARIO_ID_SIZE));
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/UsuarioDAO.php");
		$UsuarioDAO = new UsuarioDAO();
		//instituição só pode enviar e-mail para usuários específicos
		if(isInstituicao()){
			$dados=$UsuarioDAO->getAllForEntity($idprocesso);
			$idsFiscal=array();
		//outros usuários, enviam e-mail para qualquer usuário do sistema
		}else{
			$dados=$UsuarioDAO->getAll(false,true);
			$dadosFiscal=$UsuarioDAO->getAllFiscaisProcesso($idprocesso);
			$idsFiscal=array();
			foreach ($dadosFiscal as $d) {
				$idsFiscal[]=$d["idusuario"];
			}
		}
		echo "
		<form id=\"envia_email\" name=\"envia_email\" action=\"#\">
		<div class=\"form-group\">
			<div class=\"well\">
				<p>Selecione o usuário que receberá um e-mail com o link deste processo:</p>
				<select class=\"form-control\" name=\"destinatario\" id=\"destinatario\">";
					foreach($dadosFiscal as $usuario){
						if(isset($_SESSION["USUARIO"]["idusuario"]) && $usuario["idusuario"]!=$_SESSION["USUARIO"]["idusuario"]){
							echo '<option value="'.$usuario["idusuario"].'">(FISCAL DA REGIÃO) '.$usuario["nomeusuario"].'</option>';
						}
					}
					foreach($dados as $usuario){
						if(isset($_SESSION["USUARIO"]["idusuario"]) && $usuario["idusuario"]!=$_SESSION["USUARIO"]["idusuario"]){
							//if que restringe a visibilidade de usuários para as instituições
							if((isInstituicao() && $usuario["idperfil"]!=PERFIL_IDADMIN && $usuario["idperfil"]!=PERFIL_IDPRESIDENTE && $usuario["idperfil"]!=PERFIL_IDINSTITUICAO) || (!isInstituicao() && ($usuario["idperfil"]!=PERFIL_IDINSTITUICAO || ($usuario["idperfil"]==PERFIL_IDINSTITUICAO && $usuario["idusuario"]==$idinstituicao ) ) ) ){
								//procura se o usuário já não foi exibido no FOREACH de cima (dadosFiscal)
								if( array_search($usuario["idusuario"],$idsFiscal) === false){
									echo '<option value="'.$usuario["idusuario"].'">'.$usuario["nomeusuario"].' ('.$usuario["nomeperfil"];
									//se for um usuário instituição, exibe o nome da instituição
									if($usuario["idperfil"]==PERFIL_IDINSTITUICAO && isset($usuario["nome_instituicao"]) && !empty($usuario["nome_instituicao"])){
										echo ' / '.$usuario["nome_instituicao"];
									}
									echo ')</option>';
								}
							}
						}
					}
		echo "	</select>
				<p>Se desejar, digite uma mensagem ao destinatário:</p>
				<textarea class=\"form-control\" name=\"mensagem\" id=\"mensagem\"rows=\"5\"></textarea>
				<input type=\"hidden\" name=\"idprocesso\" id=\"idprocesso\" value=\"$idprocesso\">
				<p>
				<br>
				<button id=\"cancela_email\" class=\"btn btn-default\">Cancelar</button>
				<button id=\"encaminha_email\" class=\"btn btn-primary\">Enviar</button>
				</p>
			</div>
		</div>
		</form>
		";

	//gráfico de processos por municipio
	}elseif($_POST["tipo"]=="rel2_grafico1" || $_POST["tipo"]=="rel2_grafico1b"){
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");

		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		if($_POST["tipo"]=="rel2_grafico1"){
			$results=$relatorioDAO->rel2_grafico1($periodo_de,$periodo_ate);
		}elseif($_POST["tipo"]=="rel2_grafico1b"){
			$results=$relatorioDAO->rel2_grafico1b($periodo_de,$periodo_ate);
		}else{
			echo "erro_filtro";
			exit();
		}
		
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeorigem"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico1b_andamento"){
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");

		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico1b_andamento($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeorigem"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico1c"){
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");

		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico1c($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomesubsecao"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico1c_andamento"){
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");

		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico1c_andamento($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomesubsecao"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico2"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico2($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nometipo"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico2_andamento"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico2_andamento($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nometipo"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico3"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico3($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeusuario"].' ('.$r["acoes"].')&&&'.$r["acoes"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico4"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico4($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeperfil"].' ('.$r["acoes"].')&&&'.$r["acoes"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico5"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico5($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomedocumentotipo"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico6"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico6($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=exibeMes($r["mes"]).'/'.$r["ano"].' ('.$r["numacoes"].')&&&'.$r["numacoes"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico22"){
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico22($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeetapa"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico22_andamento"){
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico22_andamento($periodo_de,$periodo_ate);
		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeetapa"].' ('.$r["numregs"].')&&&'.$r["numregs"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico8"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//armazena valor passado via ajax
		$idperfil=$_POST["perfil"];
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico8($idperfil,$periodo_de,$periodo_ate);

		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=$r["nomeusuario"].' ('.$r["acoes"].')&&&'.$r["acoes"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}elseif($_POST["tipo"]=="rel2_grafico15"){
		//$resposta = "Nome do Campo&&&Valor|||Florianópolis&&&11|||Itajaí&&&2";
		//os dados são separados por ||| e dentro de cada |||, um &&& divide "Nome do Campo" do "Valor"
		//carrega DAO's das chaves estrangeiras
		require_once("../dao/RelatorioDAO.php");
		// Instanciar o DAO e retornar dados do banco
		$relatorioDAO = new RelatorioDAO();
		//pega dados do relatório em questão
		$results=$relatorioDAO->rel2_grafico15($periodo_de,$periodo_ate);

		$resposta="";
		if(sizeof($results) > 0){
			foreach($results as $r){
				$resposta.=str_replace('.','',str_replace('Navegador utilizado: ', '', $r["obs"])).' ('.$r["logins"].')&&&'.$r["logins"].'|||';
			}
			echo substr($resposta,0,-3);
		}else{
			echo "erro_filtro";
			exit();
		}

	}
}else{
	echo "erro_filtro";
	exit();
}
?>