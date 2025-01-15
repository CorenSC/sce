<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../login_verifica.php");
require_once("../bin/email/PHPMailerAutoload.php");
require_once("../bin/email/class.phpmailer.php");
require_once("../bin/email/class.smtp.php");

$head = "";

$mail = new PHPMailer();

//$mail->SMTPDebug = 3;                           // Enable verbose debug output

$mail->isSMTP();                                    // Set mailer to use SMTP
$mail->Host         = EMAIL_HOST;       // Specify main and backup SMTP servers
$mail->SMTPAuth     = EMAIL_SMTP_AUTH;   // Enable SMTP authentication
$mail->Username     = EMAIL_USER;    // SMTP username
$mail->Password     = EMAIL_PASS;      // SMTP password
$mail->SMTPSecure   = EMAIL_SMTP_SECURE; // Enable TLS encryption, `ssl` also accepted
$mail->Port         = EMAIL_PORT;      // TCP port to connect to

$mail->From         = EMAIL_SENDER;
$mail->FromName     = utf8_decode(APP_TITLE);

//IF envio do e-mail lembrete de renovação da CEEn
if(isset($tipoajaxmail) && !empty($tipoajaxmail) && $tipoajaxmail=="dtfim"
    && isset($config_infosprocesso) && !empty($config_infosprocesso)){

    //variavel de controle de envio de emails
    $emails_enviar=array();
    //verifica se existe o email1 (obrigatório) e adiciona ao array emails_enviar
    if(isset($config_infosprocesso["email1"]) && !empty($config_infosprocesso["email1"])){
       $emails_enviar[]=$config_infosprocesso["email1"];
       //verifica se existe o email2 e se é diferente do email1 e adiciona ao array emails_enviar
       if(isset($config_infosprocesso["email2"]) && !empty($config_infosprocesso["email2"]) && $config_infosprocesso["email2"]!=$config_infosprocesso["email1"]){
        $emails_enviar[]=$config_infosprocesso["email2"];
       }
    }

    //para cada envio de email (instituição pode ter mais de 1 email):
    foreach ($emails_enviar as $e) {
        
        $destinatario_nome  = $config_infosprocesso["nomeresponsavel"]." (".$config_infosprocesso["nome_instituicao"].")";
        $destinatario_email = $e;
        $processo           = "Aviso automático para renovação da Comissão de Ética de Enfermagem (CEE)";
        $mensagem           = " Detectamos em nosso sistema que a validade da Comissão de Ética de Enfermagem da sua instituição está prestes a expirar.
                                <br>Recomendamos que inicie o processo de renovação 120 dias antes de finalizar o mandato da CEE vigente, de modo a evitar descontinuidade e garantir que a posse da nova CEE ocorra dentro do prazo. 
                                <br>Para realizar o processo de renovação, acesse o site do Coren-SC e vá para a área de Comissões de Ética de Enfermagem nas Instituições ou vá direto para a tela inicial do processo de renovação <a  style=\"color:#0000FF;\" target=\"_blank\" href=\"".APP_URL."/iniciar.php\">clicando aqui</a> e efetue os passos de acordo com as orientações disponibilizadas no ".APP_TITLE.".
                                <br><br>Se você, destinatário deste e-mail, não tiver mais vínculo com a instituição em foco, por favor repasse essa informação para o e-mail <a style=\"color:#0000FF;\" href='mailto:comissao.etica@corensc.gov.br'>comissao.etica@corensc.gov.br</a>, para que a Comissão de Ética do Coren-SC possa remeter essa mensagem em tempo à instituição. Obrigada.";


        // aqui fica o Titulo do seu email
        $subject = $processo.' - enviado às '.date("H:i:s").' de '.date("d-m-Y");
        $mailheaders = "From: ".APP_TITLE."<".EMAIL_SENDER.">\nContent-Type: text/html\n";
        $html=' <html>
                <head>
                <title>'.APP_TITLE.' - '.$processo.'</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
                </head>
                <body text="#000000" link="#333333" vlink="#000000" alink="#000000" leftmargin="0" topmargin="0">
                <p><strong>Este é um e-mail automático, por favor não responda.</strong></p>
                <p>'.saudacao($destinatario_nome).'!</p>
                <p>'.$mensagem.'</p>
                <p>Atenciosamente,<br>'.APP_TITLE.'.</p>
                </body>
                </html>';

        // Resolve diferença de quebra de linhas, entre Linux/Windows

        $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());

        $head = "MIME-Version: 1.0" . $snap;
        $head.= "From: ".APP_TITLE."<".EMAIL_SENDER.">" . $snap;
        $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
        $head.= "Return-Path: ".EMAIL_SENDER." \r\n"; // return-path

        $mailheaders = $head;

        $mail->ClearAllRecipients();
        $mail->addAddress($destinatario_email);     // Add a recipient
        $mail->isHTML(true);          // Set email format to HTML
        $mail->Subject = utf8_decode($subject);
        $mail->Body = utf8_decode($html);
        $enviou=$mail->send();

        //se enviou o e-mail:
        if($enviou){
            $obs_log="Rotina Automática".APP_LINE_BREAK."Lembrete para renovar a Comissão de Ética".APP_LINE_BREAK."Para: $destinatario_nome ".APP_LINE_BREAK."E-mail: $destinatario_email";
            /* REGISTRA AÇÃO DE ENVIO DE E-MAIL NO LOG */
            //carrega DAO
            require_once("../dao/HistoricoDAO.php");
            //carrega Model
            require_once("../model/Historico.php");
            $Historico = new Historico();
            $Historico->setAcao(LOG_EMAIL);
            $Historico->setProcesso($config_infosprocesso["idprocesso"]);
            $Historico->setObs(sqlTrataString($obs_log));
            $HistoricoDAO = new HistoricoDAO();
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                //se cair aqui é pq não inseriu o log
                echo "erro - não salvou log";
                exit();
            }            
        //se não enviou o e-mail
        }else{
            echo "erro - não enviou e-mail";
            exit();
        }

    }

