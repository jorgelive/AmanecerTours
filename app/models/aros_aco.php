<?php

class ArosAco extends AppModel {
    var $name = 'ArosAco';
	var $validate = array(
		'aro_id' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el usuario o grupo para el permiso'
				,'last' => true
            )
			,'uniquecompuesta' => array(
				'rule'=>array('unicacompuesta', array('aco_id'))
				,'message'=>'Existe ya este solicitante para este recurso' 
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El indicador de solicitante debe tenner como máximo 8 caracteres'
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El indicador de solicitante debe ser un valor numérico'
            )
		)
		,'aco_id' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el recurso a controlar'
				,'last' => true
            )
			,'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El indicador de recurso a controlar debe tener como máximo 8 caracteres'
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El indicador de recurso a controlar debe ser un valor numérico'
            )
		)
		
	);
	
	function unicacompuesta($data, $fields) { 
		if (!is_array($fields)){ 
			$fields = array($fields); 
		}
		$filtrado = array_intersect_key($this->data{$this->name}, array_flip($fields));
		$filtrado = array_merge($data,$filtrado);
		$existe=$this->find('first',array('conditions'=>$filtrado));
		if(empty($existe)){
			return true;
		}else{
			//detectamos si actualiza con los mismos valores de campo
			if(count(array_intersect_assoc($filtrado,$existe{$this->name}))==count($filtrado)&&isset($this->id)&&$this->id==$existe{$this->name}['id']){
				return true;
			}else{
				return false;	
			}
		}
		
	}
}
?>