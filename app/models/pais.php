<?php

class Pais extends AppModel {
	var $name = 'Pais';
	var $validate = array(
		'name' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el nombre del país'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 25)
                ,'message' => 'El nombre debe tener como máximo 25 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El nombre debe tener como mínimo 4 caracteres'
            )
		)
		,'nivel' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el nivel de importancia'
				,'last' => true
            )
			,'maxlength' => array(
                'rule' => array('maxLength', 1)
                ,'message' => 'El nivel debe tener como máximo 2 caracteres'
            )
			,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El nivel debe ser un número'
            )
		)
	);
}
?>