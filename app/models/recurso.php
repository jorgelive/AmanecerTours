<?php
class Recurso extends AppModel {
    var $name = 'Recurso';
	
	var $actsAs = array(
		'Tree'
	);
	
	var $validate = array(
		'parent_id' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El recurso superior debe tener como máximo 8 caracteres'
            )
		)
		,'tipo' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Seleccione el tipo de recurso'
				,'last' => true
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El tipo debe ser un valor numérico'
            )
			,'maxlength' => array(
                'rule' => array('maxLength', 1)
                ,'message' => 'El tipo debe tener como máximo 1 número'
            )
		)
		,'name' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'allowEmpty' => false
                ,'message' => 'Ingrese nombre del recurso'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 20)
                ,'message' => 'El nombre debe tener como máximo 20 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El nombre debe tener como mínimo 4 caracteres'
            )
		)
		,'descripcion' => array(
			'empty' => array(
                'rule' => 'notEmpty'
				,'required' => true
                ,'message' => 'Ingrese la descripción del recurso'
				,'last' => true
            )
			,'maxlength' => array(
				'rule' => array('maxLength', 255)
				,'message' => 'La descripción debe tener como máximo 255 caracteres'
            )
			,'minlength' => array(
				'rule' => array('minLength', 10)
				,'message' => 'La descripción debe tener como mínimo 10 caracteres'
            )
		)
		,'model' => array(
			'empty' => array(
                'rule' => 'notEmpty'
				,'required' => true
                ,'message' => 'Ingrese el modelo'
				,'last' => true
            )
			,'maxlength' => array(
				'rule' => array('maxLength', 40)
				,'message' => 'El modelo debe tener como máximo 40 caracteres'
            )
			,'minlength' => array(
				'rule' => array('minLength', 3)
				,'message' => 'El modelo debe tener como mínimo 3 caracteres'
            )
			,'unique' => array(
                'rule' => 'isUnique'
                ,'message' => 'El ya existe el recurso'
            )
		)
		,'destino' => array(
			'maxlength' => array(
				'rule' => array('maxLength', 40)
				,'message' => 'El destino debe tener como máximo 80 caracteres'
            )
		)
	);
}
?>