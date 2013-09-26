<?php

class RecursosController extends AppController {
    var $name = 'Recursos';
	var $components = array('RequestHandler','Acl','Auth');
	var $helpers = array('Ext','Tree');
	
    function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('bar');
		$this->__checkAcl(
			array(
				'index'=>'read'
				,'administracion'=>'read'
				,'validar'=>'read'
				,'getnodes'=>'read'
				,'reorder'=>'read'
				,'reparent'=>'read'
				,'listado'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'recursoinfo'=>'read'
				,'borrar'=>'delete'
				,'bar'=>'read'
			)
		);
	}
	
	function index(){
		Configure::write('debug', 2);
		$this->set('title_for_layout','Recursos');
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
			if($this->Acl->check($user, 'Recurso::Auto', $accion)){
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
			$nodes = $this->Recurso->children($parent, true);
		}
		if (isset($nodes)&&!empty($nodes)){
			foreach ($nodes as $key=>$node){
				if($node['Recurso']['tipo']==4){
					$result{$key}['text']='--------Separador-------';
					$result{$key}['leaf']=true;
				}else{
					$result{$key}['text']=$node['Recurso']['name'];
					$result{$key}['leaf']=false;
				}
				$result{$key}['id']=$node['Recurso']['id'];
				$result{$key}['tipo']=$node['Recurso']['tipo'];
				$result{$key}['model']=$node['Recurso']['model'];
				$result{$key}['accion']=$node['Recurso']['accion'];
				$result{$key}['confirmar_accion']=$node['Recurso']['confirmar_accion'];
				$result{$key}['permiso']=$permiso;
				$result{$key}['disabled']=$disabled;
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
				if($this->Recurso->movedown($node, abs($delta))){
					$result['success'] = true;
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
				}
			}elseif($delta < 0) {
				if($this->Recurso->moveup($node, abs($delta))){
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

			$this->Recurso->id = $node;
			if($this->Recurso->saveField('parent_id', $parent)){
				$recursoActualizado=$this->Recurso->findById($node);
				if(isset($recursoActualizado['Recurso']['tipo'])&&$recursoActualizado['Recurso']['tipo']==1){
					$parentNode=$this->Recurso->findById($recursoActualizado['Recurso']['parent_id']);
					if(!empty($parentNode)&&$parentNode['Recurso']['tipo']==1){
						$parentNode=$this->Acl->Aco->node($parentNode['Recurso']['model'].'::Auto');
						$acodata['parent_id']=Set::extract($parentNode,"0.Aco.id");
					}else{
						$acodata['parent_id']=NULL;
					}
					$node=$this->Acl->Aco->node($recursoActualizado['Recurso']['model'].'::Auto');
					$acodata['id']=Set::extract($node,"0.Aco.id");
					$this->Acl->Aco->save($acodata);
				}
				if ($position == 0) {
					$result['success'] = true;
				}elseif($position > 0){
					$count = $this->Recurso->childcount($parent, true);
					$delta = $count-$position-1;
					if ($delta > 0) {
						if($this->Recurso->moveup($node, $delta)){
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
	
	function validar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['field'])){
			$result=$this->__validarCampo($this->params['form']['field']);
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function listado() {
		Configure::write('debug', 0);
		uses ('folder');
		$folder = &new Folder(MODELS, false);
		$files=$folder->read();
		if(!empty($files)){
			$nro=0;
			foreach ($files[1] as $model):
				$model=explode('.',$model);
				$model=ucfirst(strtolower($model[0]));
				$result['Model'][$nro]['id']=$model;
				$result['Model'][$nro]['model']=$model;
				$nro++;
			endforeach;
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function agregar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Recurso.confirmar_accion'));}
		if (!empty($this->data)) {
			if (isset($this->data['Recurso']['parent_id'])&&$this->data['Recurso']['parent_id']=='root'){
				$this->data['Recurso']['parent_id']=NULL;
			}
			$parentexiste=false;
			if($this->data['Recurso']['parent_id']!=NULL){
				$parent=$this->Recurso->findById($this->data['Recurso']['parent_id']);
				if(!empty($parent)){
					$parentexiste=true;	
				}
			}else{
				$parentexiste=true;	
			}
			if($parentexiste===true){
				if($this->data['Recurso']['tipo']==4){
					$this->data['Recurso']['name']='';
					$this->data['Recurso']['descripcion']='';
					$this->data['Recurso']['model']='';
					$this->data['Recurso']['accion']='';
					$this->data['Recurso']['confirmar_accion']=0;
					$this->Recurso->unbindValidation('remove',array('name','descripcion','model','accion'), false);
				}elseif($this->data['Recurso']['tipo']==2||$this->data['Recurso']['tipo']==3){
					$this->data['Recurso']['model']='';
					$this->Recurso->unbindValidation('remove',array('model'), false);
				}else{
					$this->data['Recurso']['accion']='';
					$this->data['Recurso']['confirmar_accion']=0;
					$this->Recurso->unbindValidation('remove',array('accion','confirmar_accion'), false);
				}
				if(isset($this->data['Recurso']['id'])){unset($this->data['Recurso']['id']);}
				$this->Recurso->set($this->data);
				if ($this->Recurso->validates($this->data)) {
					if($this->Recurso->save($this->data)){
						if($this->data['Recurso']['tipo']==1){
							$node=$this->Acl->Aco->node($this->data['Recurso']['model'].'::Auto');
							if(empty($node)){
								if(!empty($this->data['Recurso']['parent_id'])){
									$parentNode=$this->Recurso->findById($this->data['Recurso']['parent_id']);
								}
								if(!empty($parentNode)&&$parentNode['Recurso']['tipo']==1){
									$parentNode=$this->Acl->Aco->node($parentNode['Recurso']['model'].'::Auto');
									$acodata['parent_id']=Set::extract($parentNode,"0.Aco.id");
								}else{
									$acodata['parent_id']=NULL;
								}
								$acodata['model']=$this->data['Recurso']['model'];
								$acodata['alias']=$this->data['Recurso']['model'].'::Auto';
								$acodata['foreign_key']=NULL;
								$this->Acl->Aco->save($acodata);
							}
						}
						$result['success'] = true;
						$result['message'] = 'El recurso '.$this->data['Recurso']['name'].' fue agregado';
						$result['data']['newId'] = $this->Recurso->id;
					}else{
						$result['success'] = false;
						$result['errors'] = "Error al guardar";
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Recurso->validationErrors;
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
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Recurso.confirmar_accion'));}
		if(!empty($this->data)){
			$recurso=$this->Recurso->findById($this->data['Recurso']['id']);
			if(!empty($recurso)){
				if($this->data['Recurso']['tipo']==4){
					$this->data['Recurso']['name']='';
					$this->data['Recurso']['descripcion']='';
					$this->data['Recurso']['model']='';
					$this->data['Recurso']['accion']='';
					$this->data['Recurso']['confirmar_accion']=0;
					$this->Recurso->unbindValidation('remove',array('name','descripcion','model','accion'), false);
				}elseif($this->data['Recurso']['tipo']==2||$this->data['Recurso']['tipo']==3){
					$this->data['Recurso']['model']='';
					$this->Recurso->unbindValidation('remove',array('model'), false);
				}else{
					$this->data['Recurso']['accion']='';
					$this->data['Recurso']['confirmar_accion']=0;
					$this->Recurso->unbindValidation('remove',array('accion'), false);
				}
				if(isset($this->data['Recurso']['parent_id'])){unset($this->data['Recurso']['parent_id']);}
				$this->Recurso->set($this->data);
				if ($this->Recurso->validates()) {
					if ($this->Recurso->save($this->data)){
						if(($recurso['Recurso']['tipo']==$this->data['Recurso']['tipo']&&$recurso['Recurso']['model']!=$this->data['Recurso']['model']&&$this->data['Recurso']['tipo']==1)||($recurso['Recurso']['tipo']!=$this->data['Recurso']['tipo']&&$this->data['Recurso']['tipo']==1)){
							$node=$this->Acl->Aco->node($recurso['Recurso']['model'].'::Auto');
							if(!empty($node)){
								$node = Set::extract($node,"0.Aco.id");
								if (!empty($node)) {
									$acodata['id']=$node;
								}
							}else{
								$recursoActualizado=$this->Recurso->findById($this->data['Recurso']['id']);
								$parentNode=$this->Recurso->findById($recursoActualizado['Recurso']['parent_id']);
								if(!empty($parentNode)&&$parentNode['Recurso']['tipo']==1){
									$parentNode=$this->Acl->Aco->node($parentNode['Recurso']['model'].'::Auto');
									$acodata['parent_id']=Set::extract($parentNode,"0.Aco.id");
								}else{
									$acodata['parent_id']=NULL;
								}
							}
							$acodata['foreign_key']=NULL;
							$acodata['model']=$this->data['Recurso']['model'];
							$acodata['alias']=$this->data['Recurso']['model'].'::Auto';
							$this->Acl->Aco->save($acodata);
						}elseif($recurso['Recurso']['tipo']!=$this->data['Recurso']['tipo']&&$recurso['Recurso']['tipo']==1){
							$node=$this->Acl->Aco->node($recurso['Recurso']['model'].'::Auto');
							if(!empty($node)){
								$node = Set::extract($node,"0.Aco.id");
								if (!empty($node)) {
									$this->Acl->Aco->delete($node);
								}
							}	
						}
						$result['success'] = true;
						$result['message'] = 'El recurso fue modificado';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar el recurso';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Recurso->validationErrors;
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'El recurso no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	
	function recursoinfo() {
		Configure::write('debug', 0);
		if(isset($this->params['form']['id'])){
			$recurso = $this->Recurso->findById($this->params['form']['id']);
			if (empty($recurso)) {
				$result['success'] = false;
    			$result['errors'] = 'Código de recurso inválido';
			} else {
				$result['success'] = true;
				$result['data'] = $recurso;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function borrar() {
		Configure::write('debug', 0);
		if (isset($this->params['form']['id'])){
			$recurso = $this->Recurso->findById($this->params['form']['id']);
			if (empty($recurso)) {
				$result['success'] = false;
				$result['errors'] = "No selecciono un recurso o el recurso ya esta eliminado.";
			}else{
				if ($this->Recurso->removeFromTree($this->params['form']['id'],TRUE)) {
					$node=$this->Acl->Aco->node($recurso['Recurso']['model'].'::Auto');
					if(!empty($node)){
						$node = Set::extract($node,"0.Aco.id");
						if (!empty($node)) {
							$this->Acl->Aco->delete($node);
						}
					}
					$result['success'] = true;
					$result['message'] = 'El recurso '.$recurso['Recurso']['name'].' fue borrado';
				} else {
					$result['success'] = false;
					$result['errors'] = "Hubo un error al borrar el recurso.";
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function bar() {
		Configure::write('debug', 0);
		$recursos = $this->Recurso->children(NULL);
		if (!$this->Session->check('Component.barra')){
			$variable=$this->__generateTreeeArray($recursos);
			$this->Session->write('Component.barra',$variable);
		}else{
			$variable=$this->Session->read('Component.barra');
		}
		if(!empty($variable)){
			$result['success']=true;
			$result['variable']=$variable;
		}else{
			$result['success']=false;
			$result['errors']='No se genero la variable';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function __generateTreeeArray($lista=array()){
		$rows = array();
		$tree = array();
		$tree_index = array();
		$parent_column = 'parent_id';
		$user = $this->Session->read('Auth.Acluser.username');
		foreach ($lista as $fila):
			
			if($fila{$this->modelNames[0]}['tipo']==1){
				if(!file_exists(VIEWS.strtolower(Inflector::pluralize($fila{$this->modelNames[0]}['model'])).DS.'administracion.ctp')){
					$fila{$this->modelNames[0]}['hidden']='si';
				}else{
					$fila{$this->modelNames[0]}['hidden']='no';
				}
				if($this->Acl->check($user, $fila{$this->modelNames[0]}['model'].'::Auto', 'read')){
					$fila{$this->modelNames[0]}['disabled']='no';
				}else{
					$fila{$this->modelNames[0]}['disabled']='si';
				}
			}else{
				$fila{$this->modelNames[0]}['hidden']='no';
				$fila{$this->modelNames[0]}['disabled']='no';
			}
			$rows{$fila{$this->modelNames[0]}['id']}=$fila{$this->modelNames[0]};
		endforeach;
		
		while(count($rows) > 0){
			foreach($rows as $row_id => $row){
				if($row[$parent_column]){
					if((!array_key_exists($row[$parent_column], $rows)) and (!array_key_exists($row[$parent_column], $tree_index))){
						
					}else{
							if(array_key_exists($row[$parent_column], $tree_index)){
							$parent = & $tree_index[$row[$parent_column]];
							$parent['items'][$row_id] = $row;//array("node" => $row, "items" => array());
							$tree_index[$row_id] = & $parent['items'][$row_id];
							unset($rows[$row_id]);
						}
					}
				}
				else{
					$tree[$row_id] = $row;//array("node" => $row, "items" => array());
					$tree_index[$row_id] = & $tree[$row_id];
					unset($rows[$row_id]);
				}
			}
		}
		unset($tree_index);
		$string='var barraDinamica = new Ext.Toolbar({'."\n";
		
		$partes=array();
		foreach($tree as $nodo){
			$parte=$this->__print_tree($nodo);
			if(!empty($parte)){
				$partes[]=$parte;
			}
		}
		if(count($partes)!=0){
			$string.='items:['."\n";
			$string.=implode(',',$partes);
			$string.=']'."\n";
		}
		$string.='});'."\n";
		return $string;
	}
	
	function __print_tree($nodo,$nivel=1){
		$string='';
		if($nodo['hidden']!='si'){
			if($nodo['tipo']!=4){
				$string.='{'."\n";
				$string.='text:\''.$nodo['name'].'\''."\n";
				if(isset($nodo['items'])){
					if(!empty($nodo['accion'])||$nodo['tipo']==1){
						if($nivel==1){$string.=',xtype:\'tbsplit\''."\n";}
						$accion=true;
					}else{
						if($nivel==1){$string.=',xtype:\'tbbutton\''."\n";}
						$accion=false;
					}
					$partes=array();
					foreach($nodo['items'] as $hijos){
						$nivelNuevo=$nivel+1;
						$parte=$this->__print_tree($hijos,$nivelNuevo);
						if(!empty($parte)){
							$partes[]=$parte;
						}
					}
					if(count($partes)!=0){
						$string.=',menu:['."\n";
						$string.=implode(',',$partes);
						$string.= ']'."\n";
					}
				}else{
					if($nivel==1){$string.=',xtype:\'tbbutton\''."\n";}
					$accion=true;
				}
				if($nodo['disabled']=='si'){
					$string.=',disabled:true'."\n";
				}
				if($accion===true&&$nodo['tipo']==1){
					$accion='window.location = \'/'.strtolower(Inflector::pluralize($nodo['model'])).'/administracion\';';
					$prepend_confirmation='Esta seguro que desea ir a: ';
				}elseif($accion===true&&$nodo['tipo']==2){
					$accion='window.location = \''.$nodo['accion'].'\';';
					$prepend_confirmation='Esta seguro que desea ir a:  ';
				}elseif($accion===true&&$nodo['tipo']==3){
					$accion=$nodo['accion'];
					$prepend_confirmation='Esta seguro que desea realizar:  ';
				}
				if(!empty($accion)&&$nodo['confirmar_accion']==1){
					$string.=',handler:function(){Ext.MessageBox.confirm(\'Confirme\', \''.$prepend_confirmation.$nodo['descripcion'].'?\',function(btn) {if (btn == \'yes\'){'.$accion.'}});}'."\n";
				}elseif(!empty($accion)&&$nodo['confirmar_accion']==0){
					$string.=',handler:function(){'.$accion.'}'."\n";
				}
				$string.='}'."\n";
			}else{
				if(empty($nodo['parent_id'])){
					$string.='new Ext.Toolbar.Fill()'."\n";
				}else{
					$string.='\'-\''."\n";
				}
			}
		}
		return $string;
	}
}
?>