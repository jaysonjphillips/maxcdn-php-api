<?php
/**
 * Demo code for getting account information
 */

require_once realpath(dirname(__FILE__) . '/../MaxCDN.php');

//Create your object & pass in your keys
$maxcdn = new MaxCDN('your-api-key', 'your-user-id');

//Simply call your method
$result = $maxcdn->getBandwidth();

//Check for any errors returned
if(!$result->resultCode()) {
	$bandwidth = $result->value();
}

//print out your value
print $bandwidth->scalarval();

//for more information on the resultCode, value, and scalarval methods, please consult the xmlrpc.inc include
//in the lib folder. These are methods of the xmlrpcresp object returned by the MaxCDN class method calls
?>