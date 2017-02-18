<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if(isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'tab':
        if(isset($_GET['tab'])) {
          cache::setClientVariable($module->id.'_tab', $_GET['tab']);
          if(isset($_GET['id'])) {
            $_GET['id'] = rawurldecode($_GET['id']);
            cache::setClientVariable($module->id.'_id', $_GET['id']);
            $data = metadata::render($_GET['tab'], cache::getClientVariable($module->id.'_system'), $_GET['id']);
            utils::ajax(200, $data);
          } else {
            if(cache::getClientVariable($module->id.'_system') != '' && cache::getClientVariable($module->id.'_id') != '') {
              $data = metadata::render($_GET['tab'], cache::getClientVariable($module->id.'_system'), cache::getClientVariable($module->id.'_id'));
              utils::ajax(200, $data);
            } else {
              utils::ajax(500, 'No ID specified');
            }
          }
        }
      break;
      default:
      break;
    }
  }

?>