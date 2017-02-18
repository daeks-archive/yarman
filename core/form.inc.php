<?php
    
  class form {

    public static function getField($config, $id, $value = '', $system = '') {
      foreach($config as $obj) {
        if($obj['id'] == $id) {
          $data = '';
          switch ($obj['type']) {
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
            case 'image':
              $data = self::getImage($obj, $system, $value);
            break;
            case 'boolean':
              $data = self::getBoolean($obj, $value);
            break;
            default;
            break;      
          }
          return $data;
          break;
        }
      }
    }
    
    public static function getString($obj, $value = '') {
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
      if(isset($obj['validator']) && $obj['validator'] != '') {
        $data .= ' '.$obj['validator'];
      }
      if(isset($obj['maxlength']) && is_int($obj['maxlength'])) {
        $data .= ' maxlength="'.$obj['maxlength'].'"';
      }
      $data .= '/>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getInteger($obj, $value = '') {
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
      if(isset($obj['validator']) && $obj['validator'] != '') {
        $data .= ' '.$obj['validator'];
      }
      if(isset($obj['maxlength']) && is_int($obj['maxlength'])) {
        $data .= ' maxlength="'.$obj['maxlength'].'"';
      }
      $data .= '/>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getDouble($obj, $value = '') {
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
      if(isset($obj['validator']) && $obj['validator'] != '') {
        $data .= ' '.$obj['validator'];
      }
      if(isset($obj['maxlength']) && is_int($obj['maxlength'])) {
        $data .= ' maxlength="'.$obj['maxlength'].'"';
      }
      $data .= '/>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getText($obj, $value = '') {
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<textarea class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" rows="'.$obj['rowcount'].'">';
      $data .= $value;
      $data .= '</textarea>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getDate($obj, $value = '') {
      if($value != '') {
        $date = date_parse_from_format('YmdTHiS', $value);
        if($date['year'] > 0) {
          $value = $date['year'];
          if($date['month'] > 0) {
            $value = $value.'-'.sprintf("%02d", $date['month']);
          }
          if($date['day'] > 0) {
            $value = $value.'-'.sprintf("%02d", $date['day']);
          }
          if($date['hour'] > 0) {
            $value = $value.' '.sprintf("%02d", $date['hour']);
          }
          if($date['minute'] > 0) {
            $value = $value.':'.sprintf("%02d", $date['minute']);
          }
          if($date['second'] > 0) {
            $value = $value.':'.sprintf("%02d", $date['second']);
          }
        } else {
          $value = '';
        }
      }
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'" value="'.$value.'"';
      if(isset($obj['validator']) && $obj['validator'] != '') {
        $data .= ' '.$obj['validator'];
      }
      if(isset($obj['maxlength']) && is_int($obj['maxlength'])) {
        $data .= ' maxlength="'.$obj['maxlength'].'"';
      }
      $data .= '/>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getBoolean($obj, $value = '') {
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<select type="text" class="form-control" id="'.$obj['id'].'" name="'.$obj['id'].'">"';
      $data .= '<option';
      if($value == 'true') {
        $data .= ' selected';
      }
      $data .= ' value="true">True</option>';
      $data .= '<option';
      if($value == '' || $value == 'false') {
        $data .= ' selected';
      }
      $data .= ' value="false">False</option>';
      $data .= '</select>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getImage($obj, $system, $image) {
      $data = '';
      if($image != '') {
        if(isset($obj['name']) && $obj['name'] != '') {
          $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
        }
        $data .= '<div class="thumbnail">';
        $data .= '<img style="max-height: 245px" src="/media.php?sys='.$system.'&file='.rawurlencode(pathinfo($image, PATHINFO_BASENAME)).'">';
        $data .= '</div>';
      }
      return $data;
    }
    
  }
    
?>