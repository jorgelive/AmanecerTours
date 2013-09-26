<?php
class PaginasnoticiasController extends AppController {
    var $name = 'Paginasnoticias';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Paginasnoticia','Paginasenlace','Recurso','Pagina');
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
		$this->layout='paginasnoticiaindex';
		$this->set('menuPagina',$this->__menu());
		
		//enlaces
		$enlaces=$this->Paginasenlace->find('all',array('order'=>'Paginasenlace.lft ASC'));
		$this->set('enlaces',$enlaces);
		
		$noticias=$this->Paginasnoticia->find('all',array('order'=>array('Paginasnoticia.fecha DESC'),'recursive'=>0));
		$noticias=$this->__resumen($noticias,'Paginasnoticia.contenido');
		
		if(!empty($noticias)){
			$this->set('noticias',$noticias);
			$this->set('title_for_layout',__('noticias',true));
			if(is_numeric($id)){
				$actual=$this->Paginasnoticia->findById($id);
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
		$noticia = $this->Paginasnoticia->findById($id);
		if(!empty($noticia)){
			unset($noticia['Paginasnoticia']['email']);
			if (empty($noticia['Paginasnoticia']['imagen'])){
				unset($noticia['Paginasnoticia']['imagen']);
			}
			$result['success'] = true;
			$result['data'] = $noticia;
			
		}else{
			$result['success'] = false;
			$result['errors'] = 'No existe la noticia';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function listar(){
		Configure::write('debug', 0);
		if(isset($this->params['form'])){
			$this->paginate=$this->__paginacion($this->params['form']);
			$noticias = $this->paginate();
			if(!empty($noticias)){
				$user = $this->Session->read('Auth.Acluser.username');
				$acciones=array('read','create','update','delete','grant');
				
				foreach($noticias as $key=>$noticia):
					
					foreach($acciones as $accion):
						if($this->Acl->check($user, array('model'=>'Paginasnoticia','foreign_key'=>$noticia['Paginasnoticia']['id']), $accion)){
							$result['noticias'][$key]['permiso']{$accion}=true;
						}else{
							$result['noticias'][$key]['permiso']{$accion}=false;
						}
					endforeach;
					$result['noticias'][$key]['id']=$noticia['Paginasnoticia']['id'];
					$result['noticias'][$key]['title']=$noticia['Paginasnoticia']['title'];
					$result['noticias'][$key]['contenido']=$noticia['Paginasnoticia']['contenido'];
					$result['noticias'][$key]['fecha']=$noticia['Paginasnoticia']['fecha'];
					if (!empty($noticia['Paginasnoticia']['imagen'])){
						$result['noticias'][$key]['imagen']=$noticia['Paginasnoticia']['imagen']['path'];
					}
					$result['noticias'][$key]['borrar_imagen']=0;
					$result['noticias'][$key]['idioma']=Configure::read('Config.language');
				endforeach;
			}else{
				$result['noticias']='';
			}
			$result['total']=$this->params['paging']['Paginasnoticia']['count'];
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
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)) {
			$this->Paginasnoticia->set($this->data);
			if ($this->Paginasnoticia->validates($this->data)) {
				if($this->Paginasnoticia->save($this->data)){
					$user = $this->Session->read('Auth.Acluser.username');
					$this->Acl->allow($user,array('model'=>'Paginasnoticia','foreign_key'=>$this->Paginasnoticia->id),'*');
					$result['success'] = true;
					$result['message'] = 'La noticia fue agregada';
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al guardar la noticia';
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = $this->Paginasnoticia->validationErrors;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			unset($this->data['Paginasnoticia']['imagen']);
			$this->Paginasnoticia->set($this->data);
			if ($this->Paginasnoticia->validates()){
				if ($this->Paginasnoticia->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'La información de la noticia fue modificada';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar la información dla noticia'; 
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->Paginasnoticia->validationErrors;
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
			$this->Paginasnoticia->set($this->data);
			$this->Paginasnoticia->unbindValidation('remove', array('title','contenido','fecha'), false);
			if ($this->Paginasnoticia->save($this->data)){
				$result['success'] = true;
				$result['message'] = 'La imagen de la noticia fue modificada';
			}else{
				$result['success'] = false;
				$result['errors'] = 'Hubo un error al modificar la imagen de la noticia'; 
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$noticia = $this->Paginasnoticia->findById($this->params['form']['id']);
			if (!empty($noticia)){
				if ($this->Paginasnoticia->delete($this->params['form']['id'])){
					$result['success'] = true;
					$result['message'] = 'La noticia fue eliminada';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al eliminar la noticia';
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'El noticia no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
}
?>