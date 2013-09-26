<?php
class PaginascontactosController extends AppController {
	var $name = 'Paginascontactos';
	var $components = array('RequestHandler','Acl', 'Auth');

	function beforefilter(){
		parent::beforeFilter();
		$this->Auth->allow('nada');
	}
	
	function validar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['field'])){
			$result=$this->__validarCampo($this->params['form']['field']);
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			$pagina=$this->Paginascontacto->Pagina->findById($this->data['Paginascontacto']['pagina_id']);
			if(!empty($pagina)){
				$contacto=$this->Paginascontacto->findByPagina_id($this->data['Paginascontacto']['pagina_id']);
				if(empty($contacto)){
					if(isset($this->data['Paginascontacto']['id'])){unset($this->data['Paginascontacto']['id']);}
					$this->Paginascontacto->set($this->data);
					$this->Paginascontacto->unbindValidation('remove', array('contacttitle','contactemail','contactname','contactdetail'),false);
					if ($this->Paginascontacto->validates($this->data)) {
						if($this->Paginascontacto->save($this->data)){
							$result['success'] = true;
							$result['message'] = 'La información de contacto fue agregada';
						}else{
							$result["success"] = false;
							$result["errors"] = 'Hubo un error al guardar la información de contacto';
						}
					}else{
						$result["success"] = false;
						$result["errors"] = $this->Paginascontacto->validationErrors;
					}
				}else{
					$result["success"] = false;
					$result["errors"] = 'Ya existe la información de contacto';
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'No existe la página';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],'Paginasopcional.idfoto');}
		if (!empty($this->data)){
			$contacto=$this->Paginascontacto->findById($this->data['Paginascontacto']['id']);
			if(!empty($contacto)){
				$this->Paginascontacto->set($this->data);
				$this->Paginascontacto->unbindValidation('remove', array('contacttitle','contactemail','contactname','contactdetail'),false);
				if ($this->Paginascontacto->validates($this->data)) {
					if($this->Paginascontacto->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información de contacto fue modificada';
					}else{
						$result["success"] = false;
						$result["errors"] = 'Hubo un error al modificar la información de contacto';
					}
				}else{
					$result["success"] = false;
					$result["errors"] = $this->Paginascontacto->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'El contacto no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
}
?>