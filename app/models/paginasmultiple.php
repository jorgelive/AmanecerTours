<?php
class Paginasmultiple extends AppModel {
	var $name = 'Paginasmultiple';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'i18n' => array(
			'fields' => array('title','contenido','resumen')
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
                ,'message' => 'Ingrese título del texto múltiple'
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
		,'contenido' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese las el contenido'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 20000)
                ,'message' => 'El contenido debe tener como máximo 20000 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 20)
                ,'message' => 'El contenido debe tener como mínimo 20 caracteres'
            )
		)
		,'resumen' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 250)
                ,'message' => 'El resumen debe tener como máximo 250 caracteres'
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
		if(array_key_exists('pagina_id', $this->data['Paginasmultiple'])){
			if (isset($this->data['Paginasmultiple']['pagina_id'])){
				return $this->data['Paginasmultiple']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>