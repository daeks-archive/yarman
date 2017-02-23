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
    case 'confirmsave':
      modal::start('Save Changes', CONTROLLER.'?action=presave');
      echo 'Do you really want to save '.pathinfo(cache::getClientVariable($module->id.'_id'), PATHINFO_BASENAME).'?';
      modal::end('Save', 'success');
      break;
    case 'confirmdelete':
      modal::start('Delete Item', CONTROLLER.'?action=delete&id='.pathinfo(cache::getClientVariable($module->id.'_id'), PATHINFO_BASENAME));
      echo 'Do you really want to delete '.pathinfo(cache::getClientVariable($module->id.'_id'), PATHINFO_BASENAME).'?';
      modal::end('Delete', 'danger');
      break;
    default:
      break;
  }
}

?>