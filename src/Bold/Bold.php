<?php
namespace Bold;

/**
 * Dependencies
 */

use Bold\Router;
use Bold\Request;
use Bold\Response;
use Bold\util\Console;
use Bold\util\Hooks;

/**
 * Bootstrap
 *
 * Initializes Bold and global variables
 */

$GLOBALS['console'] = new Console();
$GLOBALS['hooks'] = new Hooks();

// Initiliaze helper-functions

define('BOLD_ROOT', realpath(dirname(__FILE__)));
$helpers = array_slice(scandir(BOLD_ROOT.'/helpers'), 2);
foreach ($helpers as $helper) include BOLD_ROOT."/helpers/$helper";

/**
 * A few native hooks
 */

$hooks->add('middleware', function ($req, $res) {
  $res->setHeader('X-Powered-By', 'Codeable');
});
$hooks->add('pre-dispatch', function () {
  global $console;
  $console->log('pre-dispatch');
});
$hooks->add('post-dispatch', function () {
  global $console;
  $console->output();
});

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

