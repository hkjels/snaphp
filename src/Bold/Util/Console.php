<?php

namespace Bold\Util;

/**
 * Console
 *
 */

class Console {

  /**
   * Loggers used by __call
   */

  private $loggers = array(
      'log'
    , 'info'
    , 'debug'
    , 'warn'
    , 'error'
    , 'dir'
    , 'dirxml'
    , 'group'
    , 'groupCollapsed'
    , 'groupEnd'
  );

  private $counted = array();
  private $methods = array();
  private $timers = array();

  /**
    * Count
    *
    * Count the number of times this counter has been executed
    * @param [$title] string
    */

  public function count($title = '') {
    if (!isset($this->counted[$title])) $this->counted[$title] = 0;
    $this->debug("$title : $this->counted[$title]");
    $this->counted[$title]++;
  }

  /**
    * Time
    *
    * Add a timestamp for microbenchmarking or similar
    * @param $name string
    */

  public function time($name) {
    $this->timers[$name] = microtime(true);
  }

  /**
    * Time end
    *
    * End a given timer by it's name
    * @param $name string
    */

  public function timeEnd($name) {
    if (!isset($this->timers[$name])) return $this->error("Timer $name does not exist");
    $this->debug(microtime(true) - $this->timers[$name]);
  }

  /**
    * Trace
    *
    * Create a backtrace-log
    */

  public function trace() {
    $this->debug(debug_backtrace());
  }

  /**
    * Logging
    *
    * Takes care of most other logging-capabilities. Eg. ->log
    * @param $method string
    * @param $args array
    */

  public function __call($method, $args) {
    if (!in_array($method, $this->loggers)) throw new Exception("$method is not a known logger");
    foreach ($args as $arg) $this->methods[$method][] = json_encode($arg);
  }

  /**
    * Output all arguments to console
    * @return void - Direct output
    */

  public function output() {
    if( empty($this->methods) ) return;
    echo "<script type='application/javascript'>\n";
    foreach ($this->methods as $method => $args) {
      foreach ($args as $arg) echo "\tconsole.$method($arg);\n";
    }
    echo '</script>';
  }
}

