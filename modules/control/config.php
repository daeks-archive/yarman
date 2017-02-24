<?php

require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php');
$module = module::read();

if ($module != null) {
  define('CONTROLLER', 'modules'.URL_SEPARATOR.$module->id.URL_SEPARATOR.'controller.php');
  define('DIALOG', 'modules'.URL_SEPARATOR.$module->id.URL_SEPARATOR.'dialog.php');
  
  config::includes(MODULES.DIRECTORY_SEPARATOR.$module->id);
}

?>