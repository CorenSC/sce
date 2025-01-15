<?php
class Processo {
    
    private $idprocesso;
    private $idusuario;
    private $idprocessotipo;
    private $idetapa;
    private $numero;
    private $dtcriacao;
    private $dtatualizacao;    
    private $dtposse1;
    private $dtposse2;
    private $dtposse3;
    private $obsposse;
    private $dtescolhida; //Até ter uma data escolhida, não há data definida
    private $dtfim;
    private $dtaviso;
    private $prazo;
    private $modo;
    private $militar;//define se é um processo de uma instituição MILITAR ou não
    private $nomepresidentecee;
    private $nomesecretariocee;
	private $flag;

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getId()!=NULL){
            $toLog.="Código do Processo: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getNumero()!=NULL){
            $toLog.="Número do Processo: ".$this->getNumero().APP_LINE_BREAK;
        }
        if($this->getProcessoTipo()!=NULL){
            $toLog.="Tipo do Processo: ".$this->getProcessoTipo().APP_LINE_BREAK;
        }
        if($this->getUsuario()!=NULL){
            $toLog.="Usuário responsável: ".$this->getUsuario().APP_LINE_BREAK;
        }        
        if($this->getEtapa()!=NULL){
            $toLog.="Etapa do Processo: ".$this->getEtapa().APP_LINE_BREAK;
        }
        if($this->getDtPosse1()!=NULL){
            $toLog.="Data de Posse 1: ".exibeDataTimestamp($this->getDtPosse1()).APP_LINE_BREAK;
        }
        if($this->getDtPosse2()!=NULL){
            $toLog.="Data de Posse 2: ".exibeDataTimestamp($this->getDtPosse2()).APP_LINE_BREAK;
        }
        if($this->getDtPosse3()!=NULL){
            $toLog.="Data de Posse 3: ".exibeDataTimestamp($this->getDtPosse3()).APP_LINE_BREAK;
        }
        if($this->getObsPosse()!=NULL){
            $toLog.="Observação sobre a Data de Posse: ".$this->getObsPosse().APP_LINE_BREAK;
        }
        if($this->getDtEscolhida()!=NULL){
            $toLog.="Data Escolhida para Posse: ".exibeDataTimestamp($this->getDtEscolhida()).APP_LINE_BREAK;
        }
        if($this->getDtFim()!=NULL){
            $toLog.="Lembrete Renovação - Data programada: ".exibeData($this->getDtFim()).APP_LINE_BREAK;
        }    
        if($this->getDtAviso()!=NULL){
            $toLog.="Lembrete Renovação - Data de envio: ".exibeDataTimestamp($this->getDtAviso()).APP_LINE_BREAK;
        }  
        if($this->getPrazo()!=NULL){
            $toLog.="Prazo da etapa atual: ".exibeData($this->getPrazo()).APP_LINE_BREAK;
        }   
        return $toLog;
    }

    public function getId() {
        return $this->idprocesso;
    }
    public function setId($idprocesso) {
        $this->idprocesso = $idprocesso;
        return $this;
    }

    public function getUsuario() {
        return $this->idusuario;
    }
    public function setUsuario($idusuario) {
        $this->idusuario = $idusuario;
        return $this;
    }
    
    public function getProcessoTipo() {
        return $this->idprocessotipo;
    }
    public function setProcessoTipo($v) {
        $this->idprocessotipo = $v;
        return $this;
    }

    public function getEtapa() {
        return $this->idetapa;
    }
    public function setEtapa($v) {
        $this->idetapa = $v;
        return $this;
    }

    public function getNumero() {
        return $this->numero;
    }
    public function setNumero($numero) {
        $this->numero = $numero;
        return $this;
    }

    public function getDtCriacao() {
        return $this->dtcriacao;
    }
    public function setDtCriacao($dtcriacao) {
        $this->dtcriacao = $dtcriacao;
        return $this;
    }

    public function getDtAtualizacao() {
        return $this->dtatualizacao;
    }
    public function setDtAtualizacao($v) {
        $this->dtatualizacao = $v;
        return $this;
    }

    public function getDtPosse1() {
        return $this->dtposse1;
    }
    public function setDtPosse1($v) {
        $this->dtposse1 = $v;
        return $this;
    }

    public function getDtPosse2() {
        return $this->dtposse2;
    }
    public function setDtPosse2($v) {
        $this->dtposse2 = $v;
        return $this;
    }

    public function getDtPosse3() {
        return $this->dtposse3;
    }
    public function setDtPosse3($v) {
        $this->dtposse3 = $v;
        return $this;
    }
    
    public function getObsPosse() {
        return $this->obsposse;
    }
    public function setObsPosse($v) {
        $this->obsposse = $v;
        return $this;
    }

    public function getDtEscolhida() {
        return $this->dtescolhida;
    }
    public function setDtEscolhida($v) {
        $this->dtescolhida = $v;
        return $this;
    }

    public function getDtFim() {
        return $this->dtfim;
    }
    public function setDtFim($v) {
        $this->dtfim = $v;
        return $this;
    }

    public function getDtAviso() {
        return $this->dtaviso;
    }
    public function setDtAviso($v) {
        $this->dtaviso = $v;
        return $this;
    }

    public function getPrazo() {
        return $this->prazo;
    }
    public function setPrazo($v) {
        $this->prazo = $v;
        return $this;
    }

    public function getModo() {
        return $this->modo;
    }
    public function setModo($v) {
        $this->modo = $v;
        return $this;
    }

    public function getMilitar() {
        return $this->militar;
    }
    public function setMilitar($v) {
        $this->militar = $v;
        return $this;
    }

  
    public function getNomePresidenteCEE() {
        return $this->nomepresidentecee;
    }

    public function setNomePresidenteCEE($v) {
        $this->nomepresidentecee = $v;
        return $this;
    }

    public function getNomeSecretarioCEE() {
        return $this->nomesecretariocee;
    }

    public function setNomeSecretarioCEE($v) {
        $this->nomesecretariocee = $v;
        return $this;
    }



    public function getFlag() {
        return $this->flag;
    }
    public function setFlag($flag) {
        $this->flag = $flag;
        return $this;
    }

} ?>