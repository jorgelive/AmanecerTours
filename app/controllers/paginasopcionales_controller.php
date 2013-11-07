<?php
class PaginasopcionalesController extends AppController {
	var $name = 'Paginasopcionales';
	var $components = array('RequestHandler','Acl', 'Auth');

	function beforefilter(){
		parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'validar'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
			)
		);
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
		//debug ($this->Paginasopcional);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],'Paginasopcional.duracion','Paginasopcional.imagenpath','Paginasopcional.textocontacto','Paginasopcional.textoimagen','Paginasopcional.textovideo','Paginasopcional.textoadjunto','Paginasopcional.textopromocion');}
		if (!empty($this->data)) {
			$pagina=$this->Paginasopcional->Pagina->findById($this->data['Paginasopcional']['pagina_id']);
			if(!empty($pagina)){
				$opcional=$this->Paginasopcional->findByPagina_id($this->data['Paginasopcional']['pagina_id']);
				if(empty($opcional)){
					if(isset($this->data['Paginasopcional']['id'])){unset($this->data['Paginasopcional']['id']);}
					$this->Paginasopcional->set($this->data);
					if ($this->Paginasopcional->validates($this->data)) {
						if($this->Paginasopcional->save($this->data)){
							$result['success'] = true;
							$result['message'] = 'La información opcional fue agregada';
						}else{
							$result["success"] = false;
							$result["errors"] = 'Hubo un error al guardar la información opcional';
						}
					}else{
						$result["success"] = false;
						$result["errors"] = $this->Paginasopcional->validationErrors;
					}
				}else{
					$result["success"] = false;
					$result["errors"] = 'Ya existe la información opcional';
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
		if (!empty($this->data)) {
			$opcional=$this->Paginasopcional->findById($this->data['Paginasopcional']['id']);
			if(!empty($opcional)){
				$this->Paginasopcional->set($this->data);
				if ($this->Paginasopcional->validates($this->data)) {
					if($this->Paginasopcional->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información opcional fue modificada';
					}else{
						$result["success"] = false;
						$result["errors"] = 'Hubo un error al modificar la información opcional';
					}
				}else{
					$result["success"] = false;
					$result["errors"] = $this->Paginasopcional->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'La información opcional no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
}
?>