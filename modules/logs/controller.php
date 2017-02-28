<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'view':
      if (network::get('id') != '') {
        $output = '';
        foreach (db::read($module->id) as $item) {
          if ($item['id'] == network::get('id')) {
            if (file_exists($item['value'])) {
              $output = file_get_contents($item['value']);
            }
          }
        }
        network::success($output);
      } else {
        network::success('');
      }
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>