<?php
/**
 * Localized Validation Behavior
 *
 * Backporting `Localized` plugin to Cake 1.2 while allowing
 * a transparent integration into the default model validation
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2005-2009, WDT Media Corp (http://wdtmedia.net)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2005-2009, WDT Media Corp (http://wdtmedia.net)
 * @link          http://github.com/jadb/localized
 * @package       localized
 * @subpackage    localized.models.behaviors
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LocalizedV10nBehavior extends ModelBehavior {
	/**
	 * Country to use for l10n
	 *
	 * Passed to custom validation vendor class method
	 *
	 * @var string
	 * @access public
	 */
	public $country = 'us';
	/**
	 * Behavior's name - used as cache name
	 *
	 * @var string
	 * @access public
	 */
	public $name = 'localizedv10n';
	/**
	 * Data's fieldname to identify the country for which
	 * localized validation should be loaded
	 *
	 * @var string
	 * @access public
	 */
	public $field = 'country';
	/**
	 * Custom validation vendor class used when certain methods are
	 * not defined in certain country specific validation classes
	 * (ie: CaValidation doesn't have `ssn`, but UsValidation does)
	 *
	 * @var string
	 * @access public
	 * @see localizedv10n.tests.cases for usage example
	 */
	public $default = 'CustomValidation';
	/**
	 * undocumented function
	 *
	 * @param string $Model
	 * @param string $config
	 * @return void
	 */
	public function setup(&$Model, $config = array()) {
		if (is_string($config)) {
			$config = array('default' => $config);
		}
		$this->_set($config);

		if (!$this->cached = Cache::read($this->name)) {
			App::import('Folder');
			$Folder = new Folder(APP . 'plugins' . DS . 'localized' . DS . 'plugins' . DS . 'localized' . DS . 'libs');
			$this->cached = $Folder->findRecursive('([a-z]{2})_validation\.php');
			foreach ($this->cached as $k => $cached) {
				preg_match('/([a-z]{2})_validation\.php/', $cached, $match);
				$this->cached[$k] = $match[1];
			}
			Cache::write($this->name, $this->cached);
		}
	}
	/**
	 * undocumented function
	 *
	 * @param string $Model
	 * @return void
	 */
	public function beforeValidate(&$Model) {
		$this->Validation =& new Validation;
		if (!empty($Model->data[$Model->alias][$this->field]) && in_array($Model->data[$Model->alias][$this->field], $this->cached)) {
			$this->country = $Model->data[$Model->alias][$this->field];
			$class = $this->country . 'Validation';
			App::import('Lib', 'Localized.' . $class);
			$this->Localized =& new $class;
		}
		App::import('Vendor', $this->default);
		if (class_exists($this->default)) {
			$this->Default =& new $this->default;
		}
	}
	/**
	 * undocumented function
	 *
	 * @param string $method
	 * @param string $check
	 * @return void
	 */
	public function dispatch($method, $check) {
		if (!isset($this->Localized) || !method_exists($this->Localized, $method)) {
			if (!isset($this->Default) || !method_exists($this->Default, $method)) {
				return $this->Validation->{$method}($check);
			}
			return $this->Default->{$method}($this->country, $check);
		}
		return $this->Localized->{$method}($check);
	}
	/**
	 * undocumented function
	 *
	 * @param string $Model
	 * @param string $check
	 * @return void
	 */
	public function phone(&$Model, $check) {
		return $this->dispatch('phone', current($check));
	}
	/**
	 * undocumented function
	 *
	 * @param string $Model
	 * @param string $check
	 * @return void
	 */
	public function postal(&$Model, $check) {
		return $this->dispatch('postal', current($check));
	}
	/**
	 * undocumented function
	 *
	 * @param string $Model
	 * @param string $check
	 * @return void
	 */
	public function ssn(&$Model, $check) {
		return $this->dispatch('ssn', current($check));
	}
}
?>