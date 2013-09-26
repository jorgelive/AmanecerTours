<?php

class Compania extends AppModel {
	var $name = 'Compania';
	var $validate = array(
		'name' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese el nombre de la compañia'
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
                'message' => 'El nombre debe tener como mínimo 4 caracteres'
            )
		),
		'direccion' => array(
			'empty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Ingrese la dirección de la compañia'
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
                'message' => 'Ingrese el (los) teléfono(s) de la compañia'
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 50),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'Los teléfonos deben tener como máximo 50 caracteres'
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