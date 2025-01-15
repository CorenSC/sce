<?php
class EtapaDAO {
 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
    

    public function index($paginacao_inicio, $order=NULL, $ascdesc=NULL) {
        $result=false;
        $orderby="ordem,fluxo,nome ASC";
        if($order!=NULL && $ascdesc!=NULL){
            if($order=="fluxo"){
                $orderby="fluxo ".$ascdesc;
            }
            if($order=="ordem"){
                $orderby="ordem ".$ascdesc;
            }
            if($order=="nome"){
                $orderby="nome ".$ascdesc;
            }
            if($order=="modo"){
                $orderby="modo ".$ascdesc;
            }
        }
        
        $sql='  SELECT idetapa,ordem,nome,fluxo,modo FROM etapa WHERE flag=1 ORDER BY '.$orderby;

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

    /* Função para retornar todos os registros */
    public function getAll($todosFluxos=NULL) {
        $result=false;
        try {           
            if($todosFluxos!=NULL){
                //só fluxo principal
                $clausula_where=" WHERE fluxo = 0";
            }else{
                $clausula_where=" WHERE idetapa > 0 ";
            }

            $sql = 'SELECT idetapa, nome, descricao, ordem, fluxo, aprova, prazo, modo FROM etapa 
                '.$clausula_where.' AND flag = 1
                ORDER BY ordem ASC, idetapa DESC';

            $query = $this->conn->query($sql); 
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
    public function getOne($id) {
        $result=false;
        try {
            $sql='SELECT e.*,max(e2.ordem-1) as maxordemetapa FROM etapa e, etapa e2
            WHERE e.idetapa = '.$id.' AND e2.flag = '.APP_FLAG_ACTIVE;
            $query = $this->conn->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }

    // Função que retorna a segunda etapa
    public function getSecond($tipoDesejado=NULL) {
        $result=false;
        try {
            if($tipoDesejado!=NULL){
                $tipoDesejado=' and modo='.$tipoDesejado.' and fluxo = '.ETAPA_PRINCIPAL;
            }else{
                $tipoDesejado='';
            }
            $query = $this->conn->query(
            'SELECT e.* FROM etapa e
            WHERE e.flag = '.APP_FLAG_ACTIVE.' and ordem > 1 '.$tipoDesejado.'
            ORDER BY e.ordem ASC
            LIMIT 0,1'
            );
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }
    
    // Função que retorna infos de um registro
    public function getEtapaPerfis(Etapa $Etapa) {
        $result=false;
        try {

            $query = $this->conn->query(
                'SELECT idperfil FROM etapa_perfil
                WHERE idetapa = '.$Etapa->getId()
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

    //pega a última ordem antes da ordem da etapa enviada por parâmetro 
    //(Ex.: Etapa com ordem 4, retorna a ordem 3.2, caso for a ordem maior e inferior a 4)
    public function getLastOrdemBefore(Etapa $Etapa) {
        $result=false;
        try {           
            $sql = 'SELECT ordem FROM etapa WHERE 
            flag = 1 AND ordem < '.$Etapa->getOrdem()
            .' AND idetapa <> '.$Etapa->getId()
            .' ORDER BY ordem DESC LIMIT 0,1';
            $query = $this->conn->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }

    //retorna os tipos de documento informados para uma etapa (SEM DISTINCT)
    public function getTiposDocumentos(Etapa $Etapa) {
        $result=false;
        try {
            $sql = "
            SELECT iddocumentotipo, obrigatorio FROM etapa_documentotipo 
            WHERE idetapa = ".$Etapa->getId();
            $query = $this->conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $result;
    }


    //retorna os emails definidos para uma etapa
    public function getEmails(Etapa $Etapa){
        $result=false;
        try {
            $sql = "SELECT idperfil, idusuario, tipoemail, numero 
                    FROM etapa_email WHERE idetapa = ".$Etapa->getId()."
                    ORDER BY numero ASC";
            $query = $this->conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }


    //retorna a última etapa de um processo
    public function getLastEtapaProcesso(Processo $Processo) {
        $result=false;
        try {
            $sql = "SELECT ep.*,e.fluxo,e.ordem,e.modo FROM etapa_processo ep
            INNER JOIN etapa e ON ep.idetapa = e.idetapa
            WHERE ep.idprocesso = ".$Processo->getId()."
            ORDER BY ep.idetapa_processo DESC
            LIMIT 0,1
            ";
            $query = $this->conn->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }       
        return $result; 
    }

    public function insertEtapaPerfil(Etapa $Etapa){
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO etapa_perfil 
                (idetapa_perfil,idetapa,idperfil) 
                VALUES (NULL, ?, ?)'
            );
            $query->bindValue(1, $Etapa->getId(), PDO::PARAM_INT);
            $query->bindValue(2, $Etapa->getPerfil1(), PDO::PARAM_INT);
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

    public function deleteEtapaPerfil(Etapa $Etapa){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'DELETE FROM etapa_perfil WHERE idetapa=?'
            );
            $query->bindValue(1, $Etapa->getId(), PDO::PARAM_INT);
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

    public function insertEtapaProcesso(Etapa $Etapa){

        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO etapa_processo 
                (idetapa_processo,idetapa,idprocesso,idusuariocriacao,dtcriacao,aprovacao,aprovacaomsg) 
                VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);
            $query->bindValue(2, $Etapa->getId(), PDO::PARAM_STR);
            $query->bindValue(3, $Etapa->getProcesso(), PDO::PARAM_INT);
            $query->bindValue(4, $Etapa->getUsuario1(), PDO::PARAM_INT);
            $query->bindValue(5, date("Ymd"), PDO::PARAM_INT);
            $query->bindValue(6, $Etapa->getAprova(), PDO::PARAM_INT);
            $query->bindValue(7, $Etapa->getAprovaMsg(), PDO::PARAM_STR);            

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

    public function updateEtapaProcesso(Etapa $Etapa){

        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE etapa_processo SET 
                        idusuarioatualizacao=?, dtatualizacao=?, 
                        aprovacao=?, aprovacaomsg=?
                 WHERE  idetapa_processo = ?'
            );
            $query->bindValue(1, $Etapa->getUsuario2(), PDO::PARAM_INT);
            $query->bindValue(2, date("Ymd"), PDO::PARAM_INT);
            $query->bindValue(3, $Etapa->getAprova(), PDO::PARAM_INT);
            $query->bindValue(4, $Etapa->getAprovaMsg(), PDO::PARAM_STR);
            $query->bindValue(5, $Etapa->getId(), PDO::PARAM_STR);
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

    public function updateEtapa(Etapa $Etapa){

        $atualizou=true;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                'UPDATE processo SET 
                        idetapa=?
                 WHERE  idprocesso = ?');

            $query->bindValue(1, $Etapa->getId(), PDO::PARAM_INT);
            $query->bindValue(2, $Etapa->getProcesso(), PDO::PARAM_INT);
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


    /*  LÓGICA DO UPDATE NA ORDEM PRINCIPAL:
        nova etapa é ordem 2
        entao, dá update em todas as ordens iguais ou maiores que 2:
        então 2, 2.1, 2.2 e 3 vira 3, 3.1, 3.2 e 4
    */
    public function updateOrdemPrincipal(Etapa $Etapa){
        $atualizou=true;
        $this->conn->beginTransaction();
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE etapa SET ordem=ordem+1 WHERE CAST(ordem AS DECIMAL) >= CAST(? AS DECIMAL) AND idetapa <> ? AND flag=1 '
            );
            $query->bindValue(1, $Etapa->getOrdem(), PDO::PARAM_INT);
            $query->bindValue(2, $Etapa->getId(), PDO::PARAM_INT);
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

    /*
    LÓGICA DO UPDATE NA ORDEM ALTERNATIVA:
        nova etapa é ordem 1.2
        então dá update com (+0.1) em todas as ordens iguais ou maiores a 1.2 
        até menos que o limite de ceil da ordem (arredondamento para o próximo máximo).
        então 1.1, 1.2 e 1.3 vira 1.1, 1.2, 1.3 e 1.4
    */
    public function updateOrdemAlternativa(Etapa $Etapa){
        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE etapa SET ordem=ordem+0.1 WHERE CAST(ordem AS DECIMAL) >= CAST(? AS DECIMAL) AND CAST(ordem AS DECIMAL) < CAST(? AS DECIMAL) AND idetapa <> ? AND flag=1 '
            );
            $query->bindValue(1, $Etapa->getOrdem(), PDO::PARAM_INT);
            $query->bindValue(2, ceil($Etapa->getOrdem()), PDO::PARAM_INT);
            $query->bindValue(3, $Etapa->getId(), PDO::PARAM_INT);
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

    /* Função para verificar se o registro a ser criado não possui duplicidade  */
    public function isDuplicated(Etapa $Etapa) {
        $duplicado=false;
        try {           
            $query = $this->conn->prepare(
                'SELECT idetapa FROM etapa WHERE nome = ? AND flag=1'
            ); 
            $query->bindValue(1, $Etapa->getNome(), PDO::PARAM_STR);
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

    /* Função para verificar se o usuário alterou o nome para um já existente*/
    public function isDuplicatedEdit(Etapa $Etapa) {
        $duplicado=false;
        try {           
            $query = $this->conn->prepare(
                'SELECT idetapa FROM etapa WHERE nome = ? AND idetapa <> ? AND flag=1'
            ); 
            $query->bindValue(1, $Etapa->getNome(), PDO::PARAM_STR);
            $query->bindValue(2, $Etapa->getId(), PDO::PARAM_STR);
            $query->execute(); 
            $duplicado = $query->fetch(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            $duplicado=true;
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
        }
        return $duplicado;        
    }

    public function insert(Etapa $Etapa){
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO etapa 
                (idetapa,nome,descricao,ordem,fluxo,aprova,msgemail1,msgemail2,escolhedata,msgadd,msgcapa,bloquear,expira,prazo,modo,etapatipo,flag) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//idusuario é AUTO_INCREMENT, então NULL
            $query->bindValue(2, $Etapa->getNome(), PDO::PARAM_INT);
            $query->bindValue(3, $Etapa->getDescricao(), PDO::PARAM_STR);
            $query->bindValue(4, $Etapa->getOrdem(), PDO::PARAM_INT);
            $query->bindValue(5, $Etapa->getFluxo(), PDO::PARAM_INT);
            $query->bindValue(6, $Etapa->getAprova(), PDO::PARAM_STR);
            $query->bindValue(7, $Etapa->getMsgEmail1(), PDO::PARAM_STR);
            $query->bindValue(8, $Etapa->getMsgEmail2(), PDO::PARAM_STR);
            $query->bindValue(9, $Etapa->getEscolheData(), PDO::PARAM_STR);
            $query->bindValue(10, $Etapa->getMsgAdd(), PDO::PARAM_STR);
            $query->bindValue(11, $Etapa->getMsgCapa(), PDO::PARAM_STR);
            $query->bindValue(12, $Etapa->getBloquear(), PDO::PARAM_INT);
            $query->bindValue(13, $Etapa->getExpira(), PDO::PARAM_INT);
            $query->bindValue(14, $Etapa->getPrazo(), PDO::PARAM_INT);
            $query->bindValue(15, $Etapa->getModo(), PDO::PARAM_INT);
            $query->bindValue(16, $Etapa->getEtapaTipo(), PDO::PARAM_INT);
            $query->bindValue(17, APP_FLAG_ACTIVE, PDO::PARAM_INT);//flag ATIVO

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


    public function insertEmailEtapa(Etapa $Etapa){
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO etapa_email 
                (idetapa_email,idetapa,idperfil,idusuario,tipoemail,numero) 
                VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//ID
            $query->bindValue(2, $Etapa->getId(), PDO::PARAM_INT);
            $query->bindValue(3, $Etapa->getPerfil1(), PDO::PARAM_INT);
            $query->bindValue(4, $Etapa->getUsuario1(), PDO::PARAM_INT);
            $query->bindValue(5, $Etapa->getTipoEmail1(), PDO::PARAM_STR);
            $query->bindValue(6, $Etapa->getNumero(), PDO::PARAM_STR);

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


    public function insertDocumentoTipoEtapa(Etapa $Etapa){
        $inseriu=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'INSERT INTO etapa_documentotipo 
                (idetapa_documentotipo,idetapa,iddocumentotipo,obrigatorio) 
                VALUES (?, ?, ?, ?)'
            );
            
            $query->bindValue(1, NULL, PDO::PARAM_INT);//ID
            $query->bindValue(2, $Etapa->getId(), PDO::PARAM_INT);
            $query->bindValue(3, $Etapa->getDocumentoTipo1(), PDO::PARAM_INT);
            $query->bindValue(4, $Etapa->getDocumentoTipo1Obrigatorio(), PDO::PARAM_INT);

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

    public function update(Etapa $Etapa){

        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE etapa SET nome=?,descricao=?,ordem=?,fluxo=?,aprova=?,msgemail1=?,msgemail2=?,escolhedata=?,msgadd=?,msgcapa=?,bloquear=?,expira=?,prazo=?,modo=?,etapatipo=?
                WHERE flag = 1 AND idetapa = ?'
            );
            //escolhedata,msgadd,msgcapa
            $query->bindValue(1, $Etapa->getNome(), PDO::PARAM_INT);
            $query->bindValue(2, $Etapa->getDescricao(), PDO::PARAM_INT);
            $query->bindValue(3, $Etapa->getOrdem(), PDO::PARAM_INT);
            $query->bindValue(4, $Etapa->getFluxo(), PDO::PARAM_INT);
            $query->bindValue(5, $Etapa->getAprova(), PDO::PARAM_INT);
            $query->bindValue(6, $Etapa->getMsgEmail1(), PDO::PARAM_STR);
            $query->bindValue(7, $Etapa->getMsgEmail2(), PDO::PARAM_STR);
            $query->bindValue(8, $Etapa->getEscolheData(), PDO::PARAM_INT);
            $query->bindValue(9, $Etapa->getMsgAdd(), PDO::PARAM_STR);
            $query->bindValue(10, $Etapa->getMsgCapa(), PDO::PARAM_STR);
            $query->bindValue(11, $Etapa->getBloquear(), PDO::PARAM_INT);
            $query->bindValue(12, $Etapa->getExpira(), PDO::PARAM_INT);
            $query->bindValue(13, $Etapa->getPrazo(), PDO::PARAM_INT);
            $query->bindValue(14, $Etapa->getModo(), PDO::PARAM_INT);
            $query->bindValue(15, $Etapa->getEtapaTipo(), PDO::PARAM_INT);
            $query->bindValue(16, $Etapa->getId(), PDO::PARAM_INT);
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

    /* Atualiza ordem de determinada etapa */
    public function updateOrdem($idetapa,$idordem){

        $atualizou=true;
        $this->conn->beginTransaction(); 
        $adicional="";
        try {
            $query = $this->conn->prepare(
                'UPDATE etapa SET ordem=?
                WHERE flag = 1 AND idetapa = ?'
            );
            $query->bindValue(1, $idordem, PDO::PARAM_INT);
            $query->bindValue(2, $idetapa, PDO::PARAM_STR);
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

    /* Remove da tabela etapa_email os emails definidos */
    public function deleteEmails(Etapa $Etapa){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'DELETE FROM etapa_email WHERE idetapa=?'
            );
            $query->bindValue(1, $Etapa->getId(), PDO::PARAM_INT);
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

    /* Remove da tabela etapa_documentotipo os documentos definidos */
    public function deleteDocs(Etapa $Etapa){
        $deletou=true;
        $this->conn->beginTransaction(); 
        try {
            $query = $this->conn->prepare(
                'DELETE FROM etapa_documentotipo WHERE idetapa=?'
            );
            $query->bindValue(1, $Etapa->getId(), PDO::PARAM_INT);
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

    //atualiza registro
    public function delete(Etapa $Etapa){
        $removeu=true;
        $this->conn->beginTransaction();
        try {
            $query = $this->conn->prepare(
                'UPDATE etapa SET flag=2 WHERE idetapa=?'
            );
            $query->bindValue(1, $Etapa->getId(), PDO::PARAM_STR);
            $query->execute();
            $this->conn->commit();
        }
        catch(Exception $e) {
            $this->conn->rollback();
            if(APP_SHOW_SQL_ERRORS){
                echo var_dump($this).'<hr>'.$e->getMessage();exit();
            }
            $removeu=false;
        }       
        return $removeu;
    }

}
?>