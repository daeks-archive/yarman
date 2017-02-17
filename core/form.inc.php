<?php
    
	class form {

		public static function getField($config, $id, $value = '') {
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
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" value="'.$value.'"';
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
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" value="'.$value.'"';
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
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" value="'.$value.'"';
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
      $data .= '<textarea class="form-control" id="'.$obj['id'].'" rows="'.$obj['rowcount'].'">';
      $data .= $value;
      $data .= '</textarea>';
      $data .= '</div>';
      return $data;
    }
    
    public static function getDate($obj, $value = '') {
      $data = '<div class="form-group">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label for="'.$obj['id'].'">'.$obj['name'].'</label>';
      }
      $data .= '<input type="text" class="form-control" id="'.$obj['id'].'" value="'.$value.'"';
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
      $data = '<div class="checkbox">';
      if(isset($obj['name']) && $obj['name'] != '') {
        $data .= '<label><input type="checkbox" id="'.$obj['id'].'" /> '.$obj['name'].'</label>';
      } else {
        $data .= '<input type="checkbox" id="'.$obj['id'].'" />';
      }
      $data .= '</div>';
      return $data;
    }
    
	}
    
?>