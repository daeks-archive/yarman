<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'save':
      switch (network::get('id')) {
        case 'config':
          foreach ($_POST as $key => $value) {
            db::instance()->write('config', array('value' => $value), 'id='.db::instance()->quote($key));
          }
          network::success('Successfully saved config', 'true');
          break;
        case 'emulators':
          if (network::post('data') != '') {
            foreach (json_decode(network::post('data'), true) as $key => $item) {
              $item['id'] = trim($item['id']);
              db::instance()->write('emulators', $item, 'id='.db::instance()->quote($item['id']));
            }
          }
          network::success('Successfully saved emulators', 'true');
          break;
        default:
          network::error('invalid id - '.network::get('id'));
          break;
      }
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>