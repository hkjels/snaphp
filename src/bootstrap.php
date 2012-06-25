<?php

/**
 * Bootstrap
 *
 * Initializes Bold and global variables
 */

$GLOBALS['console'] = new Bold\Util\Console();
$GLOBALS['hooks'] = new Bold\Util\Hooks();

/**
 * A few native hooks
 */

global $hooks;
$hooks->add('middleware', function ($req, $res) {
  $res->setHeader('X-Powered-By', 'Codeable');
});
$hooks->add('pre-dispatch', function () {});
$hooks->add('post-dispatch', function () {});

