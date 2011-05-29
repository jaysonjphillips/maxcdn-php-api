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

date_default_timezone_set('America/Los_Angeles');
require 'lib/xmlrpc.inc';


class MaxCDN {
		
	var $api_key;	
	var $user_id;
	var $xml_rpc_options = array();
	var $base_url = "api.netdna.com/xmlrpc/";
	private $auth_string;
	
	/**
	 * Constructor - PHP5 syntax
	 * Sets instance vars for the api key and the user id, both of which are required for
	 * request made. No need in setting/passing them manually for each call, right?
	 *
	 * @param string $api_key
	 * @param string $user_id
	 */
	function __construct($api_key, $user_id) {
		$this->api_key = $api_key;
		$this->user_id = $user_id;
	}
	
	// Utility functions for setting up the pieces for transmitting data. 
	// TODO: Separate into separate file and add as a require
	
	/**
	 * Set Auth String
	 * Required for every call. Not in constructor so that the date (in ISO8601 format) is always fresh when called.
	 * 
	 * @param string $method
	 * @return string sha-256 hash for authstring
	 */
	function setAuthString($method) {
		return hash('sha256', date('c') . ':' . $this->api_key . ':' . $method);
	}
	
	/**
	 * Encode Parameters
	 * Method abstracts xmlrpc encoding of params
	 * Allows us to pass in params for each method as a single array, then encode them along with the 3 params that
	 * must get sent along with each request (method, user_id, current_date)
	 * 
	 * @param string $method
	 * @param array $params
	 * @return array $xmlrpc_encoded_params
	 */
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
	
	
	
	function getBandwidth($from = "", $to = "") {
		return $this->sendRequest('account', 'getBandwidth', array($from, $to));
	}
	
	// 
}


?>