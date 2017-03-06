<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'reset':
      db::instance()->reset();
      network::success('Successfully Reset '.NAME, 'window.location.href = \'/\';');
      break;
    case 'backup':
      db::instance()->backup();
      network::success('Successfully Backup '.NAME, 'true');
      break;
    case 'restart':
      es::stop();
      es::start();
      network::success('Successfully Restarted Emulationstation', 'true');
      break;
    case 'reboot':
      system::reboot();
      network::success('Successfully Initiated Reboot', 'true');
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>