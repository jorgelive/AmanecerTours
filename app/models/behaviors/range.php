<?php
		/*'range' => array(
			'fields'=>array(
				'publicado'=>array(
					'inicio'=>'Paginasopcional.publicado_inicio'
					,'final'=>'Paginasopcional.publicado_final'
				)
				,'oferta'=>array(
					'inicio'=>'Paginasoferta.inicio'
					,'final'=>'Paginasoferta.final'
				)
			)
		)*/


class rangeBehavior extends ModelBehavior {

	function setup(&$model, $settings = array()) {
		if (!isset($settings['fields'])) $settings['fields']=array();
		$fields=array();
		
		$model->Behaviors->attach('Containable');
		
		
		foreach($settings['fields'] as $key=>$value) {
			$field=ife(is_numeric($key),$value,$key);
			if(!$model->hasField($field)) {
				trigger_error('El campo "'.$field.'" no existe en el modelo "'.$model->name.'".', E_USER_WARNING);
			}
			
			$conf=ife(is_numeric($key),array(),ife(is_array($value),$value,array()));
			$fields[$field]=$conf;
		}
		$settings['fields']=$fields;
		$this->settings[$model->name] = $settings;
	}
	
	function beforeFind(&$model,$querydata) { 
		echo "hola";
		print_r($querydata);
		//print_r($this->settings);
		$model->contain('Paginaspromocion');
		foreach ($this->settings as $modelName => $dummy):
			extract($this->settings[$modelName]);
			foreach ($fields as $field => $settings):
				if(array_key_exists($modelName.'.'.$field,$querydata['conditions'])||array_key_exists('`'.$modelName.'`.`'.$field.'`',$querydata['conditions'])){
					print_r($settings['inicio']);
					$querydata['conditions'][$settings['inicio'].' >=']='inicio';
					$querydata['conditions'][$settings['final'].' <=']='final';
					//print_r($querydata['conditions']);
				}
			endforeach;
			
			
		endforeach;	
		return $querydata;
	} 	
}	
?>