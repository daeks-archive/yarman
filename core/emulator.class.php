<?php
    
  class emulator {
  
    public static function readAll() {
      $emulators = array_slice(scandir(db::read('config', 'roms_path')), 2);
      $array = array();
      foreach ($emulators as $emulator) {
        $tmp = array();
        $tmp['id'] = $emulator;
        $tmp['name'] = $emulator;
        $tmp['whitelist'] = '';
        $tmp['count'] = 0;
        foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator) as $rom) {
          if(is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$rom)) {
            $tmp['count'] += 1;
            break;
          }
        }
        if($tmp['count'] > 0) {
          $array[$emulator] = $tmp;
        }
      }
      foreach(db::read('emulators') as $emulator) {
        if(array_key_exists($emulator['id'], $array)) {
          $array[$emulator['id']]['name'] = $emulator['name'];
          $array[$emulator['id']]['whitelist'] = $emulator['whitelist'];
        }
      }
      return utils::msort($array, array('name' => SORT_ASC));
    }
    
  }
    
?>