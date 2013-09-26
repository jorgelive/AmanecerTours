<?php
class Paginasopcional extends AppModel {
	var $name = 'Paginasopcional';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'i18n' => array(
			'fields' => array('etiquetas')
			,'display' => 'confidencial'
		)
	);
	
	var $validate = array(
		'pagina_id' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese identificador de la página'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El identificador de página debe tener como máximo 8 caracteres'
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El identificador de página debe ser un valor numérico'
            )
		)
		,'idfoto' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese identificador de la foto'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 2)
                ,'message' => 'El identificador de la imágen debe tener como máximo 2 caracteres'
            )
            ,'maxlength' => array(
                'rule' => 'numeric'
                ,'message' => 'El identificador de la imágen debe ser un valor numérico'
            )
		)
		,'etiquetas' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 300)
                ,'required' => true
                ,'message' => 'Las etiquetas deben tener como máximo 300 caracteres '
            )
		)
		,'urlfija' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 10)
                ,'required' => true
                ,'message' => 'La dirección fija debe tener como máximo 10 caracteres'
            )
		)
	);
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		return $this->alias;
	}
	
	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		if(array_key_exists('pagina_id', $this->data['Paginasopcional'])){
			if (isset($this->data['Paginasopcional']['pagina_id'])){
				return $this->data['Paginasopcional']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>