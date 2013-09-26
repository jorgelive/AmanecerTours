<?php

class Aclgroup extends AppModel {
    var $name = 'Aclgroup';
	var $actsAs = array(
		'Acl' => array('type' => 'requester')
		,'Tree'
	);
	var $hasMany = array(
        'Acluser' => array(
            'className' => 'Acluser'
            ,'dependent' => true
        )
    );   
	
	var $validate = array(
		'name' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese nombre del grupo'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 15)
                ,'message' => 'El nombre debe tener como maximo 15 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El nombre debe tener como mínimo 4 caracteres'
            )
		)
	);
	
	function parentNode(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		if(array_key_exists('parent_id', $this->data['Aclgroup'])){
			if (isset($this->data['Aclgroup']['parent_id'])){
				return array('Aclgroup' => array('id' => $this->data['Aclgroup']['parent_id']));
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
	
	function alias(){
		if (!$this->id) {
			return NULL;
		}
		$data = $this->read();
		if(array_key_exists('name', $data['Aclgroup'])){
			return $data['Aclgroup']['name'];

		}else{
			return 'noactjg';
		}
	}
}
?>