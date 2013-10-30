<?php

/* 
 * File for cakePHP 
 * comments, bug reports are welcome jgomez AT viajespacifico DOT com DOT pe 
 * @author Jorge Luis Gómez 
 * @version 1.0.0.0
 		File'=>array(
			'fields'=>array(
				'imagen'=>array(
					'type'=>'imagen'
					,'resize'=>array('width'=>'1200','height'=>'800','allow_enlarge'=>true)
					,'thumbnail'=>array('create'=>true)
					,'versions'=>array(
						array('prefix'=>'p','width'=>'200','height'=>'150','allow_enlarge'=>true)
						,array('prefix'=>'g','width'=>'800','height'=>'600','allow_enlarge'=>true)
					)
				)
			)
		)
 */ 

class FileBehavior extends ModelBehavior {
	var $settings = null;
	var $baseDir='';
	var $fieldDefault = array (
		'type'=> 'imagen'
		,'allowFlash'=>false
	);
	
	var $defaultThumbnail = array(
		'prefix'=>'thumb'
		,'width'=>'100'
		,'height'=>'100'
		,'aspect'=>true
		,'allow_enlarge'=>true
	);
	
	var $defaultResize = array(
		'aspect'=>true
		,'allow_enlarge'=>false
		,'width'=>'400'
		,'heigth'=>'400'
	);
	
	var $defaultVersions = array(
		'aspect'=>true
		,'allow_enlarge'=>false
	);
	
	var $allowedImage = array(
		'image/gif'
		,'image/jpeg'
		,'image/pjpeg'
		,'image/x-png'
		,'image/jpg'
		,'image/png'
	);
	
	var $allowedFile=array(
		'application/pdf'
		,'application/msword'
		,'application/vnd.ms-excel'
		,'application/vnd.ms-powerpoint'
		,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		,'application/vnd.openxmlformats-officedocument.presentationml.presentation'
		,'video/mpeg'
		,'video/quicktime'
		,'video/x-msvideo'
		,'video/x-ms-asf'
		,'video/x-ms-wmv'
		,'audio/mpeg'
		,'audio/x-wav'
		,'application/zip'
		,'application/x-rar-compressed'
		,'text/plain'
		,'text/xml'
	);
	
	var $contentsMaping=array(
		'image/gif' => 'gif'
		,'image/jpeg' => 'jpg'
		,'image/pjpeg' => 'jpg'
		,'image/x-png' => 'png'
		,'image/jpg' => 'jpg'
		,'image/png' => 'png'
		,'application/pdf' => 'pdf'
		,'application/msword' => 'doc'
		,'application/vnd.ms-excel' =>'xls'
		,'application/vnd.ms-powerpoint'=>'ppt'
		,'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
		,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx'
		,'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx'
		,'video/mpeg' => 'mpeg'
		,'video/quicktime' => 'mov'
		,'video/x-msvideo' => 'avi'
		,'video/x-ms-asf' => 'asf'
		,'video/x-ms-wmv' => 'wmv'
		,'audio/mpeg' => 'mp3'
		,'audio/x-wav' => 'wav'
		,'application/zip' => 'zip'
		,'application/x-rar-compressed' => 'rar'
		,'text/plain' => 'txt'
		,'text/xml' => 'xml'
		,'application/pgp-signature' => 'sig'
		,'application/futuresplash' => 'spl'
		,'application/postscript' => 'ps'
		,'application/x-bittorrent' => 'torrent'
		,'application/x-dvi' => 'dvi'
		,'application/x-gzip' => 'gz'
		,'application/x-ns-proxy-autoconfig' => 'pac'
		,'application/x-shockwave-flash' => 'swf'
		,'application/x-tgz' => 'tar.gz'
		,'application/x-tar' => 'tar'
		,'audio/x-mpegurl' => 'm3u'
		,'audio/x-ms-wma' => 'wma'
		,'audio/x-ms-wax' => 'wax'
		,'image/x-xbitmap' => 'xbm'          
		,'image/x-xpixmap' => 'xpm'             
		,'image/x-xwindowdump' => 'xwd'          
		,'text/css' => 'css'        
		,'text/html' => 'html'                          
		,'text/javascript' => 'js'
	);

