<?php
/*  ARQUIVO QUE EFETUA ROTINAS QUE SÃO DEFINIDAS NA TABELA config DO BANCO DE DADOS */
//verificação se o "requirente" está logado, possui as configs do sistema e tem conexão com o BD
if(isset($myDB_user) && isset($myDB_pass) && !empty(APP_TITLE) && !empty(APP_LINE_BREAK) && isset($_SESSION["USUARIO"]) && !empty($_SESSION["USUARIO"])){

//carrega MODELS/DAO's pertinentes
    require_once('../dao/ConfigDAO.php');
    require_once('../model/Config.php');

//rotina ID1 - "DTFIM"
    $ConfigDAO = new ConfigDAO();
    $result = $ConfigDAO->getLastDtFim();
    //se HOJE não tiver sido atualizado ainda, faz a atualização
    if($result["dtatualizacao"]<date("Ymd")){
        //carrega MODELS/DAO's pertinentes
        require_once('../dao/ProcessoDAO.php');
        require_once('../model/Processo.php');
        //pega processos que tem DTFIM mas não receberam AVISO (email) ainda
        $ProcessoDAO = new ProcessoDAO();
        $config_processos = $ProcessoDAO->getDtFimSemDtAviso();
        //var de controle p/ ajax
        $tipoajaxmail="dtfim";
        //se houver resultados:
        if(sizeof($config_processos)>0){
            //para cada resultado
            foreach ($config_processos as $c) {
                //se a data atual for maior que a data que deveria ter sido enviado o aviso
                if(date("Ymd")>=$c["dtfim"]){
                    //manda email & salva no histórico
                        $Processo = new Processo();
                        $Processo->setId($c["idprocesso"]);
                        $config_infosprocesso = $ProcessoDAO->getInfosCapa($Processo);
                        include("ajax_mail.php");
                    //dá update no processo indicando a data de envio do lembrete(dtaviso)
                        $updateaviso = $ProcessoDAO->updateDtAviso($Processo);
                }
            }
            //ao final dá update na verificação, pois esse tipo de conferência é feito somente 1 vez por dia.
            $config_verificado = $ConfigDAO->updateLastDt("dtfim");
            //se não conseguiu atualizar imprime erro "suave" na tela
            if(!$config_verificado){
                enviaMsg("erro","Aviso","Data de aviso para renovação não atualizada. Este é um alerta para o administrador, favor desconsiderar essa mensagem. Obrigado.");
            }
        //se não houver resultados apenas informa ao banco que já verificou esta situação hoje
        }else{
            $config_verificado = $ConfigDAO->updateLastDt("dtfim");
            //se não conseguiu atualizar imprime erro "suave" na tela
            if(!$config_verificado){
                enviaMsg("erro","Aviso","A data de aviso para renovação não foi atualizada. Este é um alerta para o administrador, favor desconsiderar essa mensagem. Obrigado.");
            }
        }
    }

//rotina ID2 - ENVIA E-MAIL PARA OS RESPONSÁVEIS DA ETAPA À 2 DIAS DO FIM DO PRAZO
    //rotina ID2 - "DTPRAZO"
    $ConfigDAO = new ConfigDAO();
    $result = $ConfigDAO->getLastDtPrazo();
    //se HOJE não tiver sido atualizado ainda, faz a atualização
    if($result["dtatualizacao"]<date("Ymd")){
        //carrega MODELS/DAO's pertinentes
        require_once('../dao/ProcessoDAO.php');
        require_once('../model/Processo.php');
        //pega etapas que tem PRAZO com fim a 3 dias ou menos da data atual mas não receberam AVISO (email) ainda
        $ProcessoDAO = new ProcessoDAO();
        $config_processos = $ProcessoDAO->getDtPrazoSemDtAviso();
        //var de controle p/ ajax
        $tipoajaxmail="dtprazo";
        //se houver resultados:
        if(sizeof($config_processos)>0){
            //para cada resultado
            foreach ($config_processos as $c) {                
                //se a data atual for maior ou igual a (dtprazo - 3 dias) envia email
                $dtprazo=date('Ymd', strtotime('-3 days', strtotime($c["dtprazo"])));
                if(date("Ymd")>=$dtprazo){
                    //manda email & salva no histórico
                        $Processo = new Processo();
                        $Processo->setId($c["idprocesso"]);
                        $config_infosprocesso = $ProcessoDAO->getInfosCapa($Processo);
                        $infosresponsaveis  = $ProcessoDAO->getResponsaveisEtapa($Processo);
                        include("ajax_mail.php");
                    //dá update no processo indicando a data de envio do lembrete(flagprazo)
                        $updateaviso = $ProcessoDAO->updateFlagPrazo($Processo);
                }
            }
            //ao final dá update na verificação, pois esse tipo de conferência é feito somente 1 vez por dia.
            $config_verificado = $ConfigDAO->updateLastDt("dtprazo");
            //se não conseguiu atualizar imprime erro "suave" na tela
            if(!$config_verificado){
                enviaMsg("erro","Aviso","Data de aviso para renovação não atualizada. Este é um alerta para o administrador, favor desconsiderar essa mensagem. Obrigado.");
            }
        //se não houver resultados apenas informa ao banco que já verificou esta situação hoje
        }else{
            $config_verificado = $ConfigDAO->updateLastDt("dtprazo");
            //se não conseguiu atualizar imprime erro "suave" na tela
            if(!$config_verificado){
                enviaMsg("erro","Aviso","A data de aviso para renovação não foi atualizada. Este é um alerta para o administrador, favor desconsiderar essa mensagem. Obrigado.");
            }
        }
    }


//se não tiver logado e carregado configs do sistema, dá erro 404:
}else{
    echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}
?>