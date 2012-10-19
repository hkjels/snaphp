<?php

namespace Snap\Util;

/**
 * Hooks
 *
 * With hooks, you can insert functionality at the given point in time when a hook
 * is executed. Native-hooks retrieve the request and response-objects as arguments,
 * these arguments can be overriden by passing an arguments-array to the `adder`.
 *
 * Existing native-hooks:
 *  load, pre-run, post-run
 */

class Hooks {

  protected $hooks = array();

  /**
   * Add hook
   *
   * You can add a function-call or an anonymous function and it will be executed using
   * Hooks->execute($name)
   * @param $event string
   * @param $fn mixed
   * @param [$args] array Numerical array of arguments to be passed to named function
   */

  public function add ($event, $fn, $args = null) {
    $this->hooks[$event][] = array('fn' => $fn, 'args' => $args);
  }

  /**
   * Execute hook
   *
   * Fire of all hooks at the specified event
   * @param $event string
   * @param $args array
   */

  public function execute($event, $args = array()) {
    if (!isset($this->hooks[$event])) {
      global $console;
      $console->warn("$event-hook is not defined");
      return;
    }
    foreach ($this->hooks[$event] as $hook) {
      $args = $hook['args'] !== null ? $hook['args'] : $args;
      call_user_func_array($hook['fn'], $args);
    }
  }

}