//IF envio de e-mail para lembrar do prazo que está prestes a expirar
}elseif(isset($tipoajaxmail) && !empty($tipoajaxmail) && ($tipoajaxmail=="dtprazo")){

    //variavel de controle de envio de emails
    $emails_enviar=array();
    $emails_nomes=array();
    //variavel de controle para saber se é fiscal ou instituição
    $instituicao=false;
    $fiscal=false;
    //para cada email de responsável da etapa adicionar para o array de emails a enviar SE:
    foreach ($infosresponsaveis as $i) {
        //se for instituição define que é instituição e não faz nada pois será tratado depois
        if($i["idperfil"]==PERFIL_IDINSTITUICAO){
            $instituicao=true;

        //se for fiscalização define que é fiscal e não faz nada pois será tratado depois
        }elseif($i["idperfil"]==PERFIL_IDFISCALIZACAO){
            $fiscal=true;

        //se não for nesses perfis vai adicionando a lista os emails/destinatarios
        }else{

            //se possui algum email (o email1 é obrigatório) e se ele não está no array
            if(isset($i["email1"]) && !empty($i["email1"]) 
                && !in_array($i["email1"], $emails_enviar)){
                $emails_enviar[]=$i["email1"];
                $emails_nomes[]=$i["nome"];
            }
            //se existe o email2 e se é diferente do email1
            if(isset($i["email2"]) && !empty($i["email2"]) 
                && !in_array($i["email2"], $emails_enviar)){
                $emails_enviar[]=$i["email2"];
                $emails_nomes[]=$i["nome"];
            }

        }
        
    }
    //se for instituição pega os emails do infosprocesso
    if($instituicao){
        //se possui algum email (o email1 é obrigatório) e se ele não está no array
        if(isset($config_infosprocesso["email1"]) && !empty($config_infosprocesso["email1"])){
            $emails_enviar[]=$config_infosprocesso["email1"];
            $emails_nomes[]=$config_infosprocesso["nomeresponsavel"];
        }
        //se existe o email2 e se é diferente do email1
        if(isset($config_infosprocesso["email2"]) && !empty($config_infosprocesso["email2"]) && !in_array($config_infosprocesso["email2"], $emails_enviar)){
            $emails_enviar[]=$config_infosprocesso["email2"];
            $emails_nomes[]=$config_infosprocesso["nomeresponsavel"];
        }
    }
    //se for fiscal pega os emails dos fiscais da subseção do processo
    if($fiscal){
        $fiscais_sub = $ProcessoDAO->getFiscaisSubsecao($config_infosprocesso["idsubsecao"]);
        //exibeVar($fiscais_sub);
        foreach ($fiscais_sub as $i) {
            //se possui algum email (o email1 é obrigatório) e se ele não está no array
            if(isset($i["email1"]) && !empty($i["email1"]) 
                && !in_array($i["email1"], $emails_enviar)){
                $emails_enviar[]=$i["email1"];
                $emails_nomes[]=$i["nome"];
            }
            //se existe o email2 e se é diferente do email1
            if(isset($i["email2"]) && !empty($i["email2"]) 
                && !in_array($i["email2"], $emails_enviar)){
                $emails_enviar[]=$i["email2"];
                $emails_nomes[]=$i["nome"];
            }            
        }
    }

    if($c["dtprazo"]>=date("Ymd")){
        $txt_expirou="prestes a expirar";
    }else{
        $txt_expirou="atrasado";
    }

    //para cada envio de email
    for($i=0;$i<sizeof($emails_enviar);$i++){
        
        $destinatario_nome  = $emails_nomes[$i];
        $destinatario_email = $emails_enviar[$i];
        $processo           = "Processo de ".$config_infosprocesso["nometipo"]." nº ".$config_infosprocesso["numero"]." - Prazo da etapa está ".$txt_expirou;
        $mensagem           = "O ".APP_TITLE." informa que uma etapa sob sua responsabilidade possui um prazo que está <strong>".$txt_expirou."</strong>, por isso solicitamos que acesse o sistema e efetue os passos necessários para a conclusão da etapa. Veja detalhes abaixo:
                                <hr><span style=\"color:#25526b\"><a  style=\"color:#0000FF;\" target=\"_blank\" href=\"".APP_URL."/control/index_doc.php?p=".$config_infosprocesso["idprocesso"]."\">Processo de ".$config_infosprocesso["nometipo"]." nº ".$config_infosprocesso["numero"]."</a>.
                                <br>Instituição: ".$config_infosprocesso["nome_instituicao"].".
                                <br>Etapa atual: ".$config_infosprocesso["nomeetapa"].".
                                <br>Prazo da etapa: ".exibeData($c["dtprazo"]).".
                                <br><a  style=\"color:#0000FF;\" target=\"_blank\" href=\"".APP_URL."/control/index_doc.php?p=".$config_infosprocesso["idprocesso"]."\">Clique aqui para acessar o processo</a>.</span>
                                <hr><br>Se você, destinatário deste e-mail, não tiver mais relação com a instituição por favor desconsidere esta mensagem e apague-a em seguida, obrigado.";

        // aqui fica o Titulo do seu email
        $subject = $processo." - enviado às ".date("H:i:s")." de ".date("d-m-Y");
        $mailheaders = "From: ".APP_TITLE."<".EMAIL_SENDER.">\nContent-Type: text/html\n";
        $html=' <html>
                <head>
                <title>'.APP_TITLE.' - '.$processo.'</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
                </head>
                <body text="#000000" link="#333333" vlink="#000000" alink="#000000" leftmargin="0" topmargin="0">
                
                <p>'.saudacao($destinatario_nome).'!</p>
                <p>'.$mensagem.'</p>
                <p><strong>Este é um e-mail automático, por favor não responda.</strong></p>
                <p>Atenciosamente,<br>'.APP_TITLE.'.</p>
                </body>
                </html>';

        // Resolve diferença de quebra de linhas, entre Linux/Windows

        $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());

        $head = "MIME-Version: 1.0" . $snap;
        $head.= "From: ".APP_TITLE."<".EMAIL_SENDER.">" . $snap;
        $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
        $head.= "Return-Path: ".EMAIL_SENDER." \r\n"; // return-path

        $mailheaders = $head;

        $mail->ClearAllRecipients();
        $mail->addAddress($destinatario_email);     // Add a recipient
        $mail->isHTML(true);          // Set email format to HTML
        $mail->Subject = utf8_decode($subject);
        $mail->Body = utf8_decode($html);
        $enviou=$mail->send();

        //se enviou o e-mail:
        if($enviou){
            $obs_log="Rotina Automática".APP_LINE_BREAK."E-mail enviado para avisar que o prazo da etapa atual expirou ou está prestes a expirar".APP_LINE_BREAK."Para: $destinatario_nome ".APP_LINE_BREAK."E-mail: $destinatario_email";
            /* REGISTRA AÇÃO DE ENVIO DE E-MAIL NO LOG */
            //carrega DAO
            require_once("../dao/HistoricoDAO.php");
            //carrega Model
            require_once("../model/Historico.php");
            $Historico = new Historico();
            $Historico->setAcao(LOG_EMAIL);
            $Historico->setProcesso($config_infosprocesso["idprocesso"]);
            $Historico->setObs(sqlTrataString($obs_log));
            $HistoricoDAO = new HistoricoDAO();
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                //se cair aqui é pq não inseriu o log
                echo "erro1390 - por favor informe o administrador do sistema, obrigado.";
                exit();
            }            
        //se não enviou o e-mail
        }

    }

