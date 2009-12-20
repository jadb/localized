<?php
class DummyFixture extends CakeTestFixture {
   public $name = 'Dummy';
   public $table = 'dummies';
   public $fields = array(
      'id' => array('type' => 'integer', 'null' => 0, 'null' => NULL, 'default' => NULL, 'length' => '11', 'key' => 'primary'),
      'first_name' => array('type' => 'string', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => '45'),
      'ssn' => array('type' => 'string', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => '255'),
      'phone' => array('type' => 'string', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => '255'),
      'zip' => array('type' => 'string', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => '255'),
      'country' => array('type' => 'string', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => '255'),
      'created' => array('type' => 'datetime', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => NULL),
      'modified' => array('type' => 'datetime', 'null' => 1, 'null' => '1', 'default' => NULL, 'length' => NULL),
   );
   public $records = array(
   );
}
?>