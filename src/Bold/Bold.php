<?php
namespace Bold;

/**
 * Dependencies
 */

use Bold\Config;
use Bold\Router;
use Bold\Request;
use Bold\Response;

/**
 * Bold
 *
 * A lightweight, straightforward php-framework
 *
 * Usage:
 *     $app = new Bold\Bold();
 *     $app->get('/', function ($req, $res) {
 *       $res->render('views/home');
 *       return $res::OK;
 *     });
 *     $app->run();
 */

class Bold extends Router {

  const VERSION = '0.0.07';

  public $req, $res;

  /**
   * Main
   *
   */

  function __construct () {
    global $hooks;
    $this->req = new Request();
    $this->res = new Response();
    $hooks->execute('load', array(
        'req' => &$this->req
      , 'res' => &$this->res
    ));

    // Default configurations

    $this->configure(function ($config) {
      $config
        ->set('view parser', 'Bold\Php', false)
        ->set('views', 'views/', false)
        ->set('view extension', '.php');
    });

    // Default layout-view

    $this->res->local('layout', 'layout');
  }

  /**
   * Configure
   *
   * Add environment specific configurations
   *
   * @param [$env] string
   * @param $fn function
   * @return Bold
   */

  public function configure() {
    global $environment;

    // Named arguments
    $args = func_get_args();
    if (is_string($args[0])) list($env, $fn) = $args;
    else {
      $env = 'all';
      $fn = $args[0];
    }

    // $fn must be callable
    if (!is_callable($fn)) throw new \Exception("$fn is not a callable configuration");

    // Only activate configurations for the current environment
    if (strstr($environment, $env) || $env === 'all') {
      return call_user_func_array($fn, array('config' => Config::getInstance()));
    }
  }

}

