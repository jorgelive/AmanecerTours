<?php

class Ciudad extends AppModel {
	var $name = 'Ciudad';
	var $validate = array(
		'name' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese el nombre de la ciudad'
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 25),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El nombre debe tener como máximo 20 caracteres'
            ),
            'minlength' => array(
                'rule' => array('minLength', 4),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El nombre debe tener como mínimo 4 caracteres'
            )
		),
		'nivel' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese el nivel de importancia'
            ),
			'maxlength' => array(
                'rule' => array('maxLength', 1),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El nivel de importancia debe tener como máximo 1 caracter'
            ),
			'numeric' => array(
                'rule' => 'numeric',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El nivel de importancia debe ser un número'
            ),
		)
	);
}
?>