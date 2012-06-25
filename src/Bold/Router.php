<?php

namespace Bold;

/**
 * Router
 *
 * Trafficing at a friendly level. Connects URI's to controllers
 */

class Router {

  /**
   * How many times __call has been executed
   */

  private $times = 0;

  /**
   * HTTP/1.1 Methods
   */

  private $methods = array(
      'connect'
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
    global $console;
    $path = $this->normalizePath($path);
    $this->routes[$method][$path] = $callbacks;
  }

  /**
   * Methods
   *
   * @return Router
   */

  public function __call ($fn, $callbacks) {
    global $console;
    if (!in_array($fn, $this->methods)) throw new Exception("$fn is an invalid HTTP-method");

    if ($this->times == 0) $this->middleware();
    $this->times++;

    $path = array_shift($callbacks);
    $callbacks = (array)$callbacks;
    $this->addRoute($fn, $path, $callbacks);
  }

  /**
   * run
   *
   * Loop through routes to find the one responsible for the URI
   */

  public function run () {
    global $console, $hooks;

    $hooks->execute('pre-run');

    $this->res->writeHeaders();
    foreach ($this->routes[$this->req->method] as $path => $callbacks) {
      $console->log($path, $this->req->path);
      if (preg_match($path, $this->req->path) >= 1) {
        foreach ($callbacks as $cb) {
          if (Response::PROCEED != call_user_func_array($cb, array($this->req, $this->res))) {
            $hooks->execute('post-run');
            return;
          }
        }
      }
    }

    $hooks->execute('post-run');
  }

  /**
  * Normalize path to regular expression
  *
  * @param $path string
  * @return string Regular expression
  */

  private function normalizePath ($path) {
    global $console;
    if (preg_match('/^\/.*\/[a-z]+$/', $path)) return $path;
    if (is_array($path)) $path = '('+implode('|', $path)+')';

    $path = "^$path/?$";
    $path = str_replace('/', '\/', $path);
    $path = "/$path/";

    return $path;
  }
}

