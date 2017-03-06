<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'reset':
      modal::start('Reset '.NAME.' to Default', CONTROLLER.'?action=reset');
      echo '<div class="alert alert-danger" role="alert"><b>Warning</b> This will delete all your own changes for '.NAME.'.</div>';
      modal::end('Reset '.NAME, 'danger');
      break;
    case 'restart':
      modal::start('Restart Emulationstation', CONTROLLER.'?action=restart');
      echo 'Do you really want to restart emulationstation?';
      modal::end('Restart', 'warning');
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