<?php
    
  class roms {
  
    public static function getAll($system) {
      $array = array();
      $whitelist = db::read('games', $system, 'whitelist');
      foreach (scandir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system) as $rom) {
        if(is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$rom)) {
          if(isset($whitelist) && $whitelist != '') {
            if(strpos($whitelist, pathinfo($rom, PATHINFO_EXTENSION)) !== false) {
              array_push($array, $rom);
            }
          } else {
            array_push($array, $rom);
          }
        }
        if(is_dir(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$rom)) {
          if($rom != '.' && $rom != '..') {
            array_push($array, $rom);
          }
        }
      }
      return $array;
    }
    
    public static function getMetadata($system, $id) {
      $xml = simplexml_load_file(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.'gamelist.xml');
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