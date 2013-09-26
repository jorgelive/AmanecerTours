<?php
class Paginasadjunto extends AppModel {
	var $name = 'Paginasadjunto';
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
				'adjunto'=>array(
					'type'=>'archivo'
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
                ,'message' => 'Ingrese título del archivo adjunto'
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
		,'adjunto' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese un archivo válido'
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
		if(array_key_exists('pagina_id', $this->data['Paginasadjunto'])){
			if (isset($this->data['Paginasadjunto']['pagina_id'])){
				return $this->data['Paginasadjunto']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>