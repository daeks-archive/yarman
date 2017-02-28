<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();
echo '<div class="row">';
echo '<div class="col-sm-12">';

echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<select name="nav-logs" id="nav-logs" class="form-control" data-mode="text" data-query="'.CONTROLLER.'?action=view&id=" data-target="module-content">';
echo '<option value="" selected>-- Select Log File --</option>';
foreach (db::read($module->id) as $item) {
  if ($item['type'] == 'file') {
    echo '<option';
    if (cache::getClientVariable($module->id.'_id') == $item['id']) {
      echo ' selected';
    }
    echo ' value="'.$item['id'].'">'.$item['value'].'</option>';
  } elseif ($item['type'] == 'folder') {
    foreach (array_slice(scandir($item['value']), 2) as $object) {
      echo '<option';
      $parts = explode('@', cache::getClientVariable($module->id.'_id'));
      if (sizeof($parts) == 2 && $parts[0] == $object) {
        echo ' selected';
      }
      echo ' value="'.$object.'@'.$item['id'].'">'.$item['value'].DIRECTORY_SEPARATOR.$object.'</option>';
    }
  }
}
echo '</select><br>';
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div id="module-content" style="width: 100%"></div>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

page::end();

?>