<?php
  
class db
{
  public static $format = '.jdb';

  public static function read($module, $id = null, $column = 'value', $key = 'id', $replace = array())
  {
    $array = array();
    if (file_exists(DATA.DIRECTORY_SEPARATOR.$module.self::$format)) {
      $array = json_decode(file_get_contents(DATA.DIRECTORY_SEPARATOR.$module.self::$format), true);
    } elseif (file_exists(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$module.self::$format)) {
      $array = json_decode(file_get_contents(MODULES.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$module.self::$format), true);
    } elseif (file_exists(DB.DIRECTORY_SEPARATOR.$module.self::$format)) {
      $array = json_decode(file_get_contents(DB.DIRECTORY_SEPARATOR.$module.self::$format), true);
    }
    
    $needle = array('%USER%');
    $default_user = get_current_user();
    if (file_exists(DATA.DIRECTORY_SEPARATOR.'user')) {
      $default_user = trim(file_get_contents(DATA.DIRECTORY_SEPARATOR.'user'));
    }
    $haystack = array($default_user);
    foreach ($replace as $key => $value) {
      array_push($needle, $key);
      array_push($haystack, $value);
    }

    if ($id != null) {
      foreach ($array['data'] as $item) {
        if (isset($item[$key]) && isset($item[$column]) && $item[$key] == $id) {
          return str_replace($needle, $haystack, $item[$column]);
        }
      }
    } else {
      $output = array();
      foreach ($array['data'] as $item) {
        foreach ($item as $key => $value) {
          $item[$key] = str_replace($needle, $haystack, $value);
        }
        array_push($output, $item);
      }
      return $output;
    }
  }
}
  
?>