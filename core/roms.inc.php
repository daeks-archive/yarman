<?php
    
  class roms {
  
    public static function getSystems($sys) {
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
      foreach(db::read('games') as $system) {
        if(array_key_exists($system['id'], $array)) {
          $array[$system['id']]['name'] = $system['name'];
          $array[$system['id']]['whitelist'] = $system['whitelist'];
        }
      }
      return utils::msort($array, array('name' => SORT_ASC));
    }

    public static function getRoms($sys) {
      $array = array();
      $whitelist = db::read('games', $sys, 'whitelist');
      foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$sys) as $rom) {
        if(is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$sys.DIRECTORY_SEPARATOR.$rom)) {
          if(isset($whitelist) && $whitelist != '') {
            if(strpos($whitelist, pathinfo($rom, PATHINFO_EXTENSION)) !== false) {
              array_push($array, $rom);
            }
          } else {
            array_push($array, $rom);
          }
        }
        if(is_dir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$sys.DIRECTORY_SEPARATOR.$rom)) {
          if($rom != '.' && $rom != '..') {
            array_push($array, $rom);
          }
        }
      }
      return $array;
    }
    
    public static function getMetadata($sys, $id) {
      $xml = simplexml_load_file(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$sys.DIRECTORY_SEPARATOR.'gamelist.xml');
      $array = json_decode(json_encode($xml),TRUE);
      if(isset($array['game'])) {
        if(!isset($array['game'][0]['path'])) {
          $array['game'] = array($array['game']);
        }
        foreach($array['game'] as $item) {
          if (substr_compare($item['path'], $id, strlen($item['path'])-strlen($id), strlen($id)) === 0) {
            foreach ($item as $key=>$value) {
              if(is_array($value)) {
                $item[$key] = '';
              }
            }
            return $item;
          }
        }
      }
      if(isset($array['folder'])) {
        if(!isset($array['folder'][0]['path'])) {
          $array['folder'] = array($array['folder']);
        }
        foreach($array['folder'] as $item) {
          if (substr_compare($item['path'], $id, strlen($item['path'])-strlen($id), strlen($id)) === 0) {
            foreach ($item as $key=>$value) {
              if(is_array($value)) {
                $item[$key] = '';
              }
            }
            return $item;
          }
        }
      }
    }
    
  }
    
?>