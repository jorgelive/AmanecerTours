<?php
class Paginascabecera extends AppModel {
	var $name = 'Paginascabecera';
	var $actsAs = array(
		'Tree'
		,'Acl' => array(
			'type' => 'controlled'
			,'mode'=>array('self')
		)
		,'i18n' => array(
			'fields' => array('title','texto')
			,'display' => 'title'
		)
		,'File'=>array(
			'fields'=>array(
				'imagen'=>array(
					'type'=>'imagen'
					,'resize'=>array('width'=>'1920','height'=>'400','allow_enlarge'=>false)
				)
			)
		)
	);
	
	var $validate = array(
		'title' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 40)
                ,'message' => 'El título ser menor de 40 caracteres'
            )
		)
		,'texto' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 300)
                ,'required' => true
                ,'message' => 'El texto debe tener como máximo 300 caracteres '
            )
		)
		,'url' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 150)
                ,'message' => 'El enlace ser menor de 150 caracteres'
            )
		)
		,'externo' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Seleccione si el enlace es externo o interno'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'externo debe ser solo 1 ó 0'
            )
		)
		,'imagen' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese una imágen válida'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 250)
                ,'message' => 'El tipo debe tener como máximo 250 caracteres'
            )
		)
		,'tiempo' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el tiempo de reproducción'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El tiempo de reproducción debe tener como máximo 3 caracteres'
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El tiempo de reproducción debe ser un valor numérico'
            )
		)
		
	);
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		//print_r($this->data);
		$lenguaje=I18n::getInstance()->l10n->__l10nCatalog[Configure::read('Empresa.language')]['locale'];
		foreach ($this->data['Paginascabecera'] as $key => $valor):
			$posicion=strpos($key, 'title_');
			if (is_numeric($posicion)){
				if ($key=='title_'.$lenguaje){
					if(!empty($valor)){
						return $valor;
					}else{
						return "Sin título";
					}
					
				}
			}
		endforeach;
		return 'noactjg';
	}
}
?>