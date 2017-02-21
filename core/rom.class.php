<?php
    
  class rom {
  
    public static function readSystem($system) {
      $array = array();
      $whitelist = db::read('systems', $system, 'whitelist');
      $blacklist = db::read('systems', $system, 'blacklist');
      foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system) as $item) {
        if(is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$item)) {
          if(isset($whitelist) && $whitelist != '') {
            if(strpos($whitelist, pathinfo($item, PATHINFO_EXTENSION)) !== false) {
              if(isset($blacklist) && $blacklist != '') {
                if(strpos($blacklist, pathinfo($item, PATHINFO_EXTENSION)) === false) {
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
        if(is_dir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$item)) {
          if($item != '.' && $item != '..') {
            if(isset($blacklist) && $blacklist != '') {
              if(strpos($blacklist, $item) === false) {
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
    
    public static function readMetadata($system, $id) {
      $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.'gamelist.xml');
      foreach($xml as $item) {
        if (substr_compare($item['fields']['path'], $id, strlen($item['fields']['path'])-strlen($id), strlen($id)) === 0) {
          return $item;
        }
      }
    }
    
    public static function writeMetadata($system, $id, $data) {
      copy(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.'gamelist.xml', db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.'gamelist.xml.bak');
      $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.'gamelist.xml');
      
      $output = array();
      foreach($xml as $item) {
        if (substr_compare($item['fields']['path'], $id, strlen($item['fields']['path'])-strlen($id), strlen($id)) === 0) {
          foreach ($data as $key => $value) {
            $field = db::read('fields', $key, 'type');
            if($field == 'date') {
              if(trim($value) != '') {
                $value = date_format(date_create($value),'Ymd\THis');
              } else {
                $value = '00000000T000000';
              }
            }            
            if($field != 'hidden') {
              if(isset($item['fields'][$key])) {
                $item['fields'][$key] = trim($value);
              } else {
                if(trim($value) != '') {
                  $item['fields'][$key] = trim($value);
                }
              }
            }
          }
        }
        array_push($output, $item);
      }
      return xml::write('gameList', $output, db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.'gamelist.xml');     
    }
    
  }
    
?>