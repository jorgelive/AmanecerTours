<?php
class Pagina extends AppModel {
	var $name = 'Pagina';
	var $actsAs = array(
		'Tree'
		,'File'
		,'Acl' => array(
			'type' => 'controlled'
			,'mode'=>array('self')
		)
		,'i18n' => array(
			'fields' => array('title')
			,'display' => 'title'
		)
		,'Video'
	);
	var $hasOne = array(
        'Paginasopcional' => array(
            'className' => 'Paginasopcional'
            ,'dependent' => true
        )
		,'Paginascontacto' => array(
            'className' => 'Paginascontacto'
            ,'dependent' => true
        )
		,'Paginastexto' => array(
            'className' => 'Paginastexto'
            ,'dependent' => true
        )
    ); 
	var $hasMany = array(
		'Paginasmultiple' => array(
            'className' => 'Paginasmultiple'
            ,'dependent' => true
        )
        ,'Paginasimagen' => array(
            'className' => 'Paginasimagen'
            ,'dependent' => true
        )
		,'Paginaspromocion' => array(
            'className' => 'Paginaspromocion'
            ,'dependent' => true
        )
		,'Paginasvideo' => array(
            'className' => 'Paginasvideo'
            ,'dependent' => true
        )
		,'Paginasadjunto' => array(
            'className' => 'Paginasadjunto'
            ,'dependent' => true
        )
    ); 
	
	var $validate = array(
		'title' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'allowEmpty' => false
                ,'message' => 'Ingrese título de la página'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'message' => 'El título debe tener como máximo 30 caracteres'
            )
            ,'minlength' => array(
                'rule' => array('minLength', 4)
                ,'message' => 'El título debe tener como mínimo 4 caracteres'
            )
		)
		,'parent_id' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'Parent debe tener como máximo 8 caracteres'
            )
		)
		,'mostrarinicio' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Seleccione si quiere mostrar o no en la página de inicio'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar en inicio debe ser solo 1 ó 0'
            )
		)
		,'texto' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
				,'message' => 'Seleccione si quiere mostrar o no el texto de la página'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar contacto debe ser solo 1 ó 0'
            )
		)
		,'imagen' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
				,'message' => 'Seleccione si quiere mostrar o no la galería de imágenes'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar galería de imágenes debe ser solo 1 ó 0'
            )
		)
		,'video' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
				,'message' => 'Seleccione si quiere mostrar o no la galería de videos'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar galería de videos debe ser solo 1 ó 0'
            )
		)
		,'adjunto' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
				,'message' => 'Seleccione si quiere mostrar o no el panel de adjuntos'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar panel de adjuntos debe ser solo 1 ó 0'
            )
		)
		,'promocion' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
				,'message' => 'Seleccione si quiere mostrar o no las promociones'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar promociones debe ser solo 1 ó 0'
            )
		)
		,'contacto' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Seleccione si quiere mostrar o no un formulario de contacto'
				,'last' => true
            )
            ,'boolean' => array(
                'rule' => array('boolean')
                ,'message' => 'Mostrar contacto debe ser solo 1 ó 0'
            )
		)
	);
	
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		$lenguaje=I18n::getInstance()->l10n->__l10nCatalog[Configure::read('Empresa.language')]['locale'];
		foreach ($this->data['Pagina'] as $key => $valor):
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