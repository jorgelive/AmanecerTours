<?php
class Paginasenlace extends AppModel {
	var $name = 'Paginasenlace';
	var $actsAs = array(
		'Tree'
		,'Acl' => array(
			'type' => 'controlled'
			,'mode'=>array('self')
		)
		,'i18n' => array(
			'fields' => array('title')
			,'display' => 'title'
		)
		,'File'=>array(
			'fields'=>array(
				'imagen'=>array(
					'allowFlash'=>true
					,'resize'=>array('width'=>'400','height'=>'400','allow_enlarge'=>true)
				)
			)
		)
	);
	
	var $validate = array(
		'title' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese título del enlace'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 40)
                ,'message' => 'El título debe ser menor de 40 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El título debe mas de 4 caracteres'
            )
		)
		,'url' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese la dirección del enlace'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 150)
                ,'message' => 'La dirección debe ser menor de 150 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 1)
                ,'message' => 'La dirección debe tener mas de 1 caracteres'
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
                ,'message' => 'Externo debe ser solo 1 ó 0'
            )
		)
	);
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		$lenguaje=I18n::getInstance()->l10n->__l10nCatalog[Configure::read('Empresa.language')]['locale'];
		foreach ($this->data['Paginasenlace'] as $key => $valor):
			$posicion=strpos($key, 'title_');
			if (is_numeric($posicion)){
				if ($key=='title_'.$lenguaje){
					return $valor;
				}
			}
		endforeach;
		return 'noactjg';
	}
}
?>