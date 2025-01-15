<?php 
class MunicipioDAO {
 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
 
    public function getAll() {
		$result=false;
        try {			
            $query = $this->conn->query(
            'SELECT idmunicipio, nome FROM municipio ORDER BY nome ASC'
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

    public function getAllSubsecao($idsubsecao=false) {
        $result=false;
        try {
            //por padrão, retorna municipios que não foram atrelados a uma subsecao
            $sqlSubsecao = " m.idmunicipio NOT IN (SELECT idmunicipio FROM subsecao_municipio)";         
            //caso seja passado um IDSUBSECAO, retorna só municipios já atrelados a tal subsecao
            if($idsubsecao){
                $sqlSubsecao = " m.idmunicipio IN (
                    SELECT idmunicipio FROM subsecao_municipio WHERE idsubsecao = ".$idsubsecao.
                                ")";
            }
            $query = $this->conn->query(
            'SELECT m.idmunicipio, m.nome FROM municipio m
            WHERE '.$sqlSubsecao.' 
            ORDER BY m.nome ASC'
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

    // Função que retorna infos de um registro
    public function getOne($idmunicipio) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
            'SELECT nome FROM municipio WHERE idmunicipio = ?'
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
    
}
?>