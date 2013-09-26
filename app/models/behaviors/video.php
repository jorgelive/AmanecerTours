<?php
class VideoBehavior extends ModelBehavior {
	function setup(&$model, $settings = array()) {
		$videoFuentes=Configure::read('Default.video');
		if (empty($videoFuentes)) {
			trigger_error("Agregue al core.php la linea: Configure::write('Default.video',array('Youtube','Vimeo'));");
		}
		if (!isset($settings['fields'])) $settings['fields']=array();
		$fields=array();
		foreach($settings['fields'] as $key=>$value) {
			$field=ife(is_numeric($key),$value,$key);
			if(!$model->hasField($field)) {
				trigger_error('El campo "'.$field.'" no existe en el modelo "'.$model->name.'".', E_USER_WARNING);
			}
			$conf=ife(is_numeric($key),array(),$value);
			$fields[$field]=$conf;
		}
		$settings['fields']=$fields;
		$this->settings[$model->name] = $settings;
	}
	
	function afterFind(&$model,$results,$primary) { 
		foreach ($this->settings as $modelName => $dummy):
			extract($this->settings[$modelName]);
			if (is_array($results)){
				if(!array_key_exists(0, $results)){
					$results=array($results); 	
					$extractAtFinish=TRUE;
				}
				$i=0;
				while (isset($results[$i][$modelName]) && is_array($results[$i][$modelName])){
					foreach ($fields as $field => $videoSourcefield){
						if (isset($results[$i][$modelName][$field])&&($results[$i][$modelName][$field]!='')){
							$code=$results[$i][$modelName][$field];
							$videoSource=$results[$i][$modelName][$videoSourcefield];
							$results[$i][$modelName][$field]=$this->__getParams($model,$code,$videoSource);
						}elseif(array_key_exists(0,$results[$i][$modelName])){
							foreach($results[$i][$modelName] as $key => $value):
								$code=$value{$field};
								$videoSource=$value[$videoSourcefield];
								$results[$i][$modelName]{$key}[$field]=$this->__getParams($model,$code,$videoSource);
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
	
	function __getParams(&$model,$code,$videoSource) {
		if (!empty($code)&&(is_string($videoSource)||is_int($videoSource))) {
			if($videoSource==1){
				$value=$this->__getVimeo($code);
			}else{
				$value=$this->__getYoutube($code);
			}
		}else{
			$value=$code;
		}
		return $value;
	}
	
	function __getYoutube($code) {
		$result['codigo']=$code;
		$result['url']='http://www.youtube.com/watch?v='.$code;
		$result['g_img']='http://i3.ytimg.com/vi/'.$code.'/0.jpg';
		$result['m_img']='http://i3.ytimg.com/vi/'.$code.'/1.jpg';
		$result['p_img']='http://i3.ytimg.com/vi/'.$code.'/2.jpg';
		return $result;
	}
	
	function __getVimeo($code) {
		App::import('Core', 'HttpSocket');
		$HttpSocket = new HttpSocket();
		$json = $HttpSocket->get('http://vimeo.com/api/v2/video/'.$code.'.json'); 
		$json=json_decode($json,true);
		if($json!=NULL){
			$result['codigo']=$code;
			$result['url']=$json[0]['url'];
			$result['g_img']=$json[0]['thumbnail_large'];
			$result['m_img']=$json[0]['thumbnail_medium'];
			$result['p_img']=$json[0]['thumbnail_small'];
			return $result;
		}else{
			return $code;
		}
	}
	
}	
?>