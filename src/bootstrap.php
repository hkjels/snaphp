<?php

namespace Snap;

/**
 * Php > javascript console
 */

$GLOBALS['console'] = new Util\Console();
global $console;

/**
 * Hooks
 */

$GLOBALS['hooks'] = new Util\Hooks();
global $hooks;
$hooks->add('load', function ($req, $res) {
  $res->header('X-Powered-By', 'Snaphp');
});

