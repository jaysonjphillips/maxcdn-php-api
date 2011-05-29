<?php

require_once realpath(dirname(__FILE__) . '/../MaxCDN.php');
require_once realpath(dirname(__FILE__) . '/Test_Settings.php');

class Reports extends PHPUnit_Framework_TestCase {
	
	function __construct() {
		$this->maxcdn = null;
	}
	
	protected function setUp() {
		$this->maxcdn = new MaxCDN(APIKEY, USERID);
		$this->test_date = date('c');
	}
	/**
     * @expectedException MissingParameterException
     */
	public function testGetTotalTransferRequiredParamsNotNull() {
		$result = $this->maxcdn->getTotalTransfer(null, null);
	}
	
	/**
     * @expectedException MissingParameterException
     */
	public function testGetTotalTransferRequiredParamsNotEmpty() {
		$result = $this->maxcdn->getTotalTransfer('', '');
	}
	
}

?>