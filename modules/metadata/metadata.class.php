<?php

class metadata
{
  public static function start($tab, $emulator)
  {
    $orphaned = metadata::findOrphaned($emulator);
  
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
    $data .= '<div class="btn-toolbar btn-group-sm" role="toolbar">';
    $data .= '<button class="btn btn-success" data-validate="form" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmsave" data-target="#modal" disabled><em class="fa fa-save"></em> Save</button>';
    $data .= '<div class="btn-group btn-group-sm pull-right">';
    $data .= '<button class="btn btn-default';
    if ((sizeof($orphaned['metadata']) + sizeof($orphaned['media'])) == 0) {
      $data .= ' disabled';
    }
    $data .= '" type="submit" id="metadata-clean" name="metadata-clean" data-toggle="modal" href="'.DIALOG.'?action=clean" data-target="#modal">Clean Orphaned</button>';
    $data .= '<button class="btn btn-danger" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmdelete" data-target="#modal"><em class="fa fa-trash"></em> Delete</button>';
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
    $rom = rom::read($emulator, $id);
    $fields = db::read('fields');
    
    $fieldset = array();

    foreach ($fields as $field) {
      if (isset($field['grid']) && isset($field['container'])) {
        if ($field['container'] == $container) {
          $parts = explode(' ', $field['grid']);
          if (sizeof($parts) == 5) {
            if (array_key_exists($parts[0], $fieldset)) {
              if ($parts[3] == 'left') {
                $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[1]][$parts[4]] = $field;
              } elseif ($parts[3] == 'right') {
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
      if (isset($field['type']) && $field['type'] == 'hidden') {
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
    
    $data .= '<form id="rom-data" name="rom-data" role="form" class="scrollbar" data-validate="form" data-toggle="post" data-query="'.CONTROLLER.'?action=save" style="overflow-y: auto !important; overflow-x: hidden !important;"><fieldset>';
    $tabindex = 1;
    foreach ($fieldset as $key => $row) {
      $data .= '<div class="row">';
      foreach ($row as $key => $column) {
        $data .= '<div class="'.str_replace(array('left-', 'right-'), array('',''), $key).'">';
        foreach ($column as $key => $field) {
          $field['index'] = $tabindex;
          $value = '';
          if ($field['type'] == 'hidden' && isset($field['guid'])) {
            if (isset($rom['fields'][$field['guid']])) {
              $value = $rom['fields'][$field['guid']];
              if ($value == '') {
                $value = $id;
              }
            } else {
              $value = $id;
            }
          } else {
            if (isset($rom['fields'][$field['id']])) {
              $value = $rom['fields'][$field['id']];
            }
          }
          $data .= form::getField($fields, $field['id'], $value, $emulator);
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
  
  public static function findOrphaned($emulator)
  {
    $romdata = rom::readAll($emulator);
    $output = array('metadata' => array(), 'media' => array());
    $media = array();
    
    foreach ($romdata as $rom) {
      if (!file_exists(db::read('config', 'roms_path').DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.rom::parse($rom['id']))) {
        array_push($output['metadata'], $rom['id']);
      }
      if (isset($rom['fields']['image']) && $rom['fields']['image'] != '') {
        array_push($media, pathinfo($rom['fields']['image'], PATHINFO_BASENAME));
      }
      if (isset($rom['fields']['video']) && $rom['fields']['video'] != '') {
        array_push($media, pathinfo($rom['fields']['video'], PATHINFO_BASENAME));
      }
      if (isset($rom['fields']['marquee']) && $rom['fields']['marquee'] != '') {
        array_push($media, pathinfo($rom['fields']['marquee'], PATHINFO_BASENAME));
      }
      if (isset($rom['fields']['thumbnail']) && $rom['fields']['thumbnail'] != '') {
        array_push($media, pathinfo($rom['fields']['thumbnail'], PATHINFO_BASENAME));
      }
    }
    
    $fields = db::read('fields');
    
    foreach ($fields as $field) {
      if ($field['type'] == 'upload') {
        foreach (scandir($field['path'].DIRECTORY_SEPARATOR.$emulator) as $item) {
          if (is_file($field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item)) {
            if (!in_array($item, $media) && !in_array($field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item, $output['media'])) {
              array_push($output['media'], $field['path'].DIRECTORY_SEPARATOR.$emulator.DIRECTORY_SEPARATOR.$item);
            }
          }
        }
      }
    }
    return $output;
  }
}

?>