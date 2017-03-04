<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();
echo '<div class="row">';
echo '<div class="col-sm-12">';

echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div class="input-group">';
echo '<span class="input-group-btn"><button class="btn btn-default" onclick="core.logs.reload();"><em class="fa fa-refresh"></em></button></span>';
echo '<select name="nav-logs" id="nav-logs" class="form-control" data-mode="text" data-query="'.CONTROLLER.'?action=view&id=" data-target="module-content">';
echo '<option value="" selected>-- Select Log File --</option>';
foreach (db::instance()->read($module->id) as $item) {
  if ($item['type'] == 'file') {
    echo '<option';
    if (cache::getClientVariable($module->id.'_id') == $item['id']) {
      echo ' selected';
    }
    echo ' value="'.$item['id'].'">'.$item['value'].' ('.filesize($item['value']).' Bytes)</option>';
  } elseif ($item['type'] == 'folder') {
    $list = array_slice(scandir($item['value']), 2);
    rsort($list);
    foreach ($list as $object) {
      if (filesize($item['value'].DIRECTORY_SEPARATOR.$object) > 0) {
        echo '<option';
        $parts = explode('@', cache::getClientVariable($module->id.'_id'));
        if (sizeof($parts) == 2 && $parts[0] == $object) {
          echo ' selected';
        }
        echo ' value="'.$object.'@'.$item['id'].'">'.$item['value'].DIRECTORY_SEPARATOR.$object.' ('.filesize($item['value'].DIRECTORY_SEPARATOR.$object).' Bytes)</option>';
      }
    }
  }
}
echo '</select>';
echo '</div>';
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