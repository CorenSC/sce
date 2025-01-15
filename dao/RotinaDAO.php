<?php

class RotinaDAO {
 
    private $conn;
 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }

    public function showVersion(){
        $result=false;
        try {           
            $query = $this->conn->prepare('select version() as `version`');
            $query->execute();
            $result = $query->fetch(PDO::FETCH_NUM);
        }
        catch(Exception $e) {
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function showTables($nome_banco){
        $result=false;
        try {
            $query = $this->conn->prepare('SHOW TABLES FROM '.$nome_banco);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_NUM);
        }
        catch(Exception $e) {
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function showTablesStatus(){
        $result=false;
        try {           
            $query = $this->conn->prepare('SHOW TABLE STATUS');
            //$query->bindValue(1, $X, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);        
        }
        catch(Exception $e) {
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function checkTable($t){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('CHECK TABLE `?`');
            $query->bindValue(1, $t, PDO::PARAM_STR);
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function analyzeTable($t){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('ANALYZE TABLE `?`');
            $query->bindValue(1, $t, PDO::PARAM_STR);
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function repairTable($t){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('REPAIR TABLE `?`');
            $query->bindValue(1, $t, PDO::PARAM_STR);
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function optimizeTable($t){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('OPTIMIZE TABLE `?`');
            $query->bindValue(1, $t, PDO::PARAM_STR);
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function flushHosts(){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('FLUSH HOSTS');
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function flushLogs(){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('FLUSH LOGS');
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function flushStatus(){
        $result=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('FLUSH STATUS');
            $query->execute();
            $this->conn->commit();      
        }
        catch(Exception $e) {
            $this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function showCreateTable($t){
        $result=false;
        //$this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('SHOW CREATE TABLE '.$t);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_NUM);
        }
        catch(Exception $e) {
            //$this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function showColumns($t){
        $result=false;
        //$this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('SHOW COLUMNS FROM '.$t);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_NUM);
        }
        catch(Exception $e) {
            //$this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

    public function select($t){
        $result=false;
        //$this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare('SELECT * FROM '.$t);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_NUM);
        }
        catch(Exception $e) {
            //$this->conn->rollback();
                echo '<br>'.get_class($this)." == ".__FUNCTION__.'<hr>'.$e->getMessage();exit();
        }       
        return $result;
    }

}
?>