<?php

require(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();

$fieldset = array();
$modules = module::readAll();
foreach ($modules as $moduleconfig) {
  $tmp = json_decode(file_get_contents($moduleconfig));
  if (isset($tmp->config)) {
    foreach ($tmp->config as $item) {
      $fieldset[$item->id] = array('id' => $tmp->id, 'name' => $item->name, 'target' => $item->target);
      if (isset($item->icon)) {
        $fieldset[$item->id]['icon'] = $item->icon;
      }
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
  $target = $item['target'];
  if (strpos($target, URL_SEPARATOR) !== 0) {
    $target = str_replace(BASE, '', MODULES).URL_SEPARATOR.$item['id'].URL_SEPARATOR.$item['target'];
  }
  echo '><a href="#" data-toggle="tab" data-query="'.$target.'" data-target="#panel">';
  if (isset($item['icon'])) {
    echo '<i class="fa fa-'.$item['icon'].' fa-fw"></i> ';
  }
  echo $item['name'].'</a></li>';
}
echo '</ul>';
echo '<br>';
echo '<div class="row">';
echo '<div class="col-sm-12" id="panel" name="panel">';
if (cache::getClientVariable($module->id.'_id') != '') {
  if (isset($fieldset[cache::getClientVariable($module->id.'_id')])) {
    $item = $fieldset[cache::getClientVariable($module->id.'_id')];
    $target = $item['target'];
    if (strpos($target, URL_SEPARATOR) == 0) {
      $target = MODULES.URL_SEPARATOR.$item['id'].URL_SEPARATOR.$item['target'];
      $target = str_replace(URL_SEPARATOR, DIRECTORY_SEPARATOR, $target);
    }
    page::load($target);
  }
}
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

page::end();

?>