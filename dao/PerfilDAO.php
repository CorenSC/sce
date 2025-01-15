<?php

class PerfilDAO { 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }

    public function update(Perfil $perfil) {
        $atualizou=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare(
                'UPDATE perfil p SET p.nome=? WHERE p.idperfil=? && p.flag=1  '
            );
            
            $query->bindValue(1, $perfil->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $perfil->getId(), PDO::PARAM_INT);
                        
            $query->execute();
            $this->conn->commit();
            $atualizou=$query->rowCount();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $atualizou;
    }

    public function getAll() {
        $result=false;      
//      $this->conn->beginTransaction(); 
        try {           
            
            $sql="  SELECT p.idperfil, p.nome FROM perfil p WHERE p.flag=1 
                    ORDER BY p.nome ASC";
            $query = $this->conn->query($sql); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    } 

    public function getOne($idperfil) {
        $result=false;      
//      $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   SELECT * FROM perfil WHERE idperfil = ?'
            ); 
            $query->bindValue(1, $idperfil, PDO::PARAM_INT);//idperfil                        
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    } 

    public function getFunctions($idperfil) {
        $result=false;      
//      $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   SELECT * FROM perfil_funcao 
                    WHERE idperfil = ?'
            ); 
            $query->bindValue(1, $idperfil, PDO::PARAM_INT);//idperfil                        
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    } 

    public function getAllFuncoes() {
        $result=false;      
//      $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->query(
'SELECT idfuncao, nome, categoria FROM funcao WHERE flag=1 ORDER BY categoria,nome ASC'
            ); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    } 

    public function insert(Perfil $perfil) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO perfil (idperfil,nome,flag) VALUES (NULL,?, ?)'
            );
            
            $query->bindValue(1, $perfil->getNome(), PDO::PARAM_STR);//nome
            $query->bindValue(2, APP_FLAG_ACTIVE, PDO::PARAM_INT);//flag ATIVO
                        
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

    public function insertFuncao($idperfil,$idfuncao) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO perfil_funcao (idperfil_funcao,idperfil,idfuncao) VALUES (NULL,?, ?)'
            );
            
            $query->bindValue(1, $idperfil, PDO::PARAM_INT);//idfuncao
            $query->bindValue(2, $idfuncao, PDO::PARAM_INT);//idperfil
                        
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

   public function deleteFuncoes($idperfil) {
        if($idperfil>0){
            $inseriu=true;
            $this->conn->beginTransaction(); 
            try {
                $query = $this->conn->prepare(
                    'DELETE FROM perfil_funcao WHERE idperfil = ? '
                );            
                $query->bindValue(1, $idperfil, PDO::PARAM_INT);//idperfil                        
                $query->execute();
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
        }else{
            return false;
        }
    }

    public function delete(Perfil $perfil){      
        $deletou=false;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                'UPDATE perfil p SET p.flag=2 WHERE p.idperfil=? '
            );          
            $query->bindValue(1, $perfil->getId(), PDO::PARAM_INT);
            $query->execute();
            $this->conn->commit();
            $deletou = $query->rowCount();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }        
        return $deletou;
    }

    /* index da página inicial de perfis */
    public function index($order=NULL, $ascdesc=NULL) {
        $result=false;
        $orderby="nomeperfil ASC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="nomeperfil"){
                $orderby=$order.' '.$ascdesc;
            }
        }
        try {           
            $query = $this->conn->query(
                '   SELECT p.idperfil, p.nome as nomeperfil, f.nome as nomefunc FROM perfil p
                    LEFT JOIN perfil_funcao pf ON p.idperfil=pf.idperfil
                    LEFT JOIN funcao f ON f.idfuncao=pf.idfuncao
                    WHERE p.flag='.APP_FLAG_ACTIVE.'
                    ORDER BY '.$orderby.',f.categoria ASC, f.nome ASC'
            );
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }

    /* Função para verificar se o registro a ser criado não possui duplicidade  */
    public function isDuplicated(Perfil $Perfil) {
        $duplicado=false;
        try {           
            $query = $this->conn->prepare(
                'SELECT idperfil FROM perfil WHERE nome = ? AND flag=1'
            ); 
            $query->bindValue(1, $Perfil->getNome(), PDO::PARAM_STR);
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

    /* Função para verificar se o registro não possuirá duplicidade */
    public function isDuplicatedEdit(Perfil $Perfil) {
        $duplicado=false;
        try {           
            $query = $this->conn->prepare(
                'SELECT idperfil FROM perfil WHERE nome = ? AND idperfil <> ? AND flag=1'
            ); 
            $query->bindValue(1, $Perfil->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $Perfil->getId(), PDO::PARAM_STR);
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

    //retorna informações de uma função
    public function getOneFuncao($idfuncao) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
                '   SELECT nome FROM funcao WHERE idfuncao = ?'
            ); 
            $query->bindValue(1, $idfuncao, PDO::PARAM_INT);//idperfil                        
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    } 

}
?>