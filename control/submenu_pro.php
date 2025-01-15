<?php

require_once('../dao/MunicipioDAO.php');

//verifica se há alguma busca já enviada
if( 	(isset($_GET["numero"]) && !empty($_GET["numero"])) 
	||	(isset($_GET["entidade_nome"]) && !empty($_GET["entidade_nome"]))
	||	(isset($_GET["responsavel"]) && !empty($_GET["responsavel"]))
	||	(isset($_GET["entidade_cidade"]) && !empty($_GET["entidade_cidade"])) ){
	
	//se o tipo da busca for por numero do processo
	if($_GET["idtipo"]=="numero"){
		//se for setado um número, atribui, se não, permite consulta a todos os números
		if(isset($_GET["numero"]) && !empty($_GET["numero"])){
			$numero=sqlTrataInteiro($_GET["numero"]);
		}else{
			$numero="";
		}
		//armazena na variavel os dois valores separados pelo "quebrador de linha" (atualmente os caracteres @@@)
		$busca = $numero;
	
	//se o tipo da busca for por Entidade	
	}elseif($_GET["idtipo"]=="entidade"){

		//se for setado um nome de entidade, atribui a busca
		if(isset($_GET["entidade_nome"]) && !empty($_GET["entidade_nome"])){
			$entidade_nome=sqlTrataString($_GET["entidade_nome"]);
		}else{
			$entidade_nome="";
		}
		//se for setado um nome de cidade, atribui a busca
		if(isset($_GET["entidade_cidade"]) && !empty($_GET["entidade_cidade"]) && $_GET["entidade_cidade"]>0){
			$entidade_cidade=sqlTrataString($_GET["entidade_cidade"]);
		}else{
			$entidade_cidade="";
		}
		//armazena na variavel os dois valores separados pelo "quebrador de linha" (atualmente os caracteres @@@) + 'entidade' para saber que não é busca por numero
		$busca = $entidade_nome.APP_LINE_BREAK.$entidade_cidade.APP_LINE_BREAK.'entidade';

	//se o tipo da busca for por Responsável	
	}elseif($_GET["idtipo"]=="busca_responsavel"){

		//se for setado um nome de entidade, atribui a busca
		if(isset($_GET["busca_responsavel"]) && !empty($_GET["busca_responsavel"])){
			$search_responsavel=sqlTrataString($_GET["busca_responsavel"]);
		}else{
			$search_responsavel="";
		}
		//armazena na variavel os dois valores separados pelo "quebrador de linha" (atualmente os caracteres @@@) + 'responsavel' para saber que não é busca por numero
		$busca = $search_responsavel.APP_LINE_BREAK.'responsavel';

	}

	

//se não tiver uma busca definida:
}else{
	$busca=NULL;
}



echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações dos processos\">";

	//adiciona busca de processos integrada
	//define variaveis para busca
	$tipo_numero="";
	$tipo_entidade="";
	$busca_numero="";
	$busca_ano="";
	$busca_entidade_nome="";
	$busca_entidade_cidade="";
	$busca_responsavel="";

	if($busca != NULL){
		echo 'Exibindo resultados da busca  <a href="#" id="botao_busca_processo">( <span class="glyphicon glyphicon-eye-open"></span> clique aqui para visualizar ou alterar os parâmetros)</a>';

		//busca por processo
			if(isset($_GET["idtipo"]) && $_GET["idtipo"]=="numero"){
				$tipo_numero=" selected=\"selected\" ";
			}		
			if(isset($_GET["numero"])){
				$busca_numero=$_GET["numero"];
			}
			if(isset($_GET["ano"])){
				$busca_ano=$_GET["ano"];
			}
		//busca por entidade
			if(isset($_GET["idtipo"]) && $_GET["idtipo"]=="entidade"){
				$tipo_entidade=" selected=\"selected\" ";
			}
			if(isset($_GET["entidade_nome"])){
				$busca_entidade_nome=$_GET["entidade_nome"];
			}
			if(isset($_GET["entidade_cidade"])){
				$busca_entidade_cidade=$_GET["entidade_cidade"];
			}
		//busca por responsavel
			if(isset($_GET["idtipo"]) && $_GET["idtipo"]=="busca_responsavel"){
				$tipo_responsavel=" selected=\"selected\" ";
			}
			if(isset($_GET["busca_responsavel"])){
				$busca_responsavel=$_GET["busca_responsavel"];
			}

	}else{
		echo '<button type="submit" class="btn btn-primary" id="botao_busca_processo"';
		if($paginaAtual=="add_pro.php"){ echo ' disabled="disabled" '; }
		echo '><span class="glyphicon glyphicon-search" aria-hidden="true"></span> <strong>BUSCAR PROCESSO</strong></button>';
	}

