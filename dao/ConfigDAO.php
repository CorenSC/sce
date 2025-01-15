<?php 
class ConfigDAO { 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }

    // Função que retorna a última data em que foi verificado o DTFIM dos processos
    public function getLastDtFim() {
        $result=false;
        try {
            $query = $this->conn->query('
                SELECT dtatualizacao FROM config WHERE nome = \'dtfim\'
                '); 
            $result = $query->fetch(PDO::FETCH_ASSOC);        
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }    
    // Função que retorna a última data em que foi verificado o DTPRAZO dos processos
    public function getLastDtPrazo() {
        $result=false;
        try {
            $query = $this->conn->query('
                SELECT dtatualizacao FROM config WHERE nome = \'dtprazo\'
                '); 
            $result = $query->fetch(PDO::FETCH_ASSOC);        
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }    

    // Função que atualiza a última data em que foi verificado alguma config
    public function updateLastDt($tipo) {
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'UPDATE config SET  dtatualizacao=? WHERE nome = ?'
            );          
            $query->bindValue(1, date("Ymd"), PDO::PARAM_INT);
            $query->bindValue(2, $tipo, PDO::PARAM_INT);
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