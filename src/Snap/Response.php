<?php

namespace Snap;

/**
 * Response
 *
 *
 */

class Response {

  /**
   * Response status-codes HTTP/1.1
   */

  const PROCEED               = 100;
  const OK                    = 200;
  const PERMANENT             = 301;
  const FOUND                 = 302;
  const BAD_REQUEST           = 400;
  const UNAUTHORIZED          = 401;
  const FORBIDDEN             = 403;
  const NOT_FOUND             = 404;
  const INTERNAL_SERVER_ERROR = 500;

  /**
   * Status
   */

  public $statusCode = 100;

  /**
   * Content types
   *
   * Full names of contenttypes
   */

  protected $contentTypes = array(
      'application/json'
    , 'text/html'
    , 'text/plain'
  );

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

  /**
   * Local
   *
   * Set and retrieve a local-variable
   *
   * @param string [$key]
   * @param mixed [$value]
   * @return mixed
   */

  public function local () {
    if (func_num_args() > 1) list($key, $value) = func_get_args();
    else $key = func_get_arg(0);

    if (!is_string($key)) {
      throw new \InvalidArgumentException('Only strings are allowed as local name');
    }

    if (isset($value)) $this->lvars[$key] = $value;
    else return $this->lvars[$key];

    return $this;
  }

  /**
   * Locals
   *
   * Set and retrieve local-variables
   *
   * @param array [$locals]
   * @return mixed
   */

  public function locals ($locals = array()) {
    if (empty($locals)) return $this->lvars;
    foreach ($locals as $key => $value) $this->local($key, $value);
    return $this;
  }

  /**
   * Header
   *
   * For setting/retrieving a response-header.
   *
   * @param string $name
   * @param string $content
   * return Response
   */

  public function header ($name, $content = '') {
    if (!empty($content)) $this->headers[$name] = $content;
    else return isset($this->headers[$name]) ? $this->headers[$name] : '';
    return $this;
  }

  /**
   * Set headers
   *
   * Pass it an associative array of header-name and content
   * to set multiple headers at a time.
   *
   * @param array $headers
   * @return Response
   */

  public function setHeaders ($headers) {
    foreach ($headers as $name => $content) $this->header($name, $content);
    return $this;
  }

  /**
   * Redirect
   *
   * Redirect to the given url
   *
   * @param string $url
   * @param integer $code
   */

  public function redirect ($url, $code = 302) {
    $this->status($code);

    /**
     * Special redirect-tokens
     */

    switch ($url) {
      case 'home': $url = '/'; break;
      case 'back': $url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/';
    }

    $this->header('location', $url);
    return $this;
  }

  /**
   * Set and retrieve cookies
   *
   * TODO Add max-age with strtotime
   *
   * @param string $name
   * @param mixed [$value]
   * @param array [$opt]
   * @return mixed
   */

  public function cookie ($name, $value = '', $opt = array()) {
    if (!is_string($name)) {
      throw new \InvalidArgumentException('Only strings are supported as cookie-names');
    }

    if (empty($value)) {
      if (isset($_COOKIE[$name])) return $_COOKIE[$name];
      return false;
    }

    /**
     * Cookie options
     */

    $opt = array_merge(
      array(
          'expire' => 0
        , 'path' => ''
        , 'domain' => ''
        , 'secure' => false
        , 'httponly' => false
      )
      , $opt
    );
    $opt['expire'] = is_string($opt['expire']) ? strtotime($opt['expire']) : $opt['expire'];

    /**
     * Set cookie
     */

    setcookie(
        $name
      , $value
      , $opt['expire']
      , $opt['path']
      , $opt['domain']
      , $opt['secure']
      , $opt['httponly']
    );

    return $this;
  }

  /**
   * Clear cookie
   *
   * @param string $name
   * @return Response
   */

  public function clearCookie($name) {
    setcookie($name, '', time() - 3600);
    return $this;
  }

  /**
   * Content type
   *
   * For setting the contenttype of a response
   *
   * @param string $type The literal representation or named type
   * @return Response
   */

  public function contentType($type) {

    // Find fullname of the contenttype

    $type = array_shift(array_filter($this->contentTypes, function ($contentType) use ($type) {
      if (!strstr($type, '/')) $contentType = substr($contentType, strstr($contentType, '/') + 1);
      return strstr($contentType, $type);
    }));

    // Unknown type

    if (empty($type)) {
      throw new \InvalidArgumentException("$type is not a known contentType");
    }

    // Set contentType header

    $this->header('Content-Type', $type);
    return $this;
  }

  /**
   * Json
   *
   * Output any data as json
   *
   * @param mixed $mixed
   * @param int [$code]
   * @return integer
   */

  public function json ($mixed, $code = 200) {
    $json = json_encode($mixed);
    return $this->contentType('json')->end($json, $code);
  }

  /**
   * Write headers
   *
   * Will write the buffered headers.
   *
   * @return Response
   */

  protected function writeHeaders () {
    foreach ($this->headers as $name => $content) header("$name: $content");
    return $this;
  }

  /**
   * Write body
   *
   * Write out the entire response
   */

  protected function writeBody() {
    $body = implode($this->body);
    echo eval(' ?>'.$body.'<?php ');
  }

  /**
   * Send
   *
   * Add to the response-body.
   *
   * @param string $body
   * @param integer [$code]
   * @return Response
   */

  public function send ($body, $code = false) {
    $this->body[] = (string) $body;
    if ($code) $this->status($code);
    return $this;
  }

  /**
   * End
   *
   * Will end the response-body and output it
   *
   * @param string [$body]
   */

  public function end ($body = '', $code = 200) {

    // Add headers and body to output

    $this->send($body);
    $this->writeHeaders();
    $this->writeBody();

    // Response is implicit, ready and finished

    return $this->status($code);
  }

  /**
   * Render
   *
   * Render a given template using Response->local('layout') as base.
   *
   * @param string $file
   * @param array $locals Array of local variables
   * @return Response
   */

  public function render ($view, $locals = array()) {
    $locals = array_merge($this->locals(), $locals);

    // Wether to use a parent layout

    if ($locals['layout'] !== false) {
      $locals['body'] = $view;
      $view = $locals['layout'];
    }

    // Initialize parser
    // PS. The weird variable-names was made to not interfere
    // with existing locals

    $partial = function ($view, $lvars = array()) use (&$partial, $locals) {
      $locals = array_merge($locals, $lvars);
      $config = Config::getInstance();

      // Viewname

      $F1leNAme = function () use ($view, $config) {
        extract(pathinfo($view));
        $dirname = $dirname !== '.' ? $dirname.'/' : '';
        $dirname = $config->get('views').$dirname;
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

    return $this->end($partial($view, $locals));
  }

  /**
   * Status
   *
   * Set an HTTP-statuscode.
   *
   * @param integer $code
   * @return Response
   */

  public function status ($code) {
    $this->statusCode = (int)$code;
    return $this->statusCode;
  }

}

