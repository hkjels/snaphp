<?php

namespace Snap;

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

  function __construct () {
    $this->headers = getallheaders();
    $this->path = $_SERVER['REQUEST_URI'];
    $this->method = strtolower($_SERVER['REQUEST_METHOD']);
  }

  /**
   * Get
   *
   * Retrieve a given header
   *
   * @param string $name
   * @return string
   */

  public function get ($name) {
    $name = strtolower($name);
    $headers = array_change_key_case($this->headers, CASE_LOWER);
    if (isset($headers[$name])) return $headers[$name];
    return false;
  }

  /**
   * Is
   *
   * Check request for content-type with shortnames
   * Eg. Request->is('html')
   *
   * @param string $type
   * @return boolean
   */

  public function is ($type) {
    $contentType = $this->get('content-type');
    return strstr($contentType, $type) !== false;
  }

  /**
   * Accepts
   *
   * Check if request allows our wanted response
   * Eg. Request->accepts('html')
   *
   * @param string $type
   * @return boolean
   */

  public function accepts ($type) {
    $accepts = $this->get('accept');
    return strstr($contentType, $type) !== false;
  }

  /**
   * Retrieve URI-parameters
   *
   * @param string $name
   * @param mixed [$default] Default return-value
   * @return mixed
   */

  public function param ($name, $default = false) {
    $name = trim($name, ':');
    if (isset($this->params[$name])) return $this->params[$name];
    else if ($this->query($name)) return $this->query($name);
    else if ($this->body($name)) return $this->body($name);
    else return $default;
  }

  /**
   * Set param
   *
   * Set a request-uri parameter.
   *
   * @param string $name
   * @param scalar $value
   * @return Request
   */

  public function setParam($name, $value) {
    if (!is_string($name)) {
      throw new \InvalidArgumentException('Param key must be of type string');
    }
    if (!is_scalar($value)) {
      throw new \InvalidArgumentException('Param value must be a scalar value');
    }
    $this->params[(string)$name] = $value;
    return $this;
  }

  /**
   * Query parameters
   *
   * @param string $name
   * @param mixed [$default] Default return-value
   * @return mixed
   */

  public function query ($name, $default = false) {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
  }

  /**
   * Body parameters
   *
   * @param string $name
   * @param mixed [$default] Default return-value
   * @return mixed
   */

  public function body ($name, $default = false) {
    return isset($_POST[$name]) ? $_POST[$name] : $default;
  }

}

