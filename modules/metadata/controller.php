<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if (network::get('action') != '') {
    switch (network::get('action')) {
      case 'change':
        if (network::get('emulator') != '') {
          cache::setClientVariable($module->id.'_emulator', network::get('emulator'));
          cache::unsetClientVariable($module->id.'_id');
          $data = '';
          foreach (emulator::readRomlist(network::get('emulator')) as $rom) {
            $data .= '<option value="'.$rom.'">'.$rom.'</option>';
          }
          network::success($data);
        } else {
          network::success('');
        }
        break;
      case 'presave':
        network::success('', "$('[data-toggle=\"post\"]').submit();");
        break;
      case 'save':
        rom::write(cache::getClientVariable($module->id.'_emulator'), network::post('id'), $_POST);
        network::success('Successfully Saved Gamelist', 'true');
        break;
      case 'delete':
        rom::delete(cache::getClientVariable($module->id.'_emulator'), network::get('id'));
        cache::unsetClientVariable($module->id.'_id');
        network::success('Successfully Deleted Rom', 'core.metadata.reset();');
        break;
      default:
        network::error('invalid Action - '.network::get('action'));
        break;
    }
  }

?>