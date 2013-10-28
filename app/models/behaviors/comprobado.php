<?php

class ComprobadoBehavior extends ModelBehavior {

	var $modelos=array();
    var $comprobarRango=array(
        'rango'=>array()
        ,'modelofields'=>null
    );
    var $comprobarDependientes=array(
        'modelos'=>array()
    );

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
            if(isset($model->$key->actsAs['Comprobado'])&&!empty($model->$key->actsAs['Comprobado'])){
                $this->modelos{$key}=$model->$key->actsAs['Comprobado'];
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
        if(!is_array($settings)){
            trigger_error("se requiere un array con los metodos a procesar por Comprobado");
            die();
            //todo: poner este metodo en el otro behavior
        }
        foreach($settings as $key => $setting):

            if(isset($this->$key)){
                $settings{$key}=Set::merge($this->$key,$setting);
            }
        endforeach;

        return $settings;
    }
	
	function afterFind(&$model, $results, $primary) {

        foreach($results as $key => $item):
            foreach($item as $modelName => $valores):
                if(
                    isset($results{$key}{$model->name}['id'])&&!empty($results{$key}{$model->name}['id'])
                    &&isset($results{$key}{$modelName}['id'])&&!empty($results{$key}{$modelName})
                    &&isset($this->modelos{$modelName}['comprobarRango'])
                    &&!empty($this->modelos{$modelName}['comprobarRango'])
                    &&array_count_values($this->modelos{$modelName}['comprobarRango']['rango'])>=1
                ){
                    $relacionado=null;
                    if(
                        isset($results{$key}{$this->modelos{$modelName}['comprobarRango']{'modelofields'}})
                        &&!empty($results{$key}{$this->modelos{$modelName}['comprobarRango']{'modelofields'}})
                    )
                    {
                        $relacionado=$results{$key}{$this->modelos{$modelName}['comprobarRango']{'modelofields'}};
                    }
                    $results{$key}{$modelName}=$this->_comprobarRango($model,$valores,$relacionado,$this->modelos{$modelName}['comprobarRango']);
                }
            endforeach;
        endforeach;

        //print_r($results);


        foreach($results as $key => $item):
            foreach($item as $modelName => $valores):
                if(
                    isset($results{$key}{$model->name}['id'])&&!empty($results{$key}{$model->name}['id'])
                    &&isset($results{$key}{$modelName}['id'])&&!empty($results{$key}{$modelName})
                    &&isset($this->modelos{$modelName}['comprobarDependientes'])
                    &&!empty($this->modelos{$modelName}['comprobarDependientes'])
                    &&array_count_values($this->modelos{$modelName}['comprobarDependientes']['modelos'])>=1
                ){
                    $results{$key}{$modelName}=$this->_comprobarDependientes($model,$valores,$results{$key},$this->modelos{$modelName}['comprobarDependientes'],$modelName);
                }

                //print_r($results);
            endforeach;
        endforeach;

        //print_r($results);
        return $results;

	}


    function _comprobarDependientes(&$model,$data,$existentes,$settings,$modelName){

        $extractAtFinish=false;

        if(!array_key_exists(0,$data)){
            $data=array($data);
            $extractAtFinish=true;
        }

        foreach($data as $key => $valor):
            $data{$key}{'notempty'}=$this->_comprobarDependienteProceso($model,$existentes,$settings{'modelos'},$data{$key}{'id'});
        endforeach;

        /*echo "<br>datos<br>";
        print_r($data);
        echo "<br>relacionado<br>";
        print_r($settings);*/

        if($extractAtFinish===true){
            $data=$data{0};
        }
       return $data;
    }

    function _comprobarDependienteProceso(&$model,$existentes,$modelos,$id){
        foreach($modelos as $modelo):
            if(isset($existentes{$modelo})&&!empty($existentes{$modelo})){
                if(!array_key_exists(0,$existentes{$modelo})){
                    if(isset($existentes{$modelo}{'id'})&&!empty($existentes{$modelo}{'id'})){
                        return 1;
                    }
                }else{
                    if(isset($existentes{$modelo}{0}{'id'})&&!empty($existentes{$modelo}{0}{'id'})){
                        return 1;
                    }
                }
            }
        endforeach;
        foreach($modelos as $modelo):
            if(isset($model->$modelo)){
                $result=$model->$modelo->find('first',array('conditions'=>array($modelo.'.'.strtolower($model->name).'_id'=>$id)));
                if(!empty($result)){
                    return 1;
                }
            }
        endforeach;
        return 0;
    }

    function _comprobarRango(&$model,$data,$relacionado,$settings){

        if(isset($settings['rango']['inicio'])&&isset($settings['rango']['fin'])&&isset($settings{'modelofields'})){

            $inicio=$settings['rango']['inicio'];
            $fin=$settings['rango']['fin'];
            $modelofields=$settings{'modelofields'};

            if(is_null($relacionado)&&isset($model->$modelofields)){
                $resultquery=$model->$modelofields->find('first',array('recursive'=>-1,'conditions'=>array($modelofields.'.'.strtolower($model->name).'_id'=>$data{'id'})));
                if(!empty($resultquery)){
                    $relacionado = $resultquery{$modelofields};
                }
            }elseif(is_null($relacionado)){
                //trigger_error("asumimos que si");
                return null;
            }

            $extractAtFinish=false;

            if(!array_key_exists(0,$data)){
                $data=array($data);
                $extractAtFinish=true;
            }

            if(!is_array($relacionado)){
                $relacionado=array(0=>array($inicio=>null,$fin=>null));

            }
            if(!array_key_exists(0,$relacionado)){
                $relacionado=array($relacionado);
            }

            foreach($data as $key => $valor):
                $data{$key}{'vigencia'}='ok';
                $relacionado{$key}{$inicio}=($relacionado{$key}{$inicio}=='0000-00-00')?null:$relacionado{$key}{$inicio};
                $relacionado{$key}{$fin}=($relacionado{$key}{$fin}=='0000-00-00')?null:$relacionado{$key}{$fin};

                if(empty($relacionado{$key}{$inicio})&&empty($relacionado{$key}{$fin})){
                    //para cuando la tabla vinculada existe pero no tiene registro asumimos ok
                    //no hago nada $data{$modelName}{'vigencia'}='vigente';
                }elseif(empty($relacionado{$key}{$inicio})){
                    if(strtotime($relacionado{$key}{$fin})<=time()){
                        $data{$key}{'vigencia'}='outdated';
                    }
                }elseif(empty($relacionado{$key}{$fin})){
                    if(strtotime($relacionado{$key}{$inicio})>=time()){
                        $data{$key}{'vigencia'}='soon';
                    }
                }else{
                    if(strtotime($relacionado{$key}{$inicio})>=time()){
                        $data{$key}{'vigencia'}='soon';

                    }else{
                        if(strtotime($relacionado{$key}{$fin})<=time()){
                            $data{$key}{'vigencia'}='outdated';
                        }
                    }
                }
            endforeach;
/*
            echo "<br>datos<br>";
            print_r($data);
            echo "<br>relacionado<br>";
            print_r($relacionado);*/

            if($extractAtFinish===true){
                $data=$data{0};
            }
        }
        return $data;
    }


}