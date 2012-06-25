<?php

namespace Bold;

/**
 * Php
 *
 * Normalize php with other template-engines
 */

class Php {

  /**
   * Render
   *
   * Will render and return the given view
   *
   * @param $file string
   * @return string
   */

  public function render($file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') $content = file_get_contents("$file.php");
    else $content = file_get_contents($file);
    return $content;
  }

}

