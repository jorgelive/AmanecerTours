<?php
class Paginasvideo extends AppModel {
	var $name = 'Paginasvideo';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'i18n' => array(
			'fields' => array('descripcion')
			,'display' => 'descripcion'
		)
		,'Video' =>array(
			'fields' => array('codigo'=>'fuente')
		)
	);
	var $validate = array(
		'pagina_id' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese identificador de la página'
				,'on' => 'create'
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
		,'fuente' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese la fuente del video'
				,'last' => true
            )
			,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'La fuente debe ser un valor numérico'
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 1)
                ,'message' => 'La fuente debe tener como máximo 1 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 1)
                ,'message' => 'La fuente debe tener como mínimo 1 caracteres'
            )
		)
		,'codigo' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el código del video'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 100)
                ,'message' => 'El código del video debe tener como máximo 100 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El código del video debe tener como mínimo 4 caracteres'
            )
		)
		,'descripcion' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese la descripción de la oferta'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 200)
                ,'message' => 'La descripción debe tener como máximo 200 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'La descripción debe tener como mínimo 4 caracteres'
            )
		)
	);
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		return $this->alias.'::'.$this->id;
	}
	
	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		if(array_key_exists('pagina_id', $this->data['Paginasvideo'])){
			if (isset($this->data['Paginasvideo']['pagina_id'])){
				return $this->data['Paginasvideo']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>