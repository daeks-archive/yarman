<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'setup':
      modal::start('Setup', CONTROLLER.'?action=install');
      echo '<p><b>'.NAME.' has detected a new installation.</b></p>';
      echo 'Do you want to search for existing roms and metadata?<br><br>';
      echo '<div class="alert alert-warning" role="alert"><b>Warning</b> Depending on your romset this might take a while.</div>';
      modal::end('Install', 'success', 'modal-install');
      break;
    default:
      break;
  }
}

?>