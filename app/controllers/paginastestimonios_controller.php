<?php
class PaginastestimoniosController extends AppController {
    var $name = 'Paginastestimonios';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Paginastestimonio','Paginasenlace','Recurso','Pagina');
	var $helpers = array('Ext','Tree');
    
	function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('index','detalle');
		$this->__checkAcl(
			array(
				'listar'=>'read'
				,'administracion'=>'read'
				,'index'=>'read'
				,'detalle'=>'read'
				,'validar'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'modificarimagen'=>'update'
				,'borrar'=>'delete'
			)
			,array('modificarimagen')
		);
	}

	function administracion(){
		Configure::write('debug', 0);
		$this->set('title_for_layout',$this->__getTitulo($this->modelNames[0]));
	}
	
	function index($id=NULL){
		Configure::write('debug', 0);
		$this->layout='paginastestimonioindex';
		$this->set('menuPagina',$this->__menu());
		
		//enlaces
		$enlaces=$this->Paginasenlace->find('all',array('order'=>'Paginasenlace.lft ASC'));
		$this->set('enlaces',$enlaces);
		
		$testimonios=$this->Paginastestimonio->find('all',array('order'=>array('Paginastestimonio.fecha DESC'),'recursive'=>0));
		$testimonios=$this->__resumen($testimonios,'Paginastestimonio.contenido');
		
		if(!empty($testimonios)){
			$this->set('testimonios',$testimonios);
			$this->set('title_for_layout',__('testimonios',true));
			if(is_numeric($id)){
				$actual=$this->Paginastestimonio->findById($id);
				if(!empty($actual)){
					$this->set('actual',$id);
				}
				
			}
		}else{
			$this->redirect(array('controller'=>'paginas','action'=>'index'));
		}
	}
	
	function detalle($id=NULL){
		Configure::write('debug', 0);
		$testimonio = $this->Paginastestimonio->findById($id);
		if(!empty($testimonio)){
			unset($testimonio['Paginastestimonio']['email']);
			if (empty($testimonio['Paginastestimonio']['imagen'])){
				unset($testimonio['Paginastestimonio']['imagen']);
			}
			$result['success'] = true;
			$result['data'] = $testimonio;
			
		}else{
			$result['success'] = false;
			$result['errors'] = 'No existe el testimonio';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function listar(){
		Configure::write('debug', 0);
		if(isset($this->params['form'])){
			$this->paginate=$this->__paginacion($this->params['form']);
			$testimonios = $this->paginate();
			if(!empty($testimonios)){
				$user = $this->Session->read('Auth.Acluser.username');
				$acciones=array('read','create','update','delete','grant');
				foreach($testimonios as $key=>$testimonio):
					foreach($acciones as $accion):
						if($this->Acl->check($user, array('model'=>'Paginastestimonio','foreign_key'=>$testimonio['Paginastestimonio']['id']), $accion)){
							$result['testimonios'][$key]['permiso']{$accion}=true;
						}else{
							$result['testimonios'][$key]['permiso']{$accion}=false;
						}
					endforeach;
					$result['testimonios'][$key]['id']=$testimonio['Paginastestimonio']['id'];
					$result['testimonios'][$key]['name']=$testimonio['Paginastestimonio']['name'];
					$result['testimonios'][$key]['nacionalidad']=$testimonio['Paginastestimonio']['nacionalidad'];
					$result['testimonios'][$key]['email']=$testimonio['Paginastestimonio']['email'];
					$result['testimonios'][$key]['contenido']=$testimonio['Paginastestimonio']['contenido'];
					$result['testimonios'][$key]['fecha']=$testimonio['Paginastestimonio']['fecha'];
					if (!empty($testimonio['Paginastestimonio']['imagen'])){
						$result['testimonios'][$key]['imagen']=$testimonio['Paginastestimonio']['imagen']['path'];
					}
					$result['testimonios'][$key]['borrar_imagen']=0;
				endforeach;
			}else{
				$result['testimonios']='';
			}
			$result['total']=$this->params['paging']['Paginastestimonio']['count'];
			$result['success']=true;
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
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Paginastestimonio.externo'));}
		if (!empty($this->data)) {
			$this->Paginastestimonio->set($this->data);
			if ($this->Paginastestimonio->validates($this->data)) {
				if($this->Paginastestimonio->save($this->data)){
					$user = $this->Session->read('Auth.Acluser.username');
					$this->Acl->allow($user,array('model'=>'Paginastestimonio','foreign_key'=>$this->Paginastestimonio->id),'*');
					$result['success'] = true;
					$result['message'] = 'El testimonio fue agregado';
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al guardar el testimonio';
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = $this->Paginastestimonio->validationErrors;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			unset($this->data['Paginastestimonio']['imagen']);
			$this->Paginastestimonio->set($this->data);
			if ($this->Paginastestimonio->validates()){
				if ($this->Paginastestimonio->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'La información del testimonio fue modificada';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar la información del testimonio'; 
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->Paginastestimonio->validationErrors;
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
			$this->Paginastestimonio->set($this->data);
			$this->Paginastestimonio->unbindValidation('remove', array('name','contenido','nacionalidad','email','fecha'), false);
			if ($this->Paginastestimonio->save($this->data)){
				$result['success'] = true;
				$result['message'] = 'La imagen del testimonio fue modificada';
			}else{
				$result['success'] = false;
				$result['errors'] = 'Hubo un error al modificar la imagen del testimonio'; 
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$testimonio = $this->Paginastestimonio->findById($this->params['form']['id']);
			if (!empty($testimonio)){
				if ($this->Paginastestimonio->delete($this->params['form']['id'])){
					$result['success'] = true;
					$result['message'] = 'El testimonio fue eliminado';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al eliminar el testimonio';
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'El testimonio no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
}
?>