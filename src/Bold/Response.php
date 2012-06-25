<?php

namespace bold;

/**
 * Response
 *
 * Response generated per request
 */

class Response {

  /**
   * Response return-codes
   */

  const ERROR = 0;
  const PROCEED = 1;
  const DONE = 2;

  /**
   * Responseheaders
   */

  protected $headers = array();

  public function setHeader ($header, $content) {
    $this->headers[] = "$header: $content";
  }

  public function setHeaders ($headers) {
    foreach ($headers as $header => $content) {
      $this->setHeader($header, $content);
    }
  }

  public function writeHeaders () {
    foreach ($this->headers as $header) {
      header($header);
    }
  }

  public function write () {}

  public function render () {}

  public function status ($code) {
    $this->statusCode = (int)$code;
  }

}

