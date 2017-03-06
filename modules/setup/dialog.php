<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'sync':
      modal::start('Getting Started - Sync with RetroPie', CONTROLLER.'?action=sync');
      echo '<p><b>'.NAME.' has detected an empty rom database.</b></p>';
      echo 'Do you want to import your existing RetroPie setup now?<br>';
      echo '<div class="checkbox"><label><input type="checkbox" name="hash" value="1">(Optional) Hash large roms - This is a very time-intensive process.</label></div><br>';
      echo '<div class="alert alert-info" role="alert"><b>Info</b> Syncing emulators can be done also manually later.</div>';
      echo '<div class="alert alert-warning" role="alert"><b>Warning</b> Depending on your romset this might take a while.</div>';
      modal::end('Sync', 'success', 'modal-install');
      break;
    default:
      break;
  }
}

?>