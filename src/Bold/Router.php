<?php

namespace Bold;

/**
 * Router
 *
 * Trafficing at a friendly level. Connects URI's to controllers
 */

class Router {

  /**
   * HTTP/1.1 Methods
   */

  private $methods = array(
      'all'
    , 'connect'
    , 'delete'
    , 'get'
    , 'head'
    , 'post'
    , 'put'
    , 'trace'
  );

  /**
   * All available routes
   */

  protected $routes = array();

  /**
   * addRoute
   *
   */

  private function addRoute ($method, $path, $callbacks) {
    $path = $this->normalizePath($path);
    $this->routes[$method][$path] = $callbacks;
  }

  /**
   * Methods
   *
   * @return Router
   */

  public function __call ($fn, $callbacks) {
    global $hooks;
    if (!in_array($fn, $this->methods)) throw new \Exception("$fn is an unknown HTTP-method");
    $path = array_shift($callbacks);
    $callbacks = (array)$callbacks;
    $this->addRoute($fn, $path, $callbacks);
  }

  /**
   * Run
   *
   * Loop through routes to find the one responsible for the URI
   */

  public function run () {
    global $hooks;
    $hooks->execute('pre-run', array('req' => &$this->req, 'res' => &$this->res));
    $methods = array('all', $this->req->method);

    // Loop through and execute controller-code

    foreach ($methods as $method) {
      if (!isset($this->routes[$method])) continue;
      foreach ($this->routes[$method] as $path => $callbacks) {
        if (preg_match($path, $this->req->path) >= 1) {
          foreach ($callbacks as $cb) {

            /**
            * Controllers are instantiated with request and response-objects as arguments
            */

            if (Response::PROCEED != call_user_func_array($cb, array($this->req, $this->res))) {
              $this->res->end();
              $hooks->execute('post-run', array('req' => &$this->req, 'res' => &$this->res));
              return;
            }
          }
        }
      }
    }
    $this->res->end();
    $hooks->execute('post-run', array('req' => &$this->req, 'res' => &$this->res));
  }

  /**
   * Normalize path to regular expression
   *
   * @param $path string
   * @return string Regular-expression
   */

  private function normalizePath ($path) {
    if (preg_match('/^\/.*\/[a-z]+$/', $path)) return $path;

    // Make array into string

    if (is_array($path)) $path = '('+implode('|', $path)+')';

    // Make string into regular expression

    $path = str_replace('/', '\/', $path);
    $path = str_replace('*', '.*', $path);
    // $path = preg_replace('/\[\:(\w)+\]/g', '(\w)+', $path);

    return "/^$path\/?/";
  }
}

