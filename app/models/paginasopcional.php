<?php
class Paginasopcional extends AppModel {
	var $name = 'Paginasopcional';
	var $belongsTo = 'Pagina'; 
	var $actsAs = array(
		'Acl'=>array(
			'type'=>'controlled'
			,'mode'=>array(
				'belongsto'=>'Pagina'
			)
		)
		,'i18n' => array(
			'fields' => array('textocontacto','textoimagen','textovideo','textoadjunto','textopromocion','duracion')
			,'display' => 'confidencial'
		)
        ,'Modresult'=>array(
            'defaultvalue'=>array(
                'campos'=>array(
                    'textocontacto'=>'formulario_contacto'
                    ,'textoimagen'=>'galeria_imagenes'
                    ,'textovideo'=>'galeria_videos'
                    ,'textoadjunto'=>'panel_adjuntos'
                    ,'textopromocion'=>'panel_promociones'
                )
            )
        )
	);
	
	var $validate = array(
		'pagina_id' => array(
			'empty' => array(
                'rule' => 'notEmpty'
                ,'required' => true
                ,'message' => 'Ingrese identificador de la página'
				,'last' => true
            )
            ,'maxlength' => array(
                'rule' => array('maxLength', 8)
                ,'message' => 'El identificador de página debe tener como máximo 8 caracteres'
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'message' => 'El identificador de página debe ser un valor numérico'
            )
		)
		,'imagenpath' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 198)
                ,'message' => 'la ruta de la imágen debe tener como máximo 198 caracteres'
            )
		)
        ,'duracion' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 2)
                ,'message' => 'La duración debe  tener como máximo 2 caracteres'
            )
            ,'numeric' => array(
                'rule' => 'numeric'
                ,'allowEmpty' => true
                ,'message' => 'La duración debe ser un valor numérico'
            )
		)
		,'textocontacto' => array(
			'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'required' => true
                ,'message' => 'El texto debe tener como máximo 30 caracteres '
            )
		)
        ,'textoimagen' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'required' => true
                ,'message' => 'El texto debe tener como máximo 30 caracteres '
            )
        )
        ,'textovideo' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'required' => true
                ,'message' => 'El texto debe tener como máximo 30 caracteres '
            )
        )
        ,'textoadjunto' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'required' => true
                ,'message' => 'El texto debe tener como máximo 30 caracteres '
            )
        )
        ,'textopromocion' => array(
            'maxlength' => array(
                'rule' => array('maxLength', 30)
                ,'required' => true
                ,'message' => 'El texto debe tener como máximo 30 caracteres '
            )
        )
	);

    function dummy(){
        $dummy = __('formulario_contacto',true);
        $dummy = __('galeria_imagenes',true);
        $dummy = __('galeria_videos',true);
        $dummy = __('panel_adjuntos',true);
        $dummy = __('panel_promociones',true);
    }
	
	function alias(){
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		return $this->alias;
	}
	
	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return NULL;
		}
		if(array_key_exists('pagina_id', $this->data['Paginasopcional'])){
			if (isset($this->data['Paginasopcional']['pagina_id'])){
				return $this->data['Paginasopcional']['pagina_id'];
			}else{
				return NULL;
			}
		}else{
			return 'noactjg';
		}
	}
}
?>