//IF do ajax mail usado em add_doc
}elseif(isset($tipoajaxmail) && !empty($tipoajaxmail) && ($tipoajaxmail=="add_doc" || $tipoajaxmail=="index_doc")){


    //carrega DAOS
    require_once("../dao/UsuarioDAO.php");

    //variável para controlar os erros de envio de e-mail
    $erros_email = array();
    //variável para controlar os e-mails enviados com sucesso
    $envios_email = array();
    //variável de controle para armazenar todos e-mails a ser enviados
    $emails_enviar=array();
    //variável para evitar 2 e-mails para o mesmo destinatario
    $emails_destinatarios=array();
    //variavel para controlar o índice
    $indice_email=-1;

    //para cada pedido de e-mail faça
    foreach ($emails as $e) {        

        //verifica para quem vai o email
        //email para instituição do processo
        if($e["tipoemail"]==1){

            $UsuarioDAO = new UsuarioDAO();
            //pega infos do usuário (se ele não tiver sido removido e nem expirado o tempo de acesso) 
            $dados=$UsuarioDAO->getOne($processousuarioid,true);
            
            //se encontrar o usuário, ele não tiver sido removido e nem expirado o tempo de acesso
            if(sizeof($dados)>0){
                //echo "testando email[".$dados["email1"]."] e email2[".$dados["email2"]."]<br>";
                if(isset($dados["email1"]) && !empty($dados["email1"]) && $dados["email1"]!=$dados["email2"] && strlen($dados["email1"])>7){
                    //verifica se o destinatário já tem um e-mail configurado, se não tiver e-mail entra na lista
                    if(!in_array($dados["email1"], $emails_destinatarios)){
                        $indice_email++;
                        $emails_enviar[$indice_email]["email"] = $dados["email1"];
                        $emails_enviar[$indice_email]["nome"] = $dados["nome"];
                        $emails_enviar[$indice_email]["msg"] = $e["mensagem"];
                        $emails_enviar[$indice_email]["id"] = $processousuarioid;
                        $emails_destinatarios[]=$dados["email1"];
                    }                    
                }
                if(isset($dados["email2"]) && !empty($dados["email2"]) && strlen($dados["email2"])>7){
                    //verifica se o destinatário já tem um e-mail configurado, se não tiver e-mail entra na lista
                    if(!in_array($dados["email2"], $emails_destinatarios)){
                        $indice_email++;
                        $emails_enviar[$indice_email]["email"] = $dados["email2"];
                        $emails_enviar[$indice_email]["nome"] = $dados["nome"];
                        $emails_enviar[$indice_email]["msg"] = $e["mensagem"];
                        $emails_enviar[$indice_email]["id"] = $processousuarioid;
                        $emails_destinatarios[]=$dados["email2"];
                    }
                }
            }
            

        //email para perfil específico
        }elseif($e["tipoemail"]==2 && $e["idperfil"]!=NULL){

            //variavel $resp será usada p/ armazenar os responsáveis, se for o caso
            $resp=false;
            //Caso haja e-mail para o perfil "Membro da Comissão de Ética", verifica se há responsáveis pelo processo, caso SIM, o e-mail vai para os responsáveis
            if($e["idperfil"]==PERFIL_IDRESPONSAVEL){
                require_once("../dao/ResponsavelDAO.php");
                $AjaxResponsavelDAO = new ResponsavelDAO();
                //$resp armazenará todos os responsáveis pelo processo
                $resp = $AjaxResponsavelDAO->getAllFrom($idprocesso);
                //se não achar RESP, manda para todos da Comissão de Ética
                if(!$resp || sizeof($resp)<=0){
                    $UsuarioDAO = new UsuarioDAO();
                    $dados=$UsuarioDAO->getAll(PERFIL_IDCOMISSAOETICA);
                    $resp=$dados;
                }
            }

            //verifica se $RESP é != false e com tamanho >= 1, se for, usa ele como dados, do contrário pega os usuários do perfil
            if($resp!==false && sizeof($resp)>0){
                $dados=$resp;
            }else{
                $UsuarioDAO = new UsuarioDAO();
                //pega todos usuários do perfil específico (se ele não tiver sido removido e nem expirado o tempo de acesso) 
                $dados=$UsuarioDAO->getAll($e["idperfil"]);
            }

            //se encontrar usuários, ele não tiver sido removido e nem expirado o tempo de acesso
            if(sizeof($dados)>0){
                foreach ($dados as $u) {
                    if(isset($u["email1"]) && !empty($u["email1"]) && $u["email1"]!=$u["email2"] && strlen($u["email1"])>7){
                        //verifica se o destinatário já tem um e-mail configurado, se não tiver entra na lista
                        if(!in_array($u["email1"], $emails_destinatarios)){
                            $indice_email++;
                            $emails_enviar[$indice_email]["email"] = $u["email1"];
                            $emails_enviar[$indice_email]["nome"] = $u["nomeusuario"];
                            $emails_enviar[$indice_email]["msg"] = $e["mensagem"];
                            $emails_enviar[$indice_email]["id"] = $u["idusuario"];
                            $emails_destinatarios[]=$u["email1"];
                        }
                    }
                    if(isset($u["email2"]) && !empty($u["email2"]) && strlen($u["email2"])>7){
                        //verifica se o destinatário já tem um e-mail configurado, se não tiver entra na lista
                        if(!in_array($u["email2"], $emails_destinatarios)){
                            $indice_email++;
                            $emails_enviar[$indice_email]["email"] = $u["email2"];
                            $emails_enviar[$indice_email]["nome"] = $u["nomeusuario"];
                            $emails_enviar[$indice_email]["msg"] = $e["mensagem"];
                            $emails_enviar[$indice_email]["id"] = $u["idusuario"];
                            $emails_destinatarios[]=$u["email2"];
                        }
                    }
                }                
            }            

        //email para usuário específico
        }elseif($e["tipoemail"]==3 && $e["idusuario"]!=NULL){

            $UsuarioDAO = new UsuarioDAO();
            //pega infos do usuário, caso ele ainda esteja ativo no sistema (flag = 1 e dtexpiracao >= data atual)
            $dados=$UsuarioDAO->getOne($e["idusuario"],true);
            //se encontrar usuários, ele não tiver sido removido e nem expirado o tempo de acesso
            if($dados!==false){
                if(isset($dados["email1"]) && !empty($dados["email1"]) && $dados["email1"]!=$dados["email2"] && strlen($dados["email1"])>7){
                    //verifica se o destinatário já tem um e-mail configurado, se não tiver e-mail entra na lista
                    if(!in_array($dados["email1"], $emails_destinatarios)){
                        $indice_email++;
                        $emails_enviar[$indice_email]["email"] = $dados["email1"];
                        $emails_enviar[$indice_email]["nome"] = $dados["nome"];
                        $emails_enviar[$indice_email]["msg"] = $e["mensagem"];
                        $emails_enviar[$indice_email]["id"] = $e["idusuario"];
                        $emails_destinatarios[]=$dados["email1"];
                    }
                }
                if(isset($dados["email2"]) && !empty($dados["email2"]) && strlen($dados["email2"])>7){
                    //verifica se o destinatário já tem um e-mail configurado, se não tiver e-mail entra na lista
                    if(!in_array($dados["email2"], $emails_destinatarios)){
                        $indice_email++;
                        $emails_enviar[$indice_email]["email"] = $dados["email2"];
                        $emails_enviar[$indice_email]["nome"] = $dados["nome"];
                        $emails_enviar[$indice_email]["msg"] = $e["mensagem"];
                        $emails_enviar[$indice_email]["id"] = $e["idusuario"];
                        $emails_destinatarios[]=$dados["email2"];
                    }
                }
            }

        }
    }

    //envia e-mails
    if(sizeof($emails_enviar)>0){
        for($i=0;$i<sizeof($emails_enviar);$i++){

            $subject = $nomeprocesso.' - atualizado às '.date("H:i:s").' de '.date("d-m-Y");
                       
            $mailheaders = "From: ".APP_TITLE."<".EMAIL_SENDER.">\nContent-Type: text/html\n";
            $html=' <html>
                    <head>
                    <title>'.APP_TITLE.' - '.$nomeprocesso.'</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
                    </head>
                    <body text="#000000" link="#0000FF" vlink="#0000FF" alink="#0000FF" leftmargin="0" topmargin="0">
                    <p>'.saudacao($emails_enviar[$i]["nome"]).'!</p>
                    <p>O <a target="_blank" href="'.$linkprocesso.'"  style="color:#0000FF;">'.$nomeprocesso.'</a> foi atualizado pela(o) '.$nomeperfilusuario.'.';
                    
                    //verifica se houve alguma aprovação/não aprovação e exibe isso e a mensagem da justificativa (se tiver)
                    if(isset($aprova) && $aprova!=ETAPA_NAO_APROVAVEL && $aprova!=ETAPA_AGUARDANDO_APROVACAO){
                        //se for capa do processo (INDEX_DOC)
                        if($tipoajaxmail=="index_doc"){
                            //se for uma etapa em que não há escolha de datas
                            if(isset($infosetapa) && $infosetapa["escolhedata"]!=ETAPA_ESCOLHE_DATA){

                                $html.='</p><p>O(s) documento(s) enviado(s) na etapa anterior ';
                                if($aprova==ETAPA_APROVADA){
                                    $html.='foram <strong>aprovado(s)</strong>!';
                                }else{
                                    $html.='<strong>não foram aprovado(s)</strong>!';
                                }
                                //se houver justificativa, anexa a mesma ao e-mail
                                if(isset($justificativa) && !empty($justificativa)){
                                    $html.='<br><strong>Justificativa:</strong> '.$justificativa;
                                }
                                
                            //se for uma etapa que exista escolha de datas
                            }else{
                                if($aprova==ETAPA_APROVADA){
                                    $html.='</p><p>Uma data foi escolhida para a posse!';
                                }else{
                                    $html.='</p><p>Nenhuma data foi aceita para a posse</strong>!';
                                    //se houver justificativa, anexa a mesma ao e-mail
                                    if(isset($justificativa) && !empty($justificativa)){
                                        $html.='<br><strong>Data/hora sugerida pela instituição:</strong> '.$justificativa;
                                    }
                                }
                            }
                            
                            
                        //se não for, é o ADD DOC
                        }else{
                            $html.='</p><p>O(s) documento(s) enviado(s) na etapa anterior ';
                            if($aprova==ETAPA_APROVADA){
                                $html.='foram <strong>aprovado(s)</strong>!';
                            }else{
                                $html.='<strong>não foram aprovado(s)</strong>!';
                            }
                            //se houver justificativa, anexa a mesma ao e-mail
                            if(isset($justificativa) && !empty($justificativa)){
                                $html.='<br><strong>Justificativa:</strong> '.$justificativa;
                            }
                        }
                        
                    }

                    
            $html.='</p><p>'.$emails_enviar[$i]["msg"].'</p>

                    <p><a target="_blank" href="'.$linkprocesso.'"  style="color:#0000FF;">Clique aqui para acessar o '.$nomeprocesso.'</a>.
                    <br><strong>Este é um e-mail automático, por favor não responda.</strong></p>';
            $html.='<p>Atenciosamente,<br>'.APP_TITLE.'.</p>
                    </body>
                    </html>';

            // Resolve diferença de quebra de linhas, entre Linux/Windows
            $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());
            $head = "MIME-Version: 1.0" . $snap;
            $head.= "From: ".APP_TITLE."<".EMAIL_SENDER.">" . $snap;
            $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
            $head.= "Return-Path: ".EMAIL_SENDER." \r\n"; // return-path
            $mailheaders = $head;

            $mail->ClearAllRecipients();
            $mail->addAddress($emails_enviar[$i]["email"]);     // Add a recipient
            $mail->isHTML(true);          // Set email format to HTML
            $mail->Subject = utf8_decode($subject);
            $mail->Body = utf8_decode($html);                    
            $enviou=$mail->send();

            //se não enviou o e-mail armazena dados para depois exibir um geral com todos erros
            if(!$enviou){
                //$erros_email[]["email"]=$emails_enviar[$i]["email"]." (".$emails_enviar[$i]["nome"]." - ID ".$emails_enviar[$i]["id"].")";
                $erros_email[]["email"]=$emails_enviar[$i]["email"]." (".$emails_enviar[$i]["nome"].")";
            }else{
                $envios_email[]["email"]=$emails_enviar[$i]["email"]." (".$emails_enviar[$i]["nome"].")";
            }
        }
    }

    //Salva no histórico os e-mails que não puderam ser enviados
    if(sizeof($erros_email)>0){
        $Historico = new Historico();
        $Historico->setAcao(LOG_EMAIL_INVALIDO);
        $Historico->setProcesso($idprocesso);
        $Historico->setDocumento(0);
        $Historico->setObs("Etapa ".$infosetapa["nome"].APP_LINE_BREAK."Emails não puderam ser enviados para ".sizeof($erros_email)." destinatário(s): ".exibeTextoComVirgulaOuE($erros_email,"email"));
        $HistoricoDAO = new HistoricoDAO();
        $inseriuLog=$HistoricoDAO->insert($Historico);
    }
    //Salva no histórico os e-mails que foram enviados
    if(sizeof($envios_email)>0){
        $Historico = new Historico();
        $Historico->setAcao(LOG_EMAIL);
        $Historico->setProcesso($idprocesso);
        $Historico->setDocumento(0);
        $Historico->setObs("Etapa ".$infosetapa["nome"].APP_LINE_BREAK."Emails enviados para ".sizeof($envios_email)." destinatário(s): ".exibeTextoComVirgulaOuE($envios_email,"email"));
        $HistoricoDAO = new HistoricoDAO();
        $inseriuLog=$HistoricoDAO->insert($Historico);
    }


