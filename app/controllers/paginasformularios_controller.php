<?php
class PaginasformulariosController extends AppController {
	var $name = 'Paginasformularios';
	var $helpers = array('Html','Form');
	var $components = array('RequestHandler','Email');
	
	function form(){
        if(isset($this->data['Paginasformulario']['idioma'])){
            Configure::write('Config.language',$this->data['Paginasformulario']['idioma']);
        }
        if (!empty($this->data)) {
			$this->Paginasformulario->set($this->data);
			if ($this->Paginasformulario->validates()) {
				$this->Email->to = base64_decode($this->data['Paginasformulario']['destinatario']);
                $this->Email->bcc = array(base64_decode($this->data['Paginasformulario']['cco']));
            	$this->Email->subject = $this->data['Paginasformulario']['title'].' enviado por: ' .$this->data['Paginasformulario']['name']. ' de ' .$this->data['Paginasformulario']['pais'];
           		$this->Email->from = $this->data['Paginasformulario']['email'];  
            	if($this->Email->send($this->data['Paginasformulario']['contenido'])){
					$this->Session->setFlash(__('message_sent',true),'/flash-jq-ui',array('class'=>'ok'));
					$this->set('ok',true);
				}else{
					$this->Session->setFlash(__('message_error_retray',true),'/flash-jq-ui',array('class'=>'error'));
				}
			}else{
				$this->Session->setFlash(__('message_error',true),'/flash-jq-ui',array('class'=>'error'));
			}
		}else{
            $this->data['Paginasformulario']['idioma']=$this->params['form']['idioma'];
            $this->data['Paginasformulario']['title']=$this->params['form']['title'];
			$this->data['Paginasformulario']['destinatario']=$this->params['form']['destinatario'];
			$this->data['Paginasformulario']['cco']=$this->params['form']['cco'];
		}
	}
}
?>