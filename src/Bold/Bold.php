<?php
namespace Bold;

/**
 * Dependencies
 */

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
    $this->req = new Request();
    $this->res = new Response();
  }

  public function configure($env) {}

}

