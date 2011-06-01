<?php

/**
 * @author Jayson J. Phillips <jayson.phillips@chroniumlabs.com>
 * @copyright Copyright (c) 2011 Chronium Labs LLC
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 0.2
 * @package chroniumlabs.maxcdn-api
 *
 */
/**
 * The MaxCDN XMLRPC API Class
 * @package chroniumlabs.maxcdn-api
 * @todo Flesh out the full API - currently the reporting methods
 * @todo Document all elements accordingly in phpDoc syntax
 */
date_default_timezone_set('America/Los_Angeles');
require 'MaxCDN_Exceptions.php';
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
   * The MaxCDN XMLRPC API Class
   * @package chroniumlabs.maxcdn-api
   * @subpackage utilities
   */

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

    if (is_array($params)) {
      foreach ($params as $param) {
        if (!empty($param)) {
          $xmlrpc_encoded_params[] = php_xmlrpc_encode($param);
        }
      }
    }
    return $xmlrpc_encoded_params;
  }

  /**
   * Set XML RPC Message
   * setXmlRpcMsg
   * Takes an array of encoded params and returns a new xmlrpcmsg object
   * 
   * @param string $namespace
   * @param string $method
   * @param array $params
   * @return object xmlrpcmsg
   */
  function setXmlRpcMsg($namespace, $method, $params) {
    $xmlrpc_msg_array = $this->encodeParameters($method, $params);
    return new xmlrpcmsg("$namespace.$method", $xmlrpc_msg_array);
  }

  /**
   * Send Request
   * sendRequest
   * Uses params internally to obtain a proper xmlrpcmsg to transmit
   * Returns a reference to the return of the xmlrpc_client->send method 
   * 
   * @param string $namespace
   * @param string $method
   * @param array $params
   * @return object $result
   */
  function sendRequest($namespace, $method, $params = null) {
    $xmlrpc_request = $this->setXmlRpcMsg($namespace, $method, $params);
    $client = new xmlrpc_client('/xmlrpc/' . $namespace, "api.netdna.com", 80, 'http11');
    return $result = & $client->send($xmlrpc_request);
  }

  /**
   * Account Methods
   * @subpackage account
   */

  /**
   * Get Bandwidth
   * getBandwidth
   * Takes optional parameters $from & $to in Y-m-d format
   * Example: $this->getBandwidth('2011-05-22', '2011-05-23');
   * 
   * @param string $form (optional)
   * @param string $to (optional)
   * @return object $xmlrpcresp 
   */
  function getBandwidth($from = null, $to = null) {
    return $this->sendRequest('account', 'getBandwidth', array($from, $to));
  }

  /**
   * Reporting methods
   * @subpackage report
   */
  function getTotalTransfer($zone_id, $type, $from = null, $to = null, $timezone = null) {
    if (empty($zone_id) || empty($type)) {
      throw new MissingRequirementException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getTotalTransfer', array($zone_id, $type, $from, $to, $timezone));
  }

  function getTotalHits($zone_id, $type, $from = null, $to = null, $timezone = null) {
    if (empty($zone_id) || empty($type)) {
      throw new MissingRequirementException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getTotalHits', array($zone_id, $type, $from, $to, $timezone));
  }

  /**
   * Get Total Stats
   * Returns transfer stats for a given company/zone and date range
   * Required: $company_id, $date_from (Y-m-d), $date_to (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $view_by (either "hourly" or "daily")
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 			 $timezone ("America/New_York")
   * 
   * Example:  $this->getTotalStats('company_id', '2011-05-23', '2011-05-28', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - Start Date
   * @param string $date_to - End Date
   * @param int $zone_id - Zone identifier
   * @param array $sort_by - an array of "column sortorder" strings, please see returned columns for possible values. sortorder can be ASC or DESC (optional)
   * @param string $view_by (optional)
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @param string $timezone (optional)
   * @return object $xmlrpcresp | array $value
   */
  function getTotalTransferStats($company_id, $date_from, $date_to, $zone_id, $sort_by = null, $maximum = null, $offset = null, $timezone = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from) || empty($date_to)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getTotalTransferStats', array($company_id, $zone_id, $date_from, $date_to, $sort_by,
        $view_by, $maximum, $offset, $timezone));
  }

  /**
   * Get Cache Hit Statistics
   * Returns the total cache hits for a given company/zone and date range
   * Required: $company_id, $date_from (Y-m-d), $date_to (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 			 $timezone ("America/New_York")
   * 
   * Example:  $this->getCacheHitStats('company_id', '2011-05-23', '2011-05-28', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - Start Date
   * @param string $date_to - End Date
   * @param int $zone_id - Zone identifier
   * @param array $sort_by (optional) - "column sortorder" strings. see return for possible values. sortorder can be ASC or DESC
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @param string $timezone (optional)
   * @return object $xmlrpcresp | int $value
   */
  function getCacheHitStats($company_id, $date_from, $date_to, $zone_id, $sort_by = null, $maximum = null, $offset = null, $timezone = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from) || empty($date_to)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getCacheHitStats', array($company_id, $zone_id, $date_from, $date_to, $sort_by,
        $maximum, $offset, $timezone));
  }

  /**
   * Get Popular Files
   * Returns a list of popular files for a given company/zone and date range
   * Required: $company_id, $date_from (Y-m-d), $date_to (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 			 $timezone ("America/New_York")
   * 
   * Example:  $this->getPopularFiles('company_id', '2011-05-23', '2011-05-28', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - Start Date
   * @param string $date_to - End Date
   * @param int $zone_id - Zone identifier
   * @param array $sort_by - an array of "column sortorder" strings, please see returned columns for possible values. sortorder can be
   *        ASC or DESC (optional)
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @param string $timezone (optional)
   * @return object $xmlrpcresp | array $value
   */
  function getPopularFiles($company_id, $date_from, $date_to, $zone_id, $sort_by = null, $maximum = null, $offset = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from) || empty($date_to)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getPopularFiles', array($company_id, $zone_id, $date_from, $date_to, $sort_by,
        $maximum, $offset));
  }

  /**
   * Get Usage Per Day
   * Returns usage stats for a give company/zone and date range
   * Required: $company_id, $date_from (Y-m-d), $date_to (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 
   * Example:  $this->getUsagePerDay('company_id', '2011-05-23', '2011-05-28', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - Start Date
   * @param string $date_to - End Date
   * @param int $zone_id - Zone identifier
   * @param array $sort_by - an array of "column sortorder" strings, please see returned columns for possible values. 
   * sortorder can be ASC or DESC (optional)
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @return object $xmlrpcresp | int $value
   */
  function getUsagePerDay($company_id, $date_from, $date_to, $zone_id, $sort_by = null, $maximum = null, $offset = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from) || empty($date_to)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getUsagePerDay', array($company_id, $zone_id, $date_from, $date_to, $sort_by,
        $maximum, $offset));
  }

  /**
   * Get Node Hits
   * Returns a list of node hits for a given company/zone and date range
   * Required: $company_id, $date_from (Y-m-d), $date_to (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 
   * Example:  $this->getNodeHits('company_id', '2011-05-23', '2011-05-28', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - Start Date
   * @param string $date_to - End Date
   * @param int $zone_id - Zone identifier
   * @param array $sort_by - an array of "column sortorder" strings, please see returned columns for possible values. 
   * sortorder can be ASC or DESC (optional)
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @return object $xmlrpcresp | array $value
   */
  function getNodeHits($company_id, $date_from, $date_to, $zone_id, $sort_by = null, $maximum = null, $offset = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from) || empty($date_to)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getNodeHits', array($company_id, $zone_id, $date_from, $date_to, $sort_by,
        $maximum, $offset));
  }

  /**
   * Get Connection Stats
   * Returns a list of live zone daily connection stats for a given company/zone and date range
   * Required: $company_id, $date_from (Y-m-d), $date_to (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 			 $timezone
   * 
   * Example:  $this->getConnectionStats('company_id', '2011-05-23', '2011-05-28', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - Start Date
   * @param string $date_to - End Date
   * @param int $zone_id - Zone identifier
   * @param array $sort_by - an array of "column sortorder" strings, please see returned columns for possible values. 
   * sortorder can be ASC or DESC (optional)
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @param string $timezone (optional)
   * @return object $xmlrpcresp | array $value
   */
  function getConnectionStats($company_id, $date_from, $date_to, $zone_id, $sort_by = null, $maximum = null, $offset = null, $timezone = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from) || empty($date_to)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getConnectionStats', array($company_id, $zone_id, $date_from, $date_to, $sort_by,
        $maximum, $offset, $timezone));
  }

  /**
   * Get Hourly Connection Stats
   * Returns a list of live zone hourly connection stats for a given company/zone and date
   * Required: $company_id, $date_from (Y-m-d), $zone_id
   * Optional: $sort_by (array)
   * 			 $maximum (number of records returned) 
   * 			 $offset 
   * 			 $timezone
   * 
   * Example:  $this->getHourlyConnectionStats('company_id', '2011-05-23', 'zone-id');
   * 
   * @param mixed $company_id - Unique company_id or alias
   * @param string $date_from - The date you want to fetch hourly stats for
   * @param int $zone_id - Zone identifier
   * @param array $sort_by - an array of "column sortorder" strings, please see returned columns for possible values. 
   * sortorder can be ASC or DESC (optional)
   * @param int $maximum - the maximum number of records to return (optional)
   * @param int $offset (optional)
   * @param string $timezone (optional)
   * @return object $xmlrpcresp | array $value
   */
  function getHourlyConnectionStats($company_id, $date_from, $zone_id, $sort_by = null, $maximum = null, $offset = null, $timezone = null) {

    if (empty($company_id) || empty($zone_id) || empty($date_from)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }
    return $this->sendRequest('report', 'getHourlyConnectionStats', array($company_id, $zone_id, $date_from, $sort_by,
        $maximum, $offset, $timezone));
  }

  /**
   * User methods
   * @subpackage users
   */

  /**
   * List Users
   * user.listUsers
   * 
   * <code>
   *  $this->getUserList();
   * </code>
   * 
   * @return object xmlrpcresp | array $value
   * 
   */
  function getUserList() {
    return $this->sendRequest('user', 'listUsers');
  }

  /**
   * Update User
   * user.update
   * 
   * <code>
   *  $this->updateUser($user_id, $update_values);
   * </code>
   * 
   * Required: int $user_id, struct $update_values
   * 
   * @param int $user_id
   * @param array $update_values (key/value pair of settings) 
   * @return object xmlrpcresp | array $value
   */
  function updateUser($user_id, $update_values) {
    if (empty($user_id) || empty($update_values)) {
      throw new MissingRequiredParameterException('One or more required parameters are empty');
    }

    if (!is_array($update_values)) {
      throw new VariableTypeException('Required parameter is expected to be an array');
    }

    return $this->sendRequest('user', 'update');
  }

}

?>