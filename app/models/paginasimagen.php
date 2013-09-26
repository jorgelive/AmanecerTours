<?php
class Paginasimagen extends AppModel {
	var $name = 'Paginasimagen';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'File'=>array(
			'fields'=>array(
				'imagen'=>array(
					'resize'=>array('width'=>'1200','height'=>'800','allow_enlarge'=>true)
				)
			)
		)
		,'i18n' => array(
			'fields' => array('title')
			,'display' => 'title'
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
                ,'message' => 'El identificador de página debe debe ser un valor numérico'
            )
		)
		,'title' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese título de la imagen'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 40)
                ,'message' => 'El título debe tener como máximo 40 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El título debe tener como mínimo 4 caracteres'
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
		if(array_key_exists('pagina_id', $this->data['Paginasimagen'])){
			if (isset($this->data['Paginasimagen']['pagina_id'])){
				return $this->data['Paginasimagen']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>