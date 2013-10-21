<?php
class PaginascabecerasController extends AppController {
    var $name = 'Paginascabeceras';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Paginascabecera','Recurso');
	var $helpers = array('Ext');
    
	function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('listarxml');
		$this->__checkAcl(
			array(
				'listar'=>'read'
				,'listarxml'=>'read'
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
		$cabeceras = $this->Paginascabecera->children(NULL, true);
		if(!empty($cabeceras)){
			$user = $this->Session->read('Auth.Acluser.username');
			$acciones=array('read','create','update','delete','grant');
			foreach($cabeceras as $key=>$cabecera):
				foreach($acciones as $accion):
					if($this->Acl->check($user, array('model'=>'Paginascabecera','foreign_key'=>$cabecera['Paginascabecera']['id']), $accion)){
						$result['cabeceras'][$key]['permiso']{$accion}=true;
					}else{
						$result['cabeceras'][$key]['permiso']{$accion}=false;
					}
				endforeach;
				$result['cabeceras'][$key]['id']=$cabecera['Paginascabecera']['id'];
				$result['cabeceras'][$key]['title']=$cabecera['Paginascabecera']['title'];
				$result['cabeceras'][$key]['texto']=$cabecera['Paginascabecera']['texto'];
				$result['cabeceras'][$key]['tiempo']=$cabecera['Paginascabecera']['tiempo'];
				$result['cabeceras'][$key]['url']=$cabecera['Paginascabecera']['url'];
				$result['cabeceras'][$key]['externo']=$cabecera['Paginascabecera']['externo'];
				if (!empty($cabecera['Paginascabecera']['imagen'])){
					$result['cabeceras'][$key]['imagen']=$cabecera['Paginascabecera']['imagen']['path'];
				}
				$result['cabeceras'][$key]['idioma']=Configure::read('Config.language');
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
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Paginascabecera.externo'));}
		if (!empty($this->data)) {
			$this->Paginascabecera->set($this->data);
			if ($this->Paginascabecera->validates($this->data)) {
				if($this->Paginascabecera->save($this->data)){
					$user = $this->Session->read('Auth.Acluser.username');
					$this->Acl->allow($user,array('model'=>'Paginascabecera','foreign_key'=>$this->Paginascabecera->id),'*');
					$result['success'] = true;
					$result['message'] = 'La cabecera fue agregada';
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al guardar la cabecera';
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = $this->Paginascabecera->validationErrors;;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form']);}
		if (!empty($this->data)){
			unset($this->data['Paginascabecera']['imagen']);
			if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
				$this->Paginascabecera->unbindValidation('remove', array('imagen','title','texto'), false);
			}
            unset($this->data['Paginascabecera']['imagen']);
            $this->Paginascabecera->unbindValidation('remove', array('imagen'), false);
			$this->Paginascabecera->set($this->data);
			if ($this->Paginascabecera->validates()){
				if ($this->Paginascabecera->save($this->data)){
					$result['success'] = true;
					$result['message'] = 'La información de la cabecera fue modificada';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al modificar la información de la cabecera'; 
				}
			}else{
				$result['success'] = false;
				$result['errors'] = $this->Paginascabecera->validationErrors;
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
			$this->Paginascabecera->set($this->data);
			$this->Paginascabecera->unbindValidation('remove', array('title','texto','tiempo','url','externo'), false);
			if ($this->Paginascabecera->save($this->data)){
				$result['success'] = true;
				$result['message'] = 'La imagen de la cabecera fue modificada';
			}else{
				$result['success'] = false;
				$result['errors'] = 'Hubo un error al modificar la imagen de la cabecera'; 
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$cabecera = $this->Paginascabecera->findById($this->params['form']['id']);
			if (!empty($cabecera)){
				if ($this->Paginascabecera->removeFromTree($this->params['form']['id'],TRUE)){
					$result['success'] = true;
					$result['message'] = 'La cabecera fue eliminado';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Hubo un error al eliminar la cabecera';
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'La cabecera no existe';
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
						if($this->Paginascabecera->movedown($node, abs($delta))){
							$result['success'] = true;
						}else{
							$result['success'] = false;
							$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
						}
					}elseif($delta < 0) {
						if($this->Paginascabecera->moveup($node, abs($delta))){
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
	
	function listarxml(){
		$this->layout = false;
		Configure::write('debug', 0);
		$cabeceras = $this->Paginascabecera->children(NULL, true);
		if(!empty($cabeceras)){
			$user = $this->Session->read('Auth.Acluser.username');
			$acciones=array('read','create','update','delete','grant');
			foreach($cabeceras as $key=>$cabecera):
				$result[$key]['path']=$cabecera['Paginascabecera']['imagen']['path'];
				$result[$key]['title']=$cabecera['Paginascabecera']['title'];
				$result[$key]['caption']=$cabecera['Paginascabecera']['texto'];
				if(!empty($cabecera['Paginascabecera']['url'])){
					if(!empty($cabecera['Paginascabecera']['externo'])){
						$result[$key]['linl']='http://'.$cabecera['Paginascabecera']['url'];
						$result[$key]['target']='_blank';
					}else{
						$cabecera['Paginascabecera']['url']=explode(':',$cabecera['Paginascabecera']['url'],3);
						$reversed=array_reverse($cabecera['Paginascabecera']['url']);
						if(is_numeric($reversed[0])){
							if(isset($cabecera['Paginascabecera']['url'][2])){
								$result[$key]['link']='/'.$cabecera['Paginascabecera']['url'][0].'/'.$cabecera['Paginascabecera'][1].'/'.$cabecera['Paginascabecera'][2].'/idioma:'.Configure::read('Config.language');
							}elseif(isset($cabecera['Paginascabecera']['url'][1])){
								$result[$key]['link']='/paginas/'.$cabecera['Paginascabecera']['url'][0].'/'.$cabecera['Paginascabecera']['url'][1].'/idioma:'.Configure::read('Config.language');
							}else{
								$result[$key]['link']='/paginas/detalle/'.$cabecera['Paginascabecera']['url'][0].'/idioma:'.Configure::read('Config.language');
							}
						}else{
							$result[$key]['link']='/'.implode('/',$cabecera['Paginascabecera']['url']);
						}
						$result[$key]['target']='_self';
					}
				}else{
					$result[$key]['link']='';
					$result[$key]['target']='';
				}
				$result[$key]['slideshowTime']=$cabecera['Paginascabecera']['tiempo'];
				$result[$key]['bar_color']='0xffffff';
				$result[$key]['bar_transparency']=40;
				$result[$key]['caption_color']='0xffffff';
				$result[$key]['caption_transparency']=60;
				$result[$key]['stroke_color']='0xffffff';
				$result[$key]['stroke_transparency']=60;
			endforeach;
			$this->RequestHandler->respondAs('xml');
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
	}
}
?>