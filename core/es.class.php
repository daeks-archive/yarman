<?php

class es
{
  public static function start()
  {
    return shell_exec('export HOME='.db::instance()->read('config', 'user_path').' && emulationstation > /dev/null 2>/dev/null &');
  }
  
  public static function stop()
  {
    return shell_exec('killall emulationstation');
  }
  
  public static function config()
  {
    $content = file_get_contents(db::instance()->read('config', 'es_path').DIRECTORY_SEPARATOR.'es_settings.cfg');
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
  
  public static function startRom($system, $id)
  {
    return shell_exec('/opt/retropie/supplementary/runcommand/runcommand.sh 0 _SYS_ '.$system.' \''.db::instance()->read('config', 'rom_path').DIRECTORY_SEPARATOR.$system.DIRECTORY_SEPARATOR.$id.'\'');
  }
}

?>