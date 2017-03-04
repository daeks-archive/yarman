<?php
  
class emulator
{
  public static $gamelist = 'gamelist.xml';

  public static function config($emulator)
  {
    $config = db::instance()->read('emulators', "id='".$emulator."'");
    if (sizeof($config) == 1) {
      return $config[0];
    } else {
      return array('id' => $emulator, 'name' => $emulator);
    }
  }
  
  public function read($emulator = null)
  {
    if ($emulator == null) {
      $output = array();
      $emulators = array_slice(scandir(db::instance()->read('config', "id='roms_path'")[0]['value']), 2);
      foreach ($emulators as $emulator) {
        $config = db::instance()->read('emulators', 'id='.db::instance()->quote($emulator));
        $tmp = array();
        if (sizeof($config) == 1) {
          $tmp = $config[0];
        } else {
          $tmp['id'] = $emulator;
          $tmp['name'] = $emulator;
        }
        array_push($output, $tmp);
      }
      return $output;
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
      $attributes = array();
      if ($item['attributes'] != '') {
        $attributes = json_decode($item['attributes']);
      }
      $tmp = array('type' => '', 'attributes' => $attributes, 'fields' => array());
      $rom = $item['path'];
      if (strpos($rom, '.') === 0) {
        $rom = $romspath.DIRECTORY_SEPARATOR.$emulator.substr($rom, 1);
      }
      if (is_file($rom)) {
        $tmp['type'] = 'game';
      } else {
        $tmp['type'] = 'folder';
      }
      
      if (sizeof($include) > 0) {
        $data = array();
        foreach (db::instance()->read('fields') as $field) {
          if (in_array($field['id'], $include)) {
            if ($field['export']) {
              $data[$field['id']] = $item[$field['id']];
            }
          } else {
            if ($field['export']) {
              $data[$field['id']] = '';
            }
          }
        }
        $tmp['fields'] = $data;
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
    $config = db::instance()->read('emulators', 'id='.db::instance()->quote($emulator));
    if (sizeof($config) == 1) {
      $config = $config[0];
    } else {
      $config = array();
      $config['whitelist'] = '';
      $config['blacklist'] = '';
    }
    $count = 0;
    
    db::instance()->delete('roms', "emulator='".$emulator."'");
    db::instance()->delete('metadata', "emulator='".$emulator."'");
    if (file_exists($romspath.DIRECTORY_SEPARATOR.$emulator)) {
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
        $tmp['attributes'] = json_encode($item['attributes']);
        $tmp['emulator'] = $emulator;
        foreach ($item['fields'] as $key => $value) {
          if (in_array($key, $fields)) {
            $tmp[$key] = $value;
          }
        }
        db::instance()->write('metadata', $tmp, 'id='.db::instance()->quote(rom::uniqid($emulator, $item['fields']['path'])));
      }
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
      if ($data['path'] == '') {
        array_push($output['metadata'], $data['id']);
      } else {
        if (strpos($data['path'], '.') === 0) {
          $data['path'] = $romspath.DIRECTORY_SEPARATOR.$emulator.substr($data['path'], 1);
          if (!file_exists($data['path'])) {
            array_push($output['metadata'], $data['id']);
          }
        } else {
          if (!file_exists($data['path'])) {
            array_push($output['metadata'], $data['id']);
          }
        }
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