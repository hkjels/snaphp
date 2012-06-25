<?php

/**
 * Bootstrap
 *
 * Initializes Bold and global variables
 */

$GLOBALS['console'] = new Bold\Util\Console();
$GLOBALS['hooks'] = new Bold\Util\Hooks();

/**
 * Benchmark timer
 */

global $hooks;
global $console;
$console->time('load');

/**
 * A few native hooks
 */

$hooks->add('middleware', function ($req, $res) {
  $res->setHeader('X-Powered-By', 'Bold');
});
$hooks->add('pre-run', function () {});
$hooks->add('post-run', function () {
  global $console;
  // $console->timeEnd('load');
  $console->output();
});

