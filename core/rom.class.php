<?php
  
class rom
{
  public static function uniqid($emulator, $id)
  {
    return md5($emulator.pathinfo($id, PATHINFO_BASENAME));
  }
  
  public static function config($id, $data = array())
  {
    if (sizeof($data) > 0) {
      $data['id'] = $id;
      db::instance()->write('roms', $data, 'id='.db::instance()->quote($id));
      return $data;
    } else {
      $config = db::instance()->read('roms', 'id='.db::instance()->quote($id));
      if (sizeof($config) == 1) {
        return current($config);
      } else {
        return array('id' => $id, 'name' => $id);
      }
    }
  }
   
  public static function read($id)
  {
    $output = db::instance()->read('metadata', 'id='.db::instance()->quote($id));
    if (sizeof($output) == 1) {
      return current($output);
    } else {
      return array();
    }
  }
  
  public static function write($id, $data)
  {
    if (!isset($data['path']) || $data['path'] == '') {
      $self = self::read($id);
      if (!isset($self['path']) || $self['path'] == '') {
        $rom = self::config($id);
        $data['path'] = current(db::instance()->read('config', 'id='.db::instance()->quote('roms_path')))['value'].DIRECTORY_SEPARATOR.$rom['emulator'].DIRECTORY_SEPARATOR.$rom['name'];
      }
    }
    foreach ($data as $key => $value) {
      $field = db::instance()->read('fields', 'id='.db::instance()->quote($key));
      if (sizeof($field) == 1) {
        if (current($field)['type'] == 'date') {
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
    $item = current(db::instance()->read('config', 'id='.db::instance()->quote('roms_path')))['value'].DIRECTORY_SEPARATOR.$rom['emulator'].DIRECTORY_SEPARATOR.$rom['name'];
    if (is_file($item)) {
      unlink($item);
    }
    
    $data = db::instance()->read('metadata', 'id='.db::instance()->quote($id));
    if (sizeof($data) == 1) {
      foreach (current($data) as $key => $value) {
        $field = current(db::instance()->read('fields', 'id='.db::instance()->quote($key)));
        if ($field['type'] == 'upload') {
          if ($value != '') {
            if (file_exists($value)) {
              unlink($value);
            }
          }
        }
      }
      db::instance()->delete('roms', 'id='.db::instance()->quote($id));
      db::instance()->delete('metadata', 'id='.db::instance()->quote($id));
    }
  }
  
  public static function sync($emulator, $id)
  {
    $xml = current(db::instance()->read('config', "id='roms_path'"))['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.emulator::$gamelist;
    if (!file_exists($xml)) {
      $xml = current(db::instance()->read('config', "id='metadata_path'"))['value'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.emulator::$gamelist;
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