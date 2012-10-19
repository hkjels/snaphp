<?php

namespace Snap;

/**
 * Php
 *
 * Normalize php-templating with other template-engines
 */

class Php {

  /**
   * Render
   *
   * Will return the given view
   *
   * @param string $file
   * @return string
   */

  public function render($file) {
    return file_get_contents($file);
  }

}

