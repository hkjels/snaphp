<?php

namespace Bold;

/**
 * Config
 *
 */

class Config {

  private $conf = array();

  public static function enable ($key) {
    if (isset($this->conf[$key])) $this->conf[$key]['enabled'] = true;
    return $this;
  }

  public static function disable ($key) {
    if (isset($this->conf[$key])) {
      if (!$this->conf[$key]['canDisable']) throw new \Exception("$key can not be disabled");
      $this->conf[$key]['enabled'] = false;
    }
    return $this;
  }

  public function get ($key) {
    return isset($this->conf[$key]) ? $this->conf[$key]['value'] : null;
  }

  public function set ($key, $value, $canDisable = true) {
    if (!is_string($key)) {
      throw new \InvalidArgumentException('String is the only valid configuration key-type');
    }
    $this->conf[$key] = array('enabled' => true, 'value' => $value, 'canDisable' => $canDisable);
    return $this;
  }

 /**
  * Singleton pattern
  */

  private static $__instance = NULL;
  private function __construct(){}
  private function __clone(){}
  public static function getInstance(){
    if(self::$__instance == NULL) self::$__instance = new Config;
    return self::$__instance;
  }

}

