<?php

require_once realpath(dirname(__FILE__) . '/../MaxCDN.php');
require_once realpath(dirname(__FILE__) . '/Test_Settings.php');

class Reports extends PHPUnit_Framework_TestCase {
	
	function __construct() {
		$this->maxcdn = null;
		$this->result = null;
		$this->company_id = null;
	}
	
	protected function setUp() {
		$this->maxcdn = new MaxCDN(APIKEY, USERID);
		$this->test_date = date('c');
	}
	
	/**
     * 
     */
	public function testGetTotalTransfer() {
		$result = $this->maxcdn->getTotalTransfer('1212', 1);
		$this->assertInternalType('integer', $result->faultCode());
		
		$this->setExpectedException('MissingRequirementException');
		$this->result = $this->maxcdn->getTotalTransfer(null, null);
		
		$this->setExpectedException('MissingRequirementException');
		$this->result = $this->maxcdn->getTotalTransfer('', '');
		

		
	}
	
}

?>