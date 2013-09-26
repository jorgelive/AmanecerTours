<?php
class PaginasofertasController extends AppController{
	var $name='Paginasofertas';
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
			$ofertas = $this->paginate();
			
			if(!empty($ofertas)){
				foreach($ofertas as $key=>$oferta):
					$result['ofertas'][$key]['id']=$oferta['Paginasoferta']['id'];
					$result['ofertas'][$key]['title']=$oferta['Paginasoferta']['title'];
					if($oferta['Paginasoferta']['inicio']!='0000-00-00'){
						$result['ofertas'][$key]['inicio']=$oferta['Paginasoferta']['inicio'];
					}else{
						$result['ofertas'][$key]['inicio']='';
					}
					if($oferta['Paginasoferta']['final']!='0000-00-00'){
						$result['ofertas'][$key]['final']=$oferta['Paginasoferta']['final'];
					}else{
						$result['ofertas'][$key]['final']='';
					}
					$result['ofertas'][$key]['notas']=$oferta['Paginasoferta']['notas'];
					$result['ofertas'][$key]['condiciones']=$oferta['Paginasoferta']['condiciones'];
					$result['ofertas'][$key]['precio']=$oferta['Paginasoferta']['precio'];
					$result['ofertas'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['ofertas']='';
			}
			$result['total']=$this->params['paging']['Paginasoferta']['count'];
			$result['success']=true;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (isset($this->data['Paginasoferta']['pagina_id'])&&!empty($this->data['Paginasoferta']['pagina_id'])) {
			$pagina=$this->Paginasoferta->Pagina->findById($this->data['Paginasoferta']['pagina_id']);
			if(!empty($pagina)){
				$this->Paginasoferta->set($this->data);
				if ($this->Paginasoferta->validates($this->data)) {
					if($this->Paginasoferta->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La oferta fue agregada';
						$result['data']['newId'] = $this->Paginasoferta->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar la oferta';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasoferta->validationErrors;
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
		if (isset($this->data['Paginasoferta']['id'])&&!empty($this->data['Paginasoferta']['id'])){
			$oferta=$this->Paginasoferta->findById($this->data['Paginasoferta']['id']);
			if(!empty($oferta)){
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Paginasoferta->unbindValidation('remove', array('notas','condiciones'), false);
				}
				$this->Paginasoferta->set($this->data);
				
				if ($this->Paginasoferta->validates()){
					if ($this->Paginasoferta->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La información de la oferta fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la información de la oferta'; 
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Paginasoferta->validationErrors;
				}
			}else{
				$result["success"] = false;
				$result["errors"] = 'La oferta no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		unset($this->params['form']['pagina_id']);
		foreach ($this->params['form'] as $id):
			$ids['Paginasoferta.id'][]=$id;
		endforeach;
		if (!empty($ids['Paginasoferta.id'])){
			$registros = $this->Paginasoferta->find('all',array('conditions'=>$ids));
			if (count($ids['Paginasoferta.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos una de las ofertas no existe';
			}else{
				foreach($ids['Paginasoferta.id'] as $id):// no deleteall por el afterdelete del acl
					if ($this->Paginasoferta->delete($id)){
						$result['success'] = true;
						$result['message'] = 'Las ofertas fueron eliminadas';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al eliminar las ofertas';
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