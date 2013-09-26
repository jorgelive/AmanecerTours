<?php
class Acluser extends AppModel {
    var $name = 'Acluser';
	var $belongsTo = array('Aclgroup');
    var $actsAs = array(
		'Acl' => array('type' => 'requester')
	);
	var $validate = array(
        'username' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese nombre de usuario'
				,'last' => true
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El nombre de usuario debe tener como mínimo 4 caracteres'
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 20)
                ,'message' => 'El nombre de usuario debe tener como máximo 20 caracteres'
            )
            ,'alphanum' => array(
                'rule' => 'alphaNumeric'
                ,'message' => 'El nombre de usuario debe tener solo letras y números'
            )
            ,'unique' => array(
                'rule' => 'isUnique'
                ,'message' => 'El nombre de usuario ya esta en uso'
            )
        )
        ,'clear_password' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'on' => 'create'
                ,'message' => 'ingrese una contraseña'
				,'last' => true
            )
            ,'length' => array(
                'rule' => array('minLength', 6)
                ,'message' => 'La contraseña debe tenet como mínimo 6 caracteres'
            )
        )
        ,'confirm_password' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'on' => 'create'
                ,'message' => 'Confirme la contraseña'
				,'last' => true
            )
            ,'eptyUpdate' => array(
                'rule' => 'emptyUpdate'
                ,'on' => 'update'
                ,'message' => 'Confirme la contraseña'
            )
            ,'match' => array(
                'rule' => 'matchPasswords'
                ,'message' => 'Las contraseñas no coinciden'
            )
        )
        ,'name' => array(
            'empty' => array(
				'rule' => 'notEmpty'
				,'required' => true
				,'message' => 'Ingrese nombre completo'
				,'last' => true
			)
            ,'maxlength' => array(
                'rule' => array('maxLength', 100)
                ,'message' => 'El nombre completo debe ter como máximo 100 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El nombre completo debe ter como mínimo 4 caracteres'
            )
        )
        ,'email' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese dirección de correo'
				,'last' => true
            )
            ,'email' => array(
                'rule' => 'email'
                ,'message' => 'Ingrese una dirección de correo válida'
            )
            ,'unique' => array(
                'rule' => 'isUnique'
                ,'message' => 'Este es el correo electrónico de otro usuario'
            )
        )
		/*,'operador_id' => array(
            'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese la oficina a la que pertenece'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El indicador de oficina debe tener como máximo 8 caracteres '
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El indicador de oficina debe ser un valor numérico'
            )
        )*/
    );

	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		if(array_key_exists('aclgroup_id', $this->data['Acluser'])){
			if (isset($this->data['Acluser']['aclgroup_id'])){
				return array('Aclgroup' => array('id' => $this->data['Acluser']['aclgroup_id']));
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		$data = $this->data;
		if (empty($this->data)) {
			$data = $this->read();
		}
		if(array_key_exists('username', $data['Acluser'])){
			return $data['Acluser']['username'];
		}else{
			return 'noactjg';
		}
	}
	
    function emptyUpdate() {
        if (!empty($this->data['Acluser']['clear_password'])&&empty($this->data['Acluser']['confirm_password'])) {
            return false;
        } else {
            return true;
        }
    }

    function matchPasswords() {
        if ($this->data['Acluser']['clear_password'] != $this->data['Acluser']['confirm_password']) {
            return false;
        } else {
            return true;
        }
    }
}
?>