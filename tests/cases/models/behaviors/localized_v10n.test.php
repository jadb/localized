<?php
/**
 * Localized Validation Behavior Tests
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
 * @subpackage    localized.tests.cases.models.behaviors
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Dummy extends Model {
	public $actsAs = array('Localized.LocalizedV10n');
	public $validate = array(
		'phone' => array('rule' => 'phone'),
		'postal' => array('rule' => 'postal'),
		'ssn' => array('rule' => 'ssn'),
	);
}
class CustomValidation {
	public function phone($country, $check) {
		App::import('Lib', 'Localized.UsValidation');
		return UsValidation::phone($check);
	}
	public function ssn($country, $check) {
		return false;
	}
}
class AnotherCustomValidation {
	public function phone($country, $check) {
		if (in_array($country, array('ca', 'ru'))) {
			App::import('Lib', 'Localized.UsValidation');
			return UsValidation::phone($check);
		}
	}
	public function ssn($country, $check) {
		return true;
	}
	public function postal($country, $check) {
		return false;
	}
}
class LocalizedV10nTest extends CakeTestCase {
	public $fixtures = array('plugin.localized.dummy');
	public function startTest() {
		$this->Dummy =& ClassRegistry::init('Dummy');
	}
	public function testCorrectInstances() {
		$this->assertTrue(is_a($this->Dummy, 'Dummy'));
		$this->assertTrue(is_a($this->Dummy->Behaviors->LocalizedV10n, 'LocalizedV10nBehavior'));
	}
	public function testLocalizedValidationMethodExists() {
		$data = array(
			'first_name' => 'John',
			'ssn' => '012-34-5678',
			'phone' => '3027653212',
			'postal' => '12345',
			'country' => 'us'
		);
		$this->Dummy->create($data);
		$this->Dummy->validates();
		$this->assertTrue(empty($this->Dummy->validationErrors));

		$data['phone'] = '123';
		$data['postal'] = 'h4r1r4';
		$data['ssn'] = '999-000-123';
		$this->Dummy->create($data);
		$this->Dummy->validates();
		$this->assertTrue(!empty($this->Dummy->validationErrors['phone']));
		$this->assertTrue(!empty($this->Dummy->validationErrors['postal']));
		$this->assertTrue(!empty($this->Dummy->validationErrors['ssn']));
	}
	public function testLocalizedValidationMethodDoesNotExistFallbackToCustomValidation() {
		$data = array(
			'first_name' => 'John',
			'ssn' => '012-343-5678',
			'phone' => '3027653212',
			'postal' => 'h3r4t4',
			'country' => 'ca'
		);
		$this->Dummy->Behaviors->v10n->default = 'CustomValidation';
		$this->Dummy->create($data);
		$this->Dummy->validates();
		$this->assertTrue(!empty($this->Dummy->validationErrors['ssn']));
	}
	public function testWithNoLocalizedValidationClass() {
		$data = array(
			'first_name' => 'John',
			'ssn' => '012-343-5678',
			'phone' => '3027653212',
			'postal' => 'h3r4t4',
			'country' => 'ru'
		);
		$this->Dummy->Behaviors->v10n->default = 'AnotherCustomValidation';
		$this->Dummy->create($data);
		$this->Dummy->validates();
		$this->assertTrue(empty($this->Dummy->validationErrors['phone']));
	}
}
?>