<?php
class Documento{

	private $iddocumento;
	private $idprocesso;
	private $idusuario;
    private $idusuarioatualizacao;
	private $iddocumentotipo;
    private $link;
    private $dtcriacao;
    private $dtatualizacao;
    private $obs;    
	private $flag;
 
    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getDtCriacao()!=NULL){
            $toLog.="Data de criação: ".$this->getDtCriacao().APP_LINE_BREAK;
        }
        if($this->getDtAtualizacao()!=NULL){
            $toLog.="Data de atualização: ".$this->getDtAtualizacao().APP_LINE_BREAK;
        }
        if($this->getObs()!=NULL){
            $toLog.="Observações a respeito do documento: ".$this->getObs().APP_LINE_BREAK;
        }
        return $toLog;
    }
	
    public function getId() {
        return $this->iddocumento;
    } 
    public function setId($iddocumento) {
        $this->iddocumento = $iddocumento;
        return $this;
    }
	
    public function getProcesso() {
        return $this->idprocesso;
    } 
    public function setProcesso($v) {
        $this->idprocesso = $v;
        return $this;
    }
	
    public function getUsuario() {
        return $this->idusuario;
    } 
    public function setUsuario($v) {
        $this->idusuario = $v;
        return $this;
    }

    public function getUsuarioAtualizacao() {
        return $this->idusuarioatualizacao;
    } 
    public function setUsuarioAtualizacao($v) {
        $this->idusuarioatualizacao = $v;
        return $this;
    }
	
    public function getDocumentoTipo() {
        return $this->iddocumentotipo;
    } 
    public function setDocumentoTipo($v) {
        $this->iddocumentotipo = $v;
        return $this;
    }

    public function getLink() {
        return $this->link;
    } 
    public function setLink($link) {
        $this->link = $link;
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
    public function setDtAtualizacao($dtatualizacao) {
        $this->dtatualizacao = $dtatualizacao;
        return $this;
    }

    public function getObs() {
        return $this->obs;
    }
    public function setObs($v) {
        $this->obs = $v;
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