<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

if(verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)!==false || verificaFuncaoUsuario(FUNCAO_USUARIO_EDIT)!==false || verificaFuncaoUsuario(FUNCAO_USUARIO_DEL)!==false){


	//carrega as bibliotecas para recuperar informações do BD
	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	//carrega DAO's das chaves estrangeiras
	require_once('../dao/UsuarioDAO.php');
	require_once('../model/Usuario.php');

	//SUBMENU DE AÇÕES
	require_once('submenu_user.php');
	//FIM SUBMENU DE AÇÕES

	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	// Instanciar o DAO e retornar dados do banco
	$UsuarioDAO = new UsuarioDAO();
	if(isset($_GET["order"]) && ($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ) {
		$results = $UsuarioDAO->index($paginacao_inicio,$busca,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$results = $UsuarioDAO->index($paginacao_inicio,$busca);
	}

	//Paginação:
	$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
	$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
	$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.

	echo "<div id=\"conteudo_borda\"><div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Usuários</h5>";

	if($tr>0){ 

	echo '
	<table class="table table-condensed table-responsive table-hover">
	    <thead>
	      <tr>
	        <th><a class="reordenar_padrao" href="#" rel="index_user|nome">'.exibeFlagReordenacao('nome').'Nome do Usuário</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_user|nome_instituicao">'.exibeFlagReordenacao('nome_instituicao').'Nome da Instituição</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_user|perfil">'.exibeFlagReordenacao('perfil').'Perfil</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_user|login">'.exibeFlagReordenacao('login').'Login</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_user|dtcriacao">'.exibeFlagReordenacao('dtcriacao').'Data de Criação</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_user|dtacesso">'.exibeFlagReordenacao('dtacesso').'Último Acesso</a></th>
	        <th class="noprint">Ações</th>
	      </tr>
	    </thead>
	    <tbody>
		';
		
		for($i=0;$i<sizeof($results);$i++){
				$id 	= $results[$i]["idusuario"];
				if($results[$i]["perfilflag"]!=1){
					$nome=insereInfo("sem_perfil");
				}else{
					$nome="";
				}
				$nome 	.= exibeTexto($results[$i]["nomeuser"],40);
				$nome_instituicao = exibeTexto($results[$i]["nome_instituicao"],100);
				$perfil = exibeTexto($results[$i]["nomeperfil"],33);
				$login 	= exibeTexto($results[$i]["login"],30);
				$dtcriacao = exibeData($results[$i]["dtcriacao"]);
				$dtacesso = exibeDataTimestamp($results[$i]["dtacesso"]);
				
				echo "
				<tr id=\"usuario_".$id."\">
				<th scope=\"row\">".$nome."</th>
				<td>".$nome_instituicao."</td>
				<td>".$perfil."</td>
				<td>".$login."</td>
				<td>".$dtcriacao."</td>
				<td>".$dtacesso."</td>
				<td><nobr>";
				
				//verifica as permissóes do usuário
				if(verificaFuncaoUsuario(FUNCAO_USUARIO_EDIT)){
					echo "<button class=\"btn btn-success edit_usuario\" aria-label=\"Left Align\" type=\"button\" title=\"Editar usuário\" id=\"usuario_".$results[$i]["idusuario"]."\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
				}
				if(verificaFuncaoUsuario(FUNCAO_USUARIO_DEL)){
					echo "&nbsp;<button class=\"btn btn-warning usuario_del\" aria-label=\"Left Align\" type=\"button\" title=\"Excluir usuário\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
				}
				
				echo "</nobr></td></tr>";

		}
		echo '</tbody>
	    </table>';

    	//Paginação:
    	//Agora vamos criar os botões "Anterior e Próximo" ao final da página
		include_once("../paginacao.php");

	}else{
		echo 'Nenhum usuário encontrado!';
	}

	?>

	<?php
}else{

	enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
	exit();
		
}
include_once("../menu_rodape.php"); 
?>