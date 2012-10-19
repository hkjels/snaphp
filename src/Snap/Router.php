<?php

namespace Snap;

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
    , 'param'
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
   * Route-parameter placeholders
   */

  protected $placeholders = array();

  /**
   * Add route
   *
   * Will add routes passed from __call that respect the private $methods
   *
   * @param string $method
   * @param mixed $path
   * @param mixed $callbacks
   */

  private function addRoute ($method, $path, $callbacks) {
    $path = $method === 'param' ? $path : $this->normalizePath($path);
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
   * @param mixed $path   array of options, regular expression or a string
   * @param mixed $cb     array to instantiate a class, string to instantiate a
   *                      function or an anonymous function
   * @param mixed [$cb..] """
   *
   * @return Router
   */

  public function __call ($method, $callbacks) {
    if (!in_array($method, $this->methods)) {
      throw new \Exception("$method is an unknown HTTP-method");
    }
    $path = array_shift($callbacks);
    $path = $method === 'param' ? trim($path, ':') : $path;
    $callbacks = (array)$callbacks;
    $this->addRoute($method, $path, $callbacks);
    return $this;
  }

  /**
   * Run
   *
   * Loop through routes to find the one responsible for the URI
   */

  public function run () {
    $methods = array('all', $this->req->method, 'path');
    $routes =& $this->routes;
    $req =& $this->req;
    $res =& $this->res;

    // Loop through and execute controller-code
    // Request-method all is always checked first

    foreach ($methods as $method) {
      if (!isset($routes[$method])) continue;
      foreach ($routes[$method] as $path => $callbacks) {
        unset($routes[$method][$path]);

        switch ($method) {

         /**
          * Namespaced routes
          */

          case 'path': {

            // Remove the path segment[s] from request

            $path = '/\/'.substr($path, 4, -5).'/';
            if (preg_match($path, $req->path, $matches)) {
              $req->path = preg_replace($path, '', $req->path);
              if (count($matches) > 1) $this->resolveParams(array_slice($matches, 1));
              // unset($this->routes);
              $this->dispatch($callbacks);
            }

            break;
          }

         /**
          * GET, POST etc
          */

          default: {

            if (preg_match($path, $req->path, $matches) >= 1) {
              if (count($matches) > 1) $this->resolveParams(array_slice($matches, 1));
              $this->dispatch($callbacks);
            }

          }

        }
      }
    }
  }

  /**
   * Resolve placeholders
   *
   * Make request-parameters available
   * TODO Move params-logic so that it lives inside request only
   */

  private function resolveParams ($segments) {
    $routes =& $this->routes;
    $req =& $this->req;

    if (!empty($this->placeholders)) {
      for ($i = 0, $c = count($this->placeholders); $i < $c; $i++) {
        if (!isset($segments[$i])) return;
        $placeholder = $this->placeholders[$i];
        $req->setParam($placeholder, $segments[$i]);

        // Pre-conditions dispatch

        if (isset($routes['param'][$placeholder])) {
          $this->dispatch(
              $routes['param'][$placeholder]
            , $req->param($placeholder)
          );
        }
      }
    }
  }

  /**
   * Dispatch
   *
   * Instructions are planed out by Response->run and
   * fired off in order
   *
   * @param callable $callbacks
   * @param string [$param]
   */

  private function dispatch ($callbacks, $param = false) {
    global $console, $hooks;
    $req =& $this->req;
    $res =& $this->res;
    $args = array('req' => &$req, 'res' => &$res);
    if ($param) $args['param'] = $param;

    $hooks->execute('pre-run', $args);

    foreach ($callbacks as $cb) {

      /**
       * Controllers are instantiated with request and
       * response-objects as arguments
       */

      if (is_array($cb) && count($cb) == 1) $cb[1] = 'init';
      $status = call_user_func_array($cb, $args);

      // Handle HTTP-statuses

      switch ($status) {

        /**
         * proceed is equivalent to HTTP/1.1 - CONTINUE
         */

        case $res::PROCEED : continue;

        /**
         * HTTP/1.1 - OK
         */

        case $res::OK : return $hooks->execute('post-run', $args);

        /**
         * HTTP/1.1 - NOT FOUND
         */

        case $res::NOT_FOUND : {
          $res->end('Not found');
          return $hooks->execute('post-run', $args);
        }

      }
    }
  }

  /**
   * Normalize path to regular expression
   *
   * @param string $path
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
    $path = "/^\/$path\/?$/";

    // Make an array of placeholders and their positions

    $placeholders = array();
    $path = preg_replace_callback('/(:(\w+))/', function ($match) use (&$placeholders) {
      $match = array_shift(array_slice($match, 2));
      $placeholders[] = $match;
      return '([^\/]+)';
    }, $path);
    $this->placeholders = $placeholders;

    return $path;
  }
}

