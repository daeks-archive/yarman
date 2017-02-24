<?php

class es
{
  public static function restart()
  {
    return shell_exec('killall emulationstation && emulationstation &');
  }
  
  public static function config()
  {
    $content = file_get_contents(db::read('config', 'es_path').DIRECTORY_SEPARATOR.'es_settings.cfg');
    $content = str_replace('<?xml version="1.0"?>', '<?xml version="1.0"?><settings>', $content).'</settings>';
    $xml = json_decode(json_encode(simplexml_load_string($content)), true);
    $output = array();
    foreach ($xml as $type) {
      foreach ($type as $item) {
        $output[$item['@attributes']['name']] = $item['@attributes']['value'];
      }
    }
    return $output;
  }
}

?>