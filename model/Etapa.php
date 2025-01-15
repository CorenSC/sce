<?php
class Etapa{

	private $idetapa;
    private $idprocesso;
	private $nome;
    private $descricao;
    private $ordem;
    private $fluxo; //0 => faz parte do fluxo principal | 1 => faz parte do fluxo alternativo
    private $numdocs;
    private $numemails;
    private $tipoemail1; //0 => não envia e-mail | 1 => envia e-mail p/ instituição | 2 => envia e-mail p/ perfil específico | 3 => envia e-mail para usuários específicos
    private $tipoemail2; //0 => não envia e-mail | 1 => envia e-mail p/ instituição | 2 => envia e-mail p/ perfil específico | 3 => envia e-mail para usuários específicos
    private $msgemail1;
    private $msgemail2;
    private $msgcapa;
    private $msgadd;
    private $escolhedata;
    private $numero;
    private $idusuario1;
    private $idusuario2;
    private $idperfil1;
    private $idperfil2;
    private $iddocumentotipo1;
    private $iddocumentotipo2;
    private $documentotipo1obrigatorio;
    private $documentotipo2obrigatorio;
    private $aprova; 
    private $aprovamsg; 
    private $bloquear;
    private $expira;
    private $prazo;
    private $modo;//0 -> normal | 1 -> c/eleição | 2 -> s/eleição
    private $etapatipo;//etapatipo = 0 (normal), 1 (escolhe se houve ou não candidatos)
    private $flag;
    //atributos provenientes de relacionamentos
    //private $tipomsg; //1 => envia mensagem encontrada no campo msgemail1 da tabela etapa | 2 => idem, msgemail2

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getId()!=NULL){
            $toLog.="Código da Etapa: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getProcesso()!=NULL){
            $toLog.="Código do Processo: ".$this->getProcesso().APP_LINE_BREAK;
        }
        if($this->getNome()!=NULL){
            $toLog.="Nome da Etapa: ".$this->getNome().APP_LINE_BREAK;
        }
        if($this->getDescricao()!=NULL){
            $toLog.="Descrição da Etapa: ".$this->getDescricao().APP_LINE_BREAK;
        }
        
        if($this->getOrdem()!=NULL){
            $toLog.="Número da Etapa: ".$this->getOrdem().APP_LINE_BREAK;
        }
        if($this->getFluxo()!=NULL){
            $toLog.="Fluxo: ";
            if($this->getFluxo()==ETAPA_PRINCIPAL){
                $toLog.="Principal".APP_LINE_BREAK;
            }else{
                $toLog.="Alternativo".APP_LINE_BREAK;
            }            
        }
        if($this->getNumDocs()!=NULL){
            $toLog.="Número de Documentos: ".$this->getNumDocs().APP_LINE_BREAK;
        }
        if($this->getTipoEmail1()!=NULL && $this->getTipoEmail1()>0){
            $toLog.="Tipo de Email 1: ";
            if($this->getTipoEmail1()==1){                
                $toLog.="E-mail para a instituição".APP_LINE_BREAK;
            }elseif($this->getTipoEmail1()==2){
                $toLog.="E-mail para perfil específico".APP_LINE_BREAK;
            }elseif($this->getTipoEmail1()==3){
                $toLog.="E-mail para usuários específicos".APP_LINE_BREAK;                
            }
        }
        if($this->getMsgEmail1()!=NULL && $this->getTipoEmail1()!=NULL && $this->getTipoEmail1()>0){
            $toLog.="Mensagem Email 1: ".$this->getMsgEmail1().APP_LINE_BREAK;
        }
        if($this->getTipoEmail2()!=NULL && $this->getTipoEmail2()>0){
            $toLog.="Tipo de Email 2: ";
            if($this->getTipoEmail2()==1){                
                $toLog.="E-mail para a instituição".APP_LINE_BREAK;
            }elseif($this->getTipoEmail2()==2){
                $toLog.="E-mail para perfil específico".APP_LINE_BREAK;
            }elseif($this->getTipoEmail2()==3){
                $toLog.="E-mail para usuários específicos".APP_LINE_BREAK;                
            }
        }
        if($this->getMsgEmail2()!=NULL && $this->getTipoEmail2()!=NULL && $this->getTipoEmail2()>0){
            $toLog.="Mensagem Email 2: ".$this->getMsgEmail2().APP_LINE_BREAK;
        }
        if($this->getAprova()!=NULL){
            //0 => não precisa aprovar | 1 => aparecer campo para aprovação do documento
            if($this->getAprova()==0){
                $toLog.="Etapa não exige aprovação".APP_LINE_BREAK;
            }else{
                $toLog.="Etapa exige aprovação".APP_LINE_BREAK;
            }
        }
        if($this->getPrazo()>0){
            $toLog.="Prazo para conclusão da etapa: ".$this->getPrazo()." dias".APP_LINE_BREAK;
        }
        if($this->getModo()!=NULL){
            $toLog.="Modo da etapa: ";
            if($this->getModo()==0){
                $toLog.="Normal";
            }elseif($this->getModo()==1){
                $toLog.="Com Eleições";
            }elseif($this->getModo()==2){
                $toLog.="Sem Eleições";
            }elseif($this->getModo()==3){
                $toLog.="Não Militar";
            }
            $toLog.=APP_LINE_BREAK;
        }
        if($this->getEtapaTipo()!=NULL){
            $toLog.="Tipo da etapa: ";
            if($this->getModo()==0){
                $toLog.="Normal";
            }elseif($this->getModo()==1){
                $toLog.="Escolha se houve ou não candidatos (eleição)";
            }
            $toLog.=APP_LINE_BREAK;
        }
        return $toLog;
    }
	
    public function getId() {
        return $this->idetapa;
    } 
    public function setId($v=NULL) {
        $this->idetapa = $v;
        return $this;
    }

    public function getProcesso() {
        return $this->idprocesso;
    } 
    public function setProcesso($v=NULL) {
        $this->idprocesso = $v;
        return $this;
    }
	
    public function getNome() {
        return $this->nome;
    } 
    public function setNome($v=NULL) {
        $this->nome = $v;
        return $this;
    }	

    public function getDescricao() {
        return $this->descricao;
    } 
    public function setDescricao($v=NULL) {
        $this->descricao = $v;
        return $this;
    }   

    public function getOrdem() {
        return $this->ordem;
    } 
    public function setOrdem($v=NULL) {
        $this->ordem = $v;
        return $this;
    }

    public function getTipoEmail1() {
        return $this->tipoemail1;
    } 
    public function setTipoEmail1($v=NULL) {
        $this->tipoemail1 = $v;
        return $this;
    }

    public function getTipoEmail2() {
        return $this->tipoemail2;
    } 
    public function setTipoEmail2($v=NULL) {
        $this->tipoemail2 = $v;
        return $this;
    }

    public function getUsuario1() {
        return $this->idusuario1;
    } 
    public function setUsuario1($v=NULL) {
        $this->idusuario1 = $v;
        return $this;
    }

    public function getUsuario2() {
        return $this->idusuario2;
    } 
    public function setUsuario2($v=NULL) {
        $this->idusuario2 = $v;
        return $this;
    }

    public function getPerfil1() {
        return $this->idperfil1;
    } 
    public function setPerfil1($v=NULL) {
        $this->idperfil1 = $v;
        return $this;
    }

    public function getPerfil2() {
        return $this->idperfil2;
    } 
    public function setPerfil2($v=NULL) {
        $this->idperfil2 = $v;
        return $this;
    }

    public function getFluxo() {
        return $this->fluxo;
    } 
    public function setFluxo($v=NULL) {
        $this->fluxo = $v;
        return $this;
    }

    public function getNumDocs() {
        return $this->numdocs;
    } 
    public function setNumDocs($v=NULL) {
        $this->numdocs = $v;
        return $this;
    }

    public function getNumEmails() {
        return $this->numemails;
    } 
    public function setNumEmails($v=NULL) {
        $this->numemails = $v;
        return $this;
    }

    public function getMsgEmail1() {
        return $this->msgemail1;
    } 
    public function setMsgEmail1($v=NULL) {
        $this->msgemail1 = $v;
        return $this;
    }

    public function getMsgEmail2() {
        return $this->msgemail2;
    } 
    public function setMsgEmail2($v=NULL) {
        $this->msgemail2 = $v;
        return $this;
    }

    public function getMsgCapa() {
        return $this->msgcapa;
    } 
    public function setMsgCapa($v=NULL) {
        $this->msgcapa = $v;
        return $this;
    }

    public function getMsgAdd() {
        return $this->msgadd;
    } 
    public function setMsgAdd($v=NULL) {
        $this->msgadd = $v;
        return $this;
    }

    public function getEscolheData() {
        return $this->escolhedata;
    } 
    public function setEscolheData($v=NULL) {
        $this->escolhedata = $v;
        return $this;
    }

    public function getAprova() {
        return $this->aprova;
    } 
    public function setAprova($v=NULL) {
        $this->aprova = $v;
        return $this;
    }

    public function getAprovaMsg() {
        return $this->aprovamsg;
    }
    public function setAprovaMsg($v=NULL) {
        $this->aprovamsg = $v;
        return $this;
    }

    public function getNumero() {
        return $this->numero;
    } 
    public function setNumero($v=NULL) {
        $this->numero = $v;
        return $this;
    }

    public function getDocumentoTipo1() {
        return $this->iddocumentotipo1;
    } 
    public function setDocumentoTipo1($v=NULL) {
        $this->iddocumentotipo1 = $v;
        return $this;
    }

    public function getDocumentoTipo2() {
        return $this->iddocumentotipo2;
    } 
    public function setDocumentoTipo2($v=NULL) {
        $this->iddocumentotipo2 = $v;
        return $this;
    }

    public function getDocumentoTipo1Obrigatorio() {
        return $this->documentotipo1obrigatorio;
    } 
    public function setDocumentoTipo1Obrigatorio($v=NULL) {
        $this->documentotipo1obrigatorio = $v;
        return $this;
    }
    
    public function getDocumentoTipo2Obrigatorio() {
        return $this->documentotipo2obrigatorio;
    } 
    public function setDocumentoTipo2Obrigatorio($v=NULL) {
        $this->documentotipo2obrigatorio = $v;
        return $this;
    }

    public function getBloquear() {
        return $this->bloquear;
    } 
    public function setBloquear($v=NULL) {
        $this->bloquear = $v;
        return $this;
    }

    public function getExpira() {
        return $this->expira;
    } 
    public function setExpira($v=NULL) {
        $this->expira = $v;
        return $this;
    }

    public function getPrazo() {
        return $this->prazo;
    } 
    public function setPrazo($v=NULL) {
        $this->prazo = $v;
        return $this;
    }

    public function getModo() {
        return $this->modo;
    } 
    public function setModo($v=NULL) {
        $this->modo = $v;
        return $this;
    }

    public function getEtapaTipo() {
        return $this->etapatipo;
    } 
    public function setEtapaTipo($v=NULL) {
        $this->etapatipo = $v;
        return $this;
    }
    
    public function getFlag() {
        return $this->flag;
    }
    public function setFlag($v=NULL) {
        $this->flag = $v;
        return $this;
    }

} ?>