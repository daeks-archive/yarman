<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'save':
      foreach ($_POST as $key => $value) {
        db::instance()->write('config', array('value' => $value), 'id='.db::instance()->quote($key));
      }
      network::success('Successfully saved config', 'true');
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>