<?php
  
class emulator
{
  public static $gamelist = 'gamelist.xml';
  public static $size = 5242880;

  public static function config($emulator)
  {
    $config = db::instance()->read('emulators', "id='".$emulator."'");
    if (sizeof($config) == 1) {
      return current($config);
    } else {
      return array('id' => $emulator, 'name' => $emulator);
    }
  }
  
  public function read($emulator = null)
  {
    if ($emulator == null) {
      $output = array();
      $romspath = current(db::instance()->read('config', "id='roms_path'"))['value'];
      $emulators = array_slice(scandir($romspath), 2);
      foreach ($emulators as $emulator) {
        $config = db::instance()->read('emulators', 'id='.db::instance()->quote($emulator));
        $tmp = array();
        if (sizeof($config) == 1) {
          $tmp = current($config);
        } else {
          $tmp['id'] = $emulator;
          $tmp['name'] = '~ '.$emulator;
          $roms = array_slice(scandir($romspath.DIRECTORY_SEPARATOR.$emulator), 2);
          $tmp['count'] = sizeof($roms);
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
    $romspath = current(db::instance()->read('config', "id='roms_path'"))['value'];
    $metadatapath = current(db::instance()->read('config', "id='metadata_path'"))['value'];
    
    $xml = $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
    if (!file_exists($xml)) {
      $xml = $metadatapath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
      copy($xml, $metadatapath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist.'.bak');
    } else {
      copy($xml, $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist.'.bak');
    }
    
    $output = array();
    $xmldata = xml::read($xml);
    $sdbdata = db::instance()->read('metadata', 'emulator='.db::instance()->quote($emulator));
    
    foreach ($xmldata as $item) {
      if (array_key_exists(rom::uniqid($emulator, $item['fields']['path']), $sdbdata)) {
        $data = $sdbdata[rom::uniqid($emulator, $item['fields']['path'])];
        if (sizeof($include) > 0) {
          foreach (db::instance()->read('fields') as $field) {
            if ($field['readonly']) {
              if ($field['import']) {
                db::instance()->write('metadata', array($field['id'] => $item['fields'][$field['id']]), 'id='.db::instance()->quote($data['id']));
              }
            } else {
              if (in_array($field['id'], $include)) {
                if ($field['export']) {
                  $item['fields'][$field['id']] = $data[$field['id']];
                }
              } else {
                if ($field['export']) {
                  $item['fields'][$field['id']] = '';
                }
              }
            }
          }
        } else {
          foreach (db::instance()->read('fields') as $field) {
            if ($field['readonly']) {
              if ($field['import']) {
                db::instance()->write('metadata', array($field['id'] => $item['fields'][$field['id']]), 'id='.db::instance()->quote($data['id']));
              }
            } else {
              if ($field['export']) {
                $item['fields'][$field['id']] = $data[$field['id']];
              }
            }
          }
        }
        unset($sdbdata[rom::uniqid($emulator, $item['fields']['path'])]);
      }
      array_push($output, $item);
    }
    
    foreach ($sdbdata as $item) {
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
          if ($field['readonly']) {
            if ($field['export']) {
              $data[$field['id']] = $item[$field['id']];
            }
          } else {
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
        }
        $tmp['fields'] = $data;
      } else {
        $data = array();
        foreach (db::instance()->read('fields') as $field) {
          if ($field['readonly']) {
            if ($field['export']) {
              $data[$field['id']] = $item[$field['id']];
            }
          } else {
            if ($field['export']) {
              $data[$field['id']] = $item[$field['id']];
            }
          }
        }
        $tmp['fields'] = $data;
      }
      array_push($output, $tmp);
    }
    return xml::write('gameList', $output, $xml);
  }
  
  public static function sync($emulator, $include = array(), $hash = false)
  {
    $romspath = current(db::instance()->read('config', "id='roms_path'"))['value'];
    $config = db::instance()->read('emulators', 'id='.db::instance()->quote($emulator));
    if (sizeof($config) == 1) {
      $config = current($config);
    } else {
      $config = array();
      $config['whitelist'] = '';
      $config['blacklist'] = '';
    }
    $count = 0;
    
    db::instance()->write('roms', array('sync' => 0), "emulator='".$emulator."'");
    if (file_exists($romspath.DIRECTORY_SEPARATOR.$emulator)) {
      foreach (scandir($romspath.DIRECTORY_SEPARATOR.$emulator) as $item) {
        if (is_file($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
          if ($config['whitelist'] != '') {
            if (strpos($config['whitelist'], pathinfo($item, PATHINFO_EXTENSION)) !== false) {
              if ($config['blacklist'] != '') {
                if (strpos($config['blacklist'], pathinfo($item, PATHINFO_EXTENSION)) === false) {
                  $count += 1;
                  $file = $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item;
                  $data = array('name' => $item, 'emulator' => $emulator, 'size' => filesize($file), 'sync' => 1);
                  if ($hash || filesize($file) <= self::$size) {
                    $data['crc32'] = strtoupper(hash_file('crc32b', $file));
                    $data['md5'] = strtoupper(hash_file('md5', $file));
                    $data['sha1'] = strtoupper(hash_file('sha1', $file));
                  }
                  rom::config(rom::uniqid($emulator, $item), $data);
                }
              } else {
                $count += 1;
                $file = $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item;
                $data = array('name' => $item, 'emulator' => $emulator, 'size' => filesize($file), 'sync' => 1);
                if ($hash || filesize($file) <= self::$size) {
                  $data['crc32'] = strtoupper(hash_file('crc32b', $file));
                  $data['md5'] = strtoupper(hash_file('md5', $file));
                  $data['sha1'] = strtoupper(hash_file('sha1', $file));
                }
                rom::config(rom::uniqid($emulator, $item), $data);
              }
            }
          } else {
            $count += 1;
            $file = $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item;
            $data = array('name' => $item, 'emulator' => $emulator, 'size' => filesize($file), 'sync' => 1);
            if ($hash || filesize($file) <= self::$size) {
              $data['crc32'] = strtoupper(hash_file('crc32b', $file));
              $data['md5'] = strtoupper(hash_file('md5', $file));
              $data['sha1'] = strtoupper(hash_file('sha1', $file));
            }
            rom::config(rom::uniqid($emulator, $item), $data);
          }
        }
        if (is_dir($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
          if ($item != '.' && $item != '..') {
            if ($config['blacklist'] != '') {
              if (strpos($config['blacklist'], 'folder') === false && strpos($config['blacklist'], $item) === false) {
                $count += 1;
                $data = array('name' => $item, 'emulator' => $emulator, 'size' => 0, 'sync' => 1);
                rom::config(rom::uniqid($emulator, $item), $data);
              }
            } else {
              $count += 1;
              $data = array('name' => $item, 'emulator' => $emulator, 'size' => 0, 'sync' => 1);
              rom::config(rom::uniqid($emulator, $item), $data);
            }
          }
        }
      }
      
      db::instance()->delete('roms', "emulator='".$emulator."' and sync = 0");
      
      $xml = current(db::instance()->read('config', "id='roms_path'"))['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
      if (!file_exists($xml)) {
        $xml = current(db::instance()->read('config', "id='metadata_path'"))['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
      }
      
      $xmldata = xml::read($xml);
      $fields = array();
      if (sizeof($include) > 0) {
        foreach (db::instance()->read('fields') as $field) {
          if (in_array($field['id'], $include) && $field['import']) {
            array_push($fields, $field['id']);
          }
        }
      } else {
        foreach (db::instance()->read('fields') as $field) {
          if ($field['import']) {
            array_push($fields, $field['id']);
          }
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
    $romspath = current(db::instance()->read('config', "id='roms_path'"))['value'];
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