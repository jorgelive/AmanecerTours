<?php
class AppController extends  Controller{
	var $helpers = array('Html','Javascript');

	function beforeFilter() {
		$this->Auth->userModel = 'Acluser';
		$this->Auth->loginAction = array('controller'=>'aclusers','action'=>'login');

		if(isset($this->params['form']['idioma'])){
			Configure::write('Config.language',$this->params['form']['idioma']);
		}
		if(isset($this->params['named']['idioma'])){
			Configure::write('Config.language',$this->params['named']['idioma']);
		}
		Configure::write('Default.tipos',array(
			//'texto'=>__('descripcion',true)
			//,'imagen'=>__('imagenes',true)
            'multiple' => __('Textos multiples',true)
			,'video'=>__('videos',true)
			,'adjunto'=>__('adjuntos',true)
			,'contacto'=>__('Formulario de Contacto',true)
		));

	}



	function __paginacion($params=array(),$defaultConditions=array(),$model=NULL){
		if(empty($model)){
			$model=$this->modelNames[0];
		}
		if (isset($params['filtro'])||!empty($defaultConditions)){
			if(isset($params['filtro'])){
				$params['filtro']=json_decode($params['filtro']);
				for($i = 0; $i < count($params['filtro']); $i++){
					$campo=explode('.',$this->__normalizedata($params['filtro'][$i]->campo));
					if(isset($campo[1])){
						$params['filtro'][$i]->valor=$this->__valuesFromSchema($campo,$params['filtro'][$i]->valor);
					}
					if($params['filtro'][$i]->operador==' LIKE'){
						$params['filtro'][$i]->valor='%'.$params['filtro'][$i]->valor.'%';
					}
					$result[$model]['conditions'][$i][implode('.',$campo).$params['filtro'][$i]->operador]=$params['filtro'][$i]->valor;


				}
			}else{
				$i=0;
			}
			if(!empty($defaultConditions)){
				foreach($defaultConditions as $key=>$valor):
					$key=explode(' ',$key,2);
					if(isset($key[1])){
						$result[$model]['conditions'][$i][$this->__normalizedata($key[0]).' '.$key[1]]=$valor;
					}else{
						$result[$model]['conditions'][$i][$this->__normalizedata($key[0])]=$valor;
					}
					$i++;
				endforeach;
			}
			//echo '/*'; print_r($result[$model]['conditions']); echo '*/';//mostrar error
		}
		if (isset($params['sort'])&&isset($params['dir'])){
			$result[$model]['order'][$model.'.'.$params['sort']]=strtolower($params['dir']);
		}else{
			$result[$model]['order'][$model.'.id']='asc';
		}
		if (isset($params['limit'])){
			$result[$model]['limit']=$params['limit'];
		}else{
			$result[$model]['limit']=Configure::read('Default.paginatorSize');
		}
		if (isset($params['page'])){
			$result[$model]['page']=$this->params['form']['page'];
		}
		return $result;
	}

	function __paramstodata($params=NULL,$campos=NULL,$model=NULL){
		if (!empty($params)){
			if ($campos!=NULL){
				if (!is_array($campos)){
					$campos=array($campos);
				}
				foreach($campos as $campo):
					$normkey=explode('.',$this->__normalizedata($campo));
					if(isset($normkey[1])){
						$data{$normkey[0]}{$normkey[1]}=0;
					}else{
						echo '/*error en el parametro'; print_r($normkey); echo '*/';//mostrar error
					}
				endforeach;
			}
			foreach ($params as $key => $value):
				$normkey=$this->__normalizedata($key);
				$normkey=explode('.',$normkey,2);
				if(isset($normkey[1])){
					$data{$normkey[0]}{$normkey[1]}=$this->__valuesFromSchema($normkey,$value);
				}else{
					$data{$normkey[0]}=$value;
				}
			endforeach;
			return $data;
		}
	}


