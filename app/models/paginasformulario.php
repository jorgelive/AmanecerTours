<?php
class Paginasformulario extends AppModel {
	var $name = 'Paginascontactoformulario';
	var $useTable = false; 
	var $validate = array(
		'name' => array(
			'empty' => array(
				'rule' => 'notEmpty'
				,'required' => true
				,'message' => 'nombre_required'
				,'last' => true
			)
			,'maxlength' => array(
				'rule' => array('maxLength', 40)
				,'required' => false
				,'allowEmpty' => true
				,'message' => 'nombre_max40'
			)
			,'minlength' => array(
				'rule' => array('minLength', 7)
				,'required' => false
				,'allowEmpty' => true
				,'message' => 'nombre_min7'
			)
		)
		,'email' => array(
			'empty' => array(
				'rule' => 'notEmpty'
				,'required' => true
				,'message' => 'correo_required'
				,'last' => true
			)
			,'email' => array(
				'rule' => 'email'
				,'required' => true
				,'allowEmpty' => true
				,'message' => 'correo_valido'
			
			)
		)
		,'title' => array(
			'empty' => array(
				'rule' => 'notEmpty'
				,'required' => true
				,'message' => 'titulo_required'
				,'last' => true
			)
			,'maxlength' => array(
				'rule' => array('maxLength', 60)
				,'required' => false
				,'allowEmpty' => true
				,'message' => 'title_max60'
			)
			,'minlength' => array(
				'rule' => array('minLength', 10)
				,'required' => false
				,'allowEmpty' => true
				,'message' => 'title_min10'
			)
		)
        ,'pais' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'pais_required'
                ,'last' => true
                )
            ,'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'required' => false
                ,'allowEmpty' => true
                ,'message' => 'pais_max30'
                )
            ,'minlength' => array(
                'rule' => array('minLength', 3)
                ,'required' => false
                ,'allowEmpty' => true
                ,'message' => 'pais_min3'
                )
            )
        ,'contenido' => array(
			'empty' => array(
				'rule' => 'notEmpty'
				,'required' => true
				,'message' => 'contenido_required'
				,'last' => true
			)
			,'maxlength' => array(
				'rule' => array('maxLength', 4000)
				,'required' => false
				,'allowEmpty' => true
				,'message' => 'contenido_max4000'
			)
			,'minlength' => array(
				'rule' => array('minLength', 10)
				,'required' => false
				,'allowEmpty' => true
				,'message' => 'contenido_min10'
			)
		)
	);
	
	function dummy(){
		$dummy = __('nombre_required',true);
		$dummy = __('nombre_max40',true);
		$dummy = __('nombre_min7',true);
		$dummy = __('correo_required',true);
		$dummy = __('correo_valido',true);
		$dummy = __('titulo_required',true);
		$dummy = __('title_max60',true);
		$dummy = __('title_min10',true);
        $dummy = __('pais_required',true);
        $dummy = __('pais_max30',true);
        $dummy = __('pais_min3',true);
		$dummy = __('contenido_required',true);
		$dummy = __('contenido_max4000',true);
		$dummy = __('contenido_min10',true);
	}
	
	function invalidate($field, $value = null){
		if (!is_array($this->validationErrors)){
			$this->validationErrors = array();
		}
		if(empty($value)){
			$value = true;
		}
		$this->validationErrors[$field] = __($value, true);
	} 
}
?>