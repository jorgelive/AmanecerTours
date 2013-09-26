<?php
class PaginaspromocionesController extends AppController{
	var $name='Paginaspromociones';
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
			$promociones = $this->paginate();
			
			if(!empty($promociones)){
				foreach($promociones as $key=>$promocion):
					$result['promociones'][$key]['id']=$promocion['Paginaspromocion']['id'];
					$result['promociones'][$key]['title']=$promocion['Paginaspromocion']['title'];
					if($promocion['Paginaspromocion']['inicio']!='0000-00-00'){
						$result['promociones'][$key]['inicio']=$promocion['Paginaspromocion']['inicio'];
					}else{
						$result['promociones'][$key]['inicio']='';
					}
					if($promocion['Paginaspromocion']['final']!='0000-00-00'){
						$result['promociones'][$key]['final']=$promocion['Paginaspromocion']['final'];
					}else{
						$result['promociones'][$key]['final']='';
					}
					$result['promociones'][$key]['notas']=$promocion['Paginaspromocion']['notas'];
					$result['promociones'][$key]['condiciones']=$promocion['Paginaspromocion']['condiciones'];
					$result['promociones'][$key]['precio']=$promocion['Paginaspromocion']['precio'];
					$result['promociones'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['promociones']='';
			}
			$result['total']=$this->params['paging']['Paginaspromocion']['count'];
			$result['success']=true;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (isset($this->data['Paginaspromocion']['pagina_id'])&&!empty($this->data['Paginaspromocion']['pagina_id'])) {
			$pagina=$this->Paginaspromocion->Pagina->findById($this->data['Paginaspromocion']['pagina_id']);
			if(!empty($pagina)){
				$this->Paginaspromocion->set($this->data);
				if ($this->Paginaspromocion->validates($this->data)) {
					if($this->Paginaspromocion->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La promoción fue agregada';
						$result['data']['newId'] = $this->Paginaspromocion->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar la promoción';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginaspromocion->validationErrors;
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
		if (isset($this->data['Paginaspromocion']['id'])&&!empty($this->data['Paginaspromocion']['id'])){
			$promocion=$this->Paginaspromocion->findById($this->data['Paginaspromocion']['id']);
			if(!empty($promocion)){
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Paginaspromocion->unbindValidation('remove', array('notas','condiciones'), false);
				}
				$this->Paginaspromocion->set($this->data);
				
				if ($this->Paginaspromocion->validates()){
					if ($this->Paginaspromocion->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información de la promoción fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la información de la promoción'; 
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginaspromocion->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'La promoción no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		unset($this->params['form']['pagina_id']);
		foreach ($this->params['form'] as $id):
			$ids['Paginaspromocion.id'][]=$id;
		endforeach;
		if (!empty($ids['Paginaspromocion.id'])){
			$registros = $this->Paginaspromocion->find('all',array('conditions'=>$ids));
			if (count($ids['Paginaspromocion.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos una de las promociones no existe';
			}else{
				foreach($ids['Paginaspromocion.id'] as $id):// no deleteall por el afterdelete del acl
					if ($this->Paginaspromocion->delete($id)){
						$result['success'] = true;
						$result['message'] = 'Las promociones fueron eliminadas';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al eliminar las promociones';
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