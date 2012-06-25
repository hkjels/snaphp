<?php

namespace Bold;

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

  /**
   * The response-body
   *
   * Numerical array of strings prepared to be outputted.
   */

  protected $body = array();

  /**
   * Set header
   *
   * For setting a response-header.
   *
   * @param $name string
   * @param $content string
   * return Response
   */

  public function setHeader ($name, $content) {
    $this->headers[] = "$name: $content";
    return $this;
  }

  /**
   * Set headers
   *
   * Pass it an associative array of header-name and content
   * to set multiple headers at a time.
   *
   * @param $headers array
   * @return Response
   */

  public function setHeaders ($headers) {
    foreach ($headers as $name => $content) $this->setHeader($name, $content);
    return $this;
  }

  /**
   * Write headers
   *
   * Will write the buffered headers.
   *
   * @return Response
   */

  protected function writeHeaders () {
    foreach ($this->headers as $header) header($header);
    return $this;
  }

  /**
   * Write body
   *
   * Write out the entire response
   */

  protected function writeBody() {
    foreach ($this->body as $body) {
      echo "$body\n";
    }
  }

  /**
   * Send
   *
   * Add to the response-body.
   *
   * @param $body string
   * @return Response
   */

  public function send ($body = '') {
    $this->body[] = $body;
    return $this;
  }

  /**
   * End
   *
   * Will end the response-body and output it
   *
   * @param [$body] string
   */

  public function end ($body = '') {
    // Prevent from running again
    if (isset($this->body[0]) && $this->body[0] == 'ended') return;

    $this->send($body);
    $this->writeHeaders();
    $this->writeBody();

    // PS. Response->end should only be run ones
    $this->body = array('ended');

    global $hooks;
    $hooks->execute('post-run');
  }

  /**
   * Render
   *
   * Render a given template to $this->body[]
   *
   * @param $file string
   * @param $parser function
   * @return Response
   */

  public function render ($file, $parser = 'Bold\Php') {
    if (!is_callable($parser, true)) throw new \Exception("Unknown template-parser $parser");

    // Initialize template-parser

    $parser = new $parser();
    if (!method_exists($parser, 'render')) {
      throw new \Exception("It is expected of the Template-adapter to have a render-method.");
    }
    $this->end($parser->render($file));
  }

  /**
   * Status
   *
   * Set an HTTP-statuscode.
   *
   * @param $code integer
   * @return Response
   */

  public function status ($code) {
    $this->statusCode = (int)$code;
    return $this;
  }

}

