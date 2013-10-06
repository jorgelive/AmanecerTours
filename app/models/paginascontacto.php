<?php
class Paginascontacto extends AppModel {
	var $name = 'Paginascontacto';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
        'i18n' => array(
            'fields' => array('texto')
            ,'display' => 'texto'
        )
		,'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array('belongsto'=>'Pagina')
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
        ,'texto' => array(
            'empty' => array(
                'rule' => 'notEmpty'
            ,'required' => true
            ,'on' => 'create'
            ,'message' => 'Ingrese el texto del formulario'
            ,'last' => true
            )
        ,'minlength' => array(
                'required' => true
            ,'rule' => array('minLength', 25)
            ,'on' => 'create'
            ,'message' => 'El texto debe tener como mínimo 25 caracteres'
            )
        ,'maxlength' => array(
                'required' => true
            ,'rule' => array('maxLength', 12000)
            ,'message' => 'El texto debe tener como máximo 12000 caracteres'
            )
        )
        ,'destinatario' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el correo electrónico'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 40)
                ,'message' => 'El correo electrónico debe tener como máximo 40 caracteres'
            )
			,'email' => array(
                'rule' => 'email'
                ,'message' => 'Ingrese un correo electrónico válido'
            )
        )
        ,'cco' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 40)
				,'required' => true
                ,'message' => 'El CCO debe tener como máximo 40 caracteres'
            )
			,'email' => array(
                'rule' => 'email'
                ,'message' => 'Ingrese una dirección CCO válida'
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
		if(array_key_exists('pagina_id', $this->data['Paginascontacto'])){
			if (isset($this->data['Paginascontacto']['pagina_id'])){
				return $this->data['Paginascontacto']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>