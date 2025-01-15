<?php
//IF do ajax mail usado em iniciar.php e no_login.php
if(isset($idusuario) && !empty($idusuario) && isset($tipoajaxmail) && !empty($tipoajaxmail) && $tipoajaxmail=="iniciar"
    && ((isset($email1) && !empty($email1)) || (isset($email2) && !empty($email2)) )
    && isset($login) && !empty($login) && isset($senha) && !empty($senha) ){

    require_once("config.php");
    require_once("bin/errors.php");
    require_once("bin/functions.php");
    require_once("bin/email/PHPMailerAutoload.php");
    require_once("bin/email/class.phpmailer.php");
    require_once("bin/email/class.smtp.php");
    
    //variavel de controle de envio de emails
    $emails_enviar=array();
    if(isset($email1) && !empty($email1)){
        $emails_enviar[]["email"]=$email1;
    }
    if(isset($email2) && !empty($email2) && ($email2!==$email1)){
        $emails_enviar[]["email"]=$email2;
    }

    //para cada envio de email
    foreach ($emails_enviar as $e) {

        $head = "";
        $mail = new PHPMailer;
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

        $destinatario_nome  = $nome." (".$nome_instituicao.")";
        $destinatario_email = $e["email"];
        $processo           = "Processo de ".$tipoprocesso["nome"]." da Comissão de Ética nº ".$Processo->getNumero()." iniciado";
        $mensagem           = saudacao($nome).", você acaba de iniciar um novo Processo de ".$tipoprocesso["nome"]." da Comissão de Ética na sua instituição!</p>
                                <p>Por favor não repita este procedimento até que o Processo de ".$tipoprocesso["nome"]." seja finalizado.</p>
                                <p>A próxima etapa do processo é feita pelo Coren/SC porém desde já você pode acessar seu processo utilizando as credenciais abaixo na tela de login:</p>

                                <p>
                                    <table border='1'>
                                    <tr bgcolor='#efefef'>
                                    <td><strong>Seu nome de usuário</strong></td>
                                    <td><strong>Sua senha (modificável)</strong></td>
                                    </tr>
                                    <tr bgcolor='#f2f9ff'>
                                    <td>".$login."</td>
                                    <td>".htmlentities($senha)."</td>
                                    </tr>
                                    </table>
                                </p>
                                <p>Para verificar seu processo no sistema <a href='".APP_URL."' target='_blank' style='color:#0000FF;'>clique aqui</a> e informe suas credenciais.</p>
                                <p> * Em todas as etapas do processo você será notificado no(s) e-mail(s) informados: ".exibeTextoComVirgulaOuE($emails_enviar,"email").".<br>
                                    ** Você pode alterar seu(s) e-mail(s) e senha de acesso clicando no seu nome de usuário (destacado na cor azul) no canto superior direito após efetuar seu login no sistema.</p>";
        // aqui fica o Titulo do seu email
        $subject = $processo;
        $mailheaders = "From: ".APP_TITLE."<".EMAIL_SENDER.">\nContent-Type: text/html\n";
        $html=' <html>
                <head>
                <title>'.APP_TITLE.' - '.$processo.'</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
                </head>
                <body text="#000000" link="#333333" vlink="#000000" alink="#000000" leftmargin="0" topmargin="0">                
                <p><strong>Este é um e-mail automático, por favor não responda.</strong></p>
                <p>'.$mensagem.'</p>
                <p>Atenciosamente,<br>'.APP_TITLE.'.</p>
                </body>
                </html>';

        // Resolvo a diferença de quebra de linhas, entre o Linux e o Windows

        $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());

        $head = "MIME-Version: 1.0" . $snap;
        $head.= "From: ".APP_TITLE."<".EMAIL_SENDER.">" . $snap;
        $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
        $head.= "Return-Path: ".EMAIL_SENDER." \r\n"; // return-path

        $mailheaders = $head;

        $mail->addAddress($destinatario_email);     // Add a recipient
        $mail->isHTML(true);          // Set email format to HTML
        $mail->Subject = utf8_decode($subject);
        $mail->Body = utf8_decode($html);
        $enviou=$mail->send();

        /* REGISTRANDO ENVIO (OU NÃO) DE E-MAIL NO LOG */
            //carrega DAO
            require_once("dao/HistoricoDAO.php");
            $HistoricoDAO = new HistoricoDAO();
            //carrega Model
            require_once("model/Historico.php");
            $Historico = new Historico();

        //se enviou o e-mail:
        if($enviou){
            $obs_log="O novo login/senha foi enviado para: $destinatario_email";
            $Historico->setAcao(LOG_EMAIL);
            $Historico->setUsuario($idusuario);
            $Historico->setProcesso($idprocesso);
            $Historico->setObs(sqlTrataString($obs_log));
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                //se cair aqui é pq não inseriu o log
                enviaMsg("erro","O log de envio do email para $destinatario_email não foi salvo","");
            }
        //se não enviou o e-mail
        }else{
            $obs_log="O novo login/senha não foi enviado para: $destinatario_email";
            $Historico->setAcao(LOG_EMAIL_INVALIDO);
            $Historico->setUsuario($idusuario);
            $Historico->setProcesso($idprocesso);
            $Historico->setObs(sqlTrataString($obs_log));
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                //se cair aqui é pq não inseriu o log
                enviaMsg("erro","O log do NÃO envio de email para $destinatario_email não foi salvo","");
            }
        }
    }