	function __valuesFromSchema($normkey=array(),$value=NULL){
		if(isset($normkey[1])){
			if(array_key_exists($normkey[1],$this->{$normkey[0]}->_schema)){

				if($this->{$normkey[0]}->_schema[$normkey[1]]['type']=='boolean'||$this->{$normkey[0]}->_schema[$normkey[1]]['type']=='integer'){
					if($value==NULL||$value=='false'){$value=0;}
					elseif($value=='true'){$value=1;}
				}
				if($this->{$normkey[0]}->_schema[$normkey[1]]['type']=='date'){
					$value=$this->__robotizeDate($value);
				}
			}
			return $value;
		}else{
			return false;
		}
	}

	function __normalizedata($key=NULL,$conModelo=true,$model=NULL){
		if(empty($key)){
			return false;
		}
		if(is_integer(strpos($key,'.'))){
			$delimiter='.';
		}elseif(is_integer(strpos($key,'_'))){
			$delimiter='_';

		}
		if($model==NULL){$model=$this->modelNames[0];}
		$lenguaje='_'.I18n::getInstance()->l10n->__l10nCatalog[Configure::read('Empresa.language')]['locale'];
		if(isset($delimiter)){
			$ekey=explode($delimiter,$key,2);
			if (!empty($ekey[0])){
				if(in_array($ekey[0],$this->modelNames)){
					if($conModelo&&(isset($this->{$ekey[0]}->_schema[$ekey[1]])||isset($this->{$ekey[0]}->_schema[$ekey[1].$lenguaje])||strpos($ekey[1],'borrar')===0)){
						return $ekey[0].'.'.$ekey[1];
					}else{
						return $ekey[1];
					}
				}else{
					if($conModelo&&(isset($this->{$model}->_schema[$ekey[0].'_'.$ekey[1]])||isset($this->{$model}->_schema[$ekey[0].'_'.$ekey[1].$lenguaje])||strpos($ekey[0],'borrar')===0)){
						return $model.'.'.$ekey[0].'_'.$ekey[1];
					}else{
						return $ekey[0].'_'.$ekey[1];
					}
				}
			}elseif(empty($ekey[0])){
				if($conModelo&&(isset($this->{$model}->_schema['_'.$ekey[1]])||isset($this->{$model}->_schema['_'.$ekey[1].$lenguaje])||strpos($ekey[1],'borrar')===0)){
					return $model.'._'.$ekey[1];
				}else{
					return '_'.$ekey[1];
				}
			}
		}else{
			if($conModelo&&(isset($this->{$model}->_schema[$key])||isset($this->{$model}->_schema[$key.$lenguaje])||strpos($key,'borrar')===0)){
				return $model.'.'.$key;
			}else{
				return $key;
			}
		}
	}

	function __robotizeDate($value){
		if(empty($value)){
			return '0000-00-00';
		}elseif(strlen($value)==10){
			return $this->__reverseDate($value,true);
		}elseif(strlen($value)==19){
			if(is_numeric(strpos($value,'T'))){
				$value=explode('T',$value);
				return $this->__reverseDate($value[0],true);
			}
		}
	}

	function __reverseDate($value,$Ymd=false){
		$partes=explode('-',$value);
		if($Ymd===true){
			if(count($partes)==3&&is_numeric($partes[0])&&is_numeric($partes[1])&&is_numeric($partes[2])&&strlen($partes[2])==4){
				$partes=array_reverse($partes);
			}
		}else{
			$partes=array_reverse($partes);
		}
		$value=implode('-',$partes);
		return $value;
	}

