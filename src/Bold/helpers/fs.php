<?php

/**
 * Filesystem helpers
 */

function inc ($path) {
  if (!is_dir($path) && file_exists($path)) include $path;
  elseif (file_exists("$path.php")) include "$path.php";
  elseif (is_dir($path) && file_exists("$path/index.php")) include "$path/index.php";
}

