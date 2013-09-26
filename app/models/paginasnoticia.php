<?php
class Paginasnoticia extends AppModel {
	var $name = 'Paginasnoticia';
	var $actsAs = array(
		'Acl' => array(
			'type' => 'controlled'
			,'mode'=>array('self')
		)
		,'i18n' => array(
			'fields' => array('title','contenido')
			,'display' => 'title'
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
		'title' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese título de la noticia'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 200)
                ,'message' => 'El título de la noticia debe tener como máximo 200 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El titulo de la noticia debe tener como mínimo 4 caracteres'
            )
		)
		,'contenido' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese contenido del noticia'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength',2000)
                ,'message' => 'El contenido debe tener como máximo 1000 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 20)
                ,'message' => 'El contenido debe tener como mínimo 20 caracteres'
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
		$lenguaje=I18n::getInstance()->l10n->__l10nCatalog[Configure::read('Empresa.language')]['locale'];
		foreach ($this->data['Paginasnoticia'] as $key => $valor):
			$posicion=strpos($key, 'title_');
			if (is_numeric($posicion)){
				if ($key=='title_'.$lenguaje){
					return $valor;
				}
			}
		endforeach;
		return 'noactjg';
	}
}
?>