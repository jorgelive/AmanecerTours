<?php
class AclgroupsController extends AppController {
    var $name = 'Aclgroups';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Aclgroup','Acluser','Recurso');
	var $helpers = array('Ext');
	
    function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'administracion'=>'read'
				,'getnodes'=>'read'
				,'reparent'=>'update'
				,'validar'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'groupinfo'=>'read'
				,'borrar'=>'delete'
			)
		);
	}

	function administracion(){
		Configure::write('debug', 2);
		$this->set('title_for_layout',$this->__getTitulo($this->modelNames[0]));
	}
	
	function permisosxroot() {
		Configure::write('debug', 0);
		$user = $this->Session->read('Auth.Acluser.username');
		$acciones=array('read','create','update','delete','grant');
		foreach ($acciones as $accion):
			if($this->Acl->check($user, 'Aclgroup::Auto', $accion)){
				$result['permiso'][0]{$accion}=true;
			}else{
				$result['permiso'][0]{$accion}=false;
			}
		endforeach;
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function getnodes() {
		Configure::write('debug', 0);
		$user = $this->Session->read('Auth.Acluser.username');
		$acciones=array('read','create','update','delete','grant');
		foreach ($acciones as $accion):
			if($this->Acl->check($user, 'Aclgroup::Auto', $accion)){
				$permisogroup{$accion}=true;
			}else{
				$permisogroup{$accion}=false;
			}
		endforeach;
		if($permisogroup['create']==false&&$permisogroup['update']==false&&$permisogroup['delete']==false&&$permisogroup['grant']==false){
			$disabledgroup=true;
		}else{
			$disabledgroup=false;	
		}
		
		foreach ($acciones as $accion):
			if($this->Acl->check($user, 'Acluser::Auto', $accion)){
				$permisouser{$accion}=true;
			}else{
				$permisouser{$accion}=false;
			}
		endforeach;
		if($permisouser['create']==false&&$permisouser['update']==false&&$permisouser['delete']==false&&$permisouser['grant']==false){
			$disableduser=true;
		}else{
			$disableduser=false;	
		}
		if (isset($this->params['form']['node'])){
			$parent = $this->params['form']['node'];
			if ($parent=='root'){
				$parent=NULL;
			}else{
				$parent=explode('_',$parent);
				$parent=$parent[1];
			}
			$groups=$this->Aclgroup->children($parent, true);
			$users=$this->Acluser->find('all',array('conditions'=>array('aclgroup_id'=>$parent)));
		}
		$key=0;
		if(isset($groups)&&!empty($groups)){
			foreach ($groups as $group){
				$result{$key}['text'] = $group['Aclgroup']['name'];
				$result{$key}['id'] = 'g_'.$group['Aclgroup']['id'];
				$result{$key}['leaf'] = false;
				$result{$key}['permiso']=$permisogroup;
				$result{$key}['disabled']=$disabledgroup;
				$key++;
			}
		}
		if(isset($users)&&!empty($users)){
			foreach ($users as $user){
				$result{$key}['text'] = $user['Acluser']['name'];
				$result{$key}['id'] = 'u_'.$user['Acluser']['id'];
				$result{$key}['leaf'] = true;
				$result{$key}['permiso']=$permisouser;
				$result{$key}['disabled']=$disableduser;
				$key++;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function reparent(){
		Configure::write('debug', 0);
		if (isset($this->params['form']['node'])&&isset($this->params['form']['parent'])){
			$node = $this->params['form']['node'];
			$parent = $this->params['form']['parent'];
			if ($parent=='root'){
				$parent=NULL;
			}else{
				$parent=explode('_',$parent);
				$parent=$parent[1];
			}
			$node=explode('_',$node);
			$group=$this->Aclgroup->find('first',array('conditions'=>array('Aclgroup.id'=>$node[1])));
			$this->data['Aclgroup']['id'] = $node[1];
			$this->data['Aclgroup']['parent_id'] = $parent;
			$this->data['Aclgroup']['name'] = $group['Aclgroup']['name'];
			if($this->Aclgroup->save($this->data, false)){
				$result['success'] = true;
			}else{
				$result['success'] = false;
    			$result['errors'] = 'Hubo un error al cambiar de grupo';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	} 
	
	function validar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['field'])){
			$result=$this->__validarCampo($this->params['form']['field']);
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
    function agregar($parent_id=NULL) {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			if (isset($this->data['Aclgroup']['parent_id'])&&count(explode('_',$this->data['Aclgroup']['parent_id']))==2){
				$parts=explode('_',$this->data['Aclgroup']['parent_id']);
				$this->data['Aclgroup']['parent_id']=$parts[1];
			}elseif (isset($this->data['Aclgroup']['parent_id'])&&$this->data['Aclgroup']['parent_id']=='root'){
				$this->data['Aclgroup']['parent_id']=NULL;
			}
			$parentexiste=false;
			if($this->data['Aclgroup']['parent_id']!=NULL){
				$parent=$this->Aclgroup->findById($this->data['Aclgroup']['parent_id']);
				if(!empty($parent)){
					$parentexiste=true;	
				}
			}else{
				$parentexiste=true;	
			}
			if($parentexiste===true){
				if(isset($this->data['Aclgroup']['id'])){unset($this->data['Aclgroup']['id']);}
				$this->Aclgroup->set($this->data);
				if ($this->Aclgroup->validates()) {
					if($this->Aclgroup->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El grupo '.$this->data['Aclgroup']['name'].' fue agregado';
						$result['data']['newId'] = 'g_'.$this->Aclgroup->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Aclgroup->validationErrors;
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'No existe el grupo superior';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }

    function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		$group = $this->Aclgroup->findById($this->data['Aclgroup']['id']);
		if (!empty($group)){
			if(isset($this->data['Aclgroup']['parent_id'])){unset($this->data['Aclgroup']['parent_id']);}
			$this->Aclgroup->set($this->data);
			if ($this->Aclgroup->validates()) {
				if ($this->Aclgroup->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'El grupo '.$this->data['Aclgroup']['name'].' fue modificado';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar';
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->Aclgroup->validationErrors;
			}
		}else{
			$result['success'] = false;
			$result['errors'] = 'No existe el grupo';
		}

		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function groupinfo() {
		Configure::write('debug', 0);
		if(isset($this->params['form']['id'])) {
			$id=explode('_',$this->params['form']['id']);
			if (count($id)==2){
				$id=$id[1];
				$group = $this->Aclgroup->findById($id);
				if (empty($group)) {
					$result['success'] = false;
					$result['errors'] = 'Código no existe';
				} else {
					$result['success'] = true;
					$result['data'] = $group;
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }

    function borrar($id=NULL) {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$id=explode('_',$this->params['form']['id']);//u_22
			if (count($id)==2){
				$id=$id[1];
				$group = $this->Aclgroup->findById($id);
				if (empty($group)) {
					$result['success'] = false;
					$result['errors']["reason"] = 'No existe el grupo';
				}else{
					if ($this->Aclgroup->removeFromTree($id,TRUE)) {
						$result['success'] = true;
						$result['message'] = 'El grupo '.$this->data['Aclgroup']['name'].' fue borrado';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Error al borrar '.$user['Aclgroup']['name'];
					}
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
}
?>