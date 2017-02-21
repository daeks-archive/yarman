<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if(isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'change':
        if(isset($_GET['system']) && $_GET['system'] != '') {
          cache::setClientVariable($module->id.'_system', $_GET['system']);
          cache::unsetClientVariable($module->id.'_id');
          $data = '';
          foreach (rom::readSystem($_GET['system']) as $rom){
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
        rom::writeMetadata(cache::getClientVariable($module->id.'_system'), $_POST['id'], $_POST);
        utils::ajax(200, 'Successfully Saved Gamelist', 'true');
      break;
      default:
        utils::ajax(500, 'invalid Action - '.$_GET['action']);
      break;
    }
  }

?>