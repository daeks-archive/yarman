<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'sync':
      modal::start('Getting Started - Sync with RetroPie', CONTROLLER.'?action=sync', 'POST');
      echo '<p><b>'.NAME.' has detected an unsynced database.</b></p>';
      echo 'Do you want to sync your existing RetroPie setup now?<br><br>';
      
      $readonly = false;
      if (sizeof(db::instance()->read('emulators', 'count is not null')) > 0) {
        $readonly = true;
      }
      
      echo form::getString(array('id' => 'romspath', 'name' => 'Location of your roms', 'readonly' => $readonly), current(db::instance()->read('config', "id='roms_path'"))['value']);
      echo '<div class="checkbox"><label><input type="checkbox" name="hash" value="1">(Optional) Hash large roms - This is a very time-intensive process.</label></div><br>';
      echo form::getString(array('id' => 'metadatapath', 'name' => 'Location of your gamelists', 'readonly' => $readonly), current(db::instance()->read('config', "id='metadata_path'"))['value']).'<br>';
      echo '<div class="alert alert-info" role="alert"><b>Info</b> Syncing emulators can be done also manually later.</div>';
      echo '<div class="alert alert-warning" role="alert"><b>Warning</b> Depending on your romset this might take a while.</div>';
      modal::end('Sync', 'success', 'modal-install');
      break;
    default:
      break;
  }
}

?>