//IF do ajax mail usado na capa do processo para alertar usuários sobre determinado processo
}elseif(isset($_POST["idprocesso"]) && !empty($_POST["idprocesso"]) && isset($_POST["destinatario"]) && !empty($_POST["destinatario"])){

    $idprocesso=$_POST["idprocesso"];
    $iddestinatario=$_POST["destinatario"];

    //conecta no banco e instacia uma conexão com o Registry
    require_once("../conexao.php");
    require_once("../model/Registry.php");
    // Armazenar essa instância (conexão) no Registry - conecta uma só vez
    $registry = Registry::getInstance();
    $registry->set('Connection', $myBD);
    //carrega DAOS
    require_once("../dao/UsuarioDAO.php");
    $UsuarioDAO = new UsuarioDAO();
    $destinatario = $UsuarioDAO->getOne($iddestinatario);
    require_once("../dao/ProcessoDAO.php");
    require_once("../model/Processo.php");
    $ProcessoDAO = new ProcessoDAO();
    $Processo    = new Processo();
    $Processo->setId($idprocesso);
    $dados = $ProcessoDAO->getInfosCapa($Processo);

    $remetente_nome     = $_SESSION['USUARIO']['nome'];
    if(isset($_SESSION['USUARIO']['email1']) && !empty($_SESSION['USUARIO']['email1'])){
        $remetente_email    = $_SESSION['USUARIO']['email1'];
    }else{
        $remetente_email    = $_SESSION['USUARIO']['email2'];
    }
    $remetente_perfil   = $_SESSION['USUARIO']['nomeperfil'];  

    $destinatario_nome  = $destinatario["nome"];
    if(isset($destinatario['email1']) && !empty($destinatario['email1'])){
        $destinatario_email    = $destinatario['email1'];
    }else{
        $destinatario_email    = $destinatario['email2'];
    }

    $link               = APP_URL.'/control/index_doc.php?p='.$idprocesso;    
    $processo             = "Processo de ".$dados["nometipo"]." da Comissão de Ética nº ".$dados["numero"];
    if(!empty($_POST["mensagem"]) && $_POST["mensagem"]!=NULL){
        $mensagem=nl2br($_POST["mensagem"]);
    }else{
        $mensagem=NULL;
    }

    // aqui fica o Titulo do seu email
    $subject = $processo.' - enviado às '.date("H:i:s").' de '.date("d-m-Y");
    $mailheaders = "From: $remetente_nome<$remetente_email>\nContent-Type: text/html\n";
    $html=' <html>
            <head>
            <title>'.APP_TITLE.' - '.$processo.'</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
            </head>
            <body text="#000000" link="#333333" vlink="#000000" alink="#000000" leftmargin="0" topmargin="0">
            <p><strong>Este é um e-mail automático, por favor não responda.</strong></p>
            <p>'.saudacao($destinatario_nome).'!</p>
            <p><em>'.$remetente_nome.'</em> ('.$remetente_perfil.', email: <em>'.$remetente_email.'</em>), solicitou seu acesso ao <strong>'.$processo.'</strong>.</p>';
        if($mensagem!=NULL){
            $html.='<p>Além de solicitar seu acesso, <em>'.$remetente_nome.'</em> te enviou a seguinte mensagem:</p>';
            $html.="<p style=\"border:1px solid #286090;padding:10px;\"><strong>$mensagem</strong></p>";
        }
    $html.='<p><a href="'.$link.'" style="color:#0000FF;">Clique aqui para acessar o '.$processo.'.</a></p>
            <p>Atenciosamente,<br>'.APP_TITLE.'.</p>
            </body>
            </html>';

    // Resolve diferença de quebra de linhas entre Linux/Windows
    $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());

    $head = "MIME-Version: 1.0" . $snap;
    $head.= "From: $remetente_nome<$remetente_email>" . $snap;
    $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
    $head.= "Return-Path: $remetente_email \r\n"; // return-path

    $mailheaders = $head;

    $mail->addAddress($destinatario_email);     // Add a recipient
    $mail->isHTML(true);          // Set email format to HTML
    $mail->Subject = utf8_decode($subject);
    $mail->Body = utf8_decode($html);

    $enviou=$mail->send();

    //se enviou o e-mail:
    if($enviou){
        echo "<div class=\"well\"><strong>E-mail enviado para <em class=\"text-success\">$destinatario_nome ($destinatario_email)</em> com sucesso!</strong></div>";

        $obs_log="Para: $destinatario_nome ".APP_LINE_BREAK."E-mail: $destinatario_email ".APP_LINE_BREAK;
        if($mensagem!=NULL){
            $obs_log.="- Mensagem opcional enviada:".APP_LINE_BREAK.$mensagem;
        }else{
            $obs_log.="Nenhuma mensagem opcional foi enviada";
        }
        /* REGISTRA AÇÃO DE ENVIO DE E-MAIL NO LOG */
        //carrega DAO
        require_once("../dao/HistoricoDAO.php");
        //carrega Model
        require_once("../model/Historico.php");
        $Historico = new Historico();
        $Historico->setAcao(LOG_EMAIL);
        $Historico->setProcesso($idprocesso);
        $Historico->setObs(sqlTrataString($obs_log));
        $HistoricoDAO = new HistoricoDAO();
        $inseriuLog=$HistoricoDAO->insert($Historico);
        if(!$inseriuLog){
            //se cair aqui é pq não inseriu o log
            echo "erro - não salvou log";
            exit();
        }else{
            //se cair aqui é pq DEU TUDO CERTO! \o/
            exit();
        }
        
    //se não enviou o e-mail
    }else{
        echo "erro - não enviou e-mail";
        exit();
    }

