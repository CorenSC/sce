<?php 
/*

SCRIPT USADO PARA ATUALIZAR TODAS LOGS COM OBSERVAÇÕES COM LINK ERRADOS. 10/08/2015

class ScriptDAO { 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
 
    public function getAll() {
		$result=false;		
//		$this->conn->beginTransaction(); 
        try {			
            $query = $this->conn->query(
'SELECT `idlog`,`obs` FROM `log` WHERE `idacao` > 4 AND `idacao` < 9 AND `obs` like "%show.php?f=2015_%" ORDER BY idlog DESC'
            ); 
 			$result = $query->fetchAll(PDO::FETCH_ASSOC);			
        }
        catch(Exception $e) {
            //$this->conn->rollback();
			echo $e->getMessage();
        }		
		return $result;		
    }

    public function update($obs,$idlog) {
        $inseriu=false;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
            'UPDATE `log` SET `obs` = ? WHERE `idlog` = ?'
            );
            $query->bindValue(1, $obs, PDO::PARAM_STR);
            $query->bindValue(2, $idlog, PDO::PARAM_INT);
            $query->execute();
            $this->conn->commit();
            $inseriu=true;
        }
        catch(Exception $e) {
            echo $e->getMessage();
            $inseriu=false;
        }
        return $inseriu;
    }
	
 
}
*/

?>







