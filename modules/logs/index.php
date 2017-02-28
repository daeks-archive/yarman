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
  echo '<option';
  if (cache::getClientVariable($module->id.'_id') == $item['id']) {
    echo ' selected';
  }
  echo ' value="'.$item['id'].'">'.$item['value'].'</option>';
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