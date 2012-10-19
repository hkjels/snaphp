<?php
namespace Snap;

/**
 * Dependencies
 */

use Snap\Config;
use Snap\Router;
use Snap\Request;
use Snap\Response;

/**
 * Snap
 *
 * A lightweight, straightforward php-framework
 *
 * Usage:
 *     $app = new Snap\Snap();
 *     $app
 *        ->get('/', function ($req, $res) {
 *          return $res->render('views/home');
 *        })
 *        ->run();
 */

class Snap extends Router {

  /**
   * Request and response-object used throughout the
   * lifespan of your application.
   */

  protected $req, $res;

  /**
   * Instantiates dependencies, executes load-hook
   * and adds a set of default-configurations.
   */

  function __construct () {
    $this->req = new Request();
    $this->res = new Response();

    global $hooks;
    $hooks->execute('load', array(
        'req' => &$this->req
      , 'res' => &$this->res
    ));

    // Default configurations

    $this->configure(function ($config) {
      $config
        ->set('view parser', 'Snap\Php', false)
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
   * @param string [$env]
   * @param function $fn
   * @return Snap
   */

  public function configure() {
    global $environment;

    // Named arguments

    $args = func_get_args();
    if (is_string($args[0])) list($env, $fn) = $args;
    else {
      $env = 'all';
      $fn = array_shift($args);
    }

    // $fn must be callable

    if (!is_callable($fn)) throw new \Exception("$fn is not a callable configuration");

    // Only activate configurations for the current environment

    if (strstr($environment, $env) || $env === 'all') {
      return call_user_func_array($fn, array('config' => Config::getInstance()));
    }
  }

}

