<?php

/**
 * Php > javascript console
 */

$GLOBALS['console'] = new Bold\Util\Console();
global $console;

/**
 * Hooks
 */

$GLOBALS['hooks'] = new Bold\Util\Hooks();
global $hooks;
$hooks->add('load', function ($req, $res) {
  $res->setHeader('X-Powered-By', 'Bold');
});

