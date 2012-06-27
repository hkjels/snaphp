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
   * Local variables
   */

  protected $lvars = array();

  /**
   * The response-body
   *
   * Numerical array of strings prepared to be outputted.
   */

  protected $body = array();

  public function local () {
    if (func_num_args() > 1) list($key, $value) = func_get_args();
    else $key = func_get_arg(0);

    if (!is_string($key)) {
      throw new \InvalidArgumentException('Only strings are allowed as local name');
    }

    if (isset($value)) $this->lvars[$key] = $value;
    else return $this->lvars[$key];
  }

  public function locals ($locals = array()) {
    if (empty($locals)) return $this->lvars;
    foreach ($locals as $key => $value) $this->local($key, $value);
  }

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
    $body = implode($this->body);
    unset($this->body);
    echo eval(' ?>'.$body.'<?php ');
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

    // Prevent re-runs

    if (!isset($this->body)) return;

    // Add headers and body to output

    $this->send($body);
    $this->writeHeaders();
    $this->writeBody();
  }

  /**
   * Render
   *
   * Render a given template using Response->local('layout') as base.
   *
   * @param $file string
   * @param $locals array Array of local variables
   * @return Response
   */

  public function render ($view, $locals = array()) {
    $locals = array_merge($locals, $this->locals());

    // Wether to use a parent layout

    if ($locals['layout'] !== false) {
      $locals['body'] = $view;
      $view = $locals['layout'];
    }

    // Initialize parser
    // PS. The weird variable-names was made to not interfere
    // with existing locals

    $partial = function ($view, $locals = array()) use (&$partial) {
      $config = Config::getInstance();

      // Viewname

      $F1leNAme = function () use ($view, $config) {
        extract(pathinfo($view));
        $dirname = $dirname !== '.' ? $dirname : $config->get('views');
        $extension = isset($extension) ? $extension : $config->get('view extension');
        $extension = '.'.ltrim($extension, '.');
        return $dirname.$filename.$extension;
      };

      // Parser

      $PaR23r = $config->get('view parser');
      $PaR23r = new $PaR23r();
      if (!method_exists($PaR23r, 'render')) {
        throw new \Exception("It is expected of the Template-adapter to have a render-method.");
      }

      // Render

      extract($locals);
      $rendered = $PaR23r->render($F1leNAme());
      return eval(" ?>$rendered<?php ");
    };

    // Render layout and sub-views

    $rendered = $partial($view, $locals);
    $this->end($rendered);
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

