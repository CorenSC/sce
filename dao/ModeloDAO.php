<?php
class ModeloDAO {
 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
 
    //função que retorna todos os Modelos
    public function getAll() {
		$result=false;
        try {			
            $query = $this->conn->query('SELECT * FROM modelo WHERE flag=1 ORDER BY nome ASC');
 			$result = $query->fetchAll(PDO::FETCH_ASSOC);			
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }		
		return $result;	
    }

    public function index($paginacao_inicio, $order=NULL, $ascdesc=NULL) {
        $result=false;
        $orderby="NUMERO2 ASC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="dtcriacao"){
                $orderby="dtcriacao ".$ascdesc;
            }
            if($order=="dtatualizacao"){
                $orderby="dtatualizacao ".$ascdesc;
            }
            if($order=="nome"){
                $orderby="NUMERO2 ".$ascdesc.", nome ".$ascdesc;
            }
        }
        
        $sql='  SELECT idmodelo,nome,dtcriacao,dtatualizacao,link, CONVERT(SUBSTRING_INDEX(nome, \'.\', 1), DECIMAL) as NUMERO2 FROM modelo WHERE flag=1 ORDER BY '.$orderby;

        try {      

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
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }

        // Função para verificar se o nome a ser criado não possui duplicidade
    public function isDuplicated(Modelo $Modelo) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT idmodelo FROM modelo WHERE nome = ? AND flag=1'
            ); 
            $query->bindValue(1, $Modelo->getNome(), PDO::PARAM_STR);
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
    // Função para verificar se o nome a ser criado não possui duplicidade
    public function isDuplicatedEdit(Modelo $Modelo) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT idmodelo FROM modelo WHERE nome = ? AND idmodelo <> ? AND flag=1'
            ); 
            $query->bindValue(1, $Modelo->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $Modelo->getId(), PDO::PARAM_STR);
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

    public function insert(Modelo $Modelo) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
            'INSERT INTO modelo (idmodelo,nome,dtcriacao,dtatualizacao,link,flag) VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//id é AUTO_INCREMENT, então NULL
            $query->bindValue(2, $Modelo->getNome(), PDO::PARAM_STR);
            $query->bindValue(3, date("Ymd"), PDO::PARAM_INT);
            $query->bindValue(4, NULL, PDO::PARAM_INT);
            $query->bindValue(5, $Modelo->getLink(), PDO::PARAM_STR);
            $query->bindValue(6, APP_FLAG_ACTIVE, PDO::PARAM_INT);//flag ativo
                        
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

    

    // Função que retorna infos de um Modelo
    public function getOne(Modelo $Modelo) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
            'SELECT idmodelo,nome,link FROM modelo WHERE idmodelo = ? AND flag=1'
            ); 
            $query->bindValue(1, $Modelo->getId(), PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }

    

    
    //atualiza registro
    public function update(Modelo $Modelo){
        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        if($Modelo->getLink()!=NULL){
            $adicional=", link=?";
        }
        try {
            $query = $this->conn->prepare(
                'UPDATE modelo SET nome=?, dtatualizacao=? '.$adicional.' WHERE idmodelo=? AND flag=1 '
            );
            $query->bindValue(1, $Modelo->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, date("Ymd"), PDO::PARAM_STR);
            if($adicional!=""){
                $query->bindValue(3, $Modelo->getLink(), PDO::PARAM_STR);
                $query->bindValue(4, $Modelo->getId(), PDO::PARAM_INT);
            }else{
                $query->bindValue(3, $Modelo->getId(), PDO::PARAM_INT);
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

    //"remove" registro (apenas desabilita o registro, não excluindo-o do banco de dados)
    public function delete(Modelo $Modelo){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'UPDATE modelo SET flag = 2 WHERE idmodelo=? AND flag=1'
            );
            $query->bindValue(1, $Modelo->getId(), PDO::PARAM_INT);
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
}
?>