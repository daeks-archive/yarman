<?php

set_time_limit(600);
require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'change':
      if (network::get('emulator') != '') {
        cache::setClientVariable($module->id.'_emulator', network::get('emulator'));
        cache::unsetClientVariable($module->id.'_id');
        $output = '';
        if (cache::getClientVariable($module->id.'_filter') != '') {
          foreach (emulator::read(network::get('emulator')) as $rom) {
            $data = rom::read($rom['id']);
            if (cache::getClientVariable($module->id.'_filter') == 'nodata') {
              if (!isset($data) || isset($data) && !isset($data['name']) || isset($data) &&  $data['name'] == '') {
                $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
              }
            } elseif (cache::getClientVariable($module->id.'_filter') == 'noimage') {
              if (!isset($data) || isset($data) && !isset($data['image']) || isset($data) &&  $data['image'] == '') {
                $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
              }
            } elseif (cache::getClientVariable($module->id.'_filter') == 'novideo') {
              if (!isset($data) || isset($data) && !isset($data['video']) || isset($data) && $data['video'] == '') {
                $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
              }
            }
          }
        } else {
          foreach (emulator::read(network::get('emulator')) as $rom) {
            $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
          }
        }
        network::success($output);
      } else {
        cache::unsetClientVariable($module->id.'_emulator');
        cache::unsetClientVariable($module->id.'_id');
        network::success('');
      }
      break;
    case 'filter':
      cache::setClientVariable($module->id.'_filter', network::get('type'));
      cache::unsetClientVariable($module->id.'_id');
      $output = '';
      foreach (emulator::read(cache::getClientVariable($module->id.'_emulator')) as $rom) {
        if (network::get('type') == '') {
          $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
        } else {
          $data = rom::read($rom['id']);
          if (network::get('type') == 'nodata') {
            if (!isset($data) || isset($data) && !isset($data['name']) || isset($data) &&  $data['name'] == '') {
              $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
            }
          } elseif (network::get('type') == 'noimage') {
            if (!isset($data) || isset($data) && !isset($data['image']) || isset($data) &&  $data['image'] == '') {
              $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
            }
          } elseif (network::get('type') == 'novideo') {
            if (!isset($data) || isset($data) && !isset($data['video']) || isset($data) && $data['video'] == '') {
              $output .= '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
            }
          }
        }
      }
      network::success($output);
      break;
    case 'clean':
      emulator::clean(cache::getClientVariable($module->id.'_emulator'));
      network::success('', "$('#metadata-clean').addClass('disabled');");
      break;
    case 'presave':
      network::success('', "$('[data-toggle=\"post\"]').submit();");
      break;
    case 'save':
      $data = array();
      $data['emulator'] = cache::getClientVariable($module->id.'_emulator');
      foreach ($_POST as $key => $value) {
        $data[$key] = $value;
      }
      rom::write(network::post('id'), $data);
      network::success('Successfully Saved Rom', 'true');
      break;
    case 'export':
      emulator::write(cache::getClientVariable($module->id.'_emulator'), $_POST['include']);
      network::success('Successfully Exported Gamelist', 'core.metadata.reset();');
      break;
    case 'syncemulator':
      cache::unsetClientVariable($module->id.'_id');
      emulator::sync(cache::getClientVariable($module->id.'_emulator'), $_POST['include'], isset($_POST['hash']));
      network::success('Successfully Synced Emulator', 'core.metadata.reset();');
      break;
    case 'syncrom':
      cache::unsetClientVariable($module->id.'_id');
      rom::sync(cache::getClientVariable($module->id.'_emulator'), cache::getClientVariable($module->id.'_id'), $_POST['include']);
      network::success('Successfully Synced Rom', 'core.metadata.reload();');
      break;
    case 'delete':
      rom::delete(network::get('id'));
      cache::unsetClientVariable($module->id.'_id');
      network::success('Successfully Deleted Rom', 'core.metadata.reset();');
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>