//se não enviou as informações necessárias quando chegar aqui
}elseif(isset($tipoajaxmail) && !empty($tipoajaxmail) && $tipoajaxmail=="no_login"
    && isset($Usuario) && isset($Processo)){

    require_once("config.php");
    require_once("bin/errors.php");
    require_once("bin/functions.php");
    require_once("bin/email/PHPMailerAutoload.php");
    require_once("bin/email/class.phpmailer.php");
    require_once("bin/email/class.smtp.php");
    
    //variavel de controle de envio de emails
    $emails_enviar=array();
    if(!empty($Usuario->getEmail1())){
        $emails_enviar[]["email"]=$Usuario->getEmail1();
    }
    if(!empty($Usuario->getEmail2())){
        $emails_enviar[]["email"]=$Usuario->getEmail2();
    }

    //para cada envio de email
    foreach ($emails_enviar as $e) {

        $head = "";
        $mail = new PHPMailer;
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

        $destinatario_nome  = $Usuario->getNome()." (".$Usuario->getNomeInstituicao().")";
        $destinatario_email = $e["email"];
        $processo           = "Recuperação dos dados de acesso ao ".APP_TITLE."";
        $mensagem           = saudacao($Usuario->getNome()).", você solicitou a recuperação dos dados de acesso ao sistema.</p>
                                <p>Este tipo de tentativa de recuperação/redefinição automática da senha pode ser feita até 5 vezes por instituição, portanto guarde com muito cuidado os novos dados de acesso abaixo:</p>
                                <p><strong>Seu nome de usuário:</strong> ".$Usuario->getLogin()."<br>
                                <strong>Sua senha (modificável):</strong> ".$Usuario->getSenha()."</p>
                                <p>Para acessar seu processo no sistema com as novas credenciais <a href='".APP_URL."' target='_blank' style='color:#0000FF;'>clique aqui</a>.</p>
                                <p> * Em todas as etapas do processo você será notificado no(s) e-mail(s) informados: ".exibeTextoComVirgulaOuE($emails_enviar,"email").".<br>
                                    ** Você pode alterar seu(s) e-mail(s) e senha de acesso clicando no seu nome de usuário (destacado na cor azul) no canto superior direito após efetuar seu login no sistema.</p>";
        // aqui fica o Titulo do seu email
        $subject = $processo;
        $mailheaders = "From: ".APP_TITLE."<".EMAIL_SENDER.">\nContent-Type: text/html\n";
        $html=' <html>
                <head>
                <title>'.APP_TITLE.' - '.$processo.'</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
                </head>
                <body text="#000000" link="#333333" vlink="#000000" alink="#000000" leftmargin="0" topmargin="0">                
                <p><strong>Este é um e-mail automático, por favor não responda.</strong></p>
                <p>'.$mensagem.'</p>
                <p>Atenciosamente,<br>'.APP_TITLE.'.</p>
                </body>
                </html>';

        // Resolvo a diferença de quebra de linhas, entre o Linux e o Windows

        $snap = (PHP_OS == "Linux") ? "\n" : ((PHP_OS == "WINNT") ? "\r\n" : exit());

        $head = "MIME-Version: 1.0" . $snap;
        $head.= "From: ".APP_TITLE."<".EMAIL_SENDER.">" . $snap;
        $head.= "Content-type: text/html; charset=\"utf-8\"" . $snap;
        $head.= "Return-Path: ".EMAIL_SENDER." \r\n"; // return-path

        $mailheaders = $head;

        $mail->addAddress($destinatario_email);     // Add a recipient
        $mail->isHTML(true);          // Set email format to HTML
        $mail->Subject = utf8_decode($subject);
        $mail->Body = utf8_decode($html);
        $enviou=$mail->send();

        /* REGISTRANDO ENVIO (OU NÃO) DE E-MAIL NO LOG */
            //carrega DAO
            require_once("dao/HistoricoDAO.php");
            $HistoricoDAO = new HistoricoDAO();
            //carrega Model
            require_once("model/Historico.php");
            $Historico = new Historico();

        //se enviou o e-mail:
        if($enviou){
            $obs_log="O login/senha restaurado foi enviado para: $destinatario_email";
            $Historico->setAcao(LOG_EMAIL);
            $Historico->setUsuario($idusuario);
            $Historico->setProcesso($idprocesso);
            $Historico->setObs(sqlTrataString($obs_log));
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                //se cair aqui é pq não inseriu o log
                enviaMsg("erro","O log de envio do email para $destinatario_email não foi salvo","");
            }
        //se não enviou o e-mail
        }else{
            $obs_log="O login/senha restaurado não foi enviado para: $destinatario_email";
            $Historico->setAcao(LOG_EMAIL_INVALIDO);
            $Historico->setUsuario($idusuario);
            $Historico->setProcesso($idprocesso);
            $Historico->setObs(sqlTrataString($obs_log));
            $inseriuLog=$HistoricoDAO->insert($Historico);
            if(!$inseriuLog){
                //se cair aqui é pq não inseriu o log
                enviaMsg("erro","O log do NÃO envio de email para $destinatario_email não foi salvo","");
            }
        }
    }

}else{

    //acesso negado
    enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou foram dados inválidos");
    echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\">";
    exit();

} ?>