	function __validarCampo($parametros=NULL){
		if ($parametros){
			$campos=explode('.',$parametros);
			if (isset($campos[1])){
				$field=$campos[1];
				$modelo=$campos[0];
			}else{
				$field=$campos[0];
				$modelo=$this->modelNames[0];
			}
			if(!is_array($this->params['form']['value'])&&strlen($this->params['form']['value'])==10){
				$partes=explode('-',$this->params['form']['value']);
				if(count($partes)==3&&is_numeric($partes[0])&&is_numeric($partes[1])&&is_numeric($partes[2])&&strlen($partes[0])==2&&strlen($partes[1])==2&&strlen($partes[2])==4){
					$partes=array_reverse($partes);
					$this->params['form']['value']=implode('-',$partes);
				}
			}
			$this->data{$modelo}{$field}=$this->params['form']['value'];
			if (isset($this->params['form']['clear_password'])){
				if (isset($campos[1])){
					$this->data{$campos[0]}['clear_password']=$this->params['form']['clear_password'];
				}else{
					$this->data{$this->modelNames[0]}['clear_password']=$this->params['form']['clear_password'];
				}
			}
			$this->{$modelo}->set($this->data);
			$this->{$modelo}->validates();
			if(isset($this->{$modelo}->validationErrors{$field})){
				$result['success']=true;
				$result['valid']=false;
				$result['reason']=$this->{$modelo}->validationErrors{$field};
			}else{
				$result['success']=true;
				$result['valid']=true;
			}
		}else{
			$result['success']=true;
			$result['valid']=false;
		}
		return $result;
	}

	function __getTitulo($modelo){
		$recursos[0]=$this->Recurso->find('first',array('conditions'=>array('model'=>$modelo)));
		if($recursos[0]['Recurso']['parent_id']!=NULL){
			$recursos[1]=$this->Recurso->find('first',array('conditions'=>array('id'=>$recursos[0]['Recurso']['parent_id'])));
		}
		foreach ($recursos as $recurso):
			$titulo[]=$recurso['Recurso']['name'];
		endforeach;
		return implode(' - ',$titulo);
	}

	function __implodeWithKey($data,$separadorInterno = '=',$separadorExterno='&') {
		$result = '';
		foreach ($data as $key => $value) {
			$result .= $separadorExterno.$key.$separadorInterno.$value;
		}
		return substr($result, strlen($separadorExterno));
	}

	function __urlVariable($key=NULL,$data=NULL){
		if(empty($key)||empty($data)){return false;}
		return $key.'='.preg_replace('#(\'|")#','',$data);
	}

