<?php
/*
ARQUIVO DE ROTINAS AUTOMÁTICAS:
    LIMPA LOGS + 10 dias
    LIMPA TEMP + 2 horas
    LIMPA SESSIONS + 24 horas
    DELETA ARQUIVOS REMOVIDOS A MAIS DE 3 MESES
    BACKUP BD
*/
require_once("../config.php");

if(isset($myDB_user) && isset($myDB_pass)){

    //mysql:host=localhost;dbname=coren_spaf_localhost;charset=utf8
    $pos_dbname             =   strpos($myDB_dsn,'dbname=')+7;
    $pos_fim_dbname         =   ( (strrpos($myDB_dsn,';')) - ( $pos_dbname ) );
    $nome_banco             =   substr( $myDB_dsn, $pos_dbname, $pos_fim_dbname );
    //pasta do upload do arquivo de backup: raiz/@admin
    $pasta                  =   APP_URL_BACKUP;
    $nome_arquivo_backup    =   $pasta.$nome_banco."_".date("Ymd").".sql";
    $nome_arquivo_backupzip =   $pasta.$nome_banco."_".date("Ymd").".zip";   

    //se o banco ainda não foi salvo no dia atual CRIA um novo
    if(!file_exists($nome_arquivo_backupzip)){

        //trecho abaixo limpa arquivos de log na pasta APP_URL/logs
            /** define the directory **/
            $dir2 = APP_URL_UPLOAD."logs/";
            /*** cycle through all files in the directory ***/
            foreach (glob($dir2."*") as $file2) {
                /*** if file is 10 days (864000 seconds) old then delete it ***/
                if (filemtime($file2) < time() - 864000) {
                    unlink($file2);
                }
            }

        //trecho abaixo limpa arquivos de pre-processo na pasta APP_URL/pre_processo
            /** define the directory **/
            $dir2 = APP_URL_UPLOAD."temp/";
            /*** cycle through all files in the directory ***/
            foreach (glob($dir2."*") as $file2) {
                /*** if file is 2,5 hours (9060 seconds) old then delete it ***/
                if (filemtime($file2) < time() - 9060) {
                    unlink($file2);
                }
            }

        //trecho abaixo, limpa arquivos de sessão na pasta APP_URL/sessions
            /** define the directory **/
            $dir = APP_URL_UPLOAD."sessions/";
            /*** cycle through all files in the directory ***/
            foreach (glob($dir."*") as $file) {
                /*** if file is 12 hours (43200 seconds) old then delete it ***/
                if (filemtime($file) < time() - 86400) {
                    unlink($file);
                }
            }

        //trecho abaixo, DELETA DO FTP docs removidos a mais de 3 meses
            //insere Classes pertinentes ao processo
            require_once('../model/Historico.php');
            require_once('../model/Documento.php');
            require_once('../dao/DocumentoDAO.php');
            require_once('../dao/HistoricoDAO.php');
            //calcula data de (no mínimo) 3 meses anterior a data atual
            $dtcriterio=date('Y-m-d 00:00:00',mktime(0, 0, 0, (date('m')-3), 01, date('Y')));
            //instancia DAO usado no script
            $DocumentoDAO = new DocumentoDAO();
            //retorna documentos removidos a mais de 3 meses e com link válido
            $excluidos = $DocumentoDAO->getAllExcluidos($dtcriterio);
            //varre array de documentos a excluir
            foreach ($excluidos as $d) {
                //se for um documento válido para a exclusão
                if(!empty($d["link"]) && $d["idprocesso"]>0 && $d["iddocumento"]>0){
                    //define variavel exclusão do arquivo
                    $destino    = APP_URL_UPLOAD.$d["idprocesso"].'/'.$d["link"];
                    //remove arquivo
                    $removeu    = @unlink($destino);
                    //cria documento com link NULL para dar update no link
                    $Documento = new Documento();
                    $Documento->setId($d["iddocumento"]);
                    $Documento->setProcesso($d["idprocesso"]);
                    $Documento->setLink(NULL);
                    //se não conseguiu remover o documento do servidor
                    if(!$removeu){
                        $obs_log="Rotina Automática".APP_LINE_BREAK."Não foi possível remover o arquivo (".$d["link"].") do FTP pois o mesmo não foi encontrado.";
                        //SALVA NO HISTÓRICO
                        $Historico = new Historico();
                        $Historico->setAcao(LOG_DEL_UPLOAD);
                        $Historico->setProcesso($d["idprocesso"]);
                        $Historico->setDocumento($d["iddocumento"]);
                        $Historico->setObs(sqlTrataString($obs_log));
                        $HistoricoDAO = new HistoricoDAO();
                        $inseriuLog=$HistoricoDAO->insert($Historico);
                    //se conseguiu remover o documento do servidor
                    }else{
                        $obs_log="Rotina Automática".APP_LINE_BREAK."Arquivo (".$d["link"].") removido do FTP por estar a mais de 3 meses excluído sem contestação";
                        //se removeu o documento com sucesso => SALVAR NO HISTÓRICO
                        $Historico = new Historico();
                        $Historico->setAcao(LOG_DEL_UPLOAD);
                        $Historico->setProcesso($d["idprocesso"]);
                        $Historico->setDocumento($d["iddocumento"]);
                        $Historico->setObs(sqlTrataString($obs_log));
                        $HistoricoDAO = new HistoricoDAO();
                        $inseriuLog=$HistoricoDAO->insert($Historico);
                    }
                    //atualiza link (seta como NULL) p/ n ser mais verificado
                    $updateLink=$DocumentoDAO->updateLink($Documento);
                    if(!$updateLink){
                        $obs_log="Rotina Automática".APP_LINE_BREAK."Link não pôde ser atualizado";
                        //se removeu o documento com sucesso => SALVAR NO HISTÓRICO
                        $Historico = new Historico();
                        $Historico->setAcao(LOG_DEL_UPLOAD);
                        $Historico->setProcesso($d["idprocesso"]);
                        $Historico->setDocumento($d["iddocumento"]);
                        $Historico->setObs(sqlTrataString($obs_log));
                        $HistoricoDAO = new HistoricoDAO();
                        $inseriuLog=$HistoricoDAO->insert($Historico);
                    }
                }
            }

        //trecho abaixo faz o backup do BD:
            //DAO das Rotinas de Backup do BD
            require_once("../dao/RotinaDAO.php");
            $RotinaDAO = new RotinaDAO();
            //salva no array "tabela" todas as tabelas do BD
            $tabelas = $RotinaDAO->showTables($nome_banco);
            foreach ($tabelas as $l) {
                if(!empty($l[0])){
                    $tabela[]=$l[0];
                }                
            }
            //descobre versao do MySql
            $v = $RotinaDAO->showVersion();
            $version = $v[0];
            //inicia variavel SQL que armazenará dados do BD                 
            $sql  = "-- Backup de Banco de Dados do CorenSC\r\n";   
            $sql .= "-- Usuario criador: ".$_SESSION['USUARIO']['login']." (".$_SESSION['USUARIO']['idusuario'].")\r\n";
            $sql .= "-- Banco de dados: ". $nome_banco ."\r\n";            
            $sql .= "-- Data backup: ". date("d/m/Y H:i:s")."\r\n";
            $sql .= "-- Versao MySQL: ".$version."\r\n";           
            $sql .= "-- Versao PHP: ". phpversion()."\r\n\r\n";
            //armazena infos do AUTO_INCREMENT das tabelas
            $re  = $RotinaDAO->showTablesStatus();
            foreach ($re as $r) {
                $tbl_stat[$r["Name"]] = $r["Auto_increment"];
            }
            //executa comandos REPAIR e OPTIMIZE para todas as tabelas
            for($i = 0; $i < count($tabela); $i++) {
                $RotinaDAO->checkTable($tabela[$i]);
                $RotinaDAO->analyzeTable($tabela[$i]);
                $RotinaDAO->repairTable($tabela[$i]);
                $RotinaDAO->optimizeTable($tabela[$i]);
            }
            //limpezas gerais do mysql
            $RotinaDAO->flushHosts();
            $RotinaDAO->flushLogs();
            $RotinaDAO->flushStatus();
            //para cada tabela gera SQL da mesma
            for($i = 0; $i < count($tabela); $i++) {
                    $sql .= "-- Estrutura da tabela $tabela[$i]\r\n\r\n";
                    $l2  = $RotinaDAO->showCreateTable($tabela[$i]);
                    //armazena a criação da estrutura da tabela
                    foreach ($l2 as $l) {
                        if($tbl_stat[$tabela[$i]] != "") {
                            $sql .= str_replace("  ", "\t", str_replace("`", "", $l[1])). " AUTO_INCREMENT=". $tbl_stat[$tabela[$i]] .";\r\n\r\n";
                        } else {
                                $sql .= str_replace("  ", "\t", str_replace("`", "", $l[1])).";\r\n\r\n";                                      
                        }
                    }
                    //armazena colunas da tabela
                    $row  = $RotinaDAO->showColumns($tabela[$i]);
                    $campos = "";
                    foreach ($row as $r) {
                        $campos[] = $r[0];
                    }
                    //faz um select * na tabela e vai armazenando os dados da mesma
                    $dt  = $RotinaDAO->select($tabela[$i]);
                    if(sizeof($dt)>0) {
                        foreach ($dt as $d) {
                            $valores = "";                  
                            for($j = 0; $j < sizeof($d); $j++){
                                    $valores[] .= "'". addslashes($d[$j]) ."'";
                            }
                            $campo = implode(", ", $campos);        
                            $valor = implode(", ", $valores);       
                            $sql  .= "INSERT INTO $tabela[$i] ($campo) VALUES ($valor);\r\n";    
                        }
                    }
                    $sql .= "\r\n";
            }
            //salva a SQL num arquivo .sql
            $fp = fopen($nome_arquivo_backup, "w+");
            chmod($nome_arquivo_backup,0660);
            if(!fwrite($fp, $sql)) {
                    echo "Erro na criação do arquivo de backup, verifique a permissao de escrita";
            }
            fclose($fp);
            //agora cria um ZIP do arquivo .sql e deleta o .sql em seguida, deixando apenas o .zip
            $zip = new ZipArchive();
            $filename = $nome_arquivo_backupzip;
            if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
                exit("Biblioteca ZIP não encontrada!");
            }
            $zip->addFile($nome_arquivo_backup,$nome_banco."_".date("Ymd").".sql");
            $zip->close();
            //deleta o arquivo .SQL pesado que foi criado e jogado pra dentro de um ZIP
            @unlink($nome_arquivo_backup);
        
    }

}else{
    echo "<meta http-equiv=\"refresh\" content=\"0; url=404.html\">";
}
?>