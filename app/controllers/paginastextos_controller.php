<?php
class PaginastextosController extends AppController {
	var $name = 'Paginastextos';
	var $components = array('RequestHandler','Acl', 'Auth');

	function beforefilter(){
		parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'agregar'=>'create'
				,'modificar'=>'update'
			)
		);
	}
	
	function agregar(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			$pagina=$this->Paginastexto->Pagina->findById($this->data['Paginastexto']['pagina_id']);
			if(!empty($pagina)){
				$texto=$this->Paginastexto->findByPagina_id($this->data['Paginastexto']['pagina_id']);
				if(empty($texto)){
					if(isset($this->data['Paginastexto']['id'])){unset($this->data['Paginastexto']['id']);}
					$this->Paginastexto->set($this->data);
					if ($this->Paginastexto->validates($this->data)) {
						if($this->Paginastexto->save($this->data)){
							$result['success'] = true;
							$result['message'] = 'El texto de la página fue agregado';
						}else{
							$result["success"] = false;
							$result["errors"] = 'Hubo un error al guardar el texto de la página';
						}
					}else{
						$result["success"] = false;
						$result["errors"] = $this->Paginastexto->validationErrors;
					}
				}else{
					$result["success"] = false;
					$result["errors"] = 'Ya existe el texto de la página';
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
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			$texto=$this->Paginastexto->findById($this->data['Paginastexto']['id']);
			if(!empty($texto)){
				if((Configure::read('Empresa.language')==Configure::read('Config.language'))&&empty($this->data['Paginastexto']['contenido'])){
					$result=$this->__borrar();
				}else{
					if($this->data['resumenCambio']=='no'){
						$this->Paginastexto->unbindValidation('remove', array('resumen'), false);
						unset($this->data['Paginastexto']['resumen']);
					}
					$this->Paginastexto->set($this->data);
					if ($this->Paginastexto->validates($this->data)) {
						if($this->Paginastexto->save($this->data)){
							$result['success'] = true;
							$result['message'] = 'El texto de la página fue modificado';
						}else{
							$result["success"] = false;
							$result["errors"] = 'Hubo un error al modificar el texto de la página';
						}
					}else{
						$result["success"] = false;
						$result["errors"] = $this->Paginastexto->validationErrors;
					}
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'El texto de la página no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function __borrar() {
		if (!empty($this->data['Paginastexto']['id'])){
			if ($this->Paginastexto->delete($this->data['Paginastexto']['id'])){
				$result['success'] = true;
				$result['message'] = 'El texto fue eliminado';
			}else{
				$result['success'] = false;
				$result['errors'] = 'Hubo un error al eliminar el texto';
			}
		}else{
			$result['success'] = false;
			$result['errors'] = 'Hubo un error al eliminar el texto';
		}
		return $result;
		
    }
	
}
?>