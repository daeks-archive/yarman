<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'change':
      if (network::get('emulator') != '') {
        cache::setClientVariable($module->id.'_emulator', network::get('emulator'));
        cache::unsetClientVariable($module->id.'_id');
        $output = '<option value="" selected>-- Select Config File --</option>';
        foreach (db::read($module->id) as $item) {
          if (strpos($item['value'], '%EMULATOR%') !== false && $item['type'] == 'file') {
            $item['value'] = str_replace('%EMULATOR%', network::get('emulator'), $item['value']);
            $output .= '<option value="'.$item['id'].'">'.$item['value'].'</option>';
          }
        }
        network::success($output);
      } else {
        cache::unsetClientVariable($module->id.'_emulator');
        cache::unsetClientVariable($module->id.'_id');
        $output = '<option value="" selected>-- Select Config File --</option>';
        foreach (db::read($module->id) as $item) {
          if ($item['type'] == 'file') {
            if (strpos($item['value'], '%') === false) {
              $output .= '<option value="'.$item['id'].'">'.$item['value'].'</option>';
            }
          } elseif ($item['type'] == 'folder') {
            foreach (array_slice(scandir($item['value']), 2) as $object) {
              $output .= '<option  alue="'.$object.'@'.$item['value'].'">'.$item['value'].DIRECTORY_SEPARATOR.$object.'</option>';
            }
          }
        }
        network::success($output);
      }
      break;
    case 'view':
      if (network::get('id') != '') {
        cache::setClientVariable($module->id.'_id', network::get('id'));
        $parts = explode('@', network::get('id'));
        $output = '';
        foreach (db::read($module->id) as $item) {
          if (sizeof($parts) == 2) {
            if ($item['id'] == $parts[1]) {
              $item['value'] = str_replace('%EMULATOR%', cache::getClientVariable($module->id.'_emulator'), $item['value']);
              if (file_exists($item['value'].DIRECTORY_SEPARATOR.$parts[0])) {
                $output = file_get_contents($item['value'].DIRECTORY_SEPARATOR.$parts[0]);
              }
            }
          } else {
            if ($item['id'] == network::get('id')) {
              $item['value'] = str_replace('%EMULATOR%', cache::getClientVariable($module->id.'_emulator'), $item['value']);
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
    case 'presave':
      network::success('', "core.editor.save('".CONTROLLER."?action=save', 'module-content');");
      break;
    case 'save':
      $parts = explode('@', cache::getClientVariable($module->id.'_id'));
      foreach (db::read($module->id) as $item) {
        if (sizeof($parts) == 2) {
          if ($item['id'] == $parts[1]) {
            $item['value'] = str_replace('%EMULATOR%', cache::getClientVariable($module->id.'_emulator'), $item['value']);
            if (file_exists($item['value'].DIRECTORY_SEPARATOR.$parts[0])) {
              copy($item['value'].DIRECTORY_SEPARATOR.$parts[0], $item['value'].DIRECTORY_SEPARATOR.$parts[0].'.bak');
              file_put_contents($item['value'].DIRECTORY_SEPARATOR.$parts[0], $_POST['data']);
            }
          }
        } else {
          if ($item['id'] == cache::getClientVariable($module->id.'_id')) {
            $item['value'] = str_replace('%EMULATOR%', cache::getClientVariable($module->id.'_emulator'), $item['value']);
            if (file_exists($item['value'])) {
              copy($item['value'], $item['value'].'.bak');
              file_put_contents($item['value'], $_POST['data']);
            }
          }
        }
      }
      network::success('Successfully Saved File', 'true');
      break;  
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>