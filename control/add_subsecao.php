<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_ADD)!==false){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/SubsecaoDAO.php");
require_once("../dao/MunicipioDAO.php");
//carrega Models
require_once('../model/Subsecao.php');

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES SUBSEÇÃO
require_once('submenu_subsecao.php');
//FIM SUBMENU DE AÇÕES SUBSEÇÃO

//se tiver enviado o formulário
if(isset($_POST) && !empty($_POST) ){

	//valida entradas do usuário
	$nome=validaLiteral($_POST["nome"],SUBSECAO_NOME_SIZE);
	$idmunicipio=validaInteiro($_POST["idmunicipio"],MUNICIPIO_ID_SIZE);

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && $idmunicipio!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);
		$idmunicipio=sqlTrataInteiro($idmunicipio);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false && $idmunicipio!==false){

			//validação jurisdicao
			$jurisdicao=false;
			if(isset($_POST["jurisdicao"]) && sizeof($_POST["jurisdicao"])>0){
				$jurisdicao=$_POST["jurisdicao"];
			}
				  
			// Instanciar as infos do usuario
			$Subsecao = new Subsecao();
			$Subsecao->setNome($nome);
			$Subsecao->setMunicipio($idmunicipio);
			// Instanciar o DAO para inserir o usuario na base
			$SubsecaoDAO = new SubsecaoDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $SubsecaoDAO->isDuplicated($Subsecao);
			//se cair aqui é pq o nome da subseção já existe
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Erro","Uma Subseção chamada <strong>".$nome."</strong> já existe, por favor, utilize outro nome");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
				exit();
			}
			
			//insere registro
			$idsubsecao = $SubsecaoDAO->insert($Subsecao);

			//atribui ID via método do Usuário
			$Subsecao->setId($idsubsecao);
			if($idsubsecao){				

				//Recupera e altera dados para salvar os valores e não os IDS no histórico
				$dado=$SubsecaoDAO->getOne($Subsecao);
				$Subsecao->setMunicipio($dado["nomecidade"]);
				//Armazena dados para a coluna OBS do log
				$log_obs = $Subsecao->toLog();

				//verificação e inserção de subsecao_municipio
				$inseriuJurisdicao=false;
				if($jurisdicao!==false && sizeof($jurisdicao)>0){
					$log_obs.="Subseção atrelada a ".sizeof($jurisdicao)." municípios".APP_LINE_BREAK;
					for($i=0;$i<sizeof($jurisdicao);$i++){
						$Subsecao->setMunicipio($jurisdicao[$i]);
						//insere irregularidades
						$inseriuJurisdicao=$SubsecaoDAO->insertJurisdicao($Subsecao);						
					}
					if(!$inseriuJurisdicao){
						//se cair aqui é pq não inseriu a Jurisdicao
						enviaMsg("erro","Erro","A subseção foi inserida porém as cidades não puderam ser atreladas a ela");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
						exit();
					}
				}

				//registra ação no log				
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_ADD_SUBSECAO);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log_obs);
				$HistoricoDAO = new HistoricoDAO();
				$idhistorico = $HistoricoDAO->insert($Historico);

				//se cair aqui é pq deu tudo certo
				if($idhistorico){					
					enviaMsg("sucesso","Subseção cadastrada com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
					exit();
				//se cair aqui é pq o histórico não foi cadastrado
				}else{					
					enviaMsg("erro","Subseção cadastrada porém o histórico não pôde ser salvo");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
					exit();
				}

			//fim if($idprocesso) - se cair aqui é pq a subseção não foi cadastrada
			}else{

				enviaMsg("erro","Subseção não cadastrada","Tente novamente mais tarde");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
				exit();

			}			
			
		}else{

			//não cadastrado - dados invalidos
			enviaMsg("erro","Subseção não cadastrada","Os dados fornecidos estavam inválidos, tente novamente");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
			exit();
		}
		
	}else{
		
		//não cadastrado - dados invalidos
		enviaMsg("erro","Subseção não cadastrada","Os dados fornecidos estavam inválidos, por favor tente novamente");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_subsecao.php\">";
		exit();
		
	}

}//fim do IF do POST
else{

?>

<div id="conteudo_borda">
    <div  id="conteudo">
        <!-- <form enctype="multipart/form-data" name="add_subsecao" action="add_subsecao.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_subsecao','nome','municipio');" > -->    
        <form id="add_subsecao" name="add_subsecao" action="add_subsecao.php" method="post" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-10 control-label">Nome da Subseção</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da Subseção" maxlength="<?php echo SUBSECAO_NOME_SIZE-20; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group">

            	<label for="idmunicipio" class="col-sm-10 control-label">Município</label>
			    <div class="col-sm-4">
			      <select id="idmunicipio" name="idmunicipio" class="form-control">
					<option value="-1">Selecione o município desta subseção&nbsp;&nbsp;&nbsp;&nbsp;</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $MunicipioDAO = new MunicipioDAO();
                    $result = $MunicipioDAO->getAll();
                    if(sizeof($result)>0){
						for($i=0;$i<sizeof($result);$i++){
							echo "<option value=\"".$result[$i]["idmunicipio"]."\">".$result[$i]["nome"]."</option>";
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
                    $result = $MunicipioDAO->getAllSubsecao();
                    if(sizeof($result)>0){
						for($i=0;$i<sizeof($result);$i++){
							echo "<div class=\"col-md-4\">
							<input type=\"checkbox\" name=\"jurisdicao[]\" id=\"jurisdicao_".$i."\" 
							value=\"".$result[$i]["idmunicipio"]."\">".$result[$i]["nome"]
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

}//fim do else do POST

//else do verificaFuncaoUsuario
}else{

		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>