	function __thumbImages($data=NULL,$imageContainers=NULL,$replace=false){
		if(empty($data)||empty($imageContainers)){
			return false;
		}
		if (!is_array($imageContainers)){
			$imageContainers=array($imageContainers);
		}
		if(!array_key_exists(0, $data)){
			$data=array($data);
			$extractAtFinish=TRUE;
		}
		foreach ($data as $numero => $dummy):
			foreach ($imageContainers as $dataContainer):
				$dataContainer=explode('.',$dataContainer);
				if(!isset($dataContainer[1])){
					$datacontainer[1]=$datacontainer[0];
					$datacontainer[0]=$this->modelNames[0];
				}
				if(isset($data{$numero}{$dataContainer[0]}{$dataContainer[1]})){
					preg_match_all('/<img[^>]+>/i',$data{$numero}{$dataContainer[0]}{$dataContainer[1]}, $imagenes);
					if(!empty($imagenes[0])){
						foreach ($imagenes[0] as $numeroImagen=>$imagen):
							preg_match_all('/(alt|title|src|width|height|style|class|id)=([\'|"][^(\'|")]*[\'|"])/i',$imagen, $parsedImagenes[$imagen]);
							$parsedImagenes[$imagen]['id']=$numeroImagen;
						endforeach;
						$imagenes=array();
						$imagen=array();

						foreach ($parsedImagenes as $completeTag=>$parsedImagen):
							$combinedTags=array();

							$combinedTags=array_combine($parsedImagen[1],$parsedImagen[2]);
							if (in_array('src',$parsedImagen[1])){

								$rutaCompleta=str_replace(array('"','\''),array('',''),$combinedTags['src']);
								$rutaCompleta=substr_replace($rutaCompleta, WWW_ROOT, 0, 1);

								if(file_exists($rutaCompleta)===true&&@getimagesize($rutaCompleta)==true){
									$data{$numero}{$dataContainer[0]}[$dataContainer[1].'_imagenes']{$parsedImagen['id']}=getimagesize($rutaCompleta);
									$data{$numero}{$dataContainer[0]}[$dataContainer[1].'_imagenes']{$parsedImagen['id']}['url']=str_replace(array('"','\''),array('',''),$combinedTags['src']);
									if($data{$numero}{$dataContainer[0]}[$dataContainer[1].'_imagenes']{$parsedImagen['id']}[0]<$data{$numero}{$dataContainer[0]}[$dataContainer[1].'_imagenes']{$parsedImagen['id']}[1]){
										$data{$numero}{$dataContainer[0]}[$dataContainer[1].'_imagenes']{$parsedImagen['id']}['orientacion']='portrait';
									}else{
										$data{$numero}{$dataContainer[0]}[$dataContainer[1].'_imagenes']{$parsedImagen['id']}['orientacion']='landscape';
									}
									if ((in_array('width',$parsedImagen[1])||in_array('height',$parsedImagen[1]))&&(strpos($combinedTags['src'],'/')==1)){
										$imagenes{$completeTag}=$combinedTags;
									}
								}
							}
						endforeach;
						if($replace!=false&&!empty($imagenes)){
							foreach ($imagenes as $key=>$imagen):
								$imageTag='<img src="/thumbs/index/?';
								$phpThumbParams=array();
								$phpThumbParams[]=$this->__urlVariable('src',$imagen['src']);
								if(isset($imagen['width'])){$phpThumbParams[]=$this->__urlVariable('w',$imagen['width']);}
								if(isset($imagen['height'])){$phpThumbParams[]=$this->__urlVariable('h',$imagen['height']);}
								$imageTag.=implode('&',$phpThumbParams).'" ';
								unset ($imagen['src']);
								$imageTag.=$this->__implodeWithKey($imagen,'=',' ');
								$imageTag.=' />';
								$data{$numero}{$dataContainer[0]}{$dataContainer[1]} = str_replace($key, $imageTag, $data{$numero}{$dataContainer[0]}{$dataContainer[1]});
							endforeach;
						}
					}
				}
			endforeach;
		endforeach;
		if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
			$data=$data[0];
		}
		return $data;
	}

	function __comprobarPublicacion($data=NULL,$borrar=false){
		if(empty($data)){
			return false;
		}
		if(!array_key_exists(0, $data)){
			$data=array($data);
			$extractAtFinish=TRUE;
		}

		foreach ($data as $numero => $dummy):
			if(isset($data{$numero}['Paginasopcional'])){
				$data{$numero}['Pagina']['publicado']=$this->__fechaEnRango($data{$numero}['Pagina']['publicado'],$data{$numero}['Paginasopcional']['publicado_inicio'],$data{$numero}['Paginasopcional']['publicado_final']);
			}else{
				$data{$numero}['Pagina']['publicado']=$this->__fechaEnRango($data{$numero}['Pagina']['publicado']);
			}
			if($borrar!=false&&$data{$numero}['Pagina']['publicado']!='si'){
				unset($data{$numero});
			}
		endforeach;

		if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
			$data=$data[0];
		}
		return $data;
	}

	function __fechaEnRango($valorCP=NULL,$inicio=NULL,$final=NULL,$consultada=false){
		if ($valorCP!==NULL&&empty($valorCP)){return 'no';}
		if($inicio=='0000-00-00'){$inicio=NULL;}elseif(!empty($inicio)){$inicio=strtotime($inicio);}
		if($final=='0000-00-00'){$final=NULL;}elseif(!empty($final)){$final=strtotime($final);}
		if(!empty($consultada)){$consultada=strtotime($consultada);}else{$consultada=time();}

		if(empty($inicio)&&empty($final)){
			if(empty($valorCP)){
				return true;
			}else{
				return 'si';
			}
		}elseif(empty($inicio)){
			if($final<=$consultada){
				if(empty($valorCP)){
					return false;
				}else{
					return 'outdated';
				}
			}
		}elseif(empty($final)){
			if($inicio>=$consultada){
				if(empty($valorCP)){
					return false;
				}else{
					return 'soon';
				}
			}
		}else{
			if($inicio>=$consultada){
				if(empty($valorCP)){
					return false;
				}else{
					return 'soon';
				}
			}else{
				if($final<=$consultada){
					if(empty($valorCP)){
						return false;
					}else{
						return 'outdated';
					}
				}
			}
		}
		if(empty($valorCP)){
			return true;
		}else{
			return 'si';
		}
	}

	function __resumen($data=NULL,$resumen=NULL,$length=NULL){
		if(empty($data)){
			return false;
		}
		if(!array_key_exists(0, $data)){
			$data=array($data);
			$extractAtFinish=TRUE;
		}
		foreach ($data as $numero => $dummy):
			if(!empty($resumen)){
				if(is_array($resumen)){
					$origen=key($resumen);
					$destino=current($resumen);
					$resumenOrigen=explode('.',$origen);
					$resumenDestino=explode('.',$destino);
				}else{
					$resumenOrigen=explode('.',$resumen);
					$resumenDestino=explode('.',$resumen.'_resumen');
				}
				if(isset($data{$numero}{$resumenOrigen[0]}{$resumenOrigen[1]})&&((isset($data{$numero}{$resumenDestino[0]}{$resumenDestino[1]})&&empty($data{$numero}{$resumenDestino[0]}{$resumenDestino[1]}))||!isset($data{$numero}{$resumenDestino[0]}{$resumenDestino[1]}))){
					$data{$numero}{$resumenDestino[0]}{$resumenDestino[1]}=$this->__hacerResumen($data{$numero}{$resumenOrigen[0]}{$resumenOrigen[1]},$length);
				}
			}
		endforeach;
		if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
			$data=$data[0];
		}
		return $data;
	}

	function __hacerResumen($data,$length=NULL){
		if(!is_numeric($length)){
			$length=Configure::read('Empresa.resumenSize');
		}
		$data = strip_tags($data,'<p><h3><h2><h1><h4><h5><h6>');
		$contador = 0;
		$arrayTexto = explode(' ',$data);
		$resumen = '';
		foreach($arrayTexto as $key => $parte):
			if($length >= strlen($resumen) + strlen($parte)){
				$resumen .= ' '.$parte;
			}else{
				break;
			}
		endforeach;
		$resumen = $this->__closetags($resumen);
		$arr_busca = array('@<p[a-z0-9\'"=:;\ \-\_]*>@i','@<\/p*>@i','@<h[a-z0-9\'"=:;\ \-\_]*>@i','@<\/h[0-9]*>@i');
		$arr_susti = array('',' ','<strong>','</strong> ');
		$resumen = '<p class="resumen">'.trim(preg_replace($arr_busca, $arr_susti, $resumen)).'</p>';
		//$resumen = trim(preg_replace(array('@(&nbsp;)*@i','@( )+@i'),array('',' '),$resumen));
		return ($resumen);
	}

	function __closetags ($html){
		preg_match_all ('#<([a-z]+)( .*)?(?!/)>#iU',$html,$result);
		$openedtags = $result[1];
		preg_match_all ('#</([a-z]+)>#iU',$html,$result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if(count($closedtags) == $len_opened){
			return $html;
		}
		$openedtags = array_reverse($openedtags);
		for($i=0;$i<$len_opened;$i++){
			if (!in_array ($openedtags[$i],$closedtags)){
				$html .= '</'.$openedtags[$i].'>';
			}else{
				unset ($closedtags[array_search($openedtags[$i],$closedtags)]);
			}
		}
		return $html;
	}

	function __checkAcl($map=array(),$exAjax=array()){

		if(!is_array($exAjax)){$extAjax=array($exAjax);}
		if(isset($this->Auth->allowedActions)&&!empty($this->Auth->allowedActions)&&in_array($this->action,$this->Auth->allowedActions)){
			$autorizacion='autorizado';
		}else{
			$autorizacion='denegado';
		}
		$administradores=array('Aco','Recurso');
		if($autorizacion=='denegado'&&!empty($map)){
			if($this->Session->read('Auth.Acluser.username')!=''){
				$user=$this->Session->read('Auth.Acluser.username');
				if(!empty($this->params['form'])){
					$datos=$this->__paramstodatavalidation($this->params['form']);
				}
				if (isset($this->{$this->modelNames[0]}->actsAs['Acl']['mode']['belongsto'])&&isset($datos[strtolower($this->{$this->modelNames[0]}->actsAs['Acl']['mode']['belongsto']).'_id'])&&!empty($datos[strtolower($this->{$this->modelNames[0]}->actsAs['Acl']['mode']['belongsto']).'_id'])){
					$model=$this->{$this->modelNames[0]}->actsAs['Acl']['mode']['belongsto'];
					$foreign_key=$datos[strtolower($this->{$this->modelNames[0]}->actsAs['Acl']['mode']['belongsto']).'_id'];
				}elseif(isset($datos['administrador'])&&!empty($datos['administrador'])&&in_array($datos['administrador'],$administradores)){
					$referer=explode('/',$this->referer());
					if(ucfirst(Inflector::singularize($referer[1]))==$datos['administrador']){
						$model=ucfirst(Inflector::singularize($referer[1]));
					}else{
						$model='Aco';//este debe ser el mas asegurado
					}
					$foreign_key=NULL;
				}elseif (isset($datos['caller'])&&!empty($datos['caller'])){
					$model=$datos['caller'];
					if(isset($datos['foreign_key'])&&$datos['foreign_key']!='root'){
						$foreign_key=$datos['foreign_key'];
					}else{
						$foreign_key=NULL;
					}
				}elseif (isset($datos['id'])&&!empty($datos['id'])&&$datos['id']!='root'){
					$model=$this->modelNames[0];
					if(isset($this->{$this->modelNames[0]}->actsAs['Acl'])&&$this->{$this->modelNames[0]}->actsAs['Acl']['type']!='requester'){
						$foreign_key=$datos['id'];
					}else{
						$foreign_key=NULL;
					}
				}elseif (isset($datos['parent_id'])&&!empty($datos['parent_id'])&&isset($this->{$this->modelNames[0]}->actsAs['Acl'])){
					$model=$this->modelNames[0];
					if($this->{$this->modelNames[0]}->actsAs['Acl']['type']!='requester'&&$datos['parent_id']!='root'){
						$foreign_key=$datos['parent_id'];
					}else{
						$foreign_key=NULL;
					}
				}else{
					$model=$this->modelNames[0];
					$foreign_key=NULL;
				}


				if(!isset($datos['cmd'])){
					if($foreign_key==NULL){
						if($this->Acl->check($user,$model.'::Auto',strtr($this->action, $map))){

							$autorizacion='autorizado';
						}
					}else{
						if($this->Acl->check($user, array('model'=>$model,'foreign_key'=>$foreign_key), strtr($this->action, $map))){
							$autorizacion='autorizado';
						}
					}
				}elseif((isset($datos['cmd'])&&$datos['cmd']=='validateField')||!isset($datos['id'])||!isset($datos['parent_id'])||!isset($datos['caller'])){
					$autorizacion='autorizado';
				}else{
					$autorizacion='denegado';
				}
			}
		}
		if($autorizacion=='denegado'){
			$this->Session->delete('Auth.Acluser');
			$this->Session->delete('Acl');
			$this->Session->delete('Component');
			if((isset($this->RequestHandler)&&$this->RequestHandler->isAjax())||(!empty($exAjax)&&in_array($this->action,$exAjax))){
				$result['success'] = false;
				$result['errors'] = 'No esta autorizado';
				$result['redirect'] = '/'.implode('/',$this->Auth->loginAction);
				echo json_encode($result);
				die();
			}else{
				if($this->Session->read('Auth.redirect')!=$this->Auth->loginAction){
					$this->Session->write('Auth.redirect', $this->here);
				}
				$this->redirect($this->Auth->loginAction);
			}
		}
	}

	function __paramstodatavalidation($params=NULL,$model=NULL){
		if (isset($params)){
			foreach ($params as $key => $value):
				$normkey=$this->__normalizedata($key,false);
				$data{$normkey}=$value;
			endforeach;
			return $data;
		}
	}
}
?>