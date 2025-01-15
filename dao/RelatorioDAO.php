<?php 
class RelatorioDAO {
 
    private $conn; 
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn = $registry->get('Connection');
    }
 
    //relatório HISTORICO de todos os processos (ATIVOS & INATIVOS)
    public function rel1($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND p.dtcriacao >= \''.transformaDataBanco($periodo_de).'\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND p.dtcriacao <= \''.transformaDataBanco($periodo_ate).'\'';
        }	
        try {			
            $sql = 'SELECT t.nome as nometipo, e.nome as nomeetapa, m.nome as nomemunicipio
            FROM processo p 
            INNER JOIN processotipo t ON p.idprocessotipo = t.idprocessotipo
            INNER JOIN etapa e ON p.idetapa=e.idetapa
            INNER JOIN usuario u ON p.idusuario = u.idusuario
            INNER JOIN municipio m ON u.idmunicipio = m.idmunicipio
            WHERE p.flag = 1 '.$clausula_periodo.'
            GROUP BY p.idprocesso
            ORDER BY nometipo,nomeetapa,nomemunicipio ASC';
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

    //relatório de todos os processos ATIVOS (que ainda não expiraram a COMISSÃO)
    public function rel1ativos($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            //sub1 -> mes, sub2 -> dia, sub3 -> ano
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_de, 4,2) - 2), substr($periodo_de, 6,2), substr($periodo_de, 0,4)));
            $clausula_periodo.=' AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_ate, 4,2) - 2), substr($periodo_ate, 6,2), substr($periodo_ate, 0,4)));
            $clausula_periodo.=' AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }  
        if($periodo_de==NULL && $periodo_ate==NULL){
            //cria variavel pra calcular a dtfim de acordo com a data atual - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (date('m') - 2), date('d'), date('Y')));
            $clausula_periodo=' AND (p.dtfim >= '.$dt_fim.' OR p.dtfim IS NULL)';
        }
       
        try {           
            $sql='   SELECT t.nome as nometipo, e.nome as nomeetapa, m.nome as nomemunicipio
            FROM processo p 
            INNER JOIN processotipo t ON p.idprocessotipo = t.idprocessotipo
            INNER JOIN etapa e ON p.idetapa=e.idetapa
            INNER JOIN usuario u ON p.idusuario = u.idusuario
            INNER JOIN municipio m ON u.idmunicipio = m.idmunicipio
            WHERE p.flag = 1 AND p.idetapa = '.ID_LAST_ETAPA.' '.$clausula_periodo.'
            GROUP BY p.idprocesso
            ORDER BY nometipo,nomeetapa,nomemunicipio ASC';
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

    //relatório de total HISTORICO (ativos e inativos) de processos por cidade
    public function rel2_grafico1($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND p.dtcriacao >= \''.transformaDataBanco($periodo_de).'\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND p.dtcriacao <= \''.transformaDataBanco($periodo_ate).'\'';
        }    
        try {           
            $query = $this->conn->query(
        '   SELECT o.nome as nomeorigem, count(*) as numregs FROM processo p
            INNER JOIN usuario u ON p.idusuario=u.idusuario
            INNER JOIN municipio o ON u.idmunicipio=o.idmunicipio
            WHERE p.flag=1 AND u.idperfil='.PERFIL_IDINSTITUICAO.' '.$clausula_periodo.'
            GROUP BY u.idmunicipio
            ORDER BY numregs DESC, nomeorigem ASC'
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

    //relatório de total de processos ATIVOS por cidade
    public function rel2_grafico1b($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo='';
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            //sub1 -> mes, sub2 -> dia, sub3 -> ano
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_de, 4,2) - 2), substr($periodo_de, 6,2), substr($periodo_de, 0,4)));
            $clausula_periodo.='AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_ate, 4,2) - 2), substr($periodo_ate, 6,2), substr($periodo_ate, 0,4)));
            $clausula_periodo.=' AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }    
        if($periodo_de==NULL && $periodo_ate==NULL){
            //cria variavel pra calcular a dtfim de acordo com a data atual - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (date('m') - 2), date('d'), date('Y')));
            $clausula_periodo=' AND (p.dtfim >= '.$dt_fim.' OR p.dtfim IS NULL)';
        }
        try {           
            $sql='   SELECT o.nome as nomeorigem, count(*) as numregs FROM processo p
            INNER JOIN usuario u ON p.idusuario=u.idusuario
            INNER JOIN municipio o ON u.idmunicipio=o.idmunicipio
            WHERE p.flag=1 AND p.idetapa = '.ID_LAST_ETAPA.' AND u.idperfil='.PERFIL_IDINSTITUICAO.' '.$clausula_periodo.'
            GROUP BY u.idmunicipio
            ORDER BY numregs DESC, nomeorigem ASC';
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

    //relatório de total de processos EM ANDAMENTO por cidade
    public function rel2_grafico1b_andamento($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo='';
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            $clausula_periodo.='AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            $clausula_periodo.=' AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }
        try {           
            $sql='   SELECT o.nome as nomeorigem, count(*) as numregs FROM processo p
            INNER JOIN usuario u ON p.idusuario=u.idusuario
            INNER JOIN municipio o ON u.idmunicipio=o.idmunicipio
            WHERE p.flag=1 AND p.idetapa != '.ID_LAST_ETAPA.' AND u.idperfil='.PERFIL_IDINSTITUICAO.' '.$clausula_periodo.'
            GROUP BY u.idmunicipio
            ORDER BY numregs DESC, nomeorigem ASC';
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

    //relatório de total de processos ATIVOS por subseção
    public function rel2_grafico1c($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo='';
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            //sub1 -> mes, sub2 -> dia, sub3 -> ano
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_de, 4,2) - 2), substr($periodo_de, 6,2), substr($periodo_de, 0,4)));
            $clausula_periodo.='AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_ate, 4,2) - 2), substr($periodo_ate, 6,2), substr($periodo_ate, 0,4)));
            $clausula_periodo.=' AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }    
        if($periodo_de==NULL && $periodo_ate==NULL){
            //cria variavel pra calcular a dtfim de acordo com a data atual - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (date('m') - 2), date('d'), date('Y')));
            $clausula_periodo=' AND (p.dtfim >= '.$dt_fim.' OR p.dtfim IS NULL)';
        }
        try {           
            $sql='SELECT m.nome as nomesubsecao, count(*) as numregs FROM processo p
            INNER JOIN usuario u ON p.idusuario=u.idusuario
            INNER JOIN subsecao s ON u.idsubsecao=s.idsubsecao
            INNER JOIN municipio m ON s.idmunicipio=m.idmunicipio
            WHERE p.flag=1 AND p.idetapa = '.ID_LAST_ETAPA.' AND u.idperfil='.PERFIL_IDINSTITUICAO.' '.$clausula_periodo.'
            GROUP BY u.idsubsecao
            ORDER BY numregs DESC, nomesubsecao ASC';
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

    //relatório de total de processos EM ANDAMENTO por subseção
    public function rel2_grafico1c_andamento($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo='';
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            $clausula_periodo.=' AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            $clausula_periodo.=' AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }
        try {           
            $sql='SELECT m.nome as nomesubsecao, count(*) as numregs FROM processo p
            INNER JOIN usuario u ON p.idusuario=u.idusuario
            INNER JOIN subsecao s ON u.idsubsecao=s.idsubsecao
            INNER JOIN municipio m ON s.idmunicipio=m.idmunicipio
            WHERE p.flag=1 AND p.idetapa != '.ID_LAST_ETAPA.' AND u.idperfil='.PERFIL_IDINSTITUICAO.' '.$clausula_periodo.'
            GROUP BY u.idsubsecao
            ORDER BY numregs DESC, nomesubsecao ASC';
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

    //relatório de total de processos ATIVOS por etapa
    public function rel2_grafico22($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;   
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            //sub1 -> mes, sub2 -> dia, sub3 -> ano
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_de, 4,2) - 2), substr($periodo_de, 6,2), substr($periodo_de, 0,4)));
            $clausula_periodo.='AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_ate, 4,2) - 2), substr($periodo_ate, 6,2), substr($periodo_ate, 0,4)));
            $clausula_periodo.=' AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }    
        if($periodo_de==NULL && $periodo_ate==NULL){
            //cria variavel pra calcular a dtfim de acordo com a data atual - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (date('m') - 2), date('d'), date('Y')));
            $clausula_periodo=' AND (p.dtfim >= '.$dt_fim.' OR p.dtfim IS NULL)';
        }
        try {           
            $query = $this->conn->query(
        '   SELECT e.nome as nomeetapa, count(*) as numregs FROM processo p
            INNER JOIN etapa e ON p.idetapa=e.idetapa
            WHERE e.flag=1 AND p.flag=1 AND p.idetapa = '.ID_LAST_ETAPA.' '.$clausula_periodo.'
            GROUP BY e.idetapa
            ORDER BY numregs DESC, nomeetapa ASC'
            ); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 


    //relatório de total de processos EM ANDAMENTO por etapa
    public function rel2_grafico22_andamento($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;   
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            $clausula_periodo.=' AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            $clausula_periodo.=' AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }
        try {           
            $query = $this->conn->query(
        '   SELECT e.nome as nomeetapa, count(*) as numregs FROM processo p
            INNER JOIN etapa e ON p.idetapa=e.idetapa
            WHERE e.flag=1 AND p.flag=1 AND p.idetapa != '.ID_LAST_ETAPA.' '.$clausula_periodo.'
            GROUP BY e.idetapa
            ORDER BY numregs DESC, nomeetapa ASC'
            ); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 

    //relatório de total de processos ATIVOS por tipo (implantação ou renovação)
    public function rel2_grafico2($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;   
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            //sub1 -> mes, sub2 -> dia, sub3 -> ano
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_de, 4,2) - 2), substr($periodo_de, 6,2), substr($periodo_de, 0,4)));
            $clausula_periodo.='AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            //cria variavel pra calcular a dtfim de acordo com a data escolhida - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (substr($periodo_ate, 4,2) - 2), substr($periodo_ate, 6,2), substr($periodo_ate, 0,4)));
            $clausula_periodo.=' AND (p.dtfim>= \''.$dt_fim.'\' OR p.dtfim IS NULL) AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }    
        if($periodo_de==NULL && $periodo_ate==NULL){
            //cria variavel pra calcular a dtfim de acordo com a data atual - 2 meses, já que o período em dtfim está com 2 meses de antecedência
            $dt_fim=date('Ymd',mktime(0, 0, 0, (date('m') - 2), date('d'), date('Y')));
            $clausula_periodo=' AND (p.dtfim >= '.$dt_fim.' OR p.dtfim IS NULL)';
        }
        try {           
            $query = $this->conn->query(
        '   SELECT pt.nome as nometipo, count(*) as numregs FROM processo p
            INNER JOIN processotipo pt ON pt.idprocessotipo=p.idprocessotipo
            WHERE p.flag=1 AND p.idetapa = '.ID_LAST_ETAPA.' '.$clausula_periodo.'
            GROUP BY pt.idprocessotipo
            ORDER BY numregs DESC, nometipo ASC'
            ); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 

    //relatório de total de processos EM ANDAMENTO por tipo
    public function rel2_grafico2_andamento($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;   
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $periodo_de=transformaDataBanco($periodo_de);
            $clausula_periodo.=' AND p.dtcriacao >= \''.$periodo_de.'\'';
        }
        if($periodo_ate!=NULL){
            $periodo_ate=transformaDataBanco($periodo_ate);
            $clausula_periodo.=' AND p.dtcriacao <= \''.$periodo_ate.'\'';
        }
        try {           
            $query = $this->conn->query(
        '   SELECT pt.nome as nometipo, count(*) as numregs FROM processo p
            INNER JOIN processotipo pt ON pt.idprocessotipo=p.idprocessotipo
            WHERE p.flag=1 AND p.idetapa <> '.ID_LAST_ETAPA.' '.$clausula_periodo.'
            GROUP BY pt.idprocessotipo
            ORDER BY numregs DESC, nometipo ASC'
            ); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 

    //relatório de total de acoes por usuario
    public function rel2_grafico3($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND l.dthistorico >= \''.transformaDataTimestamp($periodo_de).' 00:00:00\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND l.dthistorico <= \''.transformaDataTimestamp($periodo_ate).' 23:59:59\'';
        }
        try {           
            $sql = "SELECT u.nome as nomeusuario, COUNT(l.idusuario) as acoes FROM usuario u
            INNER JOIN historico l ON l.idusuario=u.idusuario
            INNER JOIN perfil p ON p.idperfil=u.idperfil
            WHERE u.flag = 1 ".$clausula_periodo." ".PERFIL_BLOCKED_SQL." 
            GROUP BY u.idusuario
            ORDER BY acoes DESC, nomeusuario ASC";
            $query = $this->conn->query($sql); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 

    //relatório de total de acoes por perfil de usuário
    public function rel2_grafico4($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND l.dthistorico >= \''.transformaDataTimestamp($periodo_de).' 00:00:00\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND l.dthistorico <= \''.transformaDataTimestamp($periodo_ate).' 23:59:59\'';
        }
        try {           
            $sql = '   
            SELECT p.nome as nomeperfil, COUNT(l.idusuario) as acoes FROM usuario u
            INNER JOIN historico l ON l.idusuario=u.idusuario
            INNER JOIN perfil p ON p.idperfil=u.idperfil
            WHERE u.flag = 1 '.$clausula_periodo.' '.PERFIL_BLOCKED_SQL.'
            GROUP BY p.idperfil
            ORDER BY acoes DESC, nomeperfil ASC';
            $query = $this->conn->query($sql); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 

    //relatório de total de documentos por tipo
    public function rel2_grafico5($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;      
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND d.dtcriacao >= \''.transformaDataBanco($periodo_de).'\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND d.dtcriacao <= \''.transformaDataBanco($periodo_ate).'\'';
        }
        try {           
            $sql = '   
            SELECT dc.nome as nomedocumentotipo, COUNT(d.iddocumentotipo) as numregs FROM documento d
            INNER JOIN documentotipo dc ON d.iddocumentotipo=dc.iddocumentotipo
            WHERE d.flag = 1 '.$clausula_periodo.'
            GROUP BY dc.iddocumentotipo
            ORDER BY numregs DESC, nomedocumentotipo ASC
            ';
            $query = $this->conn->query($sql); 
            $result = $query->fetchAll(PDO::FETCH_ASSOC);           
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }       
        return $result; 
    } 

    //relatório de total de ações por mês nos últimos 6 meses
    public function rel2_grafico6($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND `dthistorico` >= \''.transformaDataTimestamp($periodo_de).' 00:00:00\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND `dthistorico` <= \''.transformaDataTimestamp($periodo_ate).' 23:59:59\'';
        }
        try {
            $sql = '
            SELECT count(*) as numacoes, extract(month from `dthistorico`) mes, extract(year from `dthistorico`) ano
            FROM historico
            WHERE `dthistorico` != 0 '.$clausula_periodo.'
            GROUP BY extract(month from `dthistorico`), extract(year from `dthistorico`)
            ORDER BY `dthistorico` DESC
            LIMIT 0,6';
            $query = $this->conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);            
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }    

    //relatório de total de ações de um perfil especifico
    public function rel2_grafico8($idperfil=NULL,$periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND l.dthistorico >= \''.transformaDataTimestamp($periodo_de).' 00:00:00\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND l.dthistorico <= \''.transformaDataTimestamp($periodo_ate).' 23:59:59\'';
        }
        try {
            $sql = "
            SELECT DISTINCT l.iddocumento, u.nome as nomeusuario, COUNT(l.idusuario) as acoes FROM usuario u
            INNER JOIN historico l ON l.idusuario=u.idusuario
            INNER JOIN perfil p ON p.idperfil=u.idperfil
            WHERE u.flag = 1 AND l.idacao = 8 AND p.idperfil=".$idperfil." ".$clausula_periodo." ".PERFIL_BLOCKED_SQL." 
            GROUP BY u.idusuario
            ORDER BY acoes DESC, nomeusuario ASC";
            $query = $this->conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }

    //relatório de total de ações POR DISPOSITIVO
    public function rel2_grafico15($periodo_de=NULL,$periodo_ate=NULL) {
        $result=false;
        $clausula_periodo="";
        if($periodo_de!=NULL){
            $clausula_periodo.=' AND dthistorico >= \''.transformaDataTimestamp($periodo_de).' 00:00:00\'';
        }
        if($periodo_ate!=NULL){
            $clausula_periodo.=' AND dthistorico <= \''.transformaDataTimestamp($periodo_ate).' 23:59:59\'';
        }
        try {
            $sql = "
            SELECT DISTINCT obs, COUNT(idhistorico) as logins FROM historico
            WHERE obs <> '' AND idacao = 1 ".$clausula_periodo."
            GROUP BY obs
            ORDER BY COUNT(idhistorico) DESC";
            $query = $this->conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }
        
    
}
?>