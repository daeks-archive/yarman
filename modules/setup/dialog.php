<?php

require(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

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
      
      $romspath = current(db::instance()->read('config', "id='roms_path'"));
      $romspath['readonly'] = $readonly;
      echo form::getString($romspath, $romspath['value']);
      echo '<div class="checkbox"><label><input type="checkbox" name="hash" value="1">(Optional) Hash large roms - This is a very time-intensive process.</label></div><br>';
      
      $metadatapath = current(db::instance()->read('config', "id='metadata_path'"));
      $metadatapath['readonly'] = $readonly;
      echo form::getString($metadatapath, $metadatapath['value']).'<br>';
      echo '<div class="alert alert-info" role="alert"><b>Info</b> Syncing emulators can be done also manually later.</div>';
      echo '<div class="alert alert-warning" role="alert"><b>Warning</b> Depending on your romset this might take a while.</div>';
      modal::end('Sync', 'success', 'modal-install');
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>