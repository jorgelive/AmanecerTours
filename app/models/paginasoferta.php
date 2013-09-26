<?php
class Paginasoferta extends AppModel {
	var $name = 'Paginasoferta';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'i18n' => array(
			'fields' => array('title','notas','condiciones')
			,'display' => 'notas'
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
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El identificador de página debe tener como máximo 8 caracteres'
            ),
            'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El identificador de página debe ser un valor numérico'
            )
		)
		,'title' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el título de de la ofertas'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 50)
                ,'message' => 'El título de de la ofertas debe tener como máximo 50 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 10)
                ,'message' => 'El título de de la ofertas debe tener como mínimo 10 caracteres'
            )
		)
		,'notas' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese las notas de la oferta'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 500)
                ,'message' => 'Las notas deben tener como máximo 500 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 20)
                ,'message' => 'Las notas deben debe tener como mínimo 20 caracteres'
            )
		)
		,'condiciones' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese las condiciones de la oferta'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 500)
                ,'message' => 'Las condiciones deben tener como máximo 500 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 20)
                ,'message' => 'Las condiciones deben tener como mínimo 20 caracteres'
            )
		)
		,'precio' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el precio'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 10)
                ,'message' => 'El precio debe tener como máximo 10 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 2)
                ,'message' => 'El precio debe tener como mínimo 4 caracteres'
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
		if(array_key_exists('pagina_id', $this->data['Paginasoferta'])){
			if (isset($this->data['Paginasoferta']['pagina_id'])){
				return $this->data['Paginasoferta']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>