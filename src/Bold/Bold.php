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
 *       return $res::DONE;
 *     });
 *     $app->run();
 */

class Bold extends Router {

  const VERSION = '0.0.05';

  protected $router, $req, $res;

  /**
   * Main
   *
   * Initilalizes BOLD
   */

  function __construct () {
    global $hooks;

    // Make request and response available
    // from a protected scope

    $this->req = new Request();
    $this->res = new Response();

    // Default configurations

    $this->configure(function ($config) {
      $config->set('view parser', 'Bold\Php', false);
    });

    $hooks->execute('load', array('req' => &$this->req, 'res' => &$this->res));
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

