<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../login_verifica.php");
require_once("../conexao.php");

if(isset($_POST["id"])){
	$deletou = false;
	
	$id = validaInteiro($_POST["id"],ETAPA_ID_SIZE);
	
	//verifica se o id é valido e se o usuário pode deletar esse registro
	if($id!==false && verificaFuncaoUsuario(FUNCAO_ETAPA_DEL)!==false){
		
		$id = sqlTrataInteiro($id);
		if($id!==false){
			
			// Carrega DAOS e Models pertinentes
			require_once('../model/Registry.php');
			require_once('../model/Etapa.php');
			require_once('../dao/EtapaDAO.php');			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);
			// Instanciar o DAO
			$EtapaDAO = new EtapaDAO();
			// Instancia o objeto
			$Etapa = new Etapa();
			$Etapa->setId($id);
			
			$dados=$EtapaDAO->getOne($id);
			$deletou = $EtapaDAO->delete($Etapa);

			//após deletar, altera os IDS para VALORES
			$Etapa->setNome($dados["nome"]);

			if($deletou){

				//deleta relacionamentos desta etapa
					//etapa_documentotipo
					$removeDocs = $EtapaDAO->deleteDocs($Etapa);
					//etapa_emails
					$removeEmails = $EtapaDAO->deleteEmails($Etapa);

				//reordena etapas
					$listaEtapas = $EtapaDAO->getAll();				
					//variaveis p/ criação da lista organizada
					$novaLista = array();
					$valorOrdemAtual = 0;
					$valorOrdemAnterior = 0;
					for($i=0;$i<sizeof($listaEtapas);$i++){
						if($listaEtapas[$i]["fluxo"] == 0){
							$valorOrdemAtual=floor($valorOrdemAtual)+1;
						}else{
							$valorOrdemAtual+=0.1;
						}
						//Trocou ordem anterior: 1.5 vira 1.6 (35)
						//echo "<br>(".$listaEtapas[$i]["idetapa"].") ".round($listaEtapas[$i]["ordem"], 2)." - ".round($valorOrdemAnterior, 2)." = 0 ? ";
						if(round($listaEtapas[$i]["ordem"], 2)-round($valorOrdemAnterior, 2)==0){
							//echo "sim <br>";
							$idetapa_inverter=false;
							//se o repetido encontrado não for a etapa atual troca o valor da etapa anterior
							if($idetapa!=$listaEtapas[$i]["idetapa"]){
								//echo "Trocou ordem anterior: ".$listaEtapas[$i-1]["ordem"]." vira ".$valorOrdemAtual." (".$listaEtapas[$i-1]["idetapa"].")<br>";
								$listaEtapas[$i-1]["ordem"]=$valorOrdemAtual;
								//armazena ID da ordem atual que for igual a nova, para inverter posições no final
								if(round($listaEtapas[$i-1]["ordem"],2)==round($ordem,2)){
									$idetapa_inverter=$i-1;
								}
							}else{
								//inverte a ordem com quem tem a mesma ordem
								if($idetapa_inverter!==false){
									//echo "Pega o valor (".$listaEtapas[$idetapa_inverter]["ordem"].") da etapa ".$listaEtapas[$idetapa_inverter]["idetapa"]." e põe na criada agora (".$listaEtapas[$i]["idetapa"].") substituindo o valor ".$listaEtapas[$i]["ordem"]."<br>";
									$listaEtapas[$i]["ordem"]=$listaEtapas[$idetapa_inverter]["ordem"];
									$listaEtapas[$idetapa_inverter]["ordem"]=$valorOrdemAtual;	
								}else{
									//echo "Põe o valor $valorOrdemAtual que antes era ".$listaEtapas[$i]["ordem"]." (etapa ".$listaEtapas[$i]["idetapa"].")<br>";
									$listaEtapas[$i]["ordem"]=$valorOrdemAtual;
								}
							}
						}
						//se a ordematual for diferente da ordem da listagem, adiciona valor correto da ordem ao array
						if((round($valorOrdemAtual, 2)-round($listaEtapas[$i]["ordem"], 2))!=0){
							$novaLista[$i]["idetapa"]=$listaEtapas[$i]["idetapa"];
							$novaLista[$i]["ordem"]=$valorOrdemAtual;
							//echo "Diferente: ".$novaLista[$i]["ordem"]." vira ".$valorOrdemAtual." (".$novaLista[$i]["idetapa"].")<br>";
						//se forem iguais, apenas acrescenta o registro a listagem
						}else{
							$novaLista[$i]["idetapa"]=$listaEtapas[$i]["idetapa"];
							$novaLista[$i]["ordem"]=$listaEtapas[$i]["ordem"];	
							//echo "Igual: ".$novaLista[$i]["ordem"]." && ".$listaEtapas[$i]["ordem"]." (".$novaLista[$i]["idetapa"].")<br>";
						}
						$valorOrdemAnterior = $listaEtapas[$i]["ordem"];
					}

					//dá update nas ordens que estavam erradas
					for($i=0;$i<sizeof($novaLista);$i++){
						for($j=0;$j<sizeof($listaEtapas);$j++){
							if($novaLista[$i]["idetapa"]==$listaEtapas[$j]["idetapa"] && $novaLista[$i]["ordem"]!=$listaEtapas[$j]["ordem"]){
								$atualizaOrdem = $EtapaDAO->updateOrdem($novaLista[$i]["idetapa"],$novaLista[$i]["ordem"]);
								if(!$atualizaOrdem){
									enviaMsg("erro","Etapa atualizada com erros","Não foi possível gravar o envio de e-mail para um perfil específico");
									echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php\">";
									exit();
								}
							}
						}
					}
				
				$log = $Etapa->toLog();
				//registra ação no log
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_ETAPA);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log);
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);
			}
			
		}
	}	
	echo ($deletou)?'sucesso':'problema'; 
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}
?>