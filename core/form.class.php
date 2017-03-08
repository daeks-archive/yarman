<?php
  
class form
{
  public static function getField($config, $id, $value = '')
  {
    foreach ($config as $obj) {
      if ($obj['id'] == $id) {
        $data = '';
        switch ($obj['type']) {
          case 'key':
            $data = self::getHidden($obj, $value);
            break;
          case 'hidden':
            $data = self::getHidden($obj, $value);
            break;
          case 'string':
            $data = self::getString($obj, $value);
            break;
          case 'integer':
            $data = self::getInteger($obj, $value);
            break;
          case 'double':
            $data = self::getDouble($obj, $value);
            break;
          case 'text':
            $data = self::getText($obj, $value);
            break;
          case 'date':
            $data = self::getDate($obj, $value);
            break;
          case 'thumbnail':
            $data = self::getImage($obj, $value);
            break;
          case 'marquee':
            $data = self::getImage($obj, $value);
            break;
          case 'image':
            $data = self::getImage($obj, $value);
            break;
          case 'video':
            $data = self::getVideo($obj, $value);
            break;
          case 'upload':
            $data = self::getUpload($obj, $value);
            break;
          case 'boolean':
            $data = self::getBoolean($obj, $value);
            break;
          default:
            break;
        }
        return $data;
        break;
      }
    }
  }
  
  public static function getHidden($obj, $value = '')
  {
    $data = '<input type="hidden" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"/>';
    return $data;
  }
  
