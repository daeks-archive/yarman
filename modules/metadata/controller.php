<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'change':
      if (network::get('emulator') != '') {
        cache::setClientVariable($module->id.'_emulator', network::get('emulator'));
        cache::unsetClientVariable($module->id.'_id');
        $output = '';
        if (cache::getClientVariable($module->id.'_filter') != '') {
          $romdata = rom::readAll(network::get('emulator'));
          foreach (emulator::readRomlist(network::get('emulator')) as $rom) {
            $offset = array_search($rom, array_column($romdata, 'id'));
            $data = null;
            if ($offset >= 0) {
              if (isset($romdata[$offset])) {
                $data = $romdata[$offset];
              }
            }
            if (cache::getClientVariable($module->id.'_filter') == 'nodata') {
              if (!isset($data) || isset($data) && !isset($data['fields']['name']) || isset($data['fields']) &&  $data['fields']['name'] == '') {
                $output .= '<option value="'.$rom.'">'.$rom.'</option>';
              }
            } elseif (cache::getClientVariable($module->id.'_filter') == 'noimage') {
              if (!isset($data) || isset($data) && !isset($data['fields']['image']) || isset($data['fields']) &&  $data['fields']['image'] == '') {
                $output .= '<option value="'.$rom.'">'.$rom.'</option>';
              }
            } elseif (cache::getClientVariable($module->id.'_filter') == 'novideo') {
              if (!isset($data) || isset($data['fields']) && !isset($data['fields']['video']) || isset($data['fields']) && $data['fields']['video'] == '') {
                $output .= '<option value="'.$rom.'">'.$rom.'</option>';
              }
            }
          }
        } else {
          foreach (emulator::readRomlist(network::get('emulator')) as $rom) {
            $output .= '<option value="'.$rom.'">'.$rom.'</option>';
          }
        }
        network::success($output);
      } else {
        network::success('');
      }
      break;
    case 'filter':
      cache::setClientVariable($module->id.'_filter', network::get('type'));
      cache::unsetClientVariable($module->id.'_id');
      $romdata = rom::readAll(cache::getClientVariable($module->id.'_emulator'));
      $output = '';
      foreach (emulator::readRomlist(cache::getClientVariable($module->id.'_emulator')) as $rom) {
        if (network::get('type') == '') {
          $output .= '<option value="'.$rom.'">'.$rom.'</option>';
        } else {
          $offset = array_search(rom::parse($rom), array_column($romdata, 'id'));
          $data = null;
          if (isset($romdata[$offset])) {
            $data = $romdata[$offset];
          }
          if (network::get('type') == 'nodata') {
            if (!isset($data) || isset($data) && !isset($data['fields']['name']) || isset($data['fields']) &&  $data['fields']['name'] == '') {
              $output .= '<option value="'.$rom.'">'.$rom.'</option>';
            }
          } elseif (network::get('type') == 'noimage') {
            if (!isset($data) || isset($data) && !isset($data['fields']['image']) || isset($data['fields']) &&  $data['fields']['image'] == '') {
              $output .= '<option value="'.$rom.'">'.$rom.'</option>';
            }
          } elseif (network::get('type') == 'novideo') {
            if (!isset($data) || isset($data['fields']) && !isset($data['fields']['video']) || isset($data['fields']) && $data['fields']['video'] == '') {
              $output .= '<option value="'.$rom.'">'.$rom.'</option>';
            }
          }
        }
      }
      network::success($output);
      break;
    case 'clean':
      $orphaned = metadata::findOrphaned(cache::getClientVariable($module->id.'_emulator'));
      foreach ($orphaned['media'] as $item) {
        unlink($item);
      }
      rom::clean(cache::getClientVariable($module->id.'_emulator'), $orphaned['metadata']);
      network::success('', "$('#metadata-clean').addClass('disabled');");
      break;
    case 'presave':
      network::success('', "$('[data-toggle=\"post\"]').submit();");
      break;
    case 'save':
      rom::write(cache::getClientVariable($module->id.'_emulator'), network::post('id'), $_POST);
      network::success('Successfully Saved Gamelist', 'true');
      break;
    case 'delete':
      rom::delete(cache::getClientVariable($module->id.'_emulator'), network::get('id'));
      cache::unsetClientVariable($module->id.'_id');
      network::success('Successfully Deleted Rom', 'core.metadata.reset();');
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>