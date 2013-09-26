<?php
class PaginasenlacesController extends AppController {
    var $name = 'Paginasenlaces';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Paginasenlace','Recurso');
	var $helpers = array('Ext');
    
	function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('nada');
		$this->__checkAcl(
			array(
				'listar'=>'read'
				,'administracion'=>'read'
				,'validar'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'modificarimagen'=>'update'
				,'borrar'=>'delete'
				,'reorder'=>'update'
			)
			,array('modificarimagen')
		);
	}

	function administracion(){
		Configure::write('debug', 0);
		$this->set('title_for_layout',$this->__getTitulo($this->modelNames[0]));
	}
	
	function listar(){
		Configure::write('debug', 0);
		$enlaces = $this->Paginasenlace->children(NULL, true);
		if(!empty($enlaces)){
			$user = $this->Session->read('Auth.Acluser.username');
			$acciones=array('read','create','update','delete','grant');
			foreach($enlaces as $key=>$enlace):
				foreach($acciones as $accion):
					if($this->Acl->check($user, array('model'=>'Paginasenlace','foreign_key'=>$enlace['Paginasenlace']['id']), $accion)){
						$result['enlaces'][$key]['permiso']{$accion}=true;
					}else{
						$result['enlaces'][$key]['permiso']{$accion}=false;
					}
				endforeach;
				$result['enlaces'][$key]['id']=$enlace['Paginasenlace']['id'];
				$result['enlaces'][$key]['title']=$enlace['Paginasenlace']['title'];
				$result['enlaces'][$key]['externo']=$enlace['Paginasenlace']['externo'];
				$result['enlaces'][$key]['url']=$enlace['Paginasenlace']['url'];
				if (!empty($enlace['Paginasenlace']['imagen'])){
					$result['enlaces'][$key]['imagen']=$enlace['Paginasenlace']['imagen']['path'];
				}
				$result['enlaces'][$key]['borrar_imagen']=0;
				$result['enlaces'][$key]['idioma']=Configure::read('Config.language');
			endforeach;
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
	
	function agregar(){
		Configure::write('debug', 0);
		//al enviar imagen no envia encabezado ajax
		$this->layout='ajax';
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Paginasenlace.externo'));}
		if (!empty($this->data)) {
			$this->Paginasenlace->set($this->data);
			if ($this->Paginasenlace->validates($this->data)) {
				if($this->Paginasenlace->save($this->data)){
					$user = $this->Session->read('Auth.Acluser.username');
					$this->Acl->allow($user,array('model'=>'Paginasenlace','foreign_key'=>$this->Paginasenlace->id),'*');
					$result['success'] = true;
					$result['message'] = 'El enlace fue agregado';
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al guardar el enlace';
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = $this->Paginasenlace->validationErrors;;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			unset($this->data['Paginasenlace']['imagen']);
			if(isset($this->data['Paginasenlace']['borrar_imagen'])&&$this->data['Paginasenlace']['borrar_imagen']=='true'){$this->data['Paginasenlace']['borrar_imagen']=1;}
			if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
				$this->Paginasenlace->unbindValidation('remove', array('title'), false);
			}
			$this->Paginasenlace->set($this->data);
			if ($this->Paginasenlace->validates()){
				if ($this->Paginasenlace->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'La información del enlace fue modificada';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar la información del enlace'; 
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->Paginasenlace->validationErrors;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function modificarimagen() {
		Configure::write('debug', 0);
		//al enviar imagen no envia encabezado ajax
		$this->layout='ajax';
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			$this->Paginasenlace->set($this->data);
			$this->Paginasenlace->unbindValidation('remove', array('title', 'url'), false);
			if ($this->Paginasenlace->save($this->data)){
				$result['success'] = true;
				$result['message'] = 'La imagen del enlace fue modificada';
			}else{
				$result['success'] = false;
				$result['errors'] = 'Hubo un error al modificar la imagen del enlace'; 
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$enlace = $this->Paginasenlace->findById($this->params['form']['id']);
			if (!empty($enlace)){
				if ($this->Paginasenlace->removeFromTree($this->params['form']['id'],TRUE)){
					$result['success'] = true;
					$result['message'] = 'El enlace fue eliminado';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al eliminar el enlace';
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'El enlace no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function reorder() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['nodes'])&&isset($this->params['form']['delta'])){
			$nodes=json_decode($this->params['form']['nodes']);
			if(is_object($nodes)){
				foreach($nodes as $node):
					$delta = intval($this->params['form']['delta']);
					if ($delta > 0) {
						if($this->Paginasenlace->movedown($node, abs($delta))){
							$result['success'] = true;
						}else{
							$result['success'] = false;
							$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
						}
					}elseif($delta < 0) {
						if($this->Paginasenlace->moveup($node, abs($delta))){
							$result['success'] = true;
						}else{
							$result['success'] = false;
							$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
						}
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
					}
				endforeach;
			}
		}else{
			$result['success'] = false;
    		$result['errors'] = 'No se enviaron los datos correctos';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
}
?>