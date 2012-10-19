<?php

namespace Snap;

/**
 * Config
 *
 */

class Config {

  /**
   * All stored configurations
   */

  private $conf = array();

  /**
   * Enable
   *
   * Enable a certain configuration
   *
   * @param string $key
   * @return Config
   */

  public function enable ($key) {
    if ($this->disabled($key)) $this->conf[$key]['enabled'] = true;
    return $this;
  }

  /**
   * Enabled
   *
   * Wether a configuration is enabled
   *
   * @param string $key
   */

  public function enabled ($key) {
    return (isset($this->conf[$key]) && $this->conf[$key]['enabled']);
  }

  /**
   * Disable
   *
   * Disable a certain configuration
   *
   * @param string $key
   * @return Config
   */

  public function disable ($key) {
    if ($this->enabled($key)) {
      if (!$this->conf[$key]['canDisable']) throw new \Exception("$key can not be disabled");
      $this->conf[$key]['enabled'] = false;
    }
    return $this;
  }


  /**
   * Disabled
   *
   * Wether a configuration is disabled
   *
   * @param string $key
   */

  public function disabled ($key) {
    return (isset($this->conf[$key]) && !$this->conf[$key]['enabled']);
  }

  /**
   * Get
   *
   * Returns the value of a given configuration
   *
   * @param string $key
   * @return mixed
   */

  public function get ($key) {
    return isset($this->conf[$key]) ? $this->conf[$key]['value'] : null;
  }

  /**
   * Set
   *
   * Add or change a configuration
   *
   * @param string $key
   * @param mixed $value
   * @param boolean $canDisable
   * @return Config
   */

  public function set ($key, $value, $canDisable = true) {
    if (!is_string($key)) {
      throw new \InvalidArgumentException('String is the only valid configuration key-type');
    }
    $this->conf[$key] = array('enabled' => true, 'value' => $value, 'canDisable' => $canDisable);
    return $this;
  }

 /**
  * Singleton please
  */

  private static $__instance = NULL;
  private function __construct(){}
  private function __clone(){}
  public static function getInstance(){
    if(self::$__instance == NULL) self::$__instance = new Config;
    return self::$__instance;
  }

}

