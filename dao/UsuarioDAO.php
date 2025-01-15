<?php

class UsuarioDAO {
 
    private $conn;
 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
	
	/* Função para retornar infos do usuário que agora vai estar logado */
	public function getLogin(Usuario $usuario) {		
		$result=false;		
		
        $this->conn->beginTransaction(); 
        try {			
            $query = $this->conn->prepare(
            'SELECT u.idusuario, u.idperfil,u.login, u.email1, u.email2, u.nome, u.dtexpiracao, u.trocousenha,  p.nome as nomeperfil, p.flag as perfilflag, u.flag as usuarioflag 
            FROM usuario u
            INNER JOIN perfil p ON p.idperfil=u.idperfil
             WHERE u.login=? && u.senha=?'
            ); 
			$query->bindValue(1, $usuario->getLogin(), PDO::PARAM_STR);
			$query->bindValue(2, $usuario->getSenha(), PDO::PARAM_STR);
            $query->execute(); 
 			$result = $query->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
		return $result;
    }

    /* Função para verificar se o usuário que esqueceu o login é ele mesmo */
    public function checkLogin(Usuario $Usuario, Processo $Processo) {        
        $result=false;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
            'SELECT u.nome,u.idusuario,p.idprocesso
            FROM usuario u
            INNER JOIN processo p ON p.idusuario=u.idusuario
            WHERE u.idperfil = ? 
            && ( u.email1 = ? || u.email2 = ? )
            && u.idmunicipio = ?
            && u.nome_instituicao = ?
            && u.dtcriacao >= ? 
            && u.dtcriacao <= ?
            && p.idprocessotipo = ?
            && (u.dtexpiracao = 0 || u.dtexpiracao >= '.date("Ymd").')
            && u.flag = '.APP_FLAG_ACTIVE.'
            && p.flag = '.APP_FLAG_ACTIVE.'
            ORDER BY u.idusuario DESC
            LIMIT 0,1');
            $query->bindValue(1, $Usuario->getPerfil(), PDO::PARAM_INT);
            $query->bindValue(2, $Usuario->getEmail1(), PDO::PARAM_STR);
            $query->bindValue(3, $Usuario->getEmail1(), PDO::PARAM_STR);
            $query->bindValue(4, $Usuario->getMunicipio(), PDO::PARAM_INT);
            $query->bindValue(5, $Usuario->getNomeInstituicao(), PDO::PARAM_STR);
            $query->bindValue(6, $Usuario->getDtCriacao(), PDO::PARAM_INT);
            $query->bindValue(7, $Usuario->getDtExpiracao(), PDO::PARAM_INT);
            $query->bindValue(8, $Processo->getProcessoTipo(), PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para verificar se o usuário que esqueceu o login é ele mesmo */
    public function checkLogin2(Usuario $Usuario, Processo $Processo) {        
        $result=false;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
            'SELECT u.login,u.email1,u.email2,u.numlosts,u.nome,
            p.numero,
            u.nome_instituicao,tp.nome as nometipoprocesso
            FROM usuario u
            INNER JOIN processo p ON p.idusuario=u.idusuario
            INNER JOIN processotipo tp ON tp.idprocessotipo=p.idprocessotipo
            WHERE u.idusuario = ? 
            && p.idprocesso = ?
            && u.flag = '.APP_FLAG_ACTIVE.'
            && p.flag = '.APP_FLAG_ACTIVE.'
            ORDER BY u.idusuario DESC
            LIMIT 0,1');
            $query->bindValue(1, $Usuario->getId(), PDO::PARAM_INT);
            $query->bindValue(2, $Processo->getId(), PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }
	
    /* Função para retornar infos de um usuário */
    public function getOne($idusuario,$naoContaExpirados=false) {
        $result=false;      

        if($naoContaExpirados){
            $expirados_sql = ' and (u.dtexpiracao = 0 OR u.dtexpiracao >= '.date("Ymd").')';
        }else{
            $expirados_sql = "";
        }
        
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT pf.nome as nomeperfil,u.idperfil, u.login, u.nome,u.email1,u.email2,u.dtexpiracao,u.idsubsecao,u.idmunicipio,u.nome_instituicao,u.celular,u.telefone, u.tentativas_num FROM usuario u
                INNER JOIN perfil pf ON u.idperfil=pf.idperfil
                WHERE u.idusuario=? and u.flag=1 '.$expirados_sql
            );
            $query->bindValue(1, $idusuario, PDO::PARAM_STR);
            $query->execute(); 
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

/* Função para retornar permissões de um usuario */
    public function getFuncoes($idusuario) {        
        $result=false;      
        
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT idfuncao FROM perfil_funcao WHERE idperfil = (SELECT idperfil FROM usuario WHERE idusuario=?)'
            ); 
            $query->bindValue(1, $idusuario, PDO::PARAM_INT);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

        }
        catch(Exception $e) {
            //$this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       

        return $result;
        
    }
    
    /* Função para retornar os processos que o usuário pode ver */
    public function getProcessos($idusuario) {
        $result=false;
        try {
            //'SELECT idprocesso FROM padusuario WHERE idusuario = ?'
            $query = $this->conn->prepare(
                'SELECT idprocesso FROM usuario_processo WHERE idusuario = ?'
            );
            $query->bindValue(1, $idusuario, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar os processos que o usuário do perfil Membro CE pode ver */
    public function getProcessosMembroCE($idusuario) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
                'SELECT idprocesso FROM responsavel WHERE idusuario = ?'
            ); 
            $query->bindValue(1, $idusuario, PDO::PARAM_INT);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }

    public function registerLoginAttempt(Usuario $usuario){
        $deletou=true;
        $this->conn->beginTransaction();
        try {
            $data = date("Ymd");
            $query = $this->conn->prepare(
                'SELECT tentativas_num,tentativas_time FROM usuario WHERE login=? AND flag=1'
            );
            $query->bindValue(1, $usuario->getLogin(), PDO::PARAM_STR);
            $query->execute(); 
            $tentativas = $query->fetch(PDO::FETCH_ASSOC);
            
            if($tentativas["tentativas_time"] !== date("Ymd")){
                $sql='UPDATE usuario SET tentativas_time = '.$data.', tentativas_num = 1 WHERE login=? AND flag=1';
            }else{
                $sql='UPDATE usuario SET tentativas_num = (tentativas_num+1) WHERE login=? AND flag=1';
            }

            $query2 = $this->conn->prepare($sql);
            $query2->bindValue(1, $usuario->getLogin(), PDO::PARAM_STR);
            $query2->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou;
    }

    public function registerLogin($idusuario){
        $deletou=true;
        $this->conn->beginTransaction();
        try {
            $sql='UPDATE usuario SET lastlogin = NOW() WHERE idusuario=? AND flag=1';
            $query2 = $this->conn->prepare($sql);
            $query2->bindValue(1, $idusuario, PDO::PARAM_INT);
            $query2->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou;
    }

    public function blockUser(Usuario $usuario){
        $deletou=true;
        try {
            $query = $this->conn->prepare(
                'SELECT tentativas_num FROM usuario WHERE login=? AND flag=1'
            );
            $query->bindValue(1, $usuario->getLogin(), PDO::PARAM_STR);
            $query->execute(); 
            $tentativas = $query->fetch(PDO::FETCH_ASSOC);
            if($tentativas["tentativas_num"]>=APP_MAX_LOGIN_ATTEMPTS){
                $data = date("Ymd");
                $query = $this->conn->prepare(
                    'UPDATE usuario SET tentativas_time = '.$data.' WHERE login=? AND flag=1'
                );
                $query->bindValue(1, $usuario->getLogin(), PDO::PARAM_INT);
                $query->execute();
                $this->conn->commit();
            }
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou;
    }

    public function isBlocked(Usuario $usuario){
        $bloqueado=true;
        try {
            $query = $this->conn->prepare(
                'SELECT tentativas_num, tentativas_time FROM usuario WHERE login=? AND flag=1'
            );
            $query->bindValue(1, $usuario->getLogin(), PDO::PARAM_STR);
            $query->execute(); 
            $tentativas = $query->fetch(PDO::FETCH_ASSOC);
            $data=date("Ymd");
            if($tentativas["tentativas_num"]>=APP_MAX_LOGIN_ATTEMPTS && $tentativas["tentativas_time"]==$data){
                $bloqueado=$tentativas;
            }else{
                $bloqueado=false;
            }
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $bloqueado=true;
        }       
        return $bloqueado;
    }

    /* Função para retornar todos usuários */
    public function getAll($idPerfilEspecifico=false,$expirados=false,$idPerfilExcessao=false,$ordenacao=false) {
        $result=false;      
        $sqlPerfilEspecifico='';
        $sqlPerfilExcessao='';
        $sqlOrdenacao='u.nome';
        //IF abaixo permite que, se o primeiro parametro for passado para o getAll, seja tratado como um id de perfil específico para exibição
        if($idPerfilEspecifico){
            $sqlPerfilEspecifico=' and p.idperfil = '.$idPerfilEspecifico;
        }
        $sqlExpirados=' and (u.dtexpiracao = 0 OR u.dtexpiracao >= '.date("Ymd").')';
        //IF abaixo permite que, se o 2º parametro for passado seja possível exibir os usuários com acesso expirado
        if($expirados){
            $sqlExpirados='';
        }
        //IF abaixo permite que, se o 3º parametro for passado para o getAll, todos usuários exceto de um perfil específico sejam exibidos
        if($idPerfilExcessao){
            $sqlPerfilExcessao=' and p.idperfil <> '.$idPerfilExcessao;
        }
        //IF abaixo permite que, se o 4º parametro for passado exibe os registros ordenados pelo nome da instituição
        if($ordenacao){
            $sqlOrdenacao='u.nome_instituicao';
        }

        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   SELECT u.idusuario, u.nome as nomeusuario,u.nome_instituicao, p.nome as nomeperfil,p.idperfil, u.email1, u.email2,u.login FROM usuario u
                    INNER JOIN perfil p ON p.idperfil=u.idperfil
                    WHERE u.flag = 1 '.$sqlPerfilEspecifico.' '.$sqlPerfilExcessao.' '.$sqlExpirados.
                '   ORDER BY '.$sqlOrdenacao.' ASC');
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar todos usuários visíveis para a instituição */
    public function getAllForEntity($idprocesso=false) {
        $result=false;      
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   SELECT u.idusuario, u.nome as nomeusuario,u.nome_instituicao, p.nome as nomeperfil,p.idperfil, u.email1, u.email2,u.login FROM usuario u
                    INNER JOIN perfil p ON p.idperfil=u.idperfil
                    WHERE u.flag = 1 and u.idusuario IN (SELECT idusuario FROM responsavel where idprocesso = '.$idprocesso.')
                    ORDER BY u.nome ASC');
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar todos usuários fiscais que são "elegíveis" na região */
    public function getAllFiscaisProcesso($idprocesso=false) {
        $result=false;
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   
                SELECT u.nome as nomeusuario,u.idusuario FROM usuario u
                WHERE u.idperfil = '.PERFIL_IDFISCALIZACAO.' AND u.flag='.APP_FLAG_ACTIVE.' AND u.idsubsecao IN (
                    SELECT u2.idsubsecao FROM usuario u2
                    INNER JOIN processo p ON u2.idusuario=p.idusuario
                    WHERE p.idprocesso = '.$idprocesso.'
                )
                ORDER BY u.nome ASC');
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar os usuários que podem ser responsáveis por um (ou mais) processo(s) */
    public function getAllResponsaveis() {
        $result=false;
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   SELECT u.idusuario,u.nome FROM usuario u
                    WHERE u.flag = 1 AND (u.idperfil = '.PERFIL_IDCOMISSAOETICA.' OR u.idperfil = '.PERFIL_IDRESPONSAVEL.')
                    ORDER BY u.nome ASC');
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* index da página inicial de usuários */
    public function index($paginacao_inicio,$busca=NULL,$order=NULL, $ascdesc=NULL) {
        $result=false;
        $orderby="u.nome ASC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="nome"){
                $orderby="u.nome ".$ascdesc;
            }
            if($order=="perfil"){
                $orderby="nomeperfil ".$ascdesc;
            }
            if($order=="login"){
                $orderby="u.login ".$ascdesc;
            }
            if($order=="dtcriacao"){
                $orderby="u.dtcriacao ".$ascdesc;            
            }
            if($order=="dtacesso"){
                $orderby="dtacesso ".$ascdesc;            
            }
            if($order=="nome_instituicao"){
                $orderby="nome_instituicao ".$ascdesc;            
            }
        }
        try { 

            //variaveis de controle para buscas
            $busca_where='';
            //verifica se EXISTE UMA BUSCA
            if($busca!==NULL){

                //adiciona clausula a SQL
                $busca_where='AND u.nome like \'%'.$busca.'%\' ';

            //fim if EXISTE BUSCA
            }   

            $sql='  SELECT u.idusuario,u.nome as nomeuser,u.nome_instituicao,p.nome as nomeperfil, u.login,u.lastlogin as dtacesso,u.dtcriacao, p.flag as perfilflag
                    FROM usuario u 
                    INNER JOIN perfil p ON u.idperfil=p.idperfil
                    WHERE u.flag='.APP_FLAG_ACTIVE.' '.$busca_where.' 
                    GROUP BY u.idusuario
                    ORDER BY '.$orderby;

            //se foi setado para exibir todos os registros da consulta atual, define-se:
            if(isset($_GET["showAllRecords"]) && $_GET["showAllRecords"]==true){
                $query = $this->conn->query($sql.' LIMIT 0,9999999999');                 
            }else{
                //consulta normal (paginada)
                $query = $this->conn->query($sql.' LIMIT '.$paginacao_inicio.','.APP_MAX_PAGE_ROWS);                 
            }

            //consulta sem paginacao (visando pegar o total de registros)
            $query_total=$this->conn->query($sql); 

            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
            $result[0]["paginacao_numlinhas"] = $query_total->rowCount();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       

        return $result;
        
    }

    /* Função para verificar se o login do usuário a ser criado não possui duplicidade  */
    public function isDuplicated(Usuario $user) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT idusuario FROM usuario WHERE login = ? AND flag='.APP_FLAG_ACTIVE
            ); 
            $query->bindValue(1, $user->getLogin(), PDO::PARAM_STR);
            $query->execute(); 
            $duplicado = $query->fetch(PDO::FETCH_ASSOC);

        }
        catch(Exception $e) {
            $duplicado=true;
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       

        return $duplicado;
        
    }
    //método de inserção de usuários com parâmetro opcional para a coluna "trocouSenha"
    public function insert(Usuario $usuario, $trocouSenha=NULL){
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $trocousenha_campo="";
            $trocousenha_valor="";
            if($trocouSenha!=NULL){
                $trocousenha_campo=",trocousenha";
                $trocousenha_valor=",".$trocouSenha;
            }
            $query = $this->conn->prepare(
                'INSERT INTO usuario 
                (idusuario,idperfil,idmunicipio,idsubsecao,nome,email1,email2,
                    celular,telefone,nome_instituicao,
                    login,senha,dtcriacao,dtexpiracao,lastlogin,flag'.$trocousenha_campo.') 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?'.$trocousenha_valor.')'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//idusuario é AUTO_INCREMENT, então NULL
            $query->bindValue(2, $usuario->getPerfil(), PDO::PARAM_INT);
            $query->bindValue(3, $usuario->getMunicipio(), PDO::PARAM_INT);
            $query->bindValue(4, $usuario->getSubsecao(), PDO::PARAM_INT);
            $query->bindValue(5, $usuario->getNome(), PDO::PARAM_STR);
            $query->bindValue(6, $usuario->getEmail1(), PDO::PARAM_STR);
            $query->bindValue(7, $usuario->getEmail2(), PDO::PARAM_STR);
            $query->bindValue(8, $usuario->getCelular(), PDO::PARAM_STR);
            $query->bindValue(9, $usuario->getTelefone(), PDO::PARAM_STR);
            $query->bindValue(10, $usuario->getNomeInstituicao(), PDO::PARAM_STR);
            $query->bindValue(11, strtolower($usuario->getLogin()), PDO::PARAM_STR);
            $query->bindValue(12, $usuario->getSenha(), PDO::PARAM_INT);
            $query->bindValue(13, date("Ymd"), PDO::PARAM_INT);
            $query->bindValue(14, $usuario->getDtExpiracao(), PDO::PARAM_INT);
            $query->bindValue(15, NULL, PDO::PARAM_STR);
            $query->bindValue(16, APP_FLAG_ACTIVE, PDO::PARAM_INT);//flag ATIVO

            $query->execute();
            $inseriu = $this->conn->lastInsertId();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $inseriu=false;
        }       
        return $inseriu;    
    }

    
    /* Insere na tabela usuario_processo os processos que o usuário pode visualizar */
    public function insertLimitacaoProcesso($idprocesso,$idusuario){
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO usuario_processo (idusuario_processo,idprocesso,idusuario) VALUES (NULL, ?, ?)'
            );
            
            $query->bindValue(1, $idprocesso, PDO::PARAM_INT);//idprocesso
            $query->bindValue(2, $idusuario, PDO::PARAM_INT);//idusuario

            $query->execute();
            $inseriu = $this->conn->lastInsertId();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $inseriu=false;
        }       
        return $inseriu;    
    }

    /* Remove da tabela usuario_processo todos os processos que o usuário estava atrelado */
    public function deleteLimitacaoProcesso(Usuario $usuario){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'DELETE FROM usuario_processo WHERE idusuario=?'
            );
            $query->bindValue(1, $usuario->getId(), PDO::PARAM_INT);
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou; 
    }

    public function insertExpiracao(Usuario $usuario){
        $deletou=true;
        $this->conn->beginTransaction(); 
        $senha = $usuario->getSenha();
        if(!empty($senha) && $senha!==false){
            $trocaSenha=',senha=?';
        }else{
            $trocaSenha='';
        }

        try {
            $query = $this->conn->prepare(
                'UPDATE usuario SET dtexpiracao=?
                WHERE idusuario=? AND flag=1 '
            );
            $query->bindValue(1, $usuario->getDtExpiracao(), PDO::PARAM_INT);
            $query->bindValue(2, $usuario->getId(), PDO::PARAM_INT);

            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou;
    }

    public function update(Usuario $usuario){
        $deletou=true;
        $this->conn->beginTransaction(); 
        $senha = $usuario->getSenha();
        if(!empty($senha) && $senha!==false){
            $trocaSenha=',senha=?';
        }else{
            $trocaSenha='';
        }

        try {
            $query = $this->conn->prepare(
                'UPDATE usuario SET 
                nome=?,email1=?,email2=?,login=?,dtexpiracao=?,idperfil=?, 
                idsubsecao=?,idmunicipio=?,celular=?,telefone=?
                ,nome_instituicao=?, tentativas_num=? '.$trocaSenha.'
                WHERE idusuario=? AND flag=1 '
            );
            $query->bindValue(1, $usuario->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $usuario->getEmail1(), PDO::PARAM_STR);
            $query->bindValue(3, $usuario->getEmail2(), PDO::PARAM_STR);
            $query->bindValue(4, strtolower($usuario->getLogin()), PDO::PARAM_STR);
            $query->bindValue(5, $usuario->getDtExpiracao(), PDO::PARAM_INT);
            $query->bindValue(6, $usuario->getPerfil(), PDO::PARAM_INT);
            $query->bindValue(7, $usuario->getSubsecao(), PDO::PARAM_INT);    
            $query->bindValue(8, $usuario->getMunicipio(), PDO::PARAM_INT);    
            $query->bindValue(9, $usuario->getCelular(), PDO::PARAM_INT);    
            $query->bindValue(10, $usuario->getTelefone(), PDO::PARAM_INT);    
            $query->bindValue(11, $usuario->getNomeInstituicao(), PDO::PARAM_INT);  
            $query->bindValue(12, $usuario->getTentativasNum(), PDO::PARAM_INT);  
            //se houver troca de senha, atualiza o valor da senha
            if($trocaSenha!=''){
                $query->bindValue(13, $usuario->getSenha(), PDO::PARAM_STR);
                $query->bindValue(14, $usuario->getId(), PDO::PARAM_STR);
            }else{
                $query->bindValue(13, $usuario->getId(), PDO::PARAM_STR);
            }

            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou;
    }

    /* Função para atualização da senha do usuário quando ele se esqueceu */
    public function updateSenhaPerdida(Usuario $Usuario){
        $atualizou=true;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                '   UPDATE usuario SET senha=?
                    WHERE idusuario=? AND flag=1 
                    AND (dtexpiracao = 0 OR dtexpiracao >= '.date("Ymd").')');

            $query->bindValue(1, $Usuario->getSenha(), PDO::PARAM_STR);
            $query->bindValue(2, $Usuario->getId(), PDO::PARAM_INT);    
            
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $atualizou=false;
        }       
        return $atualizou;
    }


    /* Função para acrescentar +1 na tentativa de recuperação da senha do usuário */
    public function updateTentativaSenhaPerdida(Usuario $Usuario){

        $atualizou=true;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                '   UPDATE usuario SET numlosts=(numlosts+1)
                    WHERE idusuario=? AND flag=1 
                    AND (dtexpiracao = 0 OR dtexpiracao >= '.date("Ymd").')');

            $query->bindValue(1, $Usuario->getId(), PDO::PARAM_INT);    
            
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $atualizou=false;
        }       
        return $atualizou;
    }



    /* Função para verificar se o usuário alterou o login para um já existente*/
    public function isDuplicatedEdit(Usuario $Usuario) {
        $duplicado=false;
        try {           
            $query = $this->conn->prepare(
                'SELECT idusuario FROM usuario WHERE login = ? AND idusuario <> ? AND flag=1'
            ); 
            $query->bindValue(1, $Usuario->getLogin(), PDO::PARAM_STR);
            $query->bindValue(2, $Usuario->getId(), PDO::PARAM_STR);
            $query->execute(); 
            $duplicado = $query->fetch(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            $duplicado=true;
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $duplicado;
    }

    public function delete(Usuario $usuario){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            //não deleta o usuário, seta a flag = 2 e redefine o login para "LOGIN_DEL_2015-05-21 12:30:20"
            $query = $this->conn->prepare(
                'UPDATE usuario SET flag = 2, login = concat(login,\'_DEL_\',CURRENT_TIMESTAMP) WHERE idusuario=? AND flag=1'
            );
            $query->bindValue(1, $usuario->getId(), PDO::PARAM_INT);
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $deletou=false;
        }       
        return $deletou;
    }

    public function updateMyUser(Usuario $usuario){
        $atualizou=true;
        $this->conn->beginTransaction(); 
        $senha = $usuario->getSenha();
        if(!empty($senha) && $senha!==false){
            $trocaSenha=',senha=?';
        }else{
            $trocaSenha='';
        }
        try {
            $query = $this->conn->prepare(
                'UPDATE usuario SET email1=?,email2=?,nome_instituicao=?,celular=?,telefone=?,nome=?'.$trocaSenha.' WHERE idusuario=? AND flag=1'
            );
            $query->bindValue(1, $usuario->getEmail1(), PDO::PARAM_STR);
            $query->bindValue(2, $usuario->getEmail2(), PDO::PARAM_STR);
            $query->bindValue(3, $usuario->getNomeInstituicao(), PDO::PARAM_STR);
            $query->bindValue(4, $usuario->getCelular(), PDO::PARAM_STR);
            $query->bindValue(5, $usuario->getTelefone(), PDO::PARAM_STR);
            $query->bindValue(6, $usuario->getNome(), PDO::PARAM_STR);
            if($trocaSenha!=''){
                $query->bindValue(7, $usuario->getSenha(), PDO::PARAM_STR);
                $query->bindValue(8, $usuario->getId(), PDO::PARAM_INT);    
            }else{
                $query->bindValue(7, $usuario->getId(), PDO::PARAM_INT);
            }
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $atualizou=false;
        }       
        return $atualizou;
    }

    /* Função para retornar o último acesso do usuário ao sistema antes do atual */
    public function getUltimosAcessos($idusuario) {
        $result=false;        
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT dthistorico
                FROM historico 
                WHERE idusuario=? AND idacao='.LOG_LOGIN_USER.'
                ORDER BY dthistorico DESC
                LIMIT 1,5'
            );
            $query->bindValue(1, $idusuario, PDO::PARAM_STR);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar os últimos documentos visualizados por um usuário específico */
    public function getUltimosVisualizados($idusuario) {
        $result=false;
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT DISTINCT (h.obs), h.dthistorico,p.numero,p.idprocesso, dt.nome, d.flag
                FROM historico h
                INNER JOIN processo p ON h.idprocesso=p.idprocesso
                INNER JOIN documento d ON h.iddocumento=d.iddocumento
                INNER JOIN documentotipo dt ON d.iddocumentotipo=dt.iddocumentotipo
                WHERE h.idusuario=? AND h.idacao='.LOG_VIEW_DOC.'
                ORDER BY h.dthistorico DESC
                LIMIT 0,5'
            );
            $query->bindValue(1, $idusuario, PDO::PARAM_STR);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar os últimos documentos inseridos por um usuário específico */
    public function getUltimosInseridos($idusuario) {
        $result=false;
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT h.iddocumento, h.dthistorico,p.numero,p.idprocesso, dt.nome, d.flag
                FROM historico h
                INNER JOIN processo p ON h.idprocesso=p.idprocesso
                INNER JOIN documento d ON h.iddocumento=d.iddocumento
                INNER JOIN documentotipo dt ON d.iddocumentotipo=dt.iddocumentotipo
                WHERE h.idusuario=? AND h.idacao='.LOG_ADD_DOC.'
                ORDER BY h.dthistorico DESC
                LIMIT 0,5'
            );
            $query->bindValue(1, $idusuario, PDO::PARAM_STR);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar os últimos documentos removidos por um usuário específico */
    public function getUltimosRemovidos($idusuario) {
        $result=false;      
        
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT h.iddocumento,h.dthistorico,p.numero,p.idprocesso, dt.nome
                FROM historico h
                INNER JOIN processo p ON h.idprocesso=p.idprocesso
                INNER JOIN documento d ON h.iddocumento=d.iddocumento
                INNER JOIN documentotipo dt ON d.iddocumentotipo=dt.iddocumentotipo
                WHERE h.idusuario=? AND h.idacao='.LOG_DEL_DOC.'
                ORDER BY h.dthistorico DESC
                LIMIT 0,5'
            );
            $query->bindValue(1, $idusuario, PDO::PARAM_STR);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para retornar todos usuários ativos e com acesso não expirado de um setor */
    public function getAllFromSetor(Usuario $Usuario) {        
        $result=false;      
        
        //$this->conn->beginTransaction(); 
        try {           
            $sql = 'SELECT u.idusuario,u.nome,u.email,s.nome as nomesetor FROM usuario u
                    INNER JOIN setor s ON s.idsetor=u.idsetor
                    WHERE u.idsetor = ? AND u.flag = 1 AND (u.dtexpiracao = 0 OR u.dtexpiracao >= '.date("Ymd").')';
            $query = $this->conn->prepare($sql); 
            $query->bindValue(1, $Usuario->getSetor(), PDO::PARAM_INT);
            $query->execute(); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            //$this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       

        return $result;
        
    }

    //função para alterar login e senha do usuário (change_password)
    public function trocaLoginSenha(Usuario $Usuario){
        $atualizou=true;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                'UPDATE usuario SET senha=?,login=?,trocousenha=1
                WHERE idusuario=? AND flag=1 '
            );
            $query->bindValue(1, $Usuario->getSenha(), PDO::PARAM_STR);
            $query->bindValue(2, $Usuario->getLogin(), PDO::PARAM_STR);
            $query->bindValue(3, $Usuario->getId(), PDO::PARAM_INT);    
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if($GLOBALS["spaf_show_sql_errors"]){
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
            }
            $atualizou=false;
        }       
        return $atualizou;
    }


}
?>