<?php
class Paginastexto extends AppModel {
	var $name = 'Paginastexto';
	var $belongsTo = 'Pagina';
    var $displayField = 'contenido';
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'i18n' => array(
			'fields' => array('contenido','resumen')
			,'display' => 'contenido'
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
		,'contenido' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
				,'on' => 'create'
                ,'message' => 'Ingrese contenido de la página'
				,'last' => true
            )
			,'minlength' => array(
				'required' => true
				,'rule' => array('minLength', 25)
				,'on' => 'create'
				,'message' => 'El contenido debe tener como mínimo 25 caracteres'
            )
			,'maxlength' => array(
				'required' => true
				,'rule' => array('maxLength', 12000)
				,'message' => 'El contenido debe tener como máximo 12000 caracteres'
            )
		)
		,'resumen' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 350)
                ,'required' => true
                ,'message' => 'El resumen debe tener como máximo 350 caracteres '
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
		if(array_key_exists('pagina_id', $this->data['Paginastexto'])){
			if (isset($this->data['Paginastexto']['pagina_id'])){
				return $this->data['Paginastexto']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>