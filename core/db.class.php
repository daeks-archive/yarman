<?php
  
class db
{
  public static $format = '.json';

  public static function read($module, $id = null, $column = 'value', $key = 'id', $replace = array())
  {
    if (file_exists(DEFAULTS.DIRECTORY_SEPARATOR.$module.self::$format)) {
      $needle = array('%USER%');
      $haystack = array(get_current_user());      
      foreach ($replace as $key => $value) {
        array_push($needle, $key);
        array_push($haystack, $value);
      }
      $array = json_decode(file_get_contents(DEFAULTS.DIRECTORY_SEPARATOR.$module.self::$format), true);
      if ($id != null) {
        foreach ($array as $item) {
          if (isset($item[$key]) && isset($item[$column]) && $item[$key] == $id) {
            return str_replace($needle, $haystack, $item[$column]);
          }
        }
      } else {
        return $array;
      }
    }
  }
}
  
?>