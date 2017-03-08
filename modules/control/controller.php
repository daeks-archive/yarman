<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'restore':
      if (network::post('id') != '') {
        $parts = explode('.', pathinfo(network::post('id'), PATHINFO_FILENAME));
        db::instance()->restore($parts[0], $parts[2]);
        network::success('Successfully Restored '.NAME, 'window.location.href = \'/\';');
      } else {
        network::success('', 'true');
      }
      break;
    case 'reset':
      db::instance()->reset();
      network::success('Successfully Reset '.NAME, 'window.location.href = \'/\';');
      break;
    case 'backup':
      db::instance()->backup();
      network::success('Successfully Backup '.NAME, 'true');
      break;
    case 'stop':
      es::stopGame();
      network::success('Successfully Stopped Emulator', 'true');
      break;
    case 'restart':
      es::restart();
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