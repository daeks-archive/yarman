<?php
  
class emulator
{
  public static $gamelist = 'gamelist.xml';

  public static function config($emulator)
  {
    return db::instance()->read('emulators', "id='".$emulator."'")[0];
  }
  
  public function read($emulator = null)
  {
    if ($emulator == null) {
      return db::instance()->read('emulators');
    } else {
      return db::instance()->read('roms', "emulator='".$emulator."'");
    }
  }
  
  public function write($emulator, $include = array())
  {
    $romspath = db::instance()->read('config', "id='roms_path'")[0]['value'];
    $metadatapath = db::instance()->read('config', "id='metadata_path'")[0]['value'];
    
    $xml = $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
    if (!file_exists($xml)) {
      $xml = $metadatapath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
      copy($xml, $metadatapath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist.'.bak');
    } else {
      copy($xml, $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist.'.bak');
    }
    
    $output = array();
    $xmldata = xml::read($xml);
    foreach (db::instance()->read('metadata', 'emulator='.db::instance()->quote($emulator)) as $item) {
      $tmp = array('type' => '', 'attributes' => array(), 'fields' => array());
      if (is_file($item['path'])) {
        $tmp['type'] = 'game';
      } else {
        $tmp['type'] = 'folder';
      }
      if (sizeof($include) > 0) {
        echo "TEST";
      } else {
        $data = array();
        foreach (db::instance()->read('fields') as $field) {
          if ($field['export']) {
            $data[$field['id']] = $item[$field['id']];
          }
        }
        $tmp['fields'] = $data;
      }
      array_push($output, $tmp);
    }
    return xml::write('gameList', $output, $xml);
  }
  
  public static function sync($emulator)
  {
    $romspath = db::instance()->read('config', "id='roms_path'")[0]['value'];
    $config = db::instance()->read('emulators', "id='".$emulator."'");
    if (sizeof($config) > 1) {
      $config = $config[0];
    } else {
      $config = array();
      $config['whitelist'] = '';
      $config['blacklist'] = '';
    }
    $count = 0;
    
    db::instance()->delete('roms', "emulator='".$emulator."'");
    db::instance()->delete('metadata', "emulator='".$emulator."'");
    foreach (scandir($romspath.DIRECTORY_SEPARATOR.$emulator) as $item) {
      if (is_file($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
        if ($config['whitelist'] != '') {
          if (strpos($config['whitelist'], pathinfo($item, PATHINFO_EXTENSION)) !== false) {
            if ($config['blacklist'] != '') {
              if (strpos($config['blacklist'], pathinfo($item, PATHINFO_EXTENSION)) === false) {
                $count += 1;
                $data = array('name' => $item, 'emulator' => $emulator, 'size' => filesize($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item));
                rom::create(rom::uniqid($emulator, $item), $data);
              }
            } else {
              $count += 1;
              $data = array('name' => $item, 'emulator' => $emulator, 'size' => filesize($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item));
              rom::create(rom::uniqid($emulator, $item), $data);
            }
          }
        } else {
          $count += 1;
          $data = array('name' => $item, 'emulator' => $emulator, 'size' => filesize($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item));
          rom::create(rom::uniqid($emulator, $item), $data);
        }
      }
      if (is_dir($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
        if ($item != '.' && $item != '..') {
          if ($config['blacklist'] != '') {
            if (strpos($config['blacklist'], $item) === false) {
              $count += 1;
              $data = array('name' => $item, 'emulator' => $emulator, 'size' => 0);
              rom::create(rom::uniqid($emulator, $item), $data);
            }
          } else {
            $count += 1;
            $data = array('name' => $item, 'emulator' => $emulator, 'size' => 0);
            rom::create(rom::uniqid($emulator, $item), $data);
          }
        }
      }
    }
    
    $xml = db::instance()->read('config', "id='roms_path'")[0]['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
    if (!file_exists($xml)) {
      $xml = db::instance()->read('config', "id='metadata_path'")[0]['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
    }
    
    $xmldata = xml::read($xml);
    $fields = array();
    foreach (db::instance()->read('fields') as $field) {
      if ($field['type'] != 'image') {
        array_push($fields, $field['id']);
      }
    }
    
    foreach ($xmldata as $item) {
      $tmp = array();
      $tmp['id'] = rom::uniqid($emulator, $item['fields']['path']);
      $tmp['emulator'] = $emulator;
      foreach ($item['fields'] as $key => $value) {
        if (in_array($key, $fields)) {
          $tmp[$key] = $value;
        }
      }
      db::instance()->write('metadata', $tmp, 'id='.db::instance()->quote(rom::uniqid($emulator, $item['fields']['path'])));
    }
    
    db::instance()->write('emulators', array('count' => $count), 'id='.db::instance()->quote($emulator));
    return $count;
  }
  
  public static function clean($emulator)
  {
    $output = array('metadata' => array(), 'media' => array());
    $media = array();
    $romspath = db::instance()->read('config', "id='roms_path'")[0]['value'];
    foreach (db::instance()->read('metadata', 'emulator='.db::instance()->quote($emulator)) as $data) {
      if ($data['path'] == '' || !file_exists($data['path'])) {
        db::instance()->delete('metadata', 'id='.db::instance()->quote($data['id']));
      }
      if (isset($data['image']) && $data['image'] != '') {
        array_push($media, pathinfo($data['image'], PATHINFO_BASENAME));
      }
      if (isset($data['video']) && $data['video'] != '') {
        array_push($media, pathinfo($data['video'], PATHINFO_BASENAME));
      }
      if (isset($data['marquee']) && $data['marquee'] != '') {
        array_push($media, pathinfo($data['marquee'], PATHINFO_BASENAME));
      }
      if (isset($data['thumbnail']) && $data['thumbnail'] != '') {
        array_push($media, pathinfo($data['thumbnail'], PATHINFO_BASENAME));
      }
    }
    
    $fields = db::instance()->read('fields');
    
    foreach ($fields as $field) {
      if ($field['type'] == 'upload') {
        foreach (scandir($field['path'].DIRECTORY_SEPARATOR.$emulator) as $item) {
          if (is_file($field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
            if (!in_array($item, $media) && !in_array($field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item, $output['media'])) {
              unlink($field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item);
            }
          }
        }
      }
    }
    
    return $output;
  }
}
  
?>