/* //verifica se o e-mail atual é no caso do usuário que trocou de senha, se for manda a senha dele por e-mail
}elseif(isset($_POST["tipo"]) && $_POST["tipo"]=="change_password" && isset($_POST["s"]) && isset($_POST["l"]) && isset($_POST["e"])){ */

}elseif(isset($_POST["tipo"]) && $_POST["tipo"]=="change_password" && isset($_POST["first"]) && isset($_POST["last"]) && isset($_POST["l"]) && isset($_POST["e"])){    

        $login=$_POST["l"];
        $first=validaLiteral(html_entity_decode(urldecode($_POST["first"])),2);
        $last=validaLiteral(html_entity_decode(urldecode($_POST["last"])),2);
        $email=sqlTrataString(validaLiteral(strtolower($_POST["e"]),USUARIO_EMAIL_SIZE));
        //conecta no banco e instancia uma conexão com o Registry
        require_once("../conexao.php");
        require_once("../model/Registry.php");
        // Armazenar essa instância (conexão) no Registry - conecta uma só vez
        $registry = Registry::getInstance();
        $registry->set('Connection', $myBD);

        // aqui fica o Titulo do seu formulario de contato
        $subject = 'Lembrete de Usuário e Senha - '.APP_TITLE.' - enviado às '.date("H:i:s").' de '.date("d-m-Y");
        $mailheaders = "From: ".APP_TITLE."<".EMAIL_SENDER.">\nContent-Type: text/html\n";
        $html=' <html>
                <head>
                <title>Lembrete de Usuário e Senha - '.APP_TITLE.'</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
                </head>
                <body text="#000000" link="#333333" vlink="#000000" alink="#000000" leftmargin="0" topmargin="0">
                <p><strong>Este é um e-mail automático, por favor não responda.</strong></p>
                <p>'.saudacao($_SESSION["USUARIO"]["nome"]).'!</p>
                <p>Você trocou com sucesso suas credenciais no '.APP_TITLE.', veja abaixo seus novos dados de acesso:</p>
                <p>
                    <table border="1">
                    <tr bgcolor="#efefef">
                    <td><strong>Usuário</strong></td>
                    <td><strong>Primeiros caracteres da senha</strong></td>
                    <td><strong>Últimos caracteres</strong></td>
                    </tr>
                    <tr bgcolor="#f2f9ff">
                    <td>'.$login.'</td>
                    <td>'.htmlentities($first).'</td>
                    <td>'.htmlentities($last).'</td>
                    </tr>
                    </table>
                </p>
                <p><a href="'.APP_URL.'" target="_blank" style="color:#0000FF;">Clique aqui para acessar o '.APP_TITLE.'</a>.</p>';
        $html.='<p>Atenciosamente,<br>'.APP_TITLE.'.</p>
                </body>
                </html>';

        // Resolvo a diferença de quebra de linhas, entre o Linux e o Windows
        $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());

        $head = "MIME-Version: 1.0" . $snap;
        $head.= "From: ".APP_TITLE."<".EMAIL_SENDER.">" . $snap;
        //$head.= "From: $remetente_nome<$remetente_email>" . $snap;
        $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
        $head.= "Return-Path: ".EMAIL_SENDER." \r\n"; // return-path

        $mailheaders = $head;

        $mail->addAddress($email);     // Add a recipient
        $mail->isHTML(true);          // Set email format to HTML
        $mail->Subject = utf8_decode($subject);
        $mail->Body = utf8_decode($html);

        $enviou=$mail->send();

        //seta que o login do usuário é este novo, pois já é se chegar aqui
        $_SESSION["USUARIO"]["login"]=$login;

        //se enviou o e-mail:
        if($enviou){

            $obs_log="Senha enviada para o e-mail: $email ";
            /* REGISTRA AÇÃO DE ENVIO DE E-MAIL NO LOG */
            //carrega DAO
            require_once("../dao/HistoricoDAO.php");
            //carrega Model
            require_once("../model/Historico.php");
            $Historico = new Historico();
            $Historico->setAcao(LOG_UPDATE_SENHA);
            $Historico->setProcesso(0);
            $Historico->setDocumento(0);
            $Historico->setObs(sqlTrataString($obs_log));
            $HistoricoDAO = new HistoricoDAO();
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                
                //se cair aqui é pq não inseriu o log
                echo "erro - não salvou log";
                exit();

            }else{

                //se cair aqui é pq DEU TUDO CERTO! \o/
                echo "sucesso";
                exit();

            }
            
        //se não enviou o e-mail
        }else{

            //echo "erro - não enviou e-mail <br>".$mail->ErrorInfo;
            $obs_log="A senha <strong>não</strong> pôde ser enviada para o e-mail $email - Mensagem de erro: ".$mail->ErrorInfo;
            /* REGISTRA AÇÃO DE ENVIO DE E-MAIL NO LOG */
            //carrega DAO
            require_once("../dao/HistoricoDAO.php");
            //carrega Model
            require_once("../model/Historico.php");
            $Historico = new Historico();
            $Historico->setAcao(LOG_UPDATE_SENHA);
            $Historico->setProcesso(0);
            $Historico->setDocumento(0);
            $Historico->setObs(sqlTrataString($obs_log));
            $HistoricoDAO = new HistoricoDAO();
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                
                //se cair aqui é pq não inseriu o log
                echo "erro - não salvou log";
                exit();

            }else{

                //se cair aqui é pq ao menos inseriu no log mas mesmo assim deu erro de envio de email
                echo "erro - não enviou e-mail";
                exit();

            }

        }

//se não enviou as informações necessárias quando clicar em enviar e-mail cai aqui
}else{

    //acesso negado
    enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou foram dados inválidos");
    echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
    exit();

}
?>