<?php
class PaginasadjuntosController extends AppController{
	var $name='Paginasadjuntos';
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
			$adjuntos = $this->paginate();
			if(!empty($adjuntos)){
				foreach($adjuntos as $key=>$adjunto):
					$result['adjuntos'][$key]['id']=$adjunto['Paginasadjunto']['id'];
					$result['adjuntos'][$key]['icon']=$adjunto['Paginasadjunto']['adjunto']['icon'];
					$result['adjuntos'][$key]['title']=$adjunto['Paginasadjunto']['title'];
					//$result['adjuntos'][$key]['adjunto']=$adjunto['Paginasadjunto']['adjunto']['path'];
					$result['adjuntos'][$key]['pagina_id']=$adjunto['Paginasadjunto']['pagina_id'];
					$result['adjuntos'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['adjuntos']='';
			}
			$result['total']=$this->params['paging']['Paginasadjunto']['count'];
			$result['success']=true;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		//al enviar adjunto no envia encabezado ajax
		$this->layout='ajax';
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			$pagina=$this->Paginasadjunto->Pagina->findById($this->data['Paginasadjunto']['pagina_id']);
			if(!empty($pagina)){
				$this->Paginasadjunto->set($this->data);
				if ($this->Paginasadjunto->validates($this->data)) {
					if($this->Paginasadjunto->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El archivo adjunto fue agregado';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar el archivo adjunto';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasadjunto->validationErrors;
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
			$adjunto=$this->Paginasadjunto->findById($this->data['Paginasadjunto']['id']);
			if(!empty($adjunto)){
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Paginasadjunto->unbindValidation('remove', array('title'), false);
				}
				unset($this->data['Paginasadjunto']['adjunto']);
				$this->Paginasadjunto->unbindValidation('remove', array('adjunto'), false);
				$this->Paginasadjunto->set($this->data);
				if ($this->Paginasadjunto->validates()){
					if ($this->Paginasadjunto->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información del archivo adjunto fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la información del archivo adjunto'; 
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasadjunto->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'La adjunto no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		unset($this->params['form']['pagina_id']);
		foreach ($this->params['form'] as $id):
			$ids['Paginasadjunto.id'][]=$id;
		endforeach;
		if (!empty($ids['Paginasadjunto.id'])){
			$registros = $this->Paginasadjunto->find('all',array('conditions'=>$ids));
			if (count($ids['Paginasadjunto.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos uno de los archivos adjuntos no existe';
			}else{
				foreach($ids['Paginasadjunto.id'] as $id):// no deleteall por el afterdelete del acl
					if ($this->Paginasadjunto->delete($id)){
						$result['success'] = true;
						$result['message'] = 'Los archivos adjuntos fueron eliminados';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al eliminar los archivos adjuntos';
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