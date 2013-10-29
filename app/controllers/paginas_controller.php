<?php
class PaginasController extends AppController {
    var $name = 'Paginas';
	var $components = array('RequestHandler','SessionAcl','Auth');
	var $uses = array('Pagina','Recurso','Paginasenlace','Paginasnoticia','Paginastestimonio');
	var $helpers = array('Ext','Tree','Session');
    
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index','detalle');
		$this->__checkAcl(
			array(
				'index'=>'read'
				,'detalle'=>'read'
				,'paginainfo'=>'read'
				,'administracion'=>'read'
				,'getnodes'=>'read'
				,'reorder'=>'update'
				,'reparent'=>'update'
				,'validar'=>'read'
				,'listadofotos'=>'read'
				,'listadotipos'=>'read'
				,'agregar'=>'create'
				,'modificar'=>'update'
				,'borrar'=>'delete'
			)
		);
	}


	function index($id=NULL){

        $this->layout='pagina';

//echo '<br>'.'accion: inicio'.'<br>';

        $start=$this->Pagina->find('first',array('conditions' => array('Pagina.publicado' => 1),'recursive'=>-1,'order'=>array('Pagina.lft ASC')));

		if(empty($id)||(isset($id)&&$id==$start['Pagina']['id'])){
            $id=$start['Pagina']['id'];
            $isStart=true;
        }

//echo '<br>'.'accion: pagina'.'<br>';
        $pagina = $this->Pagina->findById($id);
        if (!empty($pagina)){

            $pagina = $this->__resumen($pagina,array('Paginastexto.contenido'=>'Paginastexto.resumen','Paginasmultiple.contenido'=>'Paginasmultiple.resumen'));
            $pagina = $this->__thumbImages($pagina,'Paginastexto.contenido',true);
            $pagina = $this->__comprobarImagenPath($pagina);


//echo '<br>'.'accion: menu principal'.'<br>';
            $menuPagina=$this->Pagina->children(NULL, true);
            foreach ($menuPagina as $key=>$item):
                $children=$this->Pagina->children($item['Pagina']['id']);
                $menuPagina[$key]['children']=$children;
            endforeach;

            $this->set(
                'menuPrincipal',array(
                    'items'=>array(
                        array(
                            'contenido'=>$menuPagina
                        ,'settings'=>array(
                            'tipo'=>'extractTree'
                        ,'controlador'=>'Paginas'
                        ,'accion'=>'index'
                        ,'textField'=>'title'
                        )
                        )
                    )
                ,'current'=>array('Paginas'=>$pagina{'Pagina'}{'ancestor'})
                )
            );

//echo '<br>'.'accion: menu inferior'.'<br>';
            $this->Pagina->unBindModel(array('hasMany' => array('Paginaspromocion','Paginasmultiple','Paginasimagen','Paginasvideo','Paginasadjunto'),'hasOne'=>array('Paginastexto','Paginasopcional','Paginascontacto')));
            $menuInferior=$this->Pagina->find('all', array('conditions'=>array('Pagina.mostrarfooter'=>1),'order'=>'Pagina.lft ASC'));
            $this->set('menuInferior',$menuInferior);

//echo '<br>'.'accion: items'.'<br>';
            $items=$this->Pagina->find('all',array('conditions'=>array('parent_id'=>$id)));

            if(!empty($items)){
                $items = $this->__resumen($items,array('Paginastexto.contenido'=>'Paginastexto.resumen','Paginasmultiple.contenido'=>'Paginasmultiple.resumen'));
                $items = $this->__thumbImages($items,'Paginastexto.contenido',true);
                $items = $this->__comprobarImagenPath($items);
                $pagina['items']=$items;

            }
            if(isset($pagina['Pagina'])&&!empty($pagina['Pagina'])&&$pagina['Pagina']['publicado']==1){
                //detalle
                if(isset($isStart)){
                    $pagina['Pagina']['isStart']=true;
                }
                $this->set('pagina',$pagina);
                $this->set('title_for_layout',$pagina['Pagina']['title']);

                //enlaces
                $enlaces=$this->Paginasenlace->find('all',array('order'=>'Paginasenlace.lft ASC'));
                $this->set('enlaces',$enlaces);
//echo '<br>'.'accion: relacionados'.'<br>';
                //mostrar en inicio
                $this->Pagina->unBindModel(array('hasMany' => array('Paginaspromocion','Paginasmultiple','Paginasvideo','Paginasadjunto'),'hasOne'=>array('Paginasopcional','Paginascontacto')));
                $mostrarRelateds=$this->Pagina->find(
                    'all'
                    ,array(
                        'conditions' => array(
                            'Pagina.publicado' => 1
                            ,'or' => array(
                                'and' =>array(
                                    array('Pagina.parent_id' => $pagina['Pagina']['parent_id'])
                                    ,array("not"=> array('Pagina.parent_id' => null))
                                )
                                ,array('Pagina.parent_id' => $pagina['Pagina']['id'])
                            )
                        )
                        ,'order' => 'Pagina.lft ASC'
                    )
                );
                $mostrarRelateds=$this->__resumen($mostrarRelateds,array('Paginastexto.contenido'=>'Paginastexto.resumen'));
                $mostrarRelateds=$this->__thumbImages($mostrarRelateds,'Paginastexto.contenido',false);
                $mostrarRelateds=$this->__comprobarImagenPath($mostrarRelateds);
                $this->set('mostrarRelateds',$mostrarRelateds);

//echo '<br>'.'accion: mostrarinicio'.'<br>';
                if(isset($isStart)){

                    //mostrar en inicio
                    $mostrarInicios=$this->Pagina->find('all',array('conditions'=>array('Pagina.publicado'=>1,'Pagina.mostrarinicio'=>1),'order' => 'Pagina.lft ASC'));
                    $mostrarInicios=$this->__resumen($mostrarInicios,array('Paginastexto.contenido'=>'Paginastexto.resumen'));
                    $mostrarInicios=$this->__thumbImages($mostrarInicios,'Paginastexto.contenido',false);
                    $mostrarInicios=$this->__comprobarImagenPath($mostrarInicios);
                    $this->set('mostrarInicios',$mostrarInicios);

//echo '<br>'.'accion: promocion'.'<br>';
                    //promocion
                    $this->Pagina->unBindModel(array('hasMany' => array('Paginasmultiple','Paginasimagen','Paginasvideo','Paginasadjunto'),'hasOne'=>array('Paginastexto','Paginasopcional','Paginascontacto')));
                    $promociones=$this->Pagina->find('all',array('conditions'=>array('Pagina.promocion'=>1),'order' => 'Pagina.lft ASC'));
                    $promociones=$this->__resumen($promociones,array('Paginastexto.contenido'=>'Paginastexto.resumen'));
                    $promociones=$this->__thumbImages($promociones,'Paginastexto.contenido',false);
                    $promociones=$this->__comprobarImagenPath($promociones);
                    $this->set('promociones',$promociones);

                    //noticias
                    $noticias=$this->Paginasnoticia->find('all',array('order'=>'Paginasnoticia.fecha DESC'));
                    $noticias=$this->__resumen($noticias,'Paginasnoticia.contenido',400);
                    $this->set('noticias',$noticias);

                    //testimonios
                    $testimonios=$this->Paginastestimonio->find('all',array('order'=>'Paginastestimonio.fecha DESC'));
                    $testimonios=$this->__resumen($testimonios,'Paginastestimonio.contenido');
                    $this->set('testimonios',$testimonios);

                }


            }else{
                $this->redirect(array('controller'=>'paginas','action'=>'index'));
            }

        }else{
            $this->redirect(array('controller'=>'paginas','action'=>'index'));
        }

	}
	


	function paginainfo() {
		if (isset($this->params['form']['id'])){
			$pagina = $this->Pagina->findById($this->params['form']['id']);
			$pagina = $this->__resumen($pagina,array('Paginastexto.contenido'=>'Paginastexto.resumen'));
			if(!empty($pagina)){
				$pagina = $this->__tiposPublicacionSave($pagina);
				$result['success'] = true;
				$result['data'] = $pagina;
				
			}else{
				$result['success'] = false;
				$result['errors'] = 'No existe información de la página';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }



	function __tiposPublicacionSave($pagina){
		if(!empty($pagina)){
			$tiposConfigure=Configure::read('Default.tipos');
			$tipos=array();
			foreach($tiposConfigure as $key=>$dummy):
				$tipos[]=$key;
			endforeach;
			unset($tiposConfigure);
			
			if(isset($pagina['Paginas'.current($tipos)])){ //al usar data se envia solo los campos no los has one or many
				$caso='save';
			}else{
				$caso='modifydata';
				$data=$pagina;

				if(isset($data['Pagina']['id'])){
                    unset($pagina);
                    $pagina=$this->Pagina->findById($data['Pagina']['id']);
                    $pagina=Set::merge($pagina,$data);//actualizamos los valores
                }
    		}
			$existe=false;
			foreach($pagina['Pagina'] as $key => $valor):
				if(in_array($key,$tipos)&&!empty($valor)&&!empty($pagina['Paginas'.$key])){
					if(empty($pagina['Pagina']['predeterminado'])){
						if(!empty($valor)){
							if(array_key_exists('id',$pagina['Paginas'.$key])&&!empty($pagina['Paginas'.$key]['id'])){
								$data['Pagina']['predeterminado']=$key;
								$existe=true;
								break;
							}elseif(!array_key_exists('id',$pagina['Paginas'.$key])){
								$data['Pagina']['predeterminado']=$key;
								$existe=true;
								break;
							}
						}
					}else{
						$existe=true;
					}
				}
			endforeach;
			foreach($pagina['Pagina'] as $key => $valor):
				if($pagina['Pagina']['predeterminado']==$key){
					if(empty($valor)){
						$existe=false;	
					}elseif(!isset($pagina['Paginas'.$key])){
						$existe=false;	
					}elseif(array_key_exists('id',$pagina['Paginas'.$key])&&empty($pagina['Paginas'.$key]['id'])){
						$existe=false;	
					}elseif(!array_key_exists('id',$pagina['Paginas'.$key])&&empty($pagina['Paginas'.$key])){
						$existe=false;	
					}else{
					}
				}
			endforeach;
			if($existe===false){
				$data['Pagina']['predeterminado']='';
			}
			if(
                (
                    isset($pagina['Paginastexto'])
                    &&isset($pagina['Paginastexto']['contenido'])
                    &&!empty($pagina['Paginastexto']['contenido'])
                )
                ||
                (
                    isset($data['Pagina']['id'])
                    &&isset($data['Pagina']['predeterminado'])
                    &&!empty($data['Pagina']['predeterminado'])
                )
            ){
                //todo: algo para invertir el condicional
            }elseif(isset($data['Pagina']['id'])){
                $children=$this->Pagina->find('first',array('conditions'=>array('parent_id'=>$data['Pagina']['id'])));
				if(empty($children)){
					$data['Pagina']['publicado']=0;
				}
				$data['Pagina']['mostrarinicio']=0;
				$data['Pagina']['promocion']=0;
			}

			if($caso=='modifydata'){
				return $data;
			}else{
				if(isset($pagina['Pagina']['id'])&&!empty($pagina['Pagina']['id'])&&isset($data)){
					$this->Pagina->unbindValidation('keep', array(), false);
					$data['Pagina']['id']=$pagina['Pagina']['id'];
					if($this->Pagina->save($data)){
						return Set::merge($pagina,$data);
					}else{
						return false;
					}
				}else{
					return $pagina;
				}
			}
		}else{
			return array();	
		}
	}

    function __comprobarImagenPath($data=NULL){
        if(empty($data)){
            return false;
        }

        if(!array_key_exists(0, $data)){

            $data=array($data);
            $extractAtFinish=TRUE;
        }

        //print_r($data);
        foreach ($data as $numero => $dummy)://todo: encontrar algo mejor
            $existe=false;
            if(isset($data{$numero}['Paginasopcional']['imagenpath'])&&!empty($data{$numero}['Paginasopcional']['imagenpath'])){
                $imagenPath=$data{$numero}['Paginasopcional']['imagenpath'];

                if (isset($data{$numero}['Paginastexto']['contenido_imagenes'])){
                    foreach ($data{$numero}['Paginastexto']['contenido_imagenes'] as $imagenData):
                        if ($imagenData['url']===$imagenPath){
                            $rutaCompleta=substr_replace($imagenData['url'], WWW_ROOT, 0, 1);
                            if(file_exists($rutaCompleta)===true&&@getimagesize($rutaCompleta)==true){
                                $existe=true;
                            }
                            break;
                        }

                    endforeach;
                }

                if($existe==false&&isset($data{$numero}['Paginasimagen'])&&!empty($data{$numero}['Paginasimagen'])){
                    foreach ($data{$numero}['Paginasimagen'] as $imagenData):
                        if ($imagenData['imagen']['path']===$imagenPath){
                            $rutaCompleta=substr_replace($imagenData['url'], WWW_ROOT, 0, 1);
                            if(file_exists($rutaCompleta)===true&&@getimagesize($rutaCompleta)==true){
                                $existe=true;
                            }
                            break;
                        }
                    endforeach;
                }
            }
            if($existe===false){
                if(!isset($data{$numero}['Paginasopcional']['id'])){
                    $data{$numero}['Paginasopcional']['id']=1;
                    $data{$numero}['Paginasopcional']['pagina_id']=$data{$numero}['Pagina']['id'];

                }
                if(isset($data{$numero}['Paginastexto']['contenido_imagenes'][0]['url'])){
                    $rutaCompleta=substr_replace($data{$numero}['Paginastexto']['contenido_imagenes'][0]['url'], WWW_ROOT, 0, 1);
                    if(strpos($rutaCompleta,APP)===0&&file_exists($rutaCompleta)===true&&@getimagesize($rutaCompleta)==true){
                        $data{$numero}['Paginasopcional']['imagenpath']=$data{$numero}['Paginastexto']['contenido_imagenes'][0]['url'];
                        $existe=true;
                    }
                }elseif(isset($data{$numero}['Paginasimagen'][0])){
                    $rutaCompleta=substr_replace($data{$numero}['Paginasimagen'][0]['imagen']['path'], WWW_ROOT, 0, 1);
                    if(strpos($rutaCompleta,APP)===0&&file_exists($rutaCompleta)===true&&@getimagesize($rutaCompleta)==true){
                        $data{$numero}['Paginasopcional']['imagenpath']=$data{$numero}['Paginasimagen'][0]['imagen']['path'];
                        $existe=true;
                    }
                }
            }
            if($existe===false){
                $data{$numero}['Paginasopcional']['imagenpath']=Configure::read('Default.imgNotAvailable');
            }

        endforeach;
        if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
            $data=$data[0];
        }
        return $data;
    }
	
	function __tags(){
		if(empty($data)){
			return false;
		}
		if(!array_key_exists(0, $data)){
			$data=array($data); 	
			$extractAtFinish=TRUE;
		}
		foreach ($data as $numero => $dummy):
			if(!empty($tags)){
				if(empty($data{$numero}['Paginasopcional']['etiquetas'])){
					$data{$numero}['Paginasopcional']['etiquetas']=Configure::read('Default.tags');
				}
			}
		endforeach;	
		if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
			$data=$data[0];	
		}
		return $data;
	}
	
	function administracion($id=NULL){
		$this->set('title_for_layout',$this->__getTitulo($this->modelNames[0]));
		if($id!=NULL){
			$parents = $this->Pagina->getpath($id);
			$parents = Set::extract('{n}.'.$this->modelNames[0].'.id',$parents);
			if (isset($parents)){$this->set('parents', $parents);}
		}
	}
	
	function getnodes() {
		if (isset($this->params['form']['node'])){
			if ($this->params['form']['node']=='root'){$this->params['form']['node']=NULL;}

			$nodes = $this->Pagina->children($this->params['form']['node'], true);
			//todo implementar reload tree para opcionales
			$user = $this->Session->read('Auth.Acluser.username');
			$acciones=array('read','create','update','delete','grant');
			if(!empty($nodes)){
				foreach($nodes as $key=>$node):
					$disabled=true;
					foreach ($acciones as $accion):
						if($this->Acl->check($user, array('model'=>'Pagina','foreign_key'=>$node['Pagina']['id']), $accion)){
							$nodes[$key]['Pagina']['permiso']{$accion}=true;
						}else{
							$nodes[$key]['Pagina']['permiso']{$accion}=false;
						}
					endforeach;
					if($nodes[$key]['Pagina']['permiso']['create']==false&&$nodes[$key]['Pagina']['permiso']['update']==false&&$nodes[$key]['Pagina']['permiso']['delete']==false&&$nodes[$key]['Pagina']['permiso']['grant']==false){
						$nodes[$key]['Pagina']['disabled']=true;
					}else{
						$nodes[$key]['Pagina']['disabled']=false;	
					}
				endforeach;
			}
		}
		
		if(isset($nodes)&&!empty($nodes)){
			foreach ($nodes as $node){
				$result[] = array(
					'text' => $node['Pagina']['title']
					,'id' => $node['Pagina']['id']
					,'leaf' => false
					,'iconCls' => 'x-tree-node-icon-'.($node['Pagina']['publicado']==0?'no':$node['Pagina']['vigencia'])
					,'disabled'=>$node['Pagina']['disabled']
					,'permiso'=>$node['Pagina']['permiso']
				);
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function reorder() {
		if (isset($this->params['form']['node'])&&isset($this->params['form']['delta'])){
			$node = intval($this->params['form']['node']);
			$delta = intval($this->params['form']['delta']);
			if ($delta > 0) {
				if($this->Pagina->movedown($node, abs($delta))){
					$result['success'] = true;
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
				}
			}elseif($delta < 0) {
				if($this->Pagina->moveup($node, abs($delta))){
					$result['success'] = true;
				}else{
					$result['success'] = false;
    				$result['errors'] = 'Hubo un error al mover, no se realizaron cambios';
				}
			}else{
				$result['success'] = false;
    			$result['errors'] = 'No se realizaron cambios';
			}
			
		}else{
			$result['success'] = false;
    		$result['errors'] = 'No se enviaron los datos correctos';
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function reparent(){
		if (isset($this->params['form']['node'])&&isset($this->params['form']['parent'])&&isset($this->params['form']['position'])){
			$node = intval($this->params['form']['node']);
			$parent = intval($this->params['form']['parent']);
			$position = intval($this->params['form']['position']);
			$this->Pagina->id = $node;
			if($this->Pagina->saveField('parent_id', $parent)){
				if ($position == 0) {
					$result['success'] = true;
				}elseif($position > 0){
					$count = $this->Pagina->childcount($parent, true);
					$delta = $count-$position-1;
					if ($delta > 0) {
						if($this->Pagina->moveup($node, $delta)){
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
		if (isset($this->params['form']['field'])){
			$result=$this->__validarCampo($this->params['form']['field']);
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function listadofotos() {
		if (isset($this->params['form']['id'])){
			$this->Pagina->unbindModel(array('hasOne'=>array('Paginasopcional','Paginascontacto'),'hasMany'=>array('Paginasimagen','Paginasvideo','Paginasadjunto','Paginaspromocion')));
			$pagina=$this->Pagina->findById($this->params['form']['id']);
			if(isset($pagina['Paginastexto']['contenido'])){
				preg_match_all('@src=[\'"]?([^\'" >]+)[\'" >]@',$pagina['Paginastexto']['contenido'],$imagenes);
				foreach($imagenes[1] as $key=>$imagen):
					$result['Imagen'][$key]['id']=$imagen;  //era $key
					$result['Imagen'][$key]['name']=$imagen;
				endforeach;
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
	
	function listadotipos() {
		$tipos=Configure::read('Default.tipos');
		if (isset($this->params['form']['id'])){
			$this->Pagina->unbindModel(array('hasOne'=>array('Paginasopcional'),'hasMany'=>array('Paginaspromocion','Paginasimagen')));
			$pagina=$this->Pagina->findById($this->params['form']['id']);
			$i=0;
			foreach($tipos as $key => $nombre):
				if(
					!empty($pagina['Pagina']{$key})
                    &&
                    (
						(!empty($pagina['Paginas'.$key])&&!array_key_exists('id',$pagina['Paginas'.$key]))
                        ||
                        (!empty($pagina['Paginas'.$key])&&isset($pagina['Paginas'.$key]['id'])&&!empty($pagina['Paginas'.$key]['id'])
						)
					)
				){
					$result['Tipos'][$i]['id']=$key;
					$result['Tipos'][$i]['name']=$nombre;
					$i++;
				}
			endforeach;
		}
		if (isset($result)){
			$this->set('result', $result);
		}else{
			$result['Tipos'][0]['id']='';
			$result['Tipos'][0]['name']='Seleccione contenido (verifique publicación)';
			$this->set('result', $result);
		}
		$this->render('/elements/ajax');
    }
	
	function agregar() {
		Configure::write('debug', 0);
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Pagina.publicado','Pagina.mostrarinicio','Pagina.mostrarfooter','Pagina.texto','Pagina.multiple','Pagina.imagen','Pagina.video','Pagina.adjunto','Pagina.contacto','Pagina.promocion'));}
		if (!empty($this->data)) {
			if (isset($this->data['Pagina']['parent_id'])&&$this->data['Pagina']['parent_id']=='root'){
				$this->data['Pagina']['parent_id']=NULL;
			}
			$parentexiste=false;
			if($this->data['Pagina']['parent_id']!=NULL){
				$parent=$this->Pagina->findById($this->data['Pagina']['parent_id']);
				if(!empty($parent)){
					$parentexiste=true;	
				}
			}else{
				$parentexiste=true;	
			}
			if($parentexiste===true){
				if(isset($this->data['Pagina']['id'])){unset($this->data['Pagina']['id']);}
				$this->data=$this->__tiposPublicacionSave($this->data);
				$this->Pagina->set($this->data);
				if ($this->Pagina->validates($this->data)) {
					if($this->Pagina->save($this->data)){
						$user = $this->Session->read('Auth.Acluser.username');
						$this->Acl->allow($user,array('model'=>'Pagina','foreign_key'=>$this->Pagina->id),'*');
						$result['success'] = true;
						$result['message'] = 'La página fue agregada';
						$result['data']['newId'] = $this->Pagina->id;
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al guardar la página';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Pagina->validationErrors;
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'No existe la página superior';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function modificar() {
		if (isset($this->params['form'])){$this->data=$this->__paramstodata($this->params['form'],array('Pagina.publicado','Pagina.mostrarinicio','Pagina.mostrarfooter','Pagina.imagen','Pagina.video','Pagina.adjunto','Pagina.contacto','Pagina.promocion'));}
		if (!empty($this->data)) {
			$pagina=$this->Pagina->findById($this->data['Pagina']['id']);
			if(!empty($pagina)){
				if(isset($this->data['Pagina']['parent_id'])){unset($this->data['Pagina']['parent_id']);}
				if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
					$this->Pagina->unbindValidation('remove', array('title'), false);
				}
				$this->data=$this->__tiposPublicacionSave($this->data);
				$this->Pagina->set($this->data);
				if ($this->Pagina->validates($this->data)) {
					if($this->Pagina->save($this->data)){
						$result['success'] = true;
						$result['message'] = 'La página fue modificada';
					}else{
						$result['success'] = false;
						$result['errors'] = 'Hubo un error al modificar la página';
					}
				}else{
					$result['success'] = false;
					$result['errors'] = $this->Pagina->validationErrors;
				}
			}else{
				$result['success'] = false;
				$result['errors'] = 'La página no existe';
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
	function borrar() {
		if (isset($this->params['form']['id'])){
			$pagina = $this->Pagina->findById($this->params['form']['id']);
			if (empty($pagina)) {
				$result['success'] = false;
				$result['errors'] = 'No existe la página';
			}else{
				//$this->Pagina->delete($this->params['form']['id'])
				if ($this->Pagina->removeFromTree($this->params['form']['id'],true)){
					$result['success'] = true;
					$result['message'] = 'La página '.$pagina['Pagina']['title'].' fue borrada';
				}else{
					$result['success'] = false;
					$result['errors'] = 'Error al borrar '.$pagina['Pagina']['title'];
				}
			}
		}
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
    }
}
?>