  public static function getString($obj, $value = '')
  {
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
    if (isset($obj['validator']) && $obj['validator'] != '') {
      $data .= ' data-fv '.$obj['validator'];
    }
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    if (isset($obj['maxlength']) && is_int($obj['maxlength'])) {
      $data .= ' maxlength="'.$obj['maxlength'].'"';
    }
    $data .= '/>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getInteger($obj, $value = '')
  {
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
    if (isset($obj['validator']) && $obj['validator'] != '') {
      $data .= ' data-fv '.$obj['validator'];
    }
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    if (isset($obj['maxlength']) && is_int($obj['maxlength'])) {
      $data .= ' maxlength="'.$obj['maxlength'].'"';
    }
    $data .= '/>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getDouble($obj, $value = '')
  {
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
    if (isset($obj['validator']) && $obj['validator'] != '') {
      $data .= ' data-fv '.$obj['validator'];
    }
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    if (isset($obj['maxlength']) && is_int($obj['maxlength'])) {
      $data .= ' maxlength="'.$obj['maxlength'].'"';
    }
    $data .= '/>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getText($obj, $value = '')
  {
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<textarea class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" rows="'.$obj['rowcount'].'"';
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    $data .= '>';
    $data .= $value;
    $data .= '</textarea>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getDate($obj, $value = '')
  {
    $parsed = '';
    if ($value != '') {
      $date = date_parse_from_format('YmdTHiS', $value);
      if ($date['year'] > 0) {
        $parsed = $date['year'];
        if ($date['month'] > 0) {
          $parsed = $parsed.'/'.sprintf("%02d", $date['month']);
        }
        if ($date['day'] > 0) {
          $parsed = $parsed.'/'.sprintf("%02d", $date['day']);
        }
        if ($date['hour'] > 0) {
          $parsed = $parsed.' '.sprintf("%02d", $date['hour']);
        }
        if ($date['minute'] > 0) {
          $parsed = $parsed.':'.sprintf("%02d", $date['minute']);
        }
        if ($date['second'] > 0) {
          $parsed = $parsed.':'.sprintf("%02d", $date['second']);
        }
        if (strlen($parsed) == 4) {
          $parsed = $parsed.'/01/01';
        }
      } else {
        $parsed = '';
      }
    }
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<div class="input-group">';
    $data .= '<span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>';
    $data .= '<input type="text" class="form-control" data-provider="datepicker" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$parsed.'"';
    if (isset($obj['validator']) && $obj['validator'] != '') {
      $data .= ' data-fv '.$obj['validator'];
    }
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    if (isset($obj['maxlength']) && is_int($obj['maxlength'])) {
      $data .= ' maxlength="'.$obj['maxlength'].'"';
    }
    $data .= '/>';
    $data .= '</div>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getBoolean($obj, $value = '')
  {
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<select type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'"';
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    $data .= '>"';
    $data .= '<option value=""></option>';
    $data .= '<option';
    if ($value == 'true') {
      $data .= ' selected';
    }
    $data .= ' value="true">True</option>';
    $data .= '<option';
    if ($value == 'false') {
      $data .= ' selected';
    }
    $data .= ' value="false">False</option>';
    $data .= '</select>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getImage($obj, $id)
  {
    $data = '';
    if ($id != '') {
      $rom = rom::read($id);
      if (isset($rom[$obj['type']]) && $rom[$obj['type']] != '') {
        if (isset($obj['name']) && $obj['name'] != '') {
          $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
        }
        $data .= '<div class="thumbnail">';
        $data .= '<img style="max-height: 245px" src="/core/proxy.php?action=render&type='.$obj['type'].'&id='.$id.'">';
        $data .= '</div>';
      }
    }
    return $data;
  }
  
  public static function getVideo($obj, $id)
  {
    $data = '';
    if ($id != '') {
      $rom = rom::read($id);
      if (isset($rom[$obj['type']]) && $rom[$obj['type']] != '') {
        if (isset($obj['name']) && $obj['name'] != '') {
          $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
        }
        $data .= '<div class="thumbnail">';
        $data .= '<video style="max-height: 245px" controls><source src="/core/proxy.php?action=render&type='.$obj['type'].'&id='.$id.'">No HTML5 Support</video>';
        $data .= '</div>';
      }
    }
    return $data;
  }
  
  public static function getUpload($obj, $value = '')
  {
    $data = '<div class="form-group">';
    if (isset($obj['name']) && $obj['name'] != '') {
      $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
    }
    $data .= '<div class="input-group">';
    $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
    if (isset($obj['validator']) && $obj['validator'] != '') {
      $data .= ' data-fv '.$obj['validator'];
    }
    if (isset($obj['readonly']) && $obj['readonly'] == true) {
      $data .= ' disabled';
    }
    if (isset($obj['index'])) {
      $data .= ' tabindex="'.$obj['index'].'"';
    }
    if (isset($obj['maxlength']) && is_int($obj['maxlength'])) {
      $data .= ' maxlength="'.$obj['maxlength'].'"';
    }
    $data .= '/>';
    $data .= '<label class="input-group-btn"><span class="btn btn-default btn-file"><i class="fa fa-file-image-o fa-fw"></i>';
    $data .= '<input type="file" id="object" name="object[]" data-toggle="proxy" data-query="/core/proxy.php?action=upload&type='.$obj['id'].'" data-key="#id" data-target="#'.$obj['id'].'" accept="'.(isset($obj['whitelist'])?str_replace(' ', ',', $obj['whitelist']):'').'" style="display: none;">';
    $data .= '</span></label>';
    $data .= '</div>';
    $data .= '</div>';
    return $data;
  }
  
  public static function getTable($array, $options = array())
  {
    $data = '';
    if (sizeof($array) > 0) {
      $data .= '<table class="table table-hover">';
      $data .= '<thead><tr>';
      foreach (current($array) as $key => $item) {
        iF (!isset($options[$key]['hidden'])) {
          if (isset($options[$key]['width']) && $options[$key]['width'] != '') {
            $data .= '<th width="'.$options[$key]['width'].';">'.strtoupper($key).'</th>';
          } else {
            $data .= '<th>'.strtoupper($key).'</th>';
          }
        }
      }
      $data .= '</tr></thead><tbody>';
      foreach ($array as $item) {
        $data .= '<tr id="'.$item['id'].'" name= "'.$item['id'].'">';
        foreach ($item as $key => $value) {
          if (array_key_exists($key, $options)) {
            if (!isset($options[$key]['hidden'])) {
              $data .= '<td><div class="form-group" style="margin-bottom: 0px !important;">';
              $data .= '<input type="text" class="form-control" id="'.$key.'" name="'.$key.'" value="'.$value.'"';
              if (isset($options[$key]['validator']) && $options[$key]['validator'] != '') {
                $data .= ' data-fv '.$options[$key]['validator'];
              }
              if (isset($options[$key]['readonly']) && $options[$key]['readonly'] == true) {
                $data .= ' disabled';
              }
              if (isset($options[$key]['index'])) {
                $data .= ' tabindex="'.$options[$key]['index'].'"';
              }
              if (isset($options[$key]['maxlength']) && is_int($options[$key]['maxlength'])) {
                $data .= ' maxlength="'.$options[$key]['maxlength'].'"';
              }
              $data .= '/>';
              $data .= '</div></td>';
            }
          } else {
            $data .= '<td><div class="form-group" style="margin-bottom: 0px !important;">';
            $data .= '<input type="text" class="form-control" id="'.$key.'" name="'.$key.'" value="'.$value.'">';
            $data .= '</div></td>';
          }
        };
        $data .= '</tr>';
      }
      $data .= '</tbody></table>';
    }
    return $data;
  }
}
  
?>