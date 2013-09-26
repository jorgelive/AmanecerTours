<?php
class ArosController extends AppController {
    var $name = 'Aros';
	var $components = array('RequestHandler','Acl','Auth');
	var $uses = array('Aro');
	
    function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('nada');
	}
	
	function treelist(){
		Configure::write('debug', 0);
		$this->Aro->displayField= 'alias';
		$aros=$this->Aro->generatetreelist();
		$nro=0;
		foreach ($aros as $key=>$alias):
			$result['Aro'][$nro]['id']=$key;
			$result['Aro'][$nro]['alias']=$alias;
			$nro++;
		endforeach;
		if (isset($result)){$this->set('result', $result);}else{$this->set('result', '');}
		$this->render('/elements/ajax');
	}
	
}
?>