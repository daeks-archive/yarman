<?php

  class metadata {
  
    public static function start($tab) {
      // RENDER RIGHT MENU
      $data = '<div class="row">';
      $data .= '<div class="col-sm-12">';

      // RENDER RIGHT HEADER
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-8">';
      $data .= '<ul class="nav nav-tabs">';
      $data .= '<li ';
      if($tab == 'metadata') {
        $data .= 'class="active"';
      }
      $data .= '><a href="#" data-toggle="async" data-query="'.DIALOG.'?action=render&tab=metadata" data-target="#panel-right">Metadata</a></li>';
      $data .= '<li ';
      if($tab == 'media') {
        $data .= 'class="active"';
      }
      $data .= '><a href="#" data-toggle="async" data-query="'.DIALOG.'?action=render&tab=media" data-target="#panel-right">Media</a></li>';
      $data .= '</ul>';
      $data .= '</div>';
      $data .= '<div class="col-sm-4">';
      $data .= '<div class="btn-toolbar btn-group-sm" role="toolbar">';
      $data .= '<button class="btn btn-success" data-validate="form" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmsave" data-target="#modal" disabled><em class="fa fa-save"></em> Save</button>';
      $data .= '<div class="btn-group btn-group-sm pull-right">';
      //$data .= '<button class="btn btn-default disabled" type="button">Add Image</button>';
      $data .= '<button class="btn btn-danger" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmdelete" data-target="#modal"><em class="fa fa-trash"></em> Delete</button>';
      $data .= '</div>';
      $data .= '</div>';
      $data .= '</div>';
      $data .= '</div>';
      $data .= '<br>';
      // END RENDER RIGHT MENU
      return $data;
    }
    
    public static function end() {
      $data = '</div>';
      $data .= '</div>';
      // END RNDER RIGHT
      return $data;
    }
  
    public static function render($container, $emulator, $id) {
      $rom = rom::readMetadata($emulator, $id);
      $fields = db::read('fields');
      
      $fieldset = array(); 

      foreach ($fields as $field) {
        if(isset($field['grid']) && isset($field['container'])) {
          if($field['container'] == $container) {
            $parts = explode(' ', $field['grid']);
            if(sizeof($parts) == 5) {
              if(array_key_exists($parts[0], $fieldset)) {
                if($parts[3] == 'left') {
                  $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[1]][$parts[4]] = $field;
                } else if ($parts[3] == 'right') {
                  $fieldset[$parts[0]][$parts[3].'-col-sm-'.$parts[2]][$parts[4]] = $field;
                }
              } else {
                if($parts[1] == '12' && $parts[2] == '0') {
                  $fieldset[$parts[0]] = array($parts[3].'-col-sm-'.$parts[1] => array($parts[4] => $field));
                } else {
                  if($parts[3] == 'left') {
                    $fieldset[$parts[0]] = array('left-col-sm-'.$parts[1] => array($parts[4] => $field), 'right-col-sm-'.$parts[2] => array());
                  } else if ($parts[3] == 'right') {
                    $fieldset[$parts[0]] = array('left-col-sm-'.$parts[1] => array(), 'right-col-sm-'.$parts[2] => array($parts[4] => $field));
                  }
                }
              }
            }
          }
        }
        if(isset($field['type']) && $field['type'] == 'hidden') {
          if(array_key_exists('0', $fieldset)) {
            array_push($fieldset['0']['left-col-sm-12'], $field);
          } else {
            $fieldset['0'] = array ('left-col-sm-12' => array($field));
          }
        }
      }
      
      $data = self::start($container);
      
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-12">';
      
      $data .= '<form id="rom-data" name="rom-data" role="form" class="scrollbar" data-validate="form" data-toggle="post" data-query="'.CONTROLLER.'?action=save" style="overflow-y: auto !important; overflow-x: hidden !important;"><fieldset>';
      foreach($fieldset as $key=>$row) {
        $data .= '<div class="row">';
        foreach($row as $key=>$column) {
          $data .= '<div class="'.str_replace(array('left-', 'right-'), array('',''), $key).'">';
          foreach($column as $key=>$field) {
            $data .= form::getField($fields, $field['id'], (isset($rom['fields'][$field['guid']])?$rom['fields'][$field['guid']]:''), $emulator);
          }
          $data .= '</div>';
        }
        $data .= '</div>';
      }
      $data .= '</fieldset></form>';
      
      $data .= '</div>';
      $data .= '</div>';

      $data .= self::end();
      
      return $data;
    }
  
  }

?>