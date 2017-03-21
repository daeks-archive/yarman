<?php

require(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

if (network::get('action') != '') {
  switch (network::get('action')) {
    case 'change':
      if (network::get('emulator') != '') {
        cache::setClientVariable($module->id.'_emulator', network::get('emulator'));
        $romspath = current(db::instance()->read('config', "id='roms_path'"))['value'];
        $output = '';
        $output .= '<div class="row">';
        $output .= '<div class="col-sm-12" name="rom-data" id="rom-data" style="overflow-y: auto !important; overflow-x: hidden !important;">';
        $output .= '<table class="table table-hover">';
        $output .= '<thead><tr>';
        $output .= '<th>Filename</th>';
        $output .= '<th>Size</th>';
        $output .= '<th>Created</th>';
        $output .= '<th>Modified</th>';
        $output .= '</tr></thead><tbody>';
        foreach (utils::msort(db::instance()->read('roms', 'emulator='.db::instance()->quote(network::get('emulator'))), array('type' => 'SORT_ASC', 'name' => 'SORT_ASC')) as $item) {
          if ($item['type'] == 'folder') {
            $output .= '<tr id="'.$item['id'].'" name= "'.$item['id'].'">';
            $output .= '<td colspan="4"><em class="fa fa-fw fa-folder"></em> '.$item['name'].'</td>';
            $output .= '</tr>';
          } elseif ($item['type'] == 'game') {
            $output .= '<tr id="'.$item['id'].'" name= "'.$item['id'].'">';
            $output .= '<td>';
            $rom = db::instance()->read('metadata', 'id='.db::instance()->quote($item['id']));
            if (sizeof($rom) == 1) {
              $rom = current($rom);
              if ($rom['image'] != '') {
                $output .= '<img style="border-radius: 5px" data-toggle="lazy" data-query="#rom-data" data-original="/core/proxy.php?action=render&type=image&id='.$item['id'].'" height="32" width="32"> ';
              }
            }
            $output .= $item['name'].'</td>';
            $output .= '<td>'.round($item['size']/1024/1024, 2).' KB</td>';
            $output .= '<td>'.date('Y/m/d H:i:s', fileatime($romspath.DIRECTORY_SEPARATOR.network::get('emulator').DIRECTORY_SEPARATOR.$item['name'])).'</td>';
            $output .= '<td>'.date('Y/m/d H:i:s', filemtime($romspath.DIRECTORY_SEPARATOR.network::get('emulator').DIRECTORY_SEPARATOR.$item['name'])).'</td>';
            $output .= '</tr>';
          }
        }
        $output .= '</tbody></table></div></div>';
        network::success($output);
      } else {
        cache::unsetClientVariable($module->id.'_emulator');
        network::success('');
      }
      break;
    default:
      network::error('invalid action - '.network::get('action'));
      break;
  }
}

?>