	function setup(&$model, $config = array()) {
		$settings = Set::merge(array('baseDir'=> $this->baseDir), $config);
		if (!isset($settings['fields']))
			$settings['fields']=array();
		$fields=array();
		foreach($settings['fields'] as $field=>$fieldConfiguration) {
			//para relacionadas
			if(is_numeric($field)){
				$field=$fieldConfiguration;
			}
			
			if(!$model->hasField($field)) {
				trigger_error('El campo "'.$field.'" no existe en el modelo "'.$model->name.'".', E_USER_WARNING);
			}
			if(!is_numeric($field)){
				if(is_array($fieldConfiguration)){
					
					$fieldConfiguration=Set::merge($this->fieldDefault,$fieldConfiguration);
					if($fieldConfiguration['type']=='imagen'){
						if($fieldConfiguration['allowFlash']===true){
							$this->allowedImage[]='application/x-shockwave-flash';
						}
						
						//para versions
						if(isset($fieldConfiguration['versions'])&&is_array($fieldConfiguration['versions'])){
							foreach ($fieldConfiguration['versions'] as $id=>$version){
								$fieldConfiguration['versions'][$id]=Set::merge($this->defaultVersions,$version);				
							}
						}
						//default para resize
						if (isset($fieldConfiguration['resize'])) {
							$fieldConfiguration['resize']=Set::merge($this->defaultResize,$fieldConfiguration['resize']);
						}
						//para thumbnail
						if (isset($fieldConfiguration['thumbnail'])) {
							$fieldConfiguration['thumbnail']=Set::merge($this->defaultThumbnail,$fieldConfiguration['thumbnail']);
						}
					}else{
						//eliminamos configuracion inutil
						if (isset($fields[$field]['thumbnail'])){
							unset($fields[$field]['thumbnail']);
						}
						if (isset($fields[$field]['resize'])){
							unset($fields[$field]['resize']);
						}
						if (isset($fields[$field]['versions'])){
							unset($fields[$field]['versions']);
						}
					}
				
				}else{
					$fieldConfiguration=array();
				}
			}else{
				$fieldConfiguration=array();
			}
			//insertamos la configuracion
			$fields[$field]=$fieldConfiguration;
		}
		$settings['fields']=$fields;
		$this->settings[$model->name] = $settings;
	}
	
	function beforeValidate(&$model) {
		extract($this->settings[$model->name]);
		$tempData = array();
		foreach ($fields as $key=>$value) {
			$field = is_numeric($key)? $value : $key;
			if (isset($model->data[$model->name][$field])) {
				if ($this->__isUploadFile($model->data[$model->name][$field])){
					if($value['type']=='imagen'){
						$content=$this->__getContent($model->data[$model->name][$field],$this->allowedImage);
					}elseif($value['type']=='archivo'){
						$content=$this->__getContent($model->data[$model->name][$field],$this->allowedFile);
					}
					if(isset($content)&&!empty($content)){
						$tempData[$field] = $model->data[$model->name][$field];
						$tempData[$field]['time']=$content['time'];
						$model->data[$model->name][$field]=$content['time'].'_'.$content['type'];
					}else{
						$model->data[$model->name][$field]='';
					}
				} else {
					$model->data[$model->name][$field]='';
				} 
			}
		}
		$this->runtime[$model->name]['beforeValidate'] = $tempData; 
		return true;
	} 
	
	function beforeSave(&$model) {
		extract($this->settings[$model->name]);
		foreach ($fields as $key=>$value) {
			$field = is_numeric($key) ? $value : $key;
			if (isset($model->data[$model->name]['borrar_'.$field])) {
				@ignore_user_abort(true);
				if($model->data[$model->name]['borrar_'.$field]==1){
					$model->data[$model->name][$field]='';
					$folderPath=$this->__getFullFolder($model, $field);
					uses ('folder');
					$folder = &new Folder($path = $folderPath, $create = false);
					if ($folder!==false) {
						@$folder->delete($folder->pwd());
					}	
				}
			}
		}
		return true;
	} 

	function afterSave(&$model,$creado) {
		$tempData = array();
		if(isset($this->runtime[$model->name]['beforeValidate'])){
			$tempData = $this->runtime[$model->name]['beforeValidate'];
			unset($this->runtime[$model->name]['beforeValidate']);
		}
		foreach($tempData as $field=>$value) {
			!$this->__saveFile($model, $field, $value);
		}
		return true;
	} 
	
	function __getContent($file,$allowed=array()) {
		if(!empty($allowed)){
			if (in_array($file['type'],$allowed)){
				 $result['type']=$file['type'];
			}else{
				return false;	
			}
		}else{
			$result['type']=$file['type'];
		}
		$result['time']=time();
		return $result;
	}
	
