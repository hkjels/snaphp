<?php

namespace bold;

/**
 * Request
 *
 * HTTP(S) request information
 */

class Request {

  protected $headers = array();

  function __construct() {
    $this->headers = getallheaders();
    $this->path = $_SERVER['REQUEST_URI'];
    $this->method = strtolower($_SERVER['REQUEST_METHOD']);
  }
}

