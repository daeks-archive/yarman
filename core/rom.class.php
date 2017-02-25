<?php
  
class rom
{
  public static function parse($id)
  {
    return pathinfo($id, PATHINFO_BASENAME);
  }
  
  public static function readAll($emulator)
  {
    $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
    $output = array();
    foreach ($xml as $item) {
      $item['id'] = pathinfo($item['fields']['path'], PATHINFO_BASENAME);
      array_push($output, $item);
    }
    return $output;
  }

  public static function read($emulator, $id)
  {
    $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
    foreach ($xml as $item) {
      if (pathinfo($item['fields']['path'], PATHINFO_BASENAME) == $id) {
        $item['id'] = pathinfo($item['fields']['path'], PATHINFO_BASENAME);
        return $item;
      }
    }
  }
  
  public static function write($emulator, $id, $data)
  {
    copy(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml', db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml.bak');
    $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
    
    $output = array();
    $update = false;
    foreach ($xml as $item) {
      $update = false;
      if (pathinfo($item['fields']['path'], PATHINFO_BASENAME) == $id) {
        foreach ($data as $key => $value) {
          $field = db::read('fields', $key, 'type');
          if ($field == 'date') {
            if (trim($value) != '') {
              $value = date_format(date_create($value), 'Ymd\THis');
            } else {
              $value = '00000000T000000';
            }
          }
          if ($field != 'hidden') {
            if (isset($item['fields'][$key])) {
              $item['fields'][$key] = trim($value);
            } else {
              if (trim($value) != '') {
                $item['fields'][$key] = trim($value);
              }
            }
          }
        }
        $update = true;
      }
      array_push($output, $item);
    }
    
    if (!$update) {
      $tmp = array('type' => '', 'attributes' => array(), 'fields' => array());
      $fields = db::read('fields');
    
      foreach ($fields as $field) {
        $value = '';
        if ($field['type'] == 'hidden') {
          $tmp['fields'][$field['guid']] = trim(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$id);
          if (is_file(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$id)) {
            $tmp['type'] = 'game';
          } else {
            $tmp['type'] = 'folder';
          }
        } else {
          if (isset($data[$field['id']])) {
            $value = $data[$field['id']];
          }
          if ($field['type'] == 'date') {
            if (trim($value) != '') {
              $value = date_format(date_create($value), 'Ymd\THis');
            } else {
              $value = '00000000T000000';
            }
          }
          if (!isset($tmp['fields'][$field['id']])) {
            $tmp['fields'][$field['id']] = trim($value);
          }
        }
      }
      array_push($output, $tmp);
    }
    return xml::write('gameList', $output, db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
  }
  
  public static function delete($emulator, $id)
  {
    unlink(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$id);
    
    copy(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml', db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml.bak');
    $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
    
    $output = array();
    foreach ($xml as $item) {
      if (pathinfo($item['fields']['path'], PATHINFO_BASENAME) == $id) {
        foreach ($item['fields'] as $key => $value) {
          $field = db::read('fields', $key, 'type');
          if ($field == 'upload') {
            if ($value != '') {
              unlink($value);
            }
          }
        }
      } else {
        array_push($output, $item);
      }
    }
    return xml::write('gameList', $output, db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
  }
  
  public static function clean($emulator, $ids)
  {
    copy(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml', db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml.bak');
    $xml = xml::read(db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
    
    $output = array();
    foreach ($xml as $item) {
      if (in_array(pathinfo($item['fields']['path'], PATHINFO_BASENAME), $ids)) {
        foreach ($item['fields'] as $key => $value) {
          $field = db::read('fields', $key, 'type');
          if ($field == 'upload') {
            if ($value != '') {
              if (file_exists($value)) {
                unlink($value);
              }
            }
          }
        }
      } else {
        array_push($output, $item);
      }
    }
    return xml::write('gameList', $output, db::read('config', 'metadata_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.'gamelist.xml');
  }
}
  
?>