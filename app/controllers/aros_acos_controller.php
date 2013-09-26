<?php

class ArosAcosController extends AppController {
    var $name = 'ArosAcos';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('ArosAco');
	
    function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'agregarpermisos'=>'grant'
				,'modificarpermisos'=>'grant'
				,'borrarpermisos'=>'grant'
			)
		);
	}
	
	function agregarpermisos() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['foreign_key'])&&!empty($this->params['form']['foreign_key'])&&isset($this->params['form']['caller'])&&!empty($this->params['form']['caller'])) {
			
			if($this->params['form']['foreign_key']!='root'){
				$node=$this->Acl->Aco->node(array('model'=>$this->params['form']['caller'],'foreign_key'=>$this->params['form']['foreign_key']));
			}else{
				$node=$this->Acl->Aco->node($this->params['form']['caller'].'::Auto');
			}
			unset($this->params['form']['foreign_key']);
			unset($this->params['form']['caller']);
			$this->data=$this->__paramstodata($this->params['form']);
			$this->data['ArosAco']['aco_id']=$node[0]['Aco']['id'];
			$this->ArosAco->set($this->data);
			if ($this->ArosAco->validates()){
				if ($this->ArosAco->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'El permiso fue agregado';
					$result['data']['newId'] = $this->ArosAco->id;
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al agregar el permiso'; 
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->ArosAco->validationErrors;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function modificarpermisos() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['foreign_key'])&&!empty($this->params['form']['foreign_key'])&&isset($this->params['form']['caller'])&&!empty($this->params['form']['caller'])) {
			if($this->params['form']['foreign_key']!='root'){
				$node=$this->Acl->Aco->node(array('model'=>$this->params['form']['caller'],'foreign_key'=>$this->params['form']['foreign_key']));
			}else{
				$node=$this->Acl->Aco->node($this->params['form']['caller'].'::Auto');
			}
			unset($this->params['form']['foreign_key']);
			unset($this->params['form']['caller']);
			$this->data=$this->__paramstodata($this->params['form']);
			$this->data['ArosAco']['aco_id']=$node[0]['Aco']['id'];
			$this->ArosAco->set($this->data);
			if ($this->ArosAco->validates()){
				if ($this->ArosAco->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'El permiso fue modificado';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar el permiso'; 
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->ArosAco->validationErrors;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrarpermisos() {
		Configure::write('debug', 0);
		unset($this->params['form']['caller']);
		unset($this->params['form']['foreign_key']);
		if(isset($this->params['form']['administrador'])){
			unset($this->params['form']['administrador']);
		}
		foreach ($this->params['form'] as $id):
			$ids['ArosAco.id'][]=$id;
		endforeach;
		if (!empty($ids['ArosAco.id'])) {
			$registros = $this->ArosAco->find('all',array('conditions'=>$ids));
			if (count($ids['ArosAco.id'])!=count($registros)) {
				$result['success'] = false;
				$result['errors'] = 'Al menos uno de los permisos no existe';
			}else{
				if ($this->ArosAco->deleteAll($ids)) {
					$result['success'] = true;
					$result['message'] = 'Los permisos fueron eliminados';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al eliminar los permisos';
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
}
?>