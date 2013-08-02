<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

class registry
{
 private $vars = array();

 public function __set($index, $value)
 {
  $this->vars[$index] = $value;
 }

 public function __get($index)
 {
  return $this->vars[$index];
 }
}
?>
