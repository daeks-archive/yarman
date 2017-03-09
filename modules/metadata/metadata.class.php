<?php

class metadata
{

  public static $gamelist = 'gamelist.xml';

  public static function start($tab, $emulator)
  {
    // RENDER RIGHT MENU
    $data = '<div class="row">';
    $data .= '<div class="col-sm-12">';
    
    // RENDER RIGHT HEADER
    $data .= '<div class="row">';
    $data .= '<div class="col-sm-8">';
    $data .= '<ul class="nav nav-tabs">';
    $data .= '<li ';
    if ($tab == 'metadata') {
      $data .= 'class="active"';
    }
    $data .= '><a href="#" data-toggle="async" data-query="'.DIALOG.'?action=render&tab=metadata" data-target="#panel-right">Metadata</a></li>';
    $data .= '<li ';
    if ($tab == 'media') {
      $data .= 'class="active"';
    }
    $data .= '><a href="#" data-toggle="async" data-query="'.DIALOG.'?action=render&tab=media" data-target="#panel-right">Media</a></li>';
    $data .= '</ul>';
    $data .= '</div>';
    $data .= '<div class="col-sm-4">';
    $data .= '<div class="btn-toolbar" role="toolbar">';
    $data .= '<div class="btn-group btn-group-sm">';
    $data .= '<button class="btn btn-success" data-validate="form" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmsave" data-target="#modal" disabled><em class="fa fa-save"></em> Save</button>';
    $data .= '<button class="btn btn-default" data-validate="form" type="submit" data-toggle="modal" href="'.DIALOG.'?action=syncrom" data-target="#modal"><em class="fa fa-refresh"></em></button>';
    $data .= '</div>';
    $data .= '<div class="btn-group btn-group-sm pull-right">';
    $data .= '<button class="btn btn-default';
    $data .= '" type="submit" id="metadata-clean" name="metadata-clean" data-toggle="modal" href="'.DIALOG.'?action=clean" data-target="#modal">Clean</button>';
    $data .= '<button class="btn btn-danger" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmdelete" data-target="#modal"><em class="fa fa-trash"></em></button>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '<br>';
    // END RENDER RIGHT MENU
    return $data;
  }
  
  public static function end()
  {
    $data = '</div>';
    $data .= '</div>';
    // END RNDER RIGHT
    return $data;
  }

  public static function render($container, $emulator, $id)
  {
    $rom = rom::read($id);
    $fields = db::instance()->read('fields');
    
    $fieldset = array();

    foreach ($fields as $field) {
      if (isset($field['grid']) && isset($field['container'])) {
        if ($field['container'] == $container) {
          $parts = explode(' ', $field['grid']);
          if (sizeof($parts) == 5) {
            if (array_key_exists($parts[0], $fieldset)) {
              if ($parts[3] == 'left') {
                while (isset($fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[1]][$parts[4]])) {
                  $parts[4] += 1;
                };
                $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[1]][$parts[4]] = $field;
              } elseif ($parts[3] == 'right') {
                while (isset($fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[2]][$parts[4]])) {
                  $parts[4] += 1;
                };
                $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[2]][$parts[4]] = $field;
              }
            } else {
              if ($parts[1] == '12' && $parts[2] == '0') {
                $fieldset[$parts[0]] = array($parts[3].'-col-sm-'.$parts[1] => array($parts[4] => $field));
              } else {
                if ($parts[3] == 'left') {
                  $fieldset[$parts[0]] = array('left-col-sm-'.$parts[1] => array($parts[4] => $field), 'right-col-sm-'.$parts[2] => array());
                } elseif ($parts[3] == 'right') {
                  $fieldset[$parts[0]] = array('left-col-sm-'.$parts[1] => array(), 'right-col-sm-'.$parts[2] => array($parts[4] => $field));
                }
              }
            }
          }
        }
      }
      if (isset($field['type']) && ($field['type'] == 'key' || $field['type'] == 'hidden')) {
        if (array_key_exists('0', $fieldset)) {
          array_push($fieldset['0']['left-col-sm-12'], $field);
        } else {
          $fieldset['0'] = array ('left-col-sm-12' => array($field));
        }
      }
    }
    
    $data = self::start($container, $emulator);
    
    $data .= '<div class="row">';
    $data .= '<div class="col-sm-12">';
    
    $data .= '<form id="rom-data" name="rom-data" role="form" class="scrollbar" data-validate="form" data-toggle="post" data-query="'.CONTROLLER.'?action=save&id=metadata" style="overflow-y: auto !important; overflow-x: hidden !important;"><fieldset>';
    $tabindex = 1;
    ksort($fieldset);
    foreach ($fieldset as $key => $row) {
      $data .= '<div class="row">';
      foreach ($row as $key => $column) {
        $data .= '<div class="'.str_replace(array('left-', 'right-'), array('',''), $key).'">';
        ksort($column);
        foreach ($column as $key => $field) {
          $field['index'] = $tabindex;
          $value = '';
          if (isset($field['guid'])) {
            if (isset($rom[$field['guid']])) {
              $value = $rom[$field['guid']];
              if ($value == '' && $field['type'] == 'key') {
                $value = $id;
              }
            } else {
              if ($field['type'] == 'key') {
                $value = $id;
              }
            }
          } else {
            if (isset($rom[$field['id']])) {
              $value = $rom[$field['id']];
            }
          }
          $data .= form::getField($fields, $field['id'], $value);
        }
        $data .= '</div>';
      }
      $data .= '</div>';
      $tabindex += 1;
    }
    $data .= '</fieldset></form>';
    
    $data .= '</div>';
    $data .= '</div>';

    $data .= self::end();
    
    return $data;
  }
  
  public static function clean($emulator, $quick = false)
  {
    $output = array('rom' => array(), 'gamelist' => array(), 'metadata' => array(), 'media' => array());
    $media = array();
    $romspath = current(db::instance()->read('config', "id='roms_path'"))['value'];
    
    foreach (db::instance()->read('roms', 'emulator='.db::instance()->quote($emulator)) as $data) {
      if (!file_exists($romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$data['name'])) {
        array_push($output['rom'], $data['id']);
      }
    }
    
    foreach (db::instance()->read('metadata', 'emulator='.db::instance()->quote($emulator)) as $data) {
      if ($data['path'] == '') {
        array_push($output['metadata'], $data['id']);
        if ($quick) {
          break;
        }
      } else {
        if (strpos($data['path'], '.') === 0) {
          $data['path'] = $romspath.DIRECTORY_SEPARATOR.$emulator.substr($data['path'], 1);
          if (!file_exists($data['path'])) {
            array_push($output['metadata'], $data['id']);
            if ($quick) {
              break;
            }
          }
        } else {
          if (!file_exists($data['path'])) {
            array_push($output['metadata'], $data['id']);
            if ($quick) {
              break;
            }
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
              array_push($output['media'], $field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item);
              if ($quick) {
                break;
              }
            }
          }
        }
      }
    }
    
    if (!$quick) {
      $xml = $romspath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
      if (!file_exists($xml)) {
        $metadatapath = current(db::instance()->read('config', "id='metadata_path'"))['value'];
        $xml = $metadatapath.DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.self::$gamelist;
      }
      
      $xmldata = xml::read($xml);    
      foreach ($xmldata as $item) {
        $rom = rom::config(rom::uniqid($emulator, $item['fields']['path']));
        if (sizeof($rom) == 0) {
          array_push($output['gamelist'], rom::uniqid($emulator, $item['fields']['path']));
          if ($quick) {
            break;
          }
        }
      }
    }
    
    return $output;
  }
}

?>