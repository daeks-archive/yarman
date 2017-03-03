<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'init':
      $total = 0;
      foreach (db::instance()->read('emulators') as $emulator) {
        if (isset($emulator['count'])) {
          $total += $emulator['count'];
        }
      }
      network::success(''.$total);
      break;
    case 'install':
      $output = '';
      $emulator = db::instance()->read('emulators', 'count is null');
      if (sizeof($emulator) >= 1) {
        $current = $emulator[0];
        $next = array();
        if (sizeof($emulator) >= 2) {
          $next = $emulator[1];
        }
        emulator::sync($current['id']);
        $output .= '<form class="form-horizontal" id="modal-data" name="modal-data" data-validate="modal" data-toggle="modal" data-target="#modal-body" action="'.CONTROLLER.'?action=install" method="GET"><fieldset>';
        $output .= '<div class="alert alert-warning" role="alert"><b>Warning</b> Depending on your romset this might take a while.</div>';
        if (sizeof($next) > 0) {
          $output .= '<p>Successfully Installed '.$current['name'].'...<br>';
          $output .= 'Installing <b>'.$next['name'].'</b>...</p>';
          if (sizeof($emulator) > 1) {
            $output .= 'Emulators left: <b>'.(sizeof($emulator)-1).'</b></p>';
          }
        } else {
          $output .= '<p>Installed <b>'.$current['name'].'</b>...</p>';
        }
        $output .= '</fieldset></form>';
        network::send(301, $output, 'core.install();');
      } else {
        network::success('Successfully Finished Setup', 'location.reload();');
      }
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>