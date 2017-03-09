<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'confirmsave':
      modal::start('Save Changes', CONTROLLER.'?action=presave');
      $parts = explode('@', cache::getClientVariable($module->id.'_id'));
      echo 'Do you really want to save '.$parts[0].'?';
      modal::end('Save', 'success');
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>