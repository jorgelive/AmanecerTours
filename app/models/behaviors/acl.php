<?php
/*
 */
class AclBehavior extends ModelBehavior {


	var $__typeMaps = array('requester' => 'Aro', 'controlled' => 'Aco');


	function setup(&$model, $config = array()) {
		if (is_string($config)) {
			$config = array('type' => $config);
		}
		$this->settings[$model->name] = array_merge(array('type'=>'requester','mode'=>NULL), (array)$config);

		$type = $this->__typeMaps[$this->settings[$model->name]['type']];
		if (!class_exists('AclNode')) {
			require LIBS . 'model' . DS . 'db_acl.php';
		}
		if (PHP5) {
			$model->{$type} = ClassRegistry::init($type);
		} else {
			$model->{$type} =& ClassRegistry::init($type);
		}
	}

	function node(&$model, $ref = NULL) {
		$type = $this->__typeMaps[strtolower($this->settings[$model->name]['type'])];
		if (empty($ref)) {
			$ref = array('model' => $model->name, 'foreign_key' => $model->id);
		}
		//print_r ($model->{$type}->node($ref));
		return $model->{$type}->node($ref);
	}
/**
 * Creates a new ARO/ACO node bound to this record
 *
 * @param boolean $created True if this is a new record
 * @return void
 * @access public
 */
	function afterSave(&$model, $created) {
		$type = $this->__typeMaps[strtolower($this->settings[$model->alias]['type'])];
		
		if ($this->settings[$model->alias]['mode']==NULL){
			
			if ($model->parentNode()!='noactjg'){
				$parent = $model->parentNode();
				if (!empty($parent)) {
					$parent = $this->node($model, $parent);
				}
				$data['parent_id'] = isset($parent[0][$type]['id']) ? $parent[0][$type]['id'] : null;
			}
		}else{
			foreach ($this->settings[$model->alias]['mode'] as $modo => $valor):
				break;
			endforeach;
			if ($modo=='self'){
				$parent = $model->{$type}->find('first',array('conditions'=>array('alias'=>$model->alias.'::Auto','model'=>$model->alias)));
				if (empty($parent)){
					$autodata=array();
					$autodata['alias']=$model->alias.'::Auto';
					$autodata['model']=$model->alias;
					$autodata['parent_id']=NULL;
					$autodata['foreign_key']=NULL;
					$model->{$type}->create();
					$model->{$type}->save($autodata);
					$data['parent_id']=$model->{$type}->id;
				}else{
					$data['parent_id']=$parent{$type}['id'];
				}
			}elseif($modo=='belongsto'){
				if ($model->parentNode()!='noactjg'){
					$parent = $model->{$type}->find('first',array('conditions'=>array('foreign_key'=>$model->parentNode(),'model'=>$valor)));
					$data['parent_id'] = $parent{$type}['id'];
				}
			}
		}
		
		if ($model->alias()!='noactjg'){
			$data['alias']=$model->alias();
		}
		$data['model'] = $model->alias;
		$data['foreign_key'] = $model->id;
		
		if (!$created) {
			$node = $this->node($model);
			$data['id'] = isset($node[0][$type]['id']) ? $node[0][$type]['id'] : null;
		}
		$model->{$type}->create();
		$model->{$type}->save($data);
	}
	
/**
 * Destroys the ARO/ACO node bound to the deleted record
 *
 * @return void
 * @access public
 */
	function afterDelete(&$model) {
		$type = $this->__typeMaps[strtolower($this->settings[$model->name]['type'])];
		$node = Set::extract($this->node($model), "0.{$type}.id");
		if (!empty($node)) {
			$model->{$type}->delete($node);
		}
	}
}

?>