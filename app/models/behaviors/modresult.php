<?php

class ModresultBehavior extends ModelBehavior {

	var $modelos=array();
    var $defaultvalue=array('campos'=>array(),'booldependiente'=>false);
    var $ancestorfind=false;

    function setup(&$model, $settings = array()) {
        foreach ($model->hasMany as $key => $valor):
            $this->modelos{$key}='';
        endforeach;
        foreach ($model->hasOne as $key => $valor):
            $this->modelos{$key}='';
        endforeach;
        foreach ($model->belongsTo as $key => $valor):
            $this->modelos{$key}='';
        endforeach;
        foreach ($this->modelos as $key => $value):
            if(isset($model->$key->actsAs['Modresult'])&&!empty($model->$key->actsAs['Modresult'])){
                $this->modelos{$key}=$model->$key->actsAs['Modresult'];
            }else{
                unset($this->modelos{$key});
            }
        endforeach;
        if(isset($settings)&&!empty($settings)){
            $this->modelos{$model->name}=$settings;
        }

        foreach($this->modelos as $modelname=>$settings):
            $this->modelos{$modelname}=$this->_processDefault($settings);
        endforeach;

	}

    function _processDefault($settings){
        foreach($settings as $key => $setting):
            if(isset($this->$key)){
                $settings{$key}=Set::merge($this->$key,$setting);
            }
        endforeach;
        return $settings;
    }
	
	function afterFind(&$model, $results, $primary) {
        foreach($this->modelos as $modelName => $settings):
            foreach($results as $key => $result):
                if(isset($results{$key}{$model->name}['id'])){

                    if(isset($settings['defaultvalue'])
                        &&!empty($settings['defaultvalue'])
                        &&array_count_values($settings['defaultvalue']['campos'])>=1
                    ){
                        if($settings['defaultvalue']['booldependiente']===false){
                            if(!isset($results{$key}{$modelName})){
                                $results{$key}{$modelName}=array();
                            }
                            $results{$key}{$modelName}=$this->_defaultValue($results{$key}{$modelName},$settings['defaultvalue']['campos']);
                        }elseif(is_string($settings['defaultvalue']['booldependiente'])){
                            $dependiente=explode('.',$settings['defaultvalue']['booldependiente']);
                            if(!isset($dependiente[1])){
                                $dependiente[1]=$dependiente[0];
                                $dependiente[0]=$model->name;

                            }
                            if(!empty($results{$key}{$dependiente[0]}{$dependiente[1]})){
                                if(!isset($results{$key}{$modelName})){
                                    $results{$key}{$modelName}=array();
                                }
                                $results{$key}{$modelName}=$this->_defaultValue($results{$key}{$modelName},$settings['defaultvalue']['campos']);
                            }
                        }
                    }

                    if(isset($settings['ancestorfind'])
                        &&!empty($settings['ancestorfind'])
                    ){
                        if(array_key_exists('parent_id', $results{$key}{$modelName})){
                            if(is_null($results{$key}{$modelName}{'parent_id'})){
                                $results{$key}{$modelName}{'ancestor'}=$results{$key}{$modelName}{'id'};
                            }else{
                                $results{$key}{$modelName}{'ancestor'}=$this->_ancestorFind($model,$modelName,$results{$key}{$modelName}{'parent_id'});

                            }
                        }
                    }
                }
            endforeach;
        endforeach;

        return $results;
	}

    function _ancestorFind(&$model,$modelName,$parent_id){
        $padre=$model->findById($parent_id);
        if(!empty($padre)){
            if(is_null($padre{$modelName}{'parent_id'}))
            {
                return $padre{$modelName}{'id'};
            }else{
                $this->_ancestorFind($model,$modelName,$padre{$modelName}{'parent_id'});
            }
        }else{
            return null;
        }

    }

    function _defaultValue($data,$campos){
        foreach($campos as $key => $value):
            if(
                !isset($data[$key])
                ||empty($data{$key})
            ){
                $data{$key}=$value;
            }

        endforeach;
        if(
            !isset($data['id'])
            ||empty($data['id'])
        ){
            $data['id']=1;
        }
        return $data;
    }
}