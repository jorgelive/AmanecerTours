<?php

class AcosController extends AppController {
    var $name = 'Acos';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Aco','ArosAco','Aro','Recurso');
	var $helpers = array('Ext');
	
    function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'administracion'=>'read'
				,'getnodes'=>'read'
				,'reorder'=>'read'
				,'reparent'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'acoinfo'=>'read'
				,'borrar'=>'delete'
				,'permisosxmodel'=>'grant'
				,'permisosxroot'=>'read'
			)
		);
	}
	
	function administracion(){
		Configure::write('debug', 2);
		$this->set('title_for_layout',$this->__getTitulo($this->modelNames[0]));
	}
	
	function getnodes() {
		Configure::write('debug', 0);
		$user = $this->Session->read('Auth.Acluser.username');
		$acciones=array('read','create','update','delete','grant');
		foreach ($acciones as $accion):
			if($this->Acl->check($user, 'Aco::Auto', $accion)){
				$permiso{$accion}=true;
			}else{
				$permiso{$accion}=false;
			}
		endforeach;
		if($permiso['create']==false&&$permiso['update']==false&&$permiso['delete']==false&&$permiso['grant']==false){
			$disabled=true;
		}else{
			$disabled=false;	
		}
		if (isset($this->params['form']['node'])){
			$parent = $this->params['form']['node'];
			if ($parent=='root'){$parent=NULL;}
			$nodes = $this->Aco->children($parent, true);
		}
		if (isset($nodes)&&!empty($nodes)){
			foreach ($nodes as $key=>$node){
				$result[$key]['text'] = $node['Aco']['alias'];
				$result[$key]['id'] = $node['Aco']['id'];
				$result[$key]['leaf'] = false;
				$result{$key}['permiso']=$permiso;
				$result{$key}['disabled']=$disabled;
				$result{$key}['model']=$node['Aco']['model'];
				if($node['Aco']['foreign_key']===NULL){$node['Aco']['foreign_key']='root';}
				$result{$key}['foreign_key']=$node['Aco']['foreign_key'];
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function reorder() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['node'])&&isset($this->params['form']['delta'])){
			$node = intval($this->params['form']['node']);
			$delta = intval($this->params['form']['delta']);
			if ($delta > 0) {
				if($this->Aco->movedown($node, abs($delta))){
					$result['success'] = true;
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
				}
			}elseif($delta < 0) {
				if($this->Aco->moveup($node, abs($delta))){
					$result['success'] = true;
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
			}
			
		}else{
			$result['success'] = false;
    		$result['errors'] = 'No se enviaron los datos correctos';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function reparent(){
		Configure::write('debug', 0);
		if (isset($this->params['form']['node'])&&isset($this->params['form']['parent'])&&isset($this->params['form']['position'])){
			$node = intval($this->params['form']['node']);
			$parent = intval($this->params['form']['parent']);
			$position = intval($this->params['form']['position']);
			$this->Aco->id = $node;
			if($this->Aco->saveField('parent_id', $parent)){
				if ($position == 0) {
					$result['success'] = true;
				}elseif($position > 0){
					$count = $this->Aco->childcount($parent, true);
					$delta = $count-$position-1;
					if ($delta > 0) {
						if($this->Aco->moveup($node, $delta)){
							$result['success'] = true;
						}else{
							$result['success'] = false;
							$result['errors'] = 'Hubo un error al cambiar posicion dentro del nuevo nodo';
						}
					}else{
						$result['success'] = true;
					}
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = 'Hubo un error al cambiar de nodo superior';
			}
			
		}else{
			$result['success'] = false;
    		$result['errors'] = 'No se enviaron los datos correctos';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}	
		$this->render('/elements/ajax');	
	}
	
	function agregar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			if (isset($this->data['Aco']['parent_id'])&&$this->data['Aco']['parent_id']=='root'){
				$this->data['Aco']['parent_id']=NULL;
			}
			$parentexiste=false;
			if($this->data['Aco']['parent_id']!=NULL){
				$parent=$this->Aco->findById($this->data['Aco']['parent_id']);
				if(!empty($parent)){
					$parentexiste=true;	
				}
			}else{
				$parentexiste=true;	
			}
			if($parentexiste===true){
				if(isset($this->data['Aco']['id'])){unset($this->data['Aco']['id']);}
				if (!empty($this->data['Aco']['model'])&&!empty($this->data['Aco']['alias'])) {
					$this->data['Aco']['model']=ucfirst(strtolower($this->data['Aco']['model']));
					if($this->Aco->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El recurso '.$this->data['Aco']['alias'].' fue agregado';
						$result['data']['newId'] = $this->Aco->id;
					}else{
						$result['success'] = false;
						$result['errors'] = "Error al guardar";
					}
				}else{
					$result['success'] = false;
					$result['errors'] = "Error en la validación";
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'No existe el recurso superior';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if(!empty($this->data)){
			$aco=$this->Aco->findById($this->data['Aco']['id']);
			if(!empty($aco)){
				if(isset($this->data['Aco']['parent_id'])){unset($this->data['Aco']['parent_id']);}
				if (!empty($this->data['Aco']['model'])&&!empty($this->data['Aco']['alias'])) {
					if ($this->Aco->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El recurso fue modificado';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar el recurso';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = "Error en la validación";
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'El recurso no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	
	function acoinfo() {
		Configure::write('debug', 0);
		if(isset($this->params['form']['id'])){
			$aco = $this->Aco->findById($this->params['form']['id']);
			if (empty($aco)) {
				$result['success'] = false;
    			$result['errors'] = 'Código de recurso inválido';
			} else {
				$result['success'] = true;
				$result['data'] = $aco;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$aco = $this->Aco->findById($this->params['form']['id']);
			if (empty($aco)) {
				$result['success'] = false;
				$result['errors'] = "No selecciono un recurso o el recurso ya esta eliminado.";
			}else{
				if ($this->Aco->delete($this->params['form']['id'])) {
					$result['success'] = true;
					$result['message'] = 'El recurso '.$aco['Aco']['alias'].' fue borrado';
				} else {
					$result['success'] = false;
					$result['errors'] = "Hubo un error al borrar el recurso.";
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function permisosxmodel($model=NULL,$foreign_key=NULL) {
		Configure::write('debug', 0);
		if(isset($this->params['form']['caller'])&&isset($this->params['form']['foreign_key'])){
			if($this->params['form']['foreign_key']!='root'){
				$node = $this->Aco->find('first',array('conditions'=>array('model'=>$this->params['form']['caller'],'foreign_key'=>$this->params['form']['foreign_key'])));
			}else{
				$node = $this->Aco->find('first',array('conditions'=>array('alias'=>$this->params['form']['caller'].'::Auto')));
			}
		}
		if (!empty($node)){
			foreach ($node['Aro'] as $nro=>$aro):
				if(isset($this->params['form']['administrador'])&&!empty($this->params['form']['administrador'])){
					$result['node'][$nro]['administrador']=$this->params['form']['administrador'];
				}
				$result['node'][$nro]['caller']=$node['Aco']['model'];
				if($node['Aco']['foreign_key']===NULL){$node['Aco']['foreign_key']='root';}
				$result['node'][$nro]['foreign_key']=$node['Aco']['foreign_key'];
				$result['node'][$nro]['id']=$aro['Permission']['id'];
				$result['node'][$nro]['aro_id']=$aro['Permission']['aro_id'];
				$result['node'][$nro]['_read']=$aro['Permission']['_read'];
				$result['node'][$nro]['_create']=$aro['Permission']['_create'];
				$result['node'][$nro]['_update']=$aro['Permission']['_update'];
				$result['node'][$nro]['_delete']=$aro['Permission']['_delete'];
				$result['node'][$nro]['_grant']=$aro['Permission']['_grant'];
			endforeach;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function permisosxroot() {
		Configure::write('debug', 0);
		if(isset($this->params['form']['caller'])){
			$user = $this->Session->read('Auth.Acluser.username');
			$acciones=array('read','create','update','delete','grant');
			foreach ($acciones as $accion):
				if($this->Acl->check($user, $this->params['form']['caller'].'::Auto', $accion)){
					$result['permiso'][0]{$accion}=true;
				}else{
					$result['permiso'][0]{$accion}=false;
				}
			endforeach;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
}
?>