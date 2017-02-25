<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
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
    case 'clean':
      modal::start('Clean Orphaned', CONTROLLER.'?action=clean');
      $romdata = rom::readAll(cache::getClientVariable($module->id.'_emulator'));
      $orphaned_metadata = 0;
      $orphaned_media = 0;
      $media = array();
      
      foreach ($romdata as $rom) {
        if (!file_exists(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.cache::getClientVariable($module->id.'_emulator').DIRECTORY_SEPARATOR.rom::parse($rom['id']))) {
          $orphaned_metadata += 1;
        }
        if (isset($rom['fields']['image']) && $rom['fields']['image'] != '') {
          array_push($media, pathinfo($rom['fields']['image'], PATHINFO_BASENAME));
        }
        if (isset($rom['fields']['video']) && $rom['fields']['video'] != '') {
          array_push($media, pathinfo($rom['fields']['video'], PATHINFO_BASENAME));
        }
        if (isset($rom['fields']['marquee']) && $rom['fields']['marquee'] != '') {
          array_push($media, pathinfo($rom['fields']['marquee'], PATHINFO_BASENAME));
        }
        if (isset($rom['fields']['thumbnail']) && $rom['fields']['thumbnail'] != '') {
          array_push($media, pathinfo($rom['fields']['thumbnail'], PATHINFO_BASENAME));
        }
      }
      
      foreach (scandir(db::read('config', 'media_path').DIRECTORY_SEPARATOR.cache::getClientVariable($module->id.'_emulator')) as $item) {
        if (is_file(db::read('config', 'media_path').DIRECTORY_SEPARATOR.cache::getClientVariable($module->id.'_emulator').DIRECTORY_SEPARATOR.$item)) {
          if (!in_array($item, $media)) {
            $orphaned_media += 1;
          }
        }
      }
      
      echo '<p>Orphaned Metadata: <b>'.$orphaned_metadata.'</b></p>';
      echo '<p>Orphaned Media:    <b>'.$orphaned_media.'</b></p>';
      echo 'Do you really want to clean '.cache::getClientVariable($module->id.'_emulator').'?';
      modal::end('Clean', 'success');
      break;
    case 'confirmsave':
      modal::start('Save Changes', CONTROLLER.'?action=presave');
      echo 'Do you really want to save '.cache::getClientVariable($module->id.'_id').'?';
      modal::end('Save', 'success');
      break;
    case 'confirmdelete':
      modal::start('Delete Item', CONTROLLER.'?action=delete&id='.cache::getClientVariable($module->id.'_id'));
      echo 'Do you really want to delete '.cache::getClientVariable($module->id.'_id').'?';
      modal::end('Delete', 'danger');
      break;
    default:
      break;
  }
}

?>