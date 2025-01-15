<?php 
class SubsecaoDAO {
 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
 
    public function getAll() {
		$result=false;
        try {			
            $query = $this->conn->query(
            '   SELECT s.idsubsecao, s.nome, m.nome as cidade FROM subsecao s 
                INNER JOIN municipio m ON m.idmunicipio=s.idmunicipio
                WHERE s.flag=1 
                ORDER BY nome ASC'
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

    //listagem de registros
    public function index($paginacao_inicio, $order=NULL, $ascdesc=NULL) {
        $result=false;
        $orderby="nome ASC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="nome"){
                $orderby="nome ".$ascdesc;
            }
            if($order=="nomecidade"){
                $orderby="nomecidade ".$ascdesc;
            }
        }
        
        $sql='  SELECT s.idsubsecao, s.nome, m.nome as nomecidade FROM subsecao s
                INNER JOIN municipio m ON m.idmunicipio=s.idmunicipio 
                WHERE s.flag=1 ORDER BY '.$orderby;

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

    // Função que retorna infos de um subsecao
    public function getOne(Subsecao $Subsecao) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
            '   SELECT s.idsubsecao,s.nome,s.idmunicipio,m.nome as nomecidade FROM subsecao s
                INNER JOIN municipio m ON m.idmunicipio=s.idmunicipio
                WHERE idsubsecao = ? AND flag=1'
            ); 
            $query->bindValue(1, $Subsecao->getId(), PDO::PARAM_INT);
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

    // Função que retorna id da subseção de um municipio
    public function getSubsecaoFromMunicipio($idmunicipio) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
            '   SELECT idsubsecao FROM subsecao_municipio
                WHERE idmunicipio = ?'
            ); 
            $query->bindValue(1, $idmunicipio, PDO::PARAM_INT);
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

    // Função para verificar se o nome a ser criado não possui duplicidade
    public function isDuplicated(Subsecao $Subsecao) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT idsubsecao FROM subsecao WHERE nome = ? AND flag=1'
            ); 
            $query->bindValue(1, $Subsecao->getNome(), PDO::PARAM_STR);
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
    public function isDuplicatedEdit(Subsecao $Subsecao) {
        $duplicado=false;
        //$this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'SELECT idsubsecao FROM subsecao WHERE nome = ? AND idsubsecao <> ? AND flag=1'
            ); 
            $query->bindValue(1, $Subsecao->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $Subsecao->getId(), PDO::PARAM_STR);
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
    public function insert(Subsecao $Subsecao) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
            'INSERT INTO subsecao (idsubsecao,nome,idmunicipio,flag) VALUES (?, ?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//id é AUTO_INCREMENT, então NULL
            $query->bindValue(2, $Subsecao->getNome(), PDO::PARAM_STR);
            $query->bindValue(3, $Subsecao->getMunicipio(), PDO::PARAM_STR);
            $query->bindValue(4, APP_FLAG_ACTIVE, PDO::PARAM_INT);//flag ativo
                        
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
    public function update(Subsecao $Subsecao){
        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE subsecao SET nome=?, idmunicipio=? WHERE idsubsecao=? AND flag=1 '
            );
            $query->bindValue(1, $Subsecao->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $Subsecao->getMunicipio(), PDO::PARAM_STR);
            $query->bindValue(3, $Subsecao->getId(), PDO::PARAM_INT);
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
    public function delete(Subsecao $Subsecao){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'UPDATE subsecao SET flag = 2 WHERE idsubsecao=? AND flag=1'
            );
            $query->bindValue(1, $Subsecao->getId(), PDO::PARAM_INT);
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

    //insere registro Juridiscao (subsecao_municipio)
    public function insertJurisdicao(Subsecao $Subsecao) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
            'INSERT INTO subsecao_municipio (idsubsecao,idmunicipio) VALUES (?, ?)'
            );
            
            $query->bindValue(1, $Subsecao->getId(), PDO::PARAM_INT);
            $query->bindValue(2, $Subsecao->getMunicipio(), PDO::PARAM_INT);
                        
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
    }

    //deleta municipios relacionados a determinada subseção
    public function deleteJurisdicoes(Subsecao $Subsecao){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'DELETE FROM subsecao_municipio WHERE idsubsecao=?'
            );
            $query->bindValue(1, $Subsecao->getId(), PDO::PARAM_INT);
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