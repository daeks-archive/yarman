<?php
    
  class emulator
  {
  
    public static function readAll()
    {
      $emulators = array_slice(scandir(db::read('config', 'roms_path')), 2);
      $array = array();
      foreach ($emulators as $emulator) {
        $whitelist = db::read('emulators', $emulator, 'whitelist');
        $blacklist = db::read('emulators', $emulator, 'blacklist');
        
        $tmp = array();
        $tmp['id'] = $emulator;
        $tmp['name'] = $emulator;
        $tmp['whitelist'] = '';
        $tmp['count'] = 0;
        foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator) as $item) {
          if (is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
            if (isset($whitelist) && $whitelist != '') {
              if (strpos($whitelist, pathinfo($item, PATHINFO_EXTENSION)) !== false) {
                if (isset($blacklist) && $blacklist != '') {
                  if (strpos($blacklist, pathinfo($item, PATHINFO_EXTENSION)) === false) {
                    $tmp['count'] += 1;
                  }
                } else {
                  $tmp['count'] += 1;
                }
              }
            } else {
              $tmp['count'] += 1;
            }
          }
          if (is_dir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
            if ($item != '.' && $item != '..') {
              if (isset($blacklist) && $blacklist != '') {
                if (strpos($blacklist, $item) === false) {
                  $tmp['count'] += 1;
                }
              } else {
                $tmp['count'] += 1;
              }
            }
          }
        }
        if ($tmp['count'] > 0) {
          $array[$emulator] = $tmp;
        }
      }
      foreach (db::read('emulators') as $emulator) {
        if (array_key_exists($emulator['id'], $array)) {
          $array[$emulator['id']]['name'] = $emulator['name'];
          $array[$emulator['id']]['whitelist'] = $emulator['whitelist'];
        }
      }
      return utils::msort($array, array('name' => SORT_ASC));
    }
    
    public static function readRomlist($emulator)
    {
      $array = array();
      $whitelist = db::read('emulators', $emulator, 'whitelist');
      $blacklist = db::read('emulators', $emulator, 'blacklist');
      foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator) as $item) {
        if (is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
          if (isset($whitelist) && $whitelist != '') {
            if (strpos($whitelist, pathinfo($item, PATHINFO_EXTENSION)) !== false) {
              if (isset($blacklist) && $blacklist != '') {
                if (strpos($blacklist, pathinfo($item, PATHINFO_EXTENSION)) === false) {
                  array_push($array, $item);
                }
              } else {
                array_push($array, $item);
              }
            }
          } else {
            array_push($array, $item);
          }
        }
        if (is_dir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
          if ($item != '.' && $item != '..') {
            if (isset($blacklist) && $blacklist != '') {
              if (strpos($blacklist, $item) === false) {
                array_push($array, $item);
              }
            } else {
              array_push($array, $item);
            }
          }
        }
      }
      return $array;
    }
    
  }
    
?>