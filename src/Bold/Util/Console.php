<?php

namespace bold\util;

/**
 * Console
 *
 */

class Console {

    private $counted = 0;
    private $methods = array();
    private $timers = array();

    /**
     * Count
     *
     * Count the number of times this counter has been executed
     * @param [$title] string
     */

    public function count($title = '') {
      $this->debug("$title : $this->counted");
      $this->counted++;
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
      $this->debug('debug_backtrace');
    }

    public function __call($method, $args) {
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

