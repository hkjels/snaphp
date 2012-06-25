<?php

namespace Bold\Util;

/**
 * Hooks
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

  public function add ($event, $fn, $args = array()) {
    $this->hooks[$event][] = array('fn' => $fn, 'args' => $args);
  }

  /**
   * Execute hook
   *
   * Fire of all hooks at the specified event
   * @param $event string
   * @param $args array  Will override existing arguments if any
   */

  public function execute($event, $args = null) {
    if (!isset($this->hooks[$event])) throw new \Exception("$event-hook is not defined");
    foreach ($this->hooks[$event] as $hook) {
      $args = $args != null ? $args : $hook['args'];
      call_user_func_array($hook['fn'], $args);
    }
  }

}

