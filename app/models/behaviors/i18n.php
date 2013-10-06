<?php
/**
 * Requires:
 * CakePHP 1.2.1.8004
 *
 * I18n behavior for database content internationalization using locale dependent table field names.  
 *
 * I18n behavior integration steps:
 * 1. Identify which languages you are going to use 
 *	(e.g. English and Russian)
 * 2. Identify your default language 
 *	(e.g. English);
 * 3. Identify fields of your models to be internationalized (
 *	(e.g. model Country field 'name' should be i18n compatible);
 * 4. Update your database tables for each model field to be i18n compatible 
 *	(e.g. rename 'name' field to <name>.'_'.DEFAULT_LANGUAGE - default, and create field 'name_rus' that will be russian content); 
 * 5. Add to your model this behavior;
 *	(e.g. $artAs = array('i18n' => array('fields' => array('name'), 'display' => 'name');) 
 * 6. Add to all models that are associated with i18n compatible models this behavior;
 *	(e.g. $actAs = array('i18n'); //you can simply add this to each model )
 *	Its necessary because beforeFind and afterFind invoked for the behavior of the model that calls find method. 
 *	During beforeFind and afterFind the behavior will look for any i18n behaviors, see _localizeScheme and _unlocalizeResults.
 * 7. In your model you can set $displayField as usual. The i18n behavior will unlocalize result field names in afterFind. Default $displayField is 'name'.
 * 8. In your model you can set $order as usual. The i18n behavior will localize your order field name in beforeFind.
 * 9. In your relations you can set order attribute for one field and it will be localized.
 * 10. To save multiple locales pass data with database field names.
 *  (e.g. 'name_rus', 'name_eng');
 * 11. To save data in to current locale pass data without locale profex.
 *  (e.g. 'name' will be saved to 'name_eng' if current locale is 'eng');
 * 12. To load values for all locales detach the i18n behavior before calling model read.
 * (e.g. $this->MyModel->Behaviors->detach('i18n'); $this->MyModel->read();)
 * 13. i18n can be used with Containable behaviour, but becuase it relies on recursion while searching for localizable 
 * fields througth relations, check you have enougth recursion level (default recursion=1);
 *
 * PHP versions 4 and 5
 *
 * Copyright 2008, Palivoda IT Solutions, Inc.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2008, Palivoda IT Solutions, Inc.
 * @link			http://www.palivoda.eu
 * @package		app
 * @subpackage		app.models.behaviors
 * @since			CakePHP(tm) v 1.2
 * @version			$Revision:  $
 * @modifiedby		$LastChangedBy:  $
 * @lastmodified		$Date: $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class I18nBehavior extends ModelBehavior {
 
	var $fields = array();
 
	function setup(&$model, $config = array()) {
		if (Configure::read('Config.language')==NULL||Configure::read('Empresa.language')==NULL) {
			trigger_error("Falta Configure::write('Config.language') o Configure::write('Empresa.language')");
		}
		if (!empty($config['fields'])) {
			$this->fields[$model->alias] = array_fill_keys($config['fields'], null);
		}
	}
 
	function cleanup(&$model) {
		$this->_refreshSchema($model);
	}
 
	function beforeFind(&$model, &$query) {
		$locale = $this->_getLocale($model);
		if (isset($model->locale) && $locale != $model->locale) $this->_refreshSchema($model);
 
		$recursive = empty($query['recursive']) ? 
			(empty($model->recursive) ? 0 : $model->recursive) 
				: $query['recursive'];
 
		$this->_localizeScheme($model, $locale, $recursive);
		$this->_localizeQuery($model, $query, $recursive, true);
		return $query;
	}
 
	function __localizeArrayInQuery(&$model, &$section, $localField, $localAlias, $isPrimary, &$level) {
		if ($level <= 0) return; //rectrict recursion level
		if (is_array($section)) {
			foreach($section as $queryAlias => &$queryField) {
				if (is_array($queryField)) {
					if ($queryAlias == $model->alias) $isPrimary = true;
					$this->__localizeArrayInQuery($model, $queryField, $localField, $localAlias, $isPrimary, $level);
				}
				else {
					if (preg_match('/(^|,| )('.$model->alias.'.'.$localField.')(,| |$)/i', $queryField))
						$queryField = preg_replace('/(^|,| )('.$model->alias.'.'.$localField.')(,| |$)/i', 
							'$1'.$model->alias.'.'.$localAlias.'$3', $queryField);
					else if ($isPrimary && preg_match('/(^|,| )('.$localField.')(,| |$)/i', $queryField))
						$queryField = preg_replace('/(^|,| )('.$localField.')(,| |$)/i', 
							'$1'.$localAlias.'$3', $queryField);
				}
			}
			$oldKeys = array();
			foreach($section as $queryAlias => &$queryField) {
				if (preg_match('/(^|,| )('.$model->alias.'.'.$localField.')(,| |$)/i', $queryAlias)) {
					$newKey = preg_replace('/(^|,| )('.$model->alias.'.'.$localField.')(,| |$)/i', 
							'$1'.$model->alias.'.'.$localAlias.'$3', $queryAlias);
					$section[$newKey] = $queryField;
					$oldKeys[] = $queryAlias;
				}
				else if ($isPrimary && preg_match('/(^|,| )('.$localField.')(,| |$)/i', $queryAlias)) {
					$newKey = preg_replace('/(^|,| )('.$localField.')(,| |$)/i', 
						'$1'.$localAlias.'$3', $queryAlias);
					$section[$newKey] = $queryField;
					$oldKeys[] = $queryAlias;
				}
			}
			foreach($oldKeys as $removeKey) {
				unset($section[$removeKey]);
			}
			unset($queryAlias); unset($queryField); unset($section);
		}
		else {
			if (strstr($section, $model->alias.'.'.$localField) != false)
				$section = str_replace($model->alias.'.'.$localField, $model->alias.'.'.$localAlias, $section);
			else if ($isPrimary && strstr($section, $localField) != false)
				$section = str_replace($localField, $localAlias, $section);
		}
 
	}
 
	function _localizeQuery(&$model, &$query, $recursive, $isPrimary) {
		if (isset($model->Behaviors->i18n) && isset($model->Behaviors->i18n->fields[$model->alias])) {
			foreach($model->Behaviors->i18n->fields[$model->alias] as $localField => $localAlias) { //$localAlias set by _localizeScheme
				foreach(array('fields', 'contain', 'conditions', 'order') as $section) {
					if (isset($query[$section])) {
						$level = 3; //recursion level for __localizeArrayInQuery only
						$this->__localizeArrayInQuery($model, $query[$section], $localField, $localAlias, $isPrimary, $level);
					}
				}
				if ($isPrimary && 
					is_array($query['fields']) &&
					$model->displayField == $localField &&
					!in_array($model->alias.'.'.$localAlias,  $query['fields']) &&
					!in_array($localAlias,  $query['fields']) ) {
						$query['fields'] = array_values(array_unique($query['fields']));
						$query['fields'][] = $model->alias.'.'.$localAlias;
						$query['list']['valuePath'] = '{n}.'.$model->alias.'.'.$localField; 
 
				}
			}
		}
		if (empty($recursive)) $recursive = 0;
		if ($recursive < 0) return;
		foreach(array('belongsTo','hasOne','hasMany','hasAndBelongsToMany') as $relationGroup) {
			if (isset($model->$relationGroup)) {
				foreach ($model->$relationGroup as $name => &$relation) {
					if (isset($model->Behaviors->i18n)) {
						$model->Behaviors->i18n->_localizeQuery($model->$name, $query, $recursive-1, false);
					}
				}
			}
		}
	}

	function _localizeScheme(&$model, $locale, $recursive, &$relation = null) {
		$model->locale = $locale;
		if (isset($model->Behaviors->i18n) && isset($model->Behaviors->i18n->fields[$model->alias])) {
			foreach($model->Behaviors->i18n->fields[$model->alias] as $configName => &$configAlias) {
				$foundSpecific = false;
				foreach($model->_schema as $shemaName => $v) {
					if (strpos('_'.$shemaName, $configName) == 1) { //is one of i18n fields
						if ($configName.'_'.$locale != $shemaName) { //not for default locale
							if ($configName.'_'.$locale != $shemaName) { //not for current locale
								unset($model->_schema[$shemaName]);
							}
							else {
								$foundSpecific = true;
								$configAlias = $configName.'_'.$locale;
							}
						}
					}
				}
				unset($shemaName); unset($v);
				if ($foundSpecific) { //found locale specific content, no need in default content
					unset($model->_schema[$configName.'_'.$locale]);
				}
				else {
					$configAlias = $configName.'_'.$locale;
				}
				if (empty($model->displayField) || $model->displayField == 'id') {
					if (isset($this->fields[$model->alias]['name'])) {
						$model->displayField = 'name';
					}
					if (isset($this->fields[$model->alias]['title'])) {
						$model->displayField = 'title';
					}
				}
				if (isset($relation)) {
					$sections = array(&$relation['fields'], &$relation['order'], &$relation['conditions']);
					foreach ($sections as &$section) {
						if (isset($section)) {
							if (is_array($section)) {
								foreach ($section as &$subSection) {
									if (substr_count($subSection, $configAlias) == 0)
										$subSection = str_replace($configName, $configAlias, $subSection);
								}
							} 
							else { 
								if (strlen($section) > 0 && substr_count($section, $configAlias) == 0)
									$section = str_replace($configName, $configAlias, $section);
							}
						}
					}
				}
			}
		}
		if (empty($recursive)) $recursive = 0;
		if ($recursive < 0) return;
		foreach(array('belongsTo','hasOne','hasMany','hasAndBelongsToMany') as $relationGroup) {
			if (isset($model->$relationGroup)) {
				foreach ($model->$relationGroup as $name => &$relation) {
					if (isset($model->Behaviors->i18n)) {
						$model->Behaviors->i18n->_localizeScheme($model->$name, $locale, $recursive-1, $relation);
					}
				}
			}
		}
	}
 
	function afterFind(&$model, &$results, &$primary) {
		if (empty($results)){return $results;}
		if (is_array($results)) {
			foreach ($results as &$result) {
				$this->_unlocalizeResults($model, $result, $this->_getLocale($model));
			}
		}
		if(!array_key_exists(0, $results)){
			$results=array($results); 	
			$extractAtFinish=TRUE;
		}

		$i=0;
		while (isset($results[$i][$model->name]) && is_array($results[$i][$model->name])){
			foreach($this->fields as $modelAlias => $modelFields){
				$isHasMany=FALSE;
				$resultados=array();
				if(isset($results{$i}{$modelAlias})){
					if(!isset($results{$i}{$modelAlias}[0])){
						$resultados[]=$results{$i}{$modelAlias};
						$isHasMany=TRUE;
					}else{
						$resultados=$results{$i}{$modelAlias};
					}
					foreach($resultados as $clave=>$resultado):
						if(isset($resultados{$clave}['id'])&&!empty($resultados{$clave}['id'])){
							
							foreach($modelFields as $fieldName => $fieldAlias){
								if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
									if(isset($resultados{$clave})&&array_key_exists($fieldName,$resultados{$clave})){				
										if(empty($resultados{$clave}[$fieldName])){
											$resultados{$clave}[$fieldName] = $this->__cacheTraduccion($model,$modelAlias,$resultados{$clave}['id'],$fieldName);
										}
									}
								}
							}
						}
					endforeach;
					if(empty($isHasMany)){
						$results{$i}{$modelAlias}=$resultados;
					}else{
						$results{$i}{$modelAlias}=$resultados[0];
					}
				}
			}
			$i++;
		}         
		if(isset($extractAtFinish)&&$extractAtFinish===TRUE){
			$results=$results[0];	
		}
		return $results;
		
	}
	
	function __cacheTraduccion(&$model,$modelAlias,$id,$fieldName){
		$varName=Configure::read('Config.language').'_'.$modelAlias.'_'.$id.'_'.$fieldName;
		$valor = Cache::read($varName);
		if ($valor === 'falsse') {
			return $valor;
		}else{
			$locale=I18n::getInstance()->l10n->__l10nCatalog[Configure::read('Empresa.language')]['locale'];
			$registro[$id]=Set::extract('0.'.$modelAlias, $model->query('SELECT * from '.strtolower(Inflector::pluralize($modelAlias)).' AS '.$modelAlias.' WHERE id="'.$id.'"'));
			$traducir=$registro[$id][$fieldName.'_'.$locale];

			return $traducir;
		}
	}

	function beforeSave(&$model) {
		if(Configure::read('Empresa.language')!=Configure::read('Config.language')){
			foreach($model->data as $modelo => $datos):
				if(isset($model->data{$modelo}['id'])&&!empty($model->data{$modelo}['id'])){
					foreach($datos as $clave=>$dummy):
						if(isset($this->fields{$modelo}{$clave})){
							Cache::delete(Configure::read('Config.language').'_'.$modelo.'_'.$model->data{$modelo}['id'].'_'.$clave);
						}
					endforeach;
				}
			endforeach;
		}
		$model->find('first');//nose pero algo hace
		foreach($this->fields as $modelAlias => $modelFields){
			foreach($modelFields as $fieldName => $fieldAlias){
				if(isset($model->data[$modelAlias][$fieldAlias])) {
					$this->_refreshSchema($model);
					return true; //exit
				}
			}
		}
		foreach($this->fields as $modelAlias => $modelFields){
			foreach($modelFields as $fieldName => $fieldAlias){
				if(isset($model->data[$modelAlias])&&array_key_exists($fieldName,$model->data[$modelAlias])){				
					$model->data[$modelAlias][$fieldAlias] = $model->data[$modelAlias][$fieldName];
					unset($model->data[$modelAlias][$fieldName]);
				}
			}
		}
		return true;
	}	
 	
	function _unlocalizeResults(&$model, &$result, &$locale) {
		if (isset($model->Behaviors->i18n) && isset($model->Behaviors->i18n->fields[$model->alias])) {
			if (!empty($result[$model->alias])) {
				$data = &$result[$model->alias];
			}
			else {
				$data = &$result;
			}
			foreach($model->Behaviors->i18n->fields[$model->alias] as $name => $alias) { //alias set in _localizeScheme
				if (is_array($data) && array_key_exists($alias, $data)) {
					$data[$name] = $data[$alias];
					unset($data[$alias]);
				}
			}
			unset($data);
		}
 
		if (isset($model->belongsTo)) {
			foreach ($model->belongsTo as $name => $relation) {
				$behaviors = $model->$name->Behaviors;
				if (isset($result[$name]) && isset($model->Behaviors->i18n)) {
					$model->Behaviors->i18n->_unlocalizeResults($model->$name, $result[$name], $locale);
				}
			}
		}
 
		if (isset($model->hasOne)) {
			foreach ($model->hasOne as $name => $relation) {
				$behaviors = $model->$name->Behaviors;
				if (isset($result[$name]) && isset($model->Behaviors->i18n)) {
					$model->Behaviors->i18n->_unlocalizeResults($model->$name, $result[$name], $locale);
				}
			}
		}
 
		if (isset($model->hasMany)) {
			foreach ($model->hasMany as $name => $relation) {
				$behaviors = $model->$name->Behaviors;
				if (isset($result[$name]) && isset($model->Behaviors->i18n)) {
					foreach ($result[$name] as &$record) {
						$model->Behaviors->i18n->_unlocalizeResults($model->$name, $record, $locale);
					}
				}
			}
		}
 
		if (isset($model->hasAndBelongsToMany)) {
			foreach ($model->hasAndBelongsToMany as $name => $relation) {
				$behaviors = $model->$name->Behaviors;
				if (isset($result[$name]) && isset($model->Behaviors->i18n)) {
					foreach ($result[$name] as &$record) {
						$model->Behaviors->i18n->_unlocalizeResults($model->$name, $record, $locale);
					}
				}
			}
		}
 
	}
 
	public static $_i18n = null;
 
	function _getLocale(&$model) {
		if (self::$_i18n == null) {
			if (!class_exists('I18n')) {
				uses('i18n');
			}
			self::$_i18n =& I18n::getInstance();
		}
		$locale = self::$_i18n->l10n->locale;
		return $locale;
	}
 
	function _refreshSchema(&$model) {
		$model->_schema = null;
		$model->schema();
	}
}
?>