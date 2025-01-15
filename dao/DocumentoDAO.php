<?php 
class DocumentoDAO { 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }

    //AR's não são listados // função da index_doc
    public function getAllFromProcesso(Processo $Processo, $order = NULL, $ascdesc=NULL){
        $result=false;
        $orderby="d.dtcriacao DESC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="dtenvio"){
                $orderby="d.dtcriacao ".$ascdesc;
            }
            if($order=="nomedocumento"){
                $orderby="nomedocumento ".$ascdesc;
            }
            if($order=="nomeusuario"){
                $orderby="nomeusuario ".$ascdesc;
            }
        }

        try {
            $query = $this->conn->prepare(
            '   SELECT d.link,d.dtatualizacao,d.iddocumento,d.iddocumentotipo,
                d.idusuario,d.idusuarioatualizacao,d.dtcriacao,dt.nome as nomedocumento, 
                u.nome as nomeusuario, d.obs, u2.nome as nomeusuarioatualizacao
                FROM documento d
                INNER JOIN documentotipo dt ON dt.iddocumentotipo=d.iddocumentotipo
                INNER JOIN usuario u ON u.idusuario=d.idusuario
                LEFT JOIN usuario u2 ON u2.idusuario=d.idusuarioatualizacao
                WHERE d.flag='.APP_FLAG_ACTIVE.' AND d.idprocesso = ? 
                ORDER BY '.$orderby
            ); 
            $query->bindValue(1, $Processo->getId(), PDO::PARAM_INT);
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

    public function insert(Documento $Documento) {
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                '   INSERT INTO documento (iddocumento, idprocesso, idusuario, idusuarioatualizacao, iddocumentotipo,
                                link, dtcriacao, dtatualizacao,obs,flag) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)'
            );

            $query->bindValue(1, NULL, PDO::PARAM_INT);//idprocesso é AUTO_INCREMENT, então NULL
            $query->bindValue(2, $Documento->getProcesso(), PDO::PARAM_INT);
            $query->bindValue(3, $Documento->getUsuario(), PDO::PARAM_INT);
            $query->bindValue(4, NULL, PDO::PARAM_INT);
            $query->bindValue(5, $Documento->getDocumentoTipo(), PDO::PARAM_INT);
            $query->bindValue(6, $Documento->getLink(), PDO::PARAM_INT);
            $query->bindValue(7, NULL, PDO::PARAM_STR);
            $query->bindValue(8, $Documento->getObs(), PDO::PARAM_INT);
            $query->bindValue(9, APP_FLAG_ACTIVE, PDO::PARAM_INT);//ATIVO
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


    public function getOne(Documento $Documento) {
        $result=false;
        try {           
            $query = $this->conn->prepare(
            '   SELECT d.*,dt.nome as nomedocumentotipo FROM documento d 
                INNER JOIN documentotipo dt ON dt.iddocumentotipo=d.iddocumentotipo
                WHERE d.iddocumento=?'
            );
            $query->bindValue(1,$Documento->getId(), PDO::PARAM_INT);
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
    public function show(Documento $Documento) {
        $result=false;
        try {
            $query = $this->conn->prepare(
            '   SELECT dt.nome,d.link,d.idprocesso,d.volume,d.dtrecebimento,d.paginabranca,d.carimbonum,d.carimboass,d.carimbooriginal,p.ano,p.numero, u.nome as nomeusuario, u.rubrica, u.assinatura, u.matricula as matriculausuario, u.funcao, u.paginaassinatura, us.matricula, s.nome as nomesetorconformeoriginal, de.dt as dtconformeoriginal, us.nome as usuarioconformeoriginal, us.assinatura as assinaturaconformeoriginal, d2.link as arlink, d2.iddocumento as idar
                FROM documento d 
                INNER JOIN documentotipo dt ON dt.iddocumentotipo=d.iddocumentotipo
                INNER JOIN processo p ON p.idpad=d.idpad
                INNER JOIN usuario u ON u.idusuario=d.idusuario
                LEFT JOIN desentranhamento de ON de.idoriginal=d.iddocumento
                LEFT JOIN usuario us ON us.idusuario=de.idusuario
                LEFT JOIN setor s ON s.idsetor=us.idsetor
                LEFT JOIN documentoar da ON d.iddocumento=da.iddocumento
                LEFT JOIN documento d2 ON d2.iddocumento=da.idar
                WHERE d.iddocumento=?'
            );
            $query->bindValue(1,$Documento->getId(), PDO::PARAM_INT);
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



    public function update(Documento $Documento) {
        $atualizou=false;
        $this->conn->beginTransaction(); 
        try {           
            $query = $this->conn->prepare(
                'UPDATE documento SET 
                dtatualizacao=NOW(), link=?, iddocumentotipo=?, obs=?, idusuarioatualizacao=?
                WHERE iddocumento=?'
            );

            $query->bindValue(1, $Documento->getLink(), PDO::PARAM_STR);
            $query->bindValue(2, $Documento->getDocumentoTipo(), PDO::PARAM_INT);
            $query->bindValue(3, $Documento->getObs(), PDO::PARAM_STR);
            $query->bindValue(4, $Documento->getUsuarioAtualizacao(), PDO::PARAM_STR);
            $query->bindValue(5, $Documento->getId(), PDO::PARAM_INT);

            $query->execute();
            $this->conn->commit();
            $atualizou=true;
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $atualizou;
    }
    public function delete(Documento $doc){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'UPDATE documento SET  
                dtatualizacao=NOW(), flag='.APP_FLAG_INACTIVE.'  WHERE iddocumento=? AND idprocesso=?'
            );
            $query->bindValue(1, $doc->getId(), PDO::PARAM_INT);
            $query->bindValue(2, $doc->getProcesso(), PDO::PARAM_INT);
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

    /* Função para retornar os documentos removidos há um determinado tempo */
    public function getAllExcluidos($dtcriterio) {
        $result=false;
        try {
            $sql='   SELECT d.* FROM documento d
                WHERE d.flag = 2 AND d.dtatualizacao<="'.$dtcriterio.'" AND d.link IS NOT NULL
                ORDER BY d.idprocesso ASC';
            $query = $this->conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    } 

    public function updateLink(Documento $Documento) {
        $atualizou=false;
        $this->conn->beginTransaction();
        try {           
            $query = $this->conn->prepare(
                'UPDATE documento SET link=? WHERE 
                idprocesso=? AND iddocumento=?'
            );          
            $query->bindValue(1, $Documento->getLink(), PDO::PARAM_STR);
            $query->bindValue(2, $Documento->getProcesso(), PDO::PARAM_INT);
            $query->bindValue(3, $Documento->getId(), PDO::PARAM_INT);
                        
            $query->execute();
            $this->conn->commit();
            $atualizou=true;
        }
        catch(Exception $e) {
            $this->conn->rollback();
            echo $e->getMessage();
        }
        return $atualizou;
    }
    	
}
?>