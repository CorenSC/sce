<?php
 
class ResponsavelDAO {
 
    private $conn;
 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
 
    public function insert(Responsavel $Responsavel) {
		$inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO responsavel VALUES (?, ?, ?)'
            );			
			$query->bindValue(1, NULL, PDO::PARAM_INT);//id é AUTO_INCREMENT
			$query->bindValue(2, $Responsavel->getProcesso(), PDO::PARAM_INT);
			$query->bindValue(3, $Responsavel->getUsuario(), PDO::PARAM_INT);
            $query->execute();
 			$this->conn->commit();			
        }
        catch(Exception $e) {
            $this->conn->rollback();
			echo $e->getMessage();
			$inseriu=false;
        }		
		return $inseriu;		
    }
	
	public function deleteFrom($idprocesso) {
		$deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'DELETE FROM responsavel WHERE idprocesso=?'
            );
			$query->bindValue(1, $idprocesso, PDO::PARAM_INT);//idprocesso
            $query->execute();
 			$this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
			echo $e->getMessage();
			$deletou=false;
        }
		return $deletou;
    }

    /* Função para retornar todos responsáveis do processo */
    public function getAllFrom($idprocesso) {
        $result=false;
        try {
            $sql = 'SELECT u.idusuario,u.nome as nomeusuario,u.email1,u.email2 FROM usuario u
                    INNER JOIN responsavel r ON r.idusuario=u.idusuario
                    WHERE r.idprocesso = ?
                    ORDER BY nomeusuario ASC';
            $query = $this->conn->prepare($sql); 
            $query->bindValue(1, $idprocesso, PDO::PARAM_INT);
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

    /* Função para retornar todos responsáveis de todos os processos */
    public function getAll() {
        $result=false;
        try {
            $sql = 'SELECT u.idusuario,u.nome as nomeusuario,r.idprocesso FROM usuario u
                    INNER JOIN responsavel r ON r.idusuario=u.idusuario
                    ORDER BY idprocesso,nomeusuario ASC';
            $query = $this->conn->query($sql);
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
}
?>