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
 */

class Bold extends Router {

  const VERSION = '0.0.01';

  protected $router, $req, $res;

  function __construct () {
    $this->req = new Request();
    $this->res = new Response();
  }

  protected function middleware () {
    global $hooks;
    $hooks->execute('middleware', array('req' => &$this->req, 'res' => &$this->res));
  }

}

