<?php

class HistoricoDAO {
 
    private $conn;
 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }

    public function insert(Historico $log){
        $inseriu=true;
        	$this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                'INSERT INTO historico 
                (idhistorico,dthistorico,idusuario,idacao,idprocesso,iddocumento,obs,ip) 
                VALUES 
                (NULL, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?)'
            );
            if(isset($_SESSION["USUARIO"]["idusuario"]) && !empty($_SESSION["USUARIO"]["idusuario"])){
                $usuario=$_SESSION["USUARIO"]["idusuario"];
            }elseif(!empty($log->getUsuario())){
                $usuario=$log->getUsuario();
            }else{
                $usuario=0;
            }
            $query->bindValue(1, $usuario, PDO::PARAM_INT);
            $query->bindValue(2, $log->getAcao(), PDO::PARAM_INT);
            $query->bindValue(3, $log->getProcesso(), PDO::PARAM_INT);
            $query->bindValue(4, $log->getDocumento(), PDO::PARAM_INT);
            $query->bindValue(5, $log->getObs(), PDO::PARAM_STR);
            $query->bindValue(6, getIpUsuario(), PDO::PARAM_STR);

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
		
    //cuidar com a declaração na view:
    //if(isset($_GET["order"]) && ($_GET["order"]=="dtlog" || ...
    //lembre-se de colocar todos os order abaixo lá também, se não a ordenação não funcionará.
    public function getHistorico($paginacao_inicio,$tipo,$id=NULL,$dtde=NULL,$dtate=NULL,$order=NULL, $ascdesc=NULL){
        $result=false;
        $orderby="dtlog DESC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="dtlog"){
                $orderby="dtlog ".$ascdesc;
            }
            if($order=="idusuario"){
                $orderby="u.".$order.' '.$ascdesc;
            }
            if($order=="idacao"){
                $orderby="a.nome ".$ascdesc;
            }
            if($order=="iddocumento"){
                $orderby="c.nome ".$ascdesc;            
            }
            if($order=="obs"){
                $orderby="l.obs ".$ascdesc;            
            }
            if($order=="ip"){
                $orderby="l.ip ".$ascdesc;            
            }
            if($order=="idprocesso"){
                $orderby="l.idprocesso ".$ascdesc;            
            }
        }
        $clausula_where="";
        if($tipo=="usuario"){
            $clausula_where=" l.idusuario = ? ";
        }elseif($tipo=="processo"){
            $clausula_where=" l.idprocesso = ? ";
        }else{
            $tipo="geral";
            $clausula_where=" l.idusuario <> 0 ";
        }
        $sqldata="";
        if($dtde!=NULL){
            $sqldata.=' AND l.dthistorico >= \''.transformaDataTimestamp($dtde).' 00:00:00\'';
        }
        if($dtate!=NULL){
            $sqldata.=' AND l.dthistorico <= \''.transformaDataTimestamp($dtate).' 23:59:59\'';
        }
        $sql="  SELECT  l.idprocesso, l.dthistorico as dtlog,l.obs,l.ip, c.nome as documento, 
                        p.numero, pt.nome as nometipo, u.nome as nomeusuario, a.nome as nomeacao FROM historico l
                INNER JOIN usuario u ON u.idusuario = l.idusuario
                INNER JOIN acao a ON a.idacao = l.idacao
                LEFT JOIN documento d ON d.iddocumento = l.iddocumento
                LEFT JOIN documentotipo c ON c.iddocumentotipo = d.iddocumentotipo
                LEFT JOIN processo p ON p.idprocesso = l.idprocesso
                LEFT JOIN processotipo pt ON p.idprocessotipo = pt.idprocessotipo
                WHERE ".$clausula_where.$sqldata." ORDER BY ".$orderby.", idhistorico DESC";     

        try {

            //se foi setado para exibir todos os registros da consulta atual, define-se:
            if(isset($_GET["showAllRecords"]) && $_GET["showAllRecords"]==true){
                $query = $this->conn->prepare($sql.' LIMIT 0,9999999999');                 
            }else{
                //consulta normal (paginada)
                $query = $this->conn->prepare($sql.' LIMIT '.$paginacao_inicio.','.APP_MAX_PAGE_ROWS);                 
            }
            //consulta sem paginacao (visando pegar o total de registros)
            $query_total=$this->conn->prepare($sql);

            if($tipo!="geral"){
                $query->bindValue(1, $id, PDO::PARAM_INT);//id
                $query_total->bindValue(1, $id, PDO::PARAM_INT);//id
            }

            $query->execute();
            $query_total->execute();
            
            //consulta sem paginacao (visando pegar o total de registros)
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
 
}
?>