//se não for ADD_PRO, exibe o submenu de pesquisa de processos
if($paginaAtual!="add_pro.php"){
	echo '		<form id="index_pro" name="index_pro" method="get" action="index_pro.php" class="form-inline div_busca_processo">';

	echo '		<div class="form-group">
		            <div>
		            	<select id="idtipo" name="idtipo" name="idtipo" class="form-control" onchange="showCampoViaSelect(this.value);">
		              		<option value="-1">Selecione o Tipo da Busca</option>
							<option '.$tipo_numero.' value="numero">Por Número do Processo</option>';
	echo '					<option '.$tipo_entidade.' value="entidade">Por Instituição</option>';
	echo '					<option '.$tipo_responsavel.' value="busca_responsavel">Por Responsável</option>';
	echo '	        	</select>
		            </div>
			        </div>
			        <div class="campoviaselect" id="campoviaselectnumero" style="';
				
				if($tipo_numero!=""){
					echo 'display:block">';
				}else{
					echo 'display:none">';
				}

	echo '				<div class="form-group">
			                <input placeholder="Digite o Número do Processo" style="width:230px;" class="form-control camposomente0910" maxlenght="10" type="text" id="numero" name="numero" value="'.$busca_numero.'">
			        	</div>
			        </div>
			        <div class="campoviaselect" id="campoviaselectbusca_responsavel" style="';
				
				if($tipo_responsavel!=""){
					echo 'display:block">';
				}else{
					echo 'display:none">';
				}

	echo '				<div class="form-group">
			                <input placeholder="Digite o Nome do Responsável" style="width:230px;" class="form-control" maxlenght="50" type="text" id="busca_responsavel" name="busca_responsavel" value="'.$busca_responsavel.'">
			        	</div>
			        </div>
			        <div class="campoviaselect" id="campoviaselectentidade" style="';

				if($tipo_entidade!=""){
					echo 'display:block">';
				}else{
					echo 'display:none">';
				}

	echo '	        	<div class="form-group">
			                <input placeholder="Digite o Nome da Instituição" style="width:250px;" class="form-control" type="text" id="entidade_nome" name="entidade_nome" value="'.$busca_entidade_nome.'">

			                <select id="entidade_cidade" name="entidade_cidade" class="form-control">';
	echo '<option value="-1">Escolha o município da instituição</option>';
			                // Instanciar o DAO e retornar dados da tabela
		                    $MunicipioDAO = new MunicipioDAO();
		                    $dados = $MunicipioDAO->getAll();
		                    if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){				
									if($busca_entidade_cidade==$dados[$i]["idmunicipio"]){
										$d_opcao = " selected=\"selected\" ";
									}else{
										$d_opcao = "";
									}
									echo "<option ".$d_opcao." value=\"".$dados[$i]["idmunicipio"]."\">".$dados[$i]["nome"]."</option>";
								}
							}
	echo '	        	</select>
						</div>
			        </div>';

	echo '		        <div class="div_busca_processo">';
	if($busca != NULL){

		echo '		<button id="botao_cancela_busca_processo" type="reset" class="btn btn-default"><span class="glyphicon glyphicon-eye-close"></span> Ocultar parâmetros</button>
					&nbsp;&nbsp;<button id="botao_remove_busca_processo" type="reset" class="btn btn-warning"><span class="glyphicon glyphicon-remove"></span> Remover parâmetros</button>&nbsp;&nbsp;';
	}else{
		echo '		<button id="botao_cancela_busca_processo" type="reset" class="btn btn-default">Cancelar</button>&nbsp;&nbsp;';	
	}
		echo '		<button id="botao_envia_busca_processo" type="submit" class="btn btn-primary">Buscar</button>
			    </div>
				</form>';
}

if(verificaFuncaoUsuario(FUNCAO_PROCESSO_ADD)!==false){
 	echo "&nbsp;&nbsp;<button class=\"";
 	//se estiver na pagina referente, desabilita o botão
 	if($paginaAtual=="add_pro.php"){ echo "disabled "; }
 	echo "btn btn-info\" value=\"add_pro.php\" title=\"Adicionar Processo\" type=\"button\" aria-label=\"Left Align\">		
		  				<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> PROCESSO</strong>
		  			  </button>";
}
echo "</div></div>";
?>