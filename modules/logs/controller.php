<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'view':
      if (network::get('id') != '') {
        $parts = explode('@', network::get('id'));
        $output = '';
        foreach (db::read($module->id) as $item) {
          if (sizeof($parts) == 2) {
            if ($item['id'] == $parts[1]) {
              if (file_exists($item['value'].DIRECTORY_SEPARATOR.$parts[0])) {
                $file = gzopen($item['value'].DIRECTORY_SEPARATOR.$parts[0], 'r');
                while ($line = gzgets($file, 1024)) {
                  $output .= $line;
                }
                gzclose($file);
              }
            }
          } else {
            if ($item['id'] == network::get('id')) {
              if (file_exists($item['value'])) {
                $output = file_get_contents($item['value']);
              }
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