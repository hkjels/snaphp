<?php

namespace Bold;

/**
 * Router
 *
 * Friendly trafficing. Connects URI's to controllers
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
    , 'path'
    , 'post'
    , 'put'
    , 'trace'
  );

  /**
   * All available routes
   */

  protected $routes = array();

  /**
   * Add route
   *
   * Will add routes passed from __call that respect the private $methods
   *
   * @param $method string
   * @param $path mixed
   * @param $callbacks mixed
   */

  private function addRoute ($method, $path, $callbacks) {
    $path = $this->normalizePath($path);
    $this->routes[$method][$path] = $callbacks;
  }

  /**
   * Methods used for adding routes
   *
   * You can use any of the request-methods from the HTTP/1.1 spec
   * and the additional `all` and `path`-method. All will affect no matter the
   * request-method used. Path works as a namespace where you can specify child-
   * routes. The callbacks can be any callable function, even anonymous functions.
   * If you pass an array as callback, the first index would be your controller
   * and the second would be your method. If the second index is missing, it
   * will try to fire a method named `init`.
   *
   * @param $path mixed   array of options, regular expression or a string
   * @param $cb mixed     array to instantiate a class, string to instantiate a
   *                      function or an anonymous function
   * @param [$cb..] mixed """
   *
   * @return Router
   */

  public function __call ($fn, $callbacks) {
    global $hooks;
    if (!in_array($fn, $this->methods)) throw new \Exception("$fn is an unknown HTTP-method");
    $path = array_shift($callbacks);
    $callbacks = (array)$callbacks;
    $this->addRoute($fn, $path, $callbacks);
    return $this;
  }

  /**
   * Run
   *
   * Loop through routes to find the one responsible for the URI
   */

  public function run () {
    global $console;
    $methods = array('all', $this->req->method, 'path');

    // Loop through and execute controller-code
    // Request-method all is always checked first

    foreach ($methods as $method) {
      if (!isset($this->routes[$method])) continue;
      foreach ($this->routes[$method] as $path => $callbacks) {
        unset($this->routes[$method][$path]);

        /**
         * Namespaced routes
         */

        if ($method == 'path') {

          // Remove the path segment[s] from request

          $path = '/\/'.substr($path, 4, -5).'/';
          if ($this->req->path = preg_replace($path, '', $this->req->path)) {
            unset($this->routes);
            $this->dispatch($callbacks);
          }
        }

        /**
         * GET, POST etc.
         */

        else if (preg_match($path, $this->req->path) >= 1) {
          $this->dispatch($callbacks);
        }
      }
    }
  }

  /**
   * Dispatch
   *
   * Instructions are planed out by Response->run and
   * fired off in order
   */

  private function dispatch ($callbacks) {
    global $console, $hooks;
    $req =& $this->req;
    $res =& $this->res;
    $hooks->execute('pre-run', array('req' => &$req, 'res' => &$res));

    foreach ($callbacks as $cb) {

      /**
       * Controllers are instantiated with request and
       * response-objects as arguments
       */

      if (is_array($cb) && count($cb) == 1) $cb[1] = 'init';
      $status = call_user_func_array($cb, array(&$req, &$res));

      // Handle HTTP-statuses

      switch ($status) {

        /**
         * proceed is equivalent to HTTP/1.1 - CONTINUE
         */

        case $res::PROCEED : continue;

        /**
         * HTTP/1.1 - OK
         */

        case $res::OK : return $hooks->execute('post-run', array(
          'req' => &$req, 'res' => &$res
        ));

        /**
         * HTTP/1.1 - NOT FOUND
         */

        case $res::NOT_FOUND : {
          $this->res->end('Not found');
          return $hooks->execute('post-run', array(
            'req' => &$req, 'res' => &$res
          ));
        }

      }
    }
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

    $path = trim($path, '/');
    if ($path == '*') return '/^.*\/?/';
    $path = str_replace('/', '\/', $path);
    if (empty($path)) $path = '{1}';

    return "/^\/$path\/?$/";
  }
}

