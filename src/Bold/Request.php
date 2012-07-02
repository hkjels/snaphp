<?php

namespace bold;

/**
 * Request
 *
 * HTTP(S) request information
 */

class Request {

  /**
   * Headers
   */

  protected $headers = array();

  /**
   * URI parameters
   */

  protected $params = array();

  /**
   * Populate request-object with real request-data
   */

  function __construct() {
    $headers = getallheaders();
    foreach ($headers as $name => $content) {
      $this->headers[strtolower($name)] = $content;
    }
    $this->path = $_SERVER['REQUEST_URI'];
    $this->method = strtolower($_SERVER['REQUEST_METHOD']);
  }

  /**
   * Get
   *
   * Retrieve a given header
   *
   * @param $name string
   * @return string
   */

  public function get($name) {
    $name = strtolower($name);
    if (isset($this->headers[$name])) return $this->headers[$name];
    return false;
  }

  /**
   * Is
   *
   * Check request for content-type with shortnames
   * Eg. Request->is('html')
   *
   * @param $type string
   * @return boolean
   */

  public function is($type) {
    $contentType = $this->get('content-type');
    return strstr($contentType, $type) !== false;
  }

  /**
   * Accepts
   *
   * Check if request allows our wanted response
   * Eg. Request->accepts('html')
   *
   * @param $type string
   * @return boolean
   */

  public function accepts($type) {
    $accepts = $this->get('accept');
    return strstr($contentType, $type) !== false;
  }

  /**
   * Retrieve URI-parameters
   *
   * @param $name string
   * @param [$default] mixed Default return-value
   * @return mixed
   */

  public function param($name, $default = false) {
    return isset($this->params[$name]) ? $this->params[$name] : $default;
  }

  /**
   * Query parameters
   *
   * @param $name string
   * @param [$default] mixed Default return-value
   * @return mixed
   */

  public function query($name, $default = false) {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
  }

  /**
   * Body parameters
   *
   * @param $name string
   * @param [$default] mixed Default return-value
   * @return mixed
   */

  public function body($name, $default = false) {
    return isset($_POST[$name]) ? $_POST[$name] : $default;
  }


}

