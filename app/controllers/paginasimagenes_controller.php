<?php
class PaginasimagenesController extends AppController{
	var $name='Paginasimagenes';
	var $components = array('RequestHandler','Acl','Auth');
	var $helpers = array('Ext');
	

	function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'validar'=>'read'
				,'listar'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'borrar'=>'delete'
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
	
	function listar() {
		Configure::write('debug', 0);
		if(isset($this->params['form'])&&isset($this->params['form']['pagina_id'])){
			$this->paginate=$this->__paginacion($this->params['form'],array('pagina_id'=>$this->params['form']['pagina_id']));
			$imagenes = $this->paginate();
			if(!empty($imagenes)){
				foreach($imagenes as $key=>$imagen):
					$result['imagenes'][$key]['id']=$imagen['Paginasimagen']['id'];
					$result['imagenes'][$key]['imagen']=$imagen['Paginasimagen']['imagen']['path'];
					$result['imagenes'][$key]['title']=$imagen['Paginasimagen']['title'];
					$result['imagenes'][$key]['pagina_id']=$imagen['Paginasimagen']['pagina_id'];
					$result['imagenes'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['imagenes']='';
			}
			$result['total']=$this->params['paging']['Paginasimagen']['count'];
			$result['success']=true;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		//al enviar imagen no envia encabezado ajax
		$this->layout='ajax';
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			$pagina=$this->Paginasimagen->Pagina->findById($this->data['Paginasimagen']['pagina_id']);
			if(!empty($pagina)){
				$this->Paginasimagen->set($this->data);
				if ($this->Paginasimagen->validates($this->data)) {
					if($this->Paginasimagen->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La imagen fue agregada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar la imagen';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasimagen->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'No existe la página';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			$imagen=$this->Paginasimagen->findById($this->data['Paginasimagen']['id']);
			if(!empty($imagen)){
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Paginasimagen->unbindValidation('remove', array('title'), false);
				}
				unset($this->data['Paginasimagen']['imagen']);
				$this->Paginasimagen->unbindValidation('remove', array('imagen'), false);
				$this->Paginasimagen->set($this->data);
				if ($this->Paginasimagen->validates()){
					if ($this->Paginasimagen->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información de la imagen fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la información de la imagen'; 
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasimagen->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'La imagen no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		unset($this->params['form']['pagina_id']);
		foreach ($this->params['form'] as $id):
			$ids['Paginasimagen.id'][]=$id;
		endforeach;
		if (!empty($ids['Paginasimagen.id'])){
			$registros = $this->Paginasimagen->find('all',array('conditions'=>$ids));
			if (count($ids['Paginasimagen.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos una de las imágenes no existe';
			}else{
				foreach($ids['Paginasimagen.id'] as $id):// no deleteall por el afterdelete del acl
					if ($this->Paginasimagen->delete($id)){
						$result['success'] = true;
						$result['message'] = 'Las imágenes fueron eliminadas';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al eliminar las imágenes';
						break;
					}
				endforeach;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
}
?>