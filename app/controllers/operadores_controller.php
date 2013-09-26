<?php
class OperadoresController extends AppController {
	var $name = 'Operadores';
	var $uses = array('Operador','Compania','Ciudad'); 
	var $components = array('RequestHandler','Auth','Acl');

   
   function beforefilter(){
		parent::beforeFilter();
		$this->Auth->allow('login','logout');
	}

	function index() {
		$this->pageTitle = 'Operadores';
	}

	function listado(){
		Configure::write('debug', 0);
		$listacake = $this->Operador->find('list');
		$nro=0;
		foreach ($listacake as $key => $name):
			$result['Operador'][$nro]['id']=$key;
			$result['Operador'][$nro]['name']=$name;
			$nro++;
		endforeach;
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
	}
}
?>
