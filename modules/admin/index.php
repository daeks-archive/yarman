<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();

$fieldset = array();
$modules = module::readAll();
foreach ($modules as $moduleconfig) {
  $tmp = json_decode(file_get_contents($moduleconfig));
  if (isset($tmp->config)) {
    foreach ($tmp->config as $item) {
      $fieldset[$item->id] = array('name' => $item->name, 'target' => $item->target);
    }
  }
}

ksort($fieldset);
if (cache::getClientVariable($module->id.'_id') == '') {
  cache::setClientVariable($module->id.'_id', key($fieldset));
} else {
  if (!isset($fieldset[cache::getClientVariable($module->id.'_id')])) {
    cache::setClientVariable($module->id.'_id', key($fieldset));
  }
}

echo '<div class="row">';
echo '<div class="col-sm-12">';

echo '<ul class="nav nav-tabs">';
foreach ($fieldset as $key => $item) {
  echo '<li ';
  if (cache::getClientVariable($module->id.'_id') == $key) {
    echo 'class="active"';
  }
  echo '><a href="#" data-toggle="tab" data-query="'.$item['target'].'" data-target="#panel">'.$item['name'].'</a></li>';
}
echo '</ul>';
echo '<br>';
echo '<div class="row">';
echo '<div class="col-sm-12" id="panel" name="panel">';
if (cache::getClientVariable($module->id.'_id') != '') {
  if (isset($fieldset[cache::getClientVariable($module->id.'_id')])) {
    $item = $fieldset[cache::getClientVariable($module->id.'_id')];
    $target = $item['target'];
    if (strpos($target, DIRECTORY_SEPARATOR) == 0) {
      $target = BASE.DIRECTORY_SEPARATOR.$target;
    }
    
    $offset = strpos($target, '?');
    if ($offset !== false) {
      $params = substr($target, $offset+1);
      $include = substr($target, 0, $offset);
      foreach (explode('&', $params) as $value) {
        $parts = explode('=', $value);
        if (sizeof($parts) == 2) {
          $_GET[$parts[0]] = $parts[1];
        } else {
          $_GET[$parts[0]] = '';
        }
      }
      ob_start();
      include($include);
      $input = ob_get_clean();
      $tmp = json_decode($input);
      if (isset($tmp->data)) {
        echo html_entity_decode($tmp->data);
      } else {
        echo $input;
      }
    } else {
      ob_start();
      include($target);
      $input = ob_get_clean();
      $tmp = json_decode($input);
      if (isset($tmp->data)) {
        echo html_entity_decode($tmp->data);
      } else {
        echo $input;
      }
    }
  }
}
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

page::end();

?>