<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'restart':
      modal::start('Restart Emulationstation', CONTROLLER.'?action=restart');
      echo 'Do you really want to restart emulationstation?';
      modal::end('Restart', 'danger');
      break;
    case 'reboot':
      modal::start('Reboot System', CONTROLLER.'?action=reboot');
      echo 'Do you really want to restart the system?';
      modal::end('Reboot', 'danger');
      break;
    default:
      break;
  }
}

?>