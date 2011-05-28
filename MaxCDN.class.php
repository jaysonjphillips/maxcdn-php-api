<?php
	/**
	* @author Jayson J. Phillips <jayson.phillips@chroniumlabs.com>
	* @copyright Copyright (c) 2011 Chronium Labs LLC
	* @license http://opensource.org/licenses/mit-license.php MIT License
	* @version 0.1.1
	* @package chroniumlabs.maxcdn.api
	*
	*/	
	/**
	* The MaxCDN XMLRPC API Class
	* @package chroniumlabs.maxcdn.api
	*/

date_default_timezone_set('America/New_York');
require 'lib/xmlrpc.inc';


class MaxCDN {
		
	var $api_key;	
	var $user_id;
	var $current_date;
	var $xml_rpc_options = array();
	var $base_url = "api.netdna.com/xmlrpc/";
	private $auth_string;
	
	
	function __construct($api_key, $user_id) {
		$this->api_key = $api_key;
		$this->user_id = $user_id;
	}
	
	// Utility functions for setting up the pieces for transmitting data. 
	// TODO: Separate into separate file and add as a require
	function setAuthString($method) {
		return hash('sha256', $this->current_date . ':' . $this->api_key . ':' . $method);
	}
	
	function encodeParameters($method, $params) {
		$xmlrpc_encoded_params = array(
			php_xmlrpc_encode($this->user_id),
	        php_xmlrpc_encode($this->setAuthString($method)),
	        php_xmlrpc_encode(date('c'))
		);
		
		foreach($params as $param) {
			if(!empty($param)) {
				$xmlrpc_encoded_params[] = php_xmlrpc_encode($param);
			}
		}
		
		return $xmlrpc_encoded_params;
	}
	
	function setXmlRpcMsg($namespace, $method, $params) {
		$xmlrpc_msg_array = $this->encodeParameters($method, $params);
		return new xmlrpcmsg("$namespace.$method", $xmlrpc_msg_array);
	}
		
	function sendRequest($namespace, $method, $params) {
		$xmlrpc_request = $this->setXmlRpcMsg($namespace, $method, $params);
		$client = new xmlrpc_client('/xmlrpc/'.$namespace, "api.netdna.com", 80, 'http11');
		return $result =& $client->send($xmlrpc_request);
	}
	
	
	//Account Namespace
	function getBandwidth($from = "", $to = "") {
		return $this->sendRequest('account', 'getBandwidth', array($from, $to));
	}
	
}


?>