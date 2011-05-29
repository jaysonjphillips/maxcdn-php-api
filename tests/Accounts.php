<?php

require_once realpath(dirname(__FILE__) . '/../MaxCDN.php');
require_once realpath(dirname(__FILE__) . '/Test_Settings.php');


class Accounts extends PHPUnit_Framework_TestCase {
	function __construct() {
		$this->maxcdn = null;
	}
	
	protected function setUp() {
		$this->maxcdn = new MaxCDN(APIKEY, USERID);
		$this->test_date = date('c');
	}
	
	public function testApiKeyIsSet() {
		$this->assertEquals('a5z4m2f74ds1qj8t0hnw9bth3z3h3a3a', $this->maxcdn->api_key);
        
	}
	
	public function testAccountFunctions() {
		$result = $this->maxcdn->getBandwidth();
		$this->assertTrue(!$result->faultCode());
		
		$bandwidth = $result->value();
		$this->assertEquals(1000000000000, $bandwidth->scalarval());
	}
}



?>