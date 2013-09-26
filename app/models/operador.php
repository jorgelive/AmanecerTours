<?php

class Operador extends AppModel {
	var $name = 'Operador';
	var $hasAndBelongsToMany = array(
		'Ciudad' =>	array ()
	);
	var $belongsTo = array(
		'Compania' =>	array ()
	);
	var $validate = array(
		'name' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese el nombre del operador'
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 40),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El nombre debe tener como máximo 40 caracteres'
            ),
            'minlength' => array(
                'rule' => array('minLength', 4),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El nombre debe tener como míximo 4 caracteres'
            )
		),
		'compania_id' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese la compañia'
            ),
			'maxlength' => array(
                'rule' => array('maxLength', 8),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El identificador de compañia debe tener como máximo 8 caracteres'
            ),
			'numeric' => array(
                'rule' => 'numeric',
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El identificador de compañia debe ser un número'
            ),
		),
		'direccion' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese la dirección del operador'
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 50),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'El dirección debe tener como máximo 50 caracteres'
            ),
            'minlength' => array(
                'rule' => array('minLength', 4),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'La dirección debe tener como mínimo 4 caracteres'
            )
		),
		'telefono' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese el (los) teléfono(s) del operador'
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 50),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Los teléfonos deben tener como maximo 50 caracteres'
            ),
            'minlength' => array(
                'rule' => array('minLength', 4),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Los teléfonos deben tener como mínimo 4 caracteres'
            )
		),
	);
}
?>