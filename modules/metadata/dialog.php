<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'panel':
      if (network::get('id') != '') {
        switch (network::get('id')) {
          case 'monitor':
            panel::start('RetroPie Monitor', 'info');
            $emulators = emulator::read();
            $total = 0;
            $totalroms = 0;
            foreach ($emulators as $emulator) {
              if (isset($emulator['count']) && $emulator['count'] != '') {
                $totalroms += $emulator['count'];
                $total += 1;
              }
            }
            echo '<p><div>Total Emulators: <div class="pull-right"><b>'.$total.'</b></div></div>';
            echo '<div>Total Roms: <div class="pull-right"><b>'.$totalroms.'</b></div></div></p>';
            panel::end();
            break;
          default:
            break;
        }
      }
      break;
    case 'render':
      if (network::get('tab') != '') {
        cache::setClientVariable($module->id.'_tab', network::get('tab'));
        if (network::get('id') != '') {
          $id = rawurldecode(network::get('id'));
          cache::setClientVariable($module->id.'_id', $id);
          $data = metadata::render(network::get('tab'), cache::getClientVariable($module->id.'_emulator'), $id);
          network::success($data);
        } else {
          if (cache::getClientVariable($module->id.'_emulator') != '' && cache::getClientVariable($module->id.'_id') != '') {
            $data = metadata::render(network::get('tab'), cache::getClientVariable($module->id.'_emulator'), cache::getClientVariable($module->id.'_id'));
            network::success($data);
          } else {
            network::error('No ID specified');
          }
        }
      }
      break;
    case 'syncemulator':
      modal::start('Sync Emulator', CONTROLLER.'?action=syncemulator', 'POST');
      $emulator = emulator::config(cache::getClientVariable($module->id.'_emulator'));
      echo '<label>Select fields to be synced with '.$emulator['name'].'</label>';
      foreach (db::instance()->read('fields') as $field) {
        if ($field['import']) {
          echo '<div class="checkbox">';
          echo '<label>';
          if (strpos($field['validator'], 'data-fv-notempty') !== false || $field['readonly'] == 1) {
            echo '<input type="checkbox" disabled checked><input class="hidden" type="checkbox" name="include[]" checked value="'.$field['id'].'">';
          } else {
            echo '<input type="checkbox" name="include[]" checked value="'.$field['id'].'">';
          }
          echo $field['name'].' ('.$field['id'].')';
          echo '</label></div>';
        }
      }
      modal::end('Sync', 'success');
      break;
    case 'syncrom':
      modal::start('Sync Rom', CONTROLLER.'?action=syncrom', 'POST');
      $rom = rom::config(cache::getClientVariable($module->id.'_id'));
      echo '<label>Select fields to be synced with '.$rom['name'].'</label>';
      foreach (db::instance()->read('fields') as $field) {
        if ($field['import']) {
          echo '<div class="checkbox">';
          echo '<label>';
          if (strpos($field['validator'], 'data-fv-notempty') !== false || $field['readonly'] == 1) {
            echo '<input type="checkbox" disabled checked><input class="hidden" type="checkbox" name="include[]" checked value="'.$field['id'].'">';
          } else {
            echo '<input type="checkbox" name="include[]" checked value="'.$field['id'].'">';
          }
          echo $field['name'].' ('.$field['id'].')';
          echo '</label></div>';
        }
      }
      modal::end('Sync', 'success');
      break;
    case 'clean':
      modal::start('Clean Orphaned', CONTROLLER.'?action=clean');
      $orphaned = metadata::clean(cache::getClientVariable($module->id.'_emulator'));
      echo '<p>Orphaned Metadata: <b>'.sizeof($orphaned['metadata']).'</b></p>';
      if (sizeof($orphaned['metadata']) <= 5) {
        echo '<ul>';
        foreach ($orphaned['metadata'] as $item) {
          $rom = rom::read($item);
          echo '<li>'.$rom['name'].' ('.$rom['path'].')</li>';
        }
        echo '</ul>';
      }
      echo '<p>Orphaned Media:    <b>'.sizeof($orphaned['media']).'</b></p>';
      if (sizeof($orphaned['media']) <= 5) {
        echo '<ul>';
        foreach ($orphaned['media'] as $item) {
          echo '<li>'.$item.'</li>';
        }
        echo '</ul>';
      }
      echo 'Do you really want to clean '.cache::getClientVariable($module->id.'_emulator').'?';
      modal::end('Clean', 'success');
      break;
    case 'confirmsave':
      modal::start('Save Changes', CONTROLLER.'?action=presave');
      $rom = rom::config(cache::getClientVariable($module->id.'_id'));
      echo 'Do you really want to save '.$rom['name'].'?';
      modal::end('Save', 'success');
      break;
    case 'export':
      modal::start('Export to Gamelist', CONTROLLER.'?action=export', 'POST');
      $emulator = emulator::config(cache::getClientVariable($module->id.'_emulator'));
      $config = es::config();
      if ($config['SaveGamelistsOnExit'] == 'true') {
        echo '<div class="alert alert-warning" tabindex="-1"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span><b>Warning</b> You have enabled "SAVE METADATA ON EXIT".<br>Please quit your EmulationStation!</span></div>';
      }
      echo '<label>Select fields to be exported for '.$emulator['name'].'</label>';
      foreach (db::instance()->read('fields') as $field) {
        if ($field['export']) {
          echo '<div class="checkbox">';
          echo '<label>';
          if (strpos($field['validator'], 'data-fv-notempty') !== false || $field['readonly'] == 1) {
            echo '<input type="checkbox" disabled checked><input class="hidden" type="checkbox" name="include[]" checked value="'.$field['id'].'">';
          } else {
            echo '<input type="checkbox" name="include[]" checked value="'.$field['id'].'">';
          }
          echo $field['name'].' ('.$field['id'].')';
          echo '</label></div>';
        }
      }
      modal::end('Save', 'success');
      break;
    case 'confirmdelete':
      modal::start('Delete Item', CONTROLLER.'?action=delete&id='.cache::getClientVariable($module->id.'_id'));
      $rom = rom::config(cache::getClientVariable($module->id.'_id'));
      echo 'Do you really want to delete '.$rom['name'].'?';
      modal::end('Delete', 'danger');
      break;
    default:
      break;
  }
}

?>