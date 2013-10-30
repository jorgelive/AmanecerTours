<?php
class PaginasmultiplesController extends AppController{
	var $name='Paginasmultiples';
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
			$multiples = $this->paginate();
			if(!empty($multiples)){
				foreach($multiples as $key=>$multiple):
					$result['multiples'][$key]['id']=$multiple['Paginasmultiple']['id'];
					$result['multiples'][$key]['title']=$multiple['Paginasmultiple']['title'];
					$result['multiples'][$key]['pagina_id']=$multiple['Paginasmultiple']['pagina_id'];
					$result['multiples'][$key]['contenido']=$multiple['Paginasmultiple']['contenido'];
					$result['multiples'][$key]['orden']=$multiple['Paginasmultiple']['orden'];
					$result['multiples'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['multiples']='';
			}
			$result['total']=$this->params['paging']['Paginasmultiple']['count'];
			$result['success']=true;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		//al enviar multiple no envia encabezado ajax
		$this->layout='ajax';
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			$pagina=$this->Paginasmultiple->Pagina->findById($this->data['Paginasmultiple']['pagina_id']);
			if(!empty($pagina)){
				$this->Paginasmultiple->set($this->data);
				if ($this->Paginasmultiple->validates($this->data)) {
					if($this->Paginasmultiple->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El texto múltiple fue agregado';
                        $result['data']['newId'] = $this->Paginasmultiple->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar el texto multiple';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasmultiple->validationErrors;
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
			$multiple=$this->Paginasmultiple->findById($this->data['Paginasmultiple']['id']);
			if(!empty($multiple)){
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Paginasmultiple->unbindValidation('remove', array('title'), false);
				}
				unset($this->data['Paginasmultiple']['multiple']);
				$this->Paginasmultiple->unbindValidation('remove', array('multiple'), false);
				$this->Paginasmultiple->set($this->data);
				if ($this->Paginasmultiple->validates()){
					if ($this->Paginasmultiple->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información del texto múltiple fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la información del texto múltiple'; 
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasmultiple->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'El texto múltiple no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		unset($this->params['form']['pagina_id']);
		foreach ($this->params['form'] as $id):
			$ids['Paginasmultiple.id'][]=$id;
		endforeach;
		if (!empty($ids['Paginasmultiple.id'])){
			$registros = $this->Paginasmultiple->find('all',array('conditions'=>$ids));
			if (count($ids['Paginasmultiple.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos uno de los textos múltiples no existe';
			}else{
				foreach($ids['Paginasmultiple.id'] as $id):// no deleteall por el afterdelete del acl
					if ($this->Paginasmultiple->delete($id)){
						$result['success'] = true;
						$result['message'] = 'Los textos múltiples fueron eliminados';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al eliminar los texto múltiples';
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