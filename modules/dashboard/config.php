<?php

require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php');
$module = module::read();

if ($module != null) {
  define('CONTROLLER', 'controller.php');
  define('DIALOG', 'dialog.php');
  
  config::includes(MODULES.DIRECTORY_SEPARATOR.$module->id);
}

?>