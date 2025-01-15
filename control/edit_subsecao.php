<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_EDIT)!==false){

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES SUBSEÇÃO
require_once('submenu_subsecao.php');
//FIM SUBMENU DE AÇÕES SUBSEÇÃO

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/SubsecaoDAO.php");
require_once('../dao/MunicipioDAO.php');
//carrega Models
require_once('../model/Subsecao.php');


// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);



//se tiver enviado o formulário
if(isset($_POST) && !empty($_POST) ){

	//valida entradas do usuário
	$nome=validaLiteral($_POST["nome"],SUBSECAO_NOME_SIZE);
	$idmunicipio=validaInteiro($_POST["idmunicipio"],MUNICIPIO_ID_SIZE);
	$idsubsecao=validaInteiro($_POST["p"],SUBSECAO_ID_SIZE);

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && $idmunicipio!==false && $idsubsecao!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);
		$idmunicipio=sqlTrataInteiro($idmunicipio);
		$idsubsecao=sqlTrataInteiro($idsubsecao);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false && $idmunicipio!==false && $idsubsecao!==false){


			//validação jurisdicao
			$jurisdicao=false;
			if(isset($_POST["jurisdicao"]) && sizeof($_POST["jurisdicao"])>0){
				$jurisdicao=$_POST["jurisdicao"];
			}

			// Instanciar as infos
			$Subsecao = new Subsecao();
			$Subsecao->setId($idsubsecao);		
			$Subsecao->setNome($nome);		
			$Subsecao->setMunicipio($idmunicipio);	
			// Instanciar o DAO para inserir o usuario na base
			$SubsecaoDAO = new SubsecaoDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $SubsecaoDAO->isDuplicatedEdit($Subsecao);
			//se cair aqui é pq o nome da subseção já existe
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Erro","Uma Subseção chamada <strong>".$nome."</strong> já existe, por favor, utilize outro nome");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
				exit();
			}

			//atualiza registro
			$atualizou = $SubsecaoDAO->update($Subsecao);
			//atribui ID via método do Usuário
			if($atualizou){						

				//Recupera e altera dados para salvar os valores e não os IDS no histórico
				$dado=$SubsecaoDAO->getOne($Subsecao);
				$Subsecao->setMunicipio($dado["nomecidade"]);
				//Armazena dados para a coluna OBS do log
				$log_obs = $Subsecao->toLog();

				//verificação e inserção de subsecao_municipio
				$inseriuJurisdicao=false;
				//remove anteriores
				$SubsecaoDAO->deleteJurisdicoes($Subsecao);
				if($jurisdicao!==false && sizeof($jurisdicao)>0){
					$log_obs.="Subseção atrelada a ".sizeof($jurisdicao)." municípios".APP_LINE_BREAK;
					for($i=0;$i<sizeof($jurisdicao);$i++){
						$Subsecao->setMunicipio($jurisdicao[$i]);
						//insere irregularidades
						$inseriuJurisdicao=$SubsecaoDAO->insertJurisdicao($Subsecao);						
					}
					if(!$inseriuJurisdicao){
						//se cair aqui é pq não inseriu a Jurisdicao
						enviaMsg("erro","Erro","A subseção foi atualizada porém as cidades não puderam ser atreladas a ela");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
						exit();
					}
				}
				
				//registra ação no log	
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_EDIT_SUBSECAO);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log_obs);
				$HistoricoDAO = new HistoricoDAO();
				$idhistorico = $HistoricoDAO->insert($Historico);

				//se cair aqui é pq deu tudo certo
				if($idhistorico){					
					enviaMsg("sucesso","Subseção atualizada com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
					exit();
				//se cair aqui é pq o histórico não foi inserido
				}else{					
					enviaMsg("erro","Subseção atualizada porém o histórico não pôde ser salvo");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
					exit();
				}

			//fim if atualizou	- se cair aqui é pq não atualizou
			}else{
				enviaMsg("erro","Subseção não atualizada","Tente novamente mais tarde");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
				exit();
			}

		}else{

			//não atualizada - dados invalidos
			enviaMsg("erro","Subseção não atualizada","Os dados fornecidos estavam inválidos, tente novamente");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
			exit();
		}
		
	}else{
		
		//não atualizada - dados invalidos
		enviaMsg("erro","Subseção não atualizada","Os dados fornecidos estavam inválidos, por favor tente novamente");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
		exit();
		
	}



}//fim do IF do POST
else{

	if(isset($_GET["p"]) && !empty($_GET["p"])){

		//valida informações
		$idsubsecao=validaInteiro($_GET["p"],SUBSECAO_ID_SIZE);
		if($idsubsecao!==false){

			// Instanciar as infos
			$Subsecao = new Subsecao();	
			$Subsecao->setId($idsubsecao);

			// Instanciar o DAO para recuperar as informações do banco de dados
			$SubsecaoDAO = new SubsecaoDAO();

			// Chama a função que retorna os dados do registro requisitado
			$result = $SubsecaoDAO->getOne($Subsecao);

			//se encontrou o registro, exibe na tela
			if($result!==false){
		?>
				<div id="conteudo_borda">
				    <div  id="conteudo">
				    	<!-- <form enctype="multipart/form-data" name="edit_subsecao" action="edit_subsecao.php" method="post" class="form-horizontal" onSubmit="return validaForm('edit_subsecao','nome','municipio');" > -->
				        <form id="edit_subsecao" name="edit_subsecao" action="edit_subsecao.php" method="post" class="form-horizontal">
				        <input type="hidden" name="p" id="p" value="<?php echo $idsubsecao; ?>">

				            <div class="form-group">
				                <label class="col-sm-10 control-label">Nome da Subseção</label>
				                <div class="col-lg-8">
				                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da Subseção" maxlength="<?php echo SUBSECAO_NOME_SIZE-20; ?>" value="<?php echo htmlentities($result["nome"]); ?>">
				                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
				                </div>
				            </div>

							<div class="form-group">

				            	<label for="idmunicipio" class="col-sm-10 control-label">Município</label>
							    <div class="col-sm-4">
							      <select id="idmunicipio" name="idmunicipio" class="form-control">
									<option value="-1">Selecione o município desta subseção&nbsp;&nbsp;&nbsp;&nbsp;</option>
									<?php
				                    // Instanciar o DAO e retornar dados da tabela TIPO
				                    $MunicipioDAO = new MunicipioDAO();
				                    $result2 = $MunicipioDAO->getAll();
				                    if(sizeof($result2)>0){
										for($i=0;$i<sizeof($result2);$i++){
											if($result["idmunicipio"]==$result2[$i]["idmunicipio"]){
												echo "<option value=\"".
												$result2[$i]["idmunicipio"]."\" selected=\"selected\">".
												$result2[$i]["nome"]."</option>";
											}else{
												echo "<option value=\"".
												$result2[$i]["idmunicipio"]."\">".
												$result2[$i]["nome"]."</option>";
											}
											
										}
									}
									?>
				                  </select>
							      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
							    </div>
							    
				            </div>

				            <div class="well">
					            <div class="form-group">
					            	<label for="jurisdicao" class="col-sm-10 control-label">Municípios Abrangidos</label>            	
					            </div>
					            <div class="row">
					            	<?php
					            	$MunicipioDAO = new MunicipioDAO();
					            	//primeiro retorna os municipios já selecionados
					            	$result1 = $MunicipioDAO->getAllSubsecao($idsubsecao);
				                    if(sizeof($result1)>0){
										for($i=0;$i<sizeof($result1);$i++){
											echo "<div class=\"col-md-4 bg-info\">
											<input type=\"checkbox\" name=\"jurisdicao[]\" id=\"jurisdicao_".$i."\" 
											value=\"".$result1[$i]["idmunicipio"]."\" checked>".$result1[$i]["nome"]
											."</div>";
										}
									}
									//depois retorna os municípios não atrelados a nenhuma subseção
				                    $result2 = $MunicipioDAO->getAllSubsecao();
				                    if(sizeof($result2)>0){
										for($i=0;$i<sizeof($result2);$i++){
											echo "<div class=\"col-md-4\">
											<input type=\"checkbox\" name=\"jurisdicao[]\" id=\"jurisdicao_".$i."\" 
											value=\"".$result2[$i]["idmunicipio"]."\">".$result2[$i]["nome"]
											."</div>";
										}
									}
									?>
					            </div>
				            </div>

				            <div class="form-group">
				              <div class="col-lg-10">
				                <button type="reset" id="cancelar" class="btn btn-default index_subsecao.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
				                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
				              </div>
				            </div>

				        </form>
					</div>
				</div>
				<?php

				include_once("../menu_rodape.php");

			}//fim if($result!==false){
			else{
				enviaMsg("erro","Acesso negado","A subseção foi removida ou os dados fornecidos estão inválidos");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
				exit();
			}

		}//fim if($identidadetipo!==false){	
		else{
			enviaMsg("erro","Acesso negado","A subseção foi removida ou os dados estão inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
			exit();
		}

	}//fim do IF do GET "p"
	else{
		
		enviaMsg("erro","Acesso negado","Dados da subseção não informados");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
		exit();

	}

}//fim do else do POST

//else do verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)
}else{

		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>
