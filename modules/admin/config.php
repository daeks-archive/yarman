<?php

require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config.php');
$module = module::read();

if ($module != null) {
  config::includes(MODULES.DIRECTORY_SEPARATOR.$module->id);
}

?>