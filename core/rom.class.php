<?php
  
class rom
{
  public static function uniqid($emulator, $id)
  {
    return md5($emulator.pathinfo($id, PATHINFO_BASENAME));
  }
  
  public static function config($id)
  {
    $config = db::instance()->read('roms', 'id='.db::instance()->quote($id));
    if (sizeof($config) == 1) {
      return $config[0];
    } else {
      return array('id' => $id, 'name' => $id);
    }
  }
  
  public static function create($id, $data)
  {
    if (sizeof(db::instance()->read('roms', 'id='.db::instance()->quote($id))) == 0) {
      $data['id'] = $id;
      return db::instance()->write('roms', $data);
    }
  }
   
  public static function read($id)
  {
    $output = db::instance()->read('metadata', 'id='.db::instance()->quote($id));
    if (sizeof($output) > 0) {
      return $output[0];
    } else {
      return array();
    }
  }
  
  public static function write($id, $data)
  {
    if (!isset($data['path']) || $data['path'] == '') {
      $rom = self::config($id);
      $data['path'] = db::instance()->read('config', 'id='.db::instance()->quote('roms_path'))[0]['value'].DIRECTORY_SEPARATOR.$rom['emulator'].DIRECTORY_SEPARATOR.$rom['name'];
    }
    foreach ($data as $key => $value) {
      $field = db::instance()->read('fields', 'id='.db::instance()->quote($key));
      if (sizeof($field) > 0) {
        if ($field[0]['type'] == 'date') {
          if (trim($value) != '') {
            $value = date_format(date_create($value), 'Ymd\THis');
          } else {
            $value = '00000000T000000';
          }
        }
      }
      $data[$key] = $value;
    }
    return db::instance()->write('metadata', $data, 'id='.db::instance()->quote($id));
  }
  
  public static function delete($id)
  {
    $rom = self::config($id);
    $item = db::instance()->read('config', 'id='.db::instance()->quote('roms_path'))[0]['value'].DIRECTORY_SEPARATOR.$rom['emulator'].DIRECTORY_SEPARATOR.$rom['name'];
    if (is_file($item)) {
      unlink($item);
    }
    
    $data = db::instance()->read('metadata', 'id='.db::instance()->quote($id));
    if (sizeof($data) > 0) {
      foreach ($data[0] as $key => $value) {
        $field = db::instance()->read('fields', 'id='.db::instance()->quote($key))[0];
        if ($field['type'] == 'upload') {
          if ($value != '') {
            unlink($value);
          }
        }
      }
      db::instance()->delete('roms', 'id='.db::instance()->quote($id));
      db::instance()->delete('metadata', 'id='.db::instance()->quote($id));
    }
  }
  
  public static function sync($emulator, $id)
  {
    $xml = db::instance()->read('config', "id='roms_path'")[0]['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.emulator::$gamelist;
    if (!file_exists($xml)) {
      $xml = db::instance()->read('config', "id='metadata_path'")[0]['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.emulator::$gamelist;
    }
    
    $xmldata = xml::read($xml);
    $fields = array();
    foreach (db::instance()->read('fields') as $field) {
      if ($field['export']) {
        array_push($fields, $field['id']);
      }
    }
    
    foreach ($xmldata as $item) {
      if (rom::uniqid($emulator, $item['fields']['path']) == $id) {
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
        break;
      }
    }
  }
}
  
?>