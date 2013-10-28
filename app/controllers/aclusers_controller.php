<?php
class AclusersController extends AppController {
    var $name = 'Aclusers';
	var $components = array('RequestHandler','Acl','Auth');
    
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login','logout');
		$this->__checkAcl(
			array(
				'login'=>'read'
				,'logout'=>'read'
				,'reparent'=>'update'
				,'validar'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'userinfo'=>'read'
				,'borrar'=>'delete'
				,'password'=>'modify'
			)
		);
	}

    function login(){
		Configure::write('debug', 0);
		$this->set('title_for_layout','Ingreso de usuarios'.' - '.Configure::read('Empresa.nombre'));
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)&&isset($this->data['Acluser']['password'])&&isset($this->data['Acluser']['username'])){
			$this->data['Acluser']['password']=$this->Auth->password($this->data['Acluser']['password']);
			if ($this->Auth->login($this->data)) {
            	$result['success'] = true;
				$result['redirect'] = $this->Auth->redirect();
        	} else {
            	$result['success'] = false;
    			$result['errors'] = 'Falló la comprobación, intente nuevamente.';
			}
			///echo $this->Auth->password($this->data['Acluser']['password']).'<br>';
			if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
			$this->render('/elements/ajax');
		}else{
			if($this->Session->check('Auth.Acluser')){
				$this->Session->setFlash("Ya estas registrado y puedes ingresar a la sección administrativa",'/flash-jq-ui',array('class'=>'ok'));
				$this->redirect('/');
			}
		}
		
	}
	
    function logout() {
        Configure::write('debug', 0);
		if(isset($this->RequestHandler)&&$this->RequestHandler->isAjax()){
			if ($this->Session->delete('Auth.Acluser')) {
				$this->Session->delete('Acl');
				$this->Session->delete('Component');
				$result['success'] = true;
				$result['message'] = 'Saliste de la aplicación';
				$this->Session->setFlash("Saliste correctamente",'/flash-jq-ui',array('class'=>'ok'));
				$result['redirect'] = '/';
			} else {
				$result['success'] = false;
				$result['errors'] = 'Ya no existia la sessión';
				$this->Session->setFlash("Saliste con una advertencia",'/flash-jq-ui',array('class'=>'error')); 
				$result['redirect'] = '/';
			}
			if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
			$this->render('/elements/ajax');
		}else{
			$this->Session->setFlash("Saliste correctamente",'/flash-jq-ui',array('class'=>'ok'));
			$this->redirect('/');
		}
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
			$user=$this->Acluser->find('first',array('conditions'=>array('Acluser.id'=>$node[1])));
			$this->data['Acluser']['id'] = $node[1];
			$this->data['Acluser']['aclgroup_id'] = $parent;
			$this->data['Acluser']['username'] = $user['Acluser']['username'];
			if($this->Acluser->save($this->data, false)){
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
		if (isset($this->params['form']['field'])){
			$result=$this->__validarCampo($this->params['form']['field']);
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar() {
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],null,array('Acluser_clear_password','Acluser_confirm_password'));}
		if (!empty($this->data)){
			if (isset($this->data['Acluser']['aclgroup_id'])&&count(explode('_',$this->data['Acluser']['aclgroup_id']))==2){
				$parts=explode('_',$this->data['Acluser']['aclgroup_id']);
				$this->data['Acluser']['aclgroup_id']=$parts[1];
			}elseif (isset($this->data['Acluser']['aclgroup_id'])&&$this->data['Acluser']['aclgroup_id']=='root'){
				$this->data['Acluser']['aclgroup_id']=NULL;
			}
			$aclgroupexiste=false;
			if($this->data['Acluser']['aclgroup_id']!=NULL){
				$aclgroup=$this->Acluser->Aclgroup->findById($this->data['Acluser']['aclgroup_id']);
				if(!empty($aclgroup)){
					$aclgroupexiste=true;	
				}
			}else{
				$aclgroupexiste=true;	
			}
			if($aclgroupexiste===true){
				if(isset($this->data['Acluser']['id'])){unset($this->data['Acluser']['id']);}
				$this->Acluser->set($this->data);
				if ($this->Acluser->validates()) {
					$this->data['Acluser']['password'] = $this->Auth->password($this->data['Acluser']['clear_password']);
					if($this->Acluser->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'El usuario '.$this->data['Acluser']['name'].' fue agregado';
						$result['data']['newId'] = 'u_'.$this->Acluser->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Acluser->validationErrors;
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'No existe el grupo superior';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function modificar(){
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		$user = $this->Acluser->findById($this->data['Acluser']['id']);
		if (!empty($user)){
			if(empty($this->data['Acluser']['clear_password'])&&empty($this->data['Acluser']['confirm_password'])){
				$this->Acluser->unbindValidation('remove', array('clear_password', 'confirm_password'), false);
			}
			if(isset($this->data['Acluser']['aclgroup_id'])){unset($this->data['Acluser']['aclgroup_id']);}
			$this->Acluser->set($this->data);
			if ($this->Acluser->validates()){
				if(!empty($this->data['Acluser']['clear_password'])){
					$this->data['Acluser']['password'] = $this->Auth->password($this->data['Acluser']['clear_password']);
				}
				if ($this->Acluser->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'El usuario '.$this->data['Acluser']['name'].' fue modificado';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar';
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->Acluser->validationErrors;
			}
		}else{
			$result['success'] = false;
			$result['errors'] = 'No existe el usuario';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function userinfo(){
		Configure::write('debug', 0);
		if(isset($this->params['form']['id'])) {
			$id=explode('_',$this->params['form']['id']);
			if (count($id)==2){
				$id=$id[1];
				$user = $this->Acluser->findById($id);
				if (!empty($user)) {
					$result['success'] = true;
					unset($user['Acluser']['password']);
					$result['data'] = $user;
				} else {
					$result['success'] = false;
					$result['errors'] = 'No existe información del usuario';

				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$id=explode('_',$this->params['form']['id']);//u_22
			if (count($id)==2){
				$id=$id[1];
				$user = $this->Acluser->findById($id);
				if (empty($user)) {
					$result['success'] = false;
					$result['errors'] = 'No existe el usuario';
				}else{
					if ($user['Acluser']['id']==$this->Session->read('Auth.Acluser.id')) {
						$result['success'] = false;
						$result['errors'] = 'No te puedes borrar tu solo, pidelo a un administrador';
					}else{
						if ($this->Acluser->delete($id)) {
							$result['success'] = true;
							$result['message'] = 'El usuario '.$user['Acluser']['name'].' fue borrado';
						}else{
							$result['success'] = false;
							$result['errors'] = 'Error al borrar '.$user['Acluser']['name'];
						}
					}
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function password(){
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (isset($this->data['Acluser']['id'])){
			$this->data['Acluser']['id']=explode('_',$this->data['Acluser']['id']);
			if (count($this->data['Acluser']['id'])==2){
				$this->data['Acluser']['id']=$this->data['Acluser']['id'][1];
				$user = $this->Acluser->findById($this->data['Acluser']['id']);
				if (empty($user)) {
					$result['success'] = false;
					$result['errors'] = 'No existe el usuario';
				}else{
					$this->Acluser->unbindValidation('keep', array('clear_password', 'confirm_password'), false);
					$this->Acluser->set($this->data);
					if ($this->Acluser->validates()) {
						$this->data['Acluser']['password'] = $this->Auth->password($this->data['Acluser']['clear_password']);
						if ($this->Acluser->save($this->data)){
							$result['success'] = true;
							$result['message'] = 'La contraseña de '.$user['Acluser']['name'].' fue modificada';
						}else{
							$result['success'] = false;
							$result['errors'] = 'Hubo un error al modificar la contraseña';
						}
					}else{
						$result['success'] = false;
						$result['errors'] = $this->Acluser->validationErrors;
					}
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
}
?>