	function __saveFile(&$model, $field, $fileData) {
		extract($this->settings[$model->name]);
		$folderName = $this->__getFullFolder($model, $field);
		$content=$this->__getContent($fileData);
		//obtenemos la extension
		$ext=strtr($content['type'],$this->contentsMaping);
		$fileName=$fileData['time'].'_'.$field.'.'.$ext;
		uses ('folder'); 
		uses ('file'); 
		$folder = &new Folder($folderName, true, 0777);
		$files = $folder->find($fileName);
		$file= &new File($folder->pwd().DS.$fileName);
		$fileExists=($file!==false);
		if ($fileExists) { 
			@$file->delete();
		} 
		if($fields[$field]['type']=='imagen'&&$content['type']!='application/x-shockwave-flash'){
			
			//procesamos resize
			if (isset($fields[$field]['resize'])) {	
				$file=$folder->pwd().DS.'tmp_'.$fileName;
				copy($fileData['tmp_name'], $file);
				$this->__resize($folder->pwd(),'tmp_'.$fileName,$fileName,$field, $fields[$field]['resize']);
				@unlink($file);			
			} else {		
				$file=$folder->pwd().DS.$fileName;
				copy($fileData['tmp_name'], $file);
			}
			//procesamos thumbnail
			if (isset($fields[$field]['thumbnail'])) {
				$fieldParams=$fields[$field]['thumbnail'];
				$newFile=$this->__getPrefix($fieldParams).'_'.$fileName;
				$this->__resize($folder->pwd(),$fileName,$newFile, $field, $fieldParams);
			}
			//procesamos versiones
			if(isset($fields[$field]['versions'])&&!empty($fields[$field]['versions'])){
				foreach($fields[$field]['versions'] as $version) {
					$newFile=$this->__getPrefix($version).'_'.$fileName;
					$this->__resize($folder->pwd(),$fileName,$newFile,$field, $version);
				}
			}
		}else{
			//procesamos texto
			$file=$folder->pwd().DS.$fileName;
			copy($fileData['tmp_name'], $file);
		}
		return true;
	}
	
	function afterFind(&$model, $results, $primary) { 
		foreach ($this->settings as $modelName => $dummy):
			extract($this->settings[$modelName]);
			if (is_array($results)){
				if(!array_key_exists(0, $results)){
					$results=array($results); 	
					$extractAtFinish=TRUE;
				}
				$i=0;
				while (isset($results[$i][$modelName]) && is_array($results[$i][$modelName])){
					foreach ($fields as $field => $fieldParams){
						if (isset($results[$i][$modelName][$field]) && ($results[$i][$modelName][$field]!='')){
							$value=$results[$i][$modelName][$field];
							$results[$i][$modelName][$field]=$this->__getParams($model,$field,$value,$fieldParams,$results[$i][$modelName],$modelName);
						}elseif(array_key_exists(0, $results[$i][$modelName])){
							foreach($results[$i][$modelName] as $key => $value):
								$value=$value{$field};
								$results[$i][$modelName]{$key}[$field]=$this->__getParams($model,$field,$value,$fieldParams,$results[$i][$modelName][$key],$modelName);
							endforeach;
						}
					}
					$i++;
				}             		
				if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
					$results=$results[0];	
				}
			}	
			endforeach;	
		return $results;
	} 	
	
	function __getParams(&$model,$field,$value,$fieldParams,$record,$modelName=NULL) {
		if (empty($modelName)){$modelName=$model->name;}
		extract($this->settings[$modelName]);
		$result=array();
		if ($value!=''){
			$value=explode('_',$value);
			$folderName = $this->__getFolder($model,$record,$field,$modelName);
			$ext=strtr($value[1],$this->contentsMaping);
			$fileName=$value[0].'_'.$field.'.'.$ext;
			$result['path']=$folderName.$fileName;
			$result['icon']=$this->baseDir.DS.IMAGES_URL.'Filebehavior/'.$ext.'.png';

            if($fields[$field]['type']=='imagen'){
                if (file_exists(WWW_ROOT.$result['path'])) {
                    $imageSize = getimagesize(WWW_ROOT.$result['path']);
                    if(is_array($imageSize)){
                        $result['width'] = $imageSize[0];
                        $result['height'] = $imageSize[1];
                    }
                }
				if(isset($fields[$field]['thumbnail'])){
					$thumb=$fields[$field]['thumbnail'];
					$result['thumb']=$folderName.$this->__getPrefix($thumb).'_'.$fileName;
				}
				if(isset($fields[$field]['versions'])){
					foreach($fields[$field]['versions'] as $version) {
						$result[$this->__getPrefix($version)]=$folderName.$this->__getPrefix($version).'_'.$fileName;
					}
				}
			}
		}
		return $result;
	}
	
	function beforeDelete(&$model) {
		$this->runtime[$model->name]['ignoreUserAbort'] = ignore_user_abort();
		@ignore_user_abort(true);
		return true;
	} 

	function afterDelete(&$model) { 
		extract($this->settings[$model->name]);
		foreach ($fields as $field=>$fieldParams) {
			$folderPath=$this->__getFullFolder($model, $field);
			uses ('folder'); 
			$folder = &new Folder($path = $folderPath, $create = false);
			if ($folder!==false) {
				@$folder->delete($folder->pwd());
			}			
		}
		@ignore_user_abort((bool) $this->runtime[$model->name]['ignoreUserAbort']);
		unset($this->runtime[$model->name]['ignoreUserAbort']); 
		return true;
	} 	
	
	function __isUploadFile($file) {
		if (!isset($file['tmp_name'])){
			return false;
		}else{
			if (file_exists($file['tmp_name'])&&$file['error']==0){
				return true; 
			}else{
				return false;	
			}
		}
	}

	function __saveAs($fileData, $fileName=null, $folder) {
		if (is_writable($folder)) {
			if (is_uploaded_file($_FILES[$fileData]['tmp_name'])){
				if (empty($fileName)) $fileName = $_FILES[$fileData]['name'];
				copy($_FILES[$fileData]['tmp_name'], $folder.$fileName);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function __getFolder(&$model,$record,$field,$modelName=NULL) {
		if(empty($modelName)){$modelName=$model->name;}
		extract($this->settings[$modelName]);
		if($fields[$field]['type']=='imagen'){
			$tipo=IMAGES_URL;
		}else{
			$tipo=FILES_URL;
		}
		return  $this->baseDir.DS.$tipo.Inflector::camelize($modelName).DS.$record[$model->primaryKey].DS;
	}
	
	function __getFullFolder(&$model, $field) {
		extract($this->settings[$model->name]);
        if(isset($fields[$field])&&$fields[$field]['type']=='imagen'){
			$tipo=IMAGES_URL;
		}else{
			$tipo=FILES_URL;
		}
		return  WWW_ROOT.$tipo.$this->baseDir.DS.Inflector::camelize($model->name).DS.$model->id.DS;
	}
	
	function __getPrefix($fieldParams) {
		if (isset($fieldParams['prefix'])) {
			return $fieldParams['prefix'];
		}else{
			return $fieldParams['width'].'x'.$fieldParams['height'];
		}
	}
	
    function __resize($folder, $originalName, $newName, $field, $fieldParams) { 
        $types = array(1 => 'gif', 'jpeg', 'png'); // used to determine image type 
        $fullpath = $folder; 
        $url = $folder.DS.$originalName; 
        if (!($size = getimagesize($url)))  
            return; // image doesn't exist 
		$width=$fieldParams['width'];
		$height=$fieldParams['height']; 
        if ($fieldParams['allow_enlarge']===false) { // don't enlarge image
			if (($width>$size[0])||($height>$size[1])) {
				$width=$size[0];
				$height=$size[1]; 
			}
		} else {
	        if ($fieldParams['aspect']) { // adjust to aspect. 
	            if (($size[1]/$height) > ($size[0]/$width))  // $size[0]:width, [1]:height, [2]:type 
	                $width = ceil(($size[0]/$size[1]) * $height); 
	            else  
	                $height = ceil($width / ($size[0]/$size[1])); 
	        } 
        }
        $cachefile = $fullpath.DS.$newName;  // location on server 
        if (file_exists($cachefile)) { 
            $csize = getimagesize($cachefile); 
            $cached = ($csize[0] == $width && $csize[1] == $height); // image is cached 
            if (@filemtime($cachefile) < @filemtime($url)) // check if up to date 
                $cached = false; 
        } else { 
            $cached = false; 
        } 
        if (!$cached) { 
            $resize = ($size[0] > $width || $size[1] > $height) || ($size[0] < $width || $size[1] < $height || ($fieldParams['allow_enlarge']===false)); 
        } else { 
            $resize = false; 
        } 
        if ($resize) { 
            $image = call_user_func('imagecreatefrom'.$types[$size[2]], $url); 
            if (function_exists('imagecreatetruecolor') && ($temp = imagecreatetruecolor ($width, $height))) { 
                imagecopyresampled ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]); 
              } else { 
                $temp = imagecreate ($width, $height); 
                imagecopyresized ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]); 
            } 
            call_user_func('image'.$types[$size[2]], $temp, $cachefile); 
            imagedestroy ($image); 
            imagedestroy ($temp); 
        }          
    } 
}	
?>