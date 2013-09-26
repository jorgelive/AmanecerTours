<?php
class Paginastestimonio extends AppModel {
	var $name = 'Paginastestimonio';
	var $actsAs = array(
		'Acl' => array(
			'type' => 'controlled'
			,'mode'=>array('self')
		)
		,'File'=>array(
			'fields'=>array(
				'imagen'=>array(
					'resize'=>array('width'=>'400','height'=>'400','allow_enlarge'=>true)
				)
			)
		)
	);
	
	var $validate = array(
		'name' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese nombre del pasajero'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 40)
                ,'message' => 'El nombre del pasajero debe tener como máximo 40 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El nombre del pasajero debe tener como mínimo 4 caracteres'
            )
		)
		,'contenido' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese contenido del testimonio'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength',2000)
                ,'message' => 'El contenido debe tener como máximo 2000 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 30)
                ,'message' => 'El contenido debe tener como mínimo 30 caracter'
            )
		)
		,'nacionalidad' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese la nacionalidad'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 20)
                ,'message' => 'La nacionalidad debe tener como máximo 20 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'La nacionalidad debe tener como mínimo 4 caracteres'
            )
		)
		,'email' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese el email del pasajero'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 40)
                ,'message' => 'El correo electrónico debe tener como máximo 40 caracteres'
            )
			,'email' => array(
                'rule' => 'email'
                ,'message' => 'Ingrese un correo electrónico válido'
            )
		)
		,'fecha' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese la fecha'
				,'last' => true
            )
			,'date' => array(
                'rule' => array('date','ymd')
                ,'message' => 'Ingrese una fecha válida'
            )
		)
	);
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		if(isset($this->data['Paginastestimonio']['name'])){
			return $this->data['Paginastestimonio']['name'];
		}else{
			return 'noactjg';
		}
	}
}
?>