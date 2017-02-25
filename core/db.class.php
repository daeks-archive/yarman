<?php
  
class db
{
  public static $format = '.json';

  public static function read($module, $id = null, $column = 'value', $key = 'id', $replace = array())
  {
    if (file_exists(DEFAULTS.DIRECTORY_SEPARATOR.$module.self::$format)) {
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
      $array = json_decode(file_get_contents(DEFAULTS.DIRECTORY_SEPARATOR.$module.self::$format), true);
      if ($id != null) {
        foreach ($array as $item) {
          if (isset($item[$key]) && isset($item[$column]) && $item[$key] == $id) {
            return str_replace($needle, $haystack, $item[$column]);
          }
        }
      } else {
        $output = array();
        foreach ($array as $item) {
          foreach ($item as $key => $value) {
            $item[$key] = str_replace($needle, $haystack, $value);
          }
          array_push($output, $item);
        }
        return $output;
      }
    }
  }
}
  
?>