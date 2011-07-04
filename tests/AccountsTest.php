<?php

require_once realpath(dirname(__FILE__) . '/../MaxCDN.php');
require_once realpath(dirname(__FILE__) . '/Test_Settings.php');


class AccountsTest extends PHPUnit_Framework_TestCase {
	function __construct() {
		$this->maxcdn = null;
	}
	
	protected function setUp() {
		$this->maxcdn = new MaxCDN(APIKEY, USERID);
		$this->test_date = date('c');
	}
	
	public function testApiKeyIsSet() {
		$this->assertEquals(APIKEY, $this->maxcdn->api_key);
        
	}
	
	public function testAccountFunctions() {
		$result = $this->maxcdn->getBandwidth();
		$this->assertTrue(!$result->faultCode());
		
		$bandwidth = $result->value();
		$this->assertLessThanOrEqual(CURRENT_BANDWIDTH, $bandwidth->scalarval());
	}
}



?>