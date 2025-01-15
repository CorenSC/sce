<?php 
class DocumentoTipoDAO { 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }	

    public function getAll($pegaInativos=NULL){
        $result=false;
        $sql_adicional="";
        if($pegaInativos==NULL){
           $sql_adicional=" WHERE flag=".APP_FLAG_ACTIVE." ";
        }
        try {
            $query = $this->conn->prepare(
            '   SELECT iddocumentotipo, nome
                FROM documentotipo
                '.$sql_adicional.'   
                ORDER BY nome ASC'
            ); 
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

    public function getOne(DocumentoTipo $DocumentoTipo){
        $result=false;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
            '   SELECT nome
                FROM documentotipo
                WHERE iddocumentotipo=?'
            ); 
            $query->bindValue(1, $DocumentoTipo->getId(), PDO::PARAM_INT);//id é AUTO_INCREMENT, então NULL
            $query->execute();
            $this->conn->commit();
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

    //listagem de registros
    public function index($paginacao_inicio, $order=NULL, $ascdesc=NULL) {
        $result=false;
        $orderby="nome ASC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="nome"){
                $orderby="nome ".$ascdesc;
            }
        }
        
        $sql='  SELECT iddocumentotipo, nome FROM documentotipo WHERE flag='.APP_FLAG_ACTIVE.' ORDER BY '.$orderby;

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
    public function isDuplicated(DocumentoTipo $DocumentoTipo) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT iddocumentotipo FROM documentotipo WHERE nome = ? AND flag='.APP_FLAG_ACTIVE.''
            ); 
            $query->bindValue(1, $DocumentoTipo->getNome(), PDO::PARAM_STR);
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
    public function isDuplicatedEdit(DocumentoTipo $DocumentoTipo) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT iddocumentotipo FROM documentotipo WHERE nome = ? AND iddocumentotipo <> ? AND flag='.APP_FLAG_ACTIVE.''
            ); 
            $query->bindValue(1, $DocumentoTipo->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $DocumentoTipo->getId(), PDO::PARAM_STR);
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

    //insere registro
    public function insert(DocumentoTipo $DocumentoTipo) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
            'INSERT INTO documentotipo (iddocumentotipo,nome,flag) VALUES (?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//id é AUTO_INCREMENT, então NULL
            $query->bindValue(2, $DocumentoTipo->getNome(), PDO::PARAM_STR);
            $query->bindValue(3, APP_FLAG_ACTIVE, PDO::PARAM_INT);//flag ativo
                        
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

    //atualiza registro
    public function update(DocumentoTipo $DocumentoTipo){
        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE documentotipo SET nome=? WHERE iddocumentotipo=? AND flag=1 '
            );
            $query->bindValue(1, $DocumentoTipo->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $DocumentoTipo->getId(), PDO::PARAM_INT);
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
    public function delete(DocumentoTipo $DocumentoTipo){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'UPDATE documentotipo SET flag = 2 WHERE iddocumentotipo=? AND flag=1'
            );
            $query->bindValue(1, $DocumentoTipo->getId(), PDO::PARAM_INT);
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