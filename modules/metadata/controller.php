<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if(isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'change':
        if(isset($_GET['emulator']) && $_GET['emulator'] != '') {
          cache::setClientVariable($module->id.'_emulator', $_GET['emulator']);
          cache::unsetClientVariable($module->id.'_id');
          $data = '';
          foreach (rom::reademulator($_GET['emulator']) as $rom){
            $data .= '<option value="'.$rom.'">'.$rom.'</option>';
          }
          utils::ajax(200, $data);
        } else {
          utils::ajax(200, '');
        }
      break;
      case 'presave':
        utils::ajax(200, '', "$('[data-toggle=\"post\"]').submit();");
      break;
      case 'save':
        rom::writeMetadata(cache::getClientVariable($module->id.'_emulator'), $_POST['id'], $_POST);
        utils::ajax(200, 'Successfully Saved Gamelist', 'true');
      break;
      case 'delete':
        rom::remove(cache::getClientVariable($module->id.'_emulator'), $_GET['id']);
        cache::unsetClientVariable($module->id.'_id');
        utils::ajax(200, 'Successfully Deleted Rom', 'core.metadata.reset();');
      break;
      default:
        utils::ajax(500, 'invalid Action - '.$_GET['action']);
      break;
    }
  }

?>