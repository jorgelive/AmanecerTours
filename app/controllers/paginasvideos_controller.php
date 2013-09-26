<?php
class PaginasvideosController extends AppController{
	var $name='Paginasvideos';
	var $components = array('RequestHandler','Acl','Auth');
	var $helpers = array('Ext');
	

	function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'validar'=>'read'
				,'listar'=>'read'
				,'listarfuentes'=>'read'
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
	
	function listarfuentes() {
		Configure::write('debug', 0);
		foreach(Configure::read('Default.video') as $key=>$fuente):
			$result['fuentes'][$key]['id']=$key;
			$result['fuentes'][$key]['name']=$fuente;
		endforeach;
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function listar() {
		Configure::write('debug', 0);
		if(isset($this->params['form'])&&isset($this->params['form']['pagina_id'])){
			$this->paginate=$this->__paginacion($this->params['form'],array('pagina_id'=>$this->params['form']['pagina_id']));
			$videos = $this->paginate();
			if(!empty($videos)){
				foreach($videos as $key=>$video):
					$result['videos'][$key]['id']=$video['Paginasvideo']['id'];
					$result['videos'][$key]['fuente']=$video['Paginasvideo']['fuente'];
					if(is_array($video['Paginasvideo']['codigo'])){
						$result['videos'][$key]['codigo']=$video['Paginasvideo']['codigo']['codigo'];
						$result['videos'][$key]['imagen']=$video['Paginasvideo']['codigo']['p_img'];
					}else{
						$result['videos'][$key]['codigo']=$video['Paginasvideo']['codigo'];
						$result['videos'][$key]['imagen']='';
					}
					$result['videos'][$key]['descripcion']=$video['Paginasvideo']['descripcion'];
					$result['videos'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['videos']='';
			}
			$result['total']=$this->params['paging']['Paginasvideo']['count'];
			$result['success']=true;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (isset($this->data['Paginasvideo']['pagina_id'])&&!empty($this->data['Paginasvideo']['pagina_id'])) {
			$pagina=$this->Paginasvideo->Pagina->findById($this->data['Paginasvideo']['pagina_id']);
			if(!empty($pagina)){
				$this->Paginasvideo->set($this->data);
				if ($this->Paginasvideo->validates($this->data)) {
					if($this->Paginasvideo->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El video fue agregado';
						$result['data']['newId'] = $this->Paginasvideo->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar el video';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasvideo->validationErrors;
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
		if (isset($this->data['Paginasvideo']['id'])&&!empty($this->data['Paginasvideo']['id'])){
			$video=$this->Paginasvideo->findById($this->data['Paginasvideo']['id']);
			if(!empty($video)){
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Paginasvideo->unbindValidation('remove', array('descripcion'), false);
				}
				$this->Paginasvideo->set($this->data);
				
				if ($this->Paginasvideo->validates()){
					if ($this->Paginasvideo->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información del video fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la información del video'; 
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasvideo->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'El video no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		unset($this->params['form']['pagina_id']);
		foreach ($this->params['form'] as $id):
			$ids['Paginasvideo.id'][]=$id;
		endforeach;
		if (!empty($ids['Paginasvideo.id'])){
			$registros = $this->Paginasvideo->find('all',array('conditions'=>$ids));
			if (count($ids['Paginasvideo.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos una de los videos no existe';
			}else{
				foreach($ids['Paginasvideo.id'] as $id):// no deleteall por el afterdelete del acl
					if ($this->Paginasvideo->delete($id)){
						$result['success'] = true;
						$result['message'] = 'Lo videos fueron eliminados';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al eliminar los videos';
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