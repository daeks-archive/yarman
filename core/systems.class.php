<?php
    
  class systems {
  
    public static function getAll() {
      $systems = array_slice(scandir(db::read('config', 'roms_path')), 2);
      $array = array();
      foreach ($systems as $system) {
        $tmp = array();
        $tmp['id'] = $system;
        $tmp['name'] = $system;
        $tmp['whitelist'] = '';
        $tmp['count'] = 0;
        foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system) as $rom) {
          if(is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$rom)) {
            $tmp['count'] += 1;
            break;
          }
        }
        if($tmp['count'] > 0) {
          $array[$system] = $tmp;
        }
      }
      foreach(db::read('systems') as $system) {
        if(array_key_exists($system['id'], $array)) {
          $array[$system['id']]['name'] = $system['name'];
          $array[$system['id']]['whitelist'] = $system['whitelist'];
        }
      }
      return utils::msort($array, array('name' => SORT_ASC));
    }
    
  }
    
?>