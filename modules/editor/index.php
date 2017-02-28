<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();
echo '<div class="row">';
echo '<div class="col-sm-12">';

echo '<div class="row">';
echo '<div class="col-sm-4">';
echo '<select name="nav-emulator" id="nav-emulator" class="form-control" data-toggle="select" data-query="'.CONTROLLER.'?action=change&emulator=" data-target="#nav-editor">';
echo '<option value="">Show General Config</option>';
foreach (emulator::readAll(false) as $emulator) {
  echo '<option';
  if (cache::getClientVariable($module->id.'_emulator') == $emulator['id']) {
    echo ' selected';
  }
  echo ' value="'.$emulator['id'].'">Show Config for '.$emulator['name'].'</option>';
}
echo '</select>';
echo '</div>';
echo '<div class="col-sm-8">';
echo '<div class="input-group">';
echo '<select name="nav-editor" id="nav-editor" class="form-control" data-mode="ini" data-query="'.CONTROLLER.'?action=view&id=" data-target="module-content">';
echo '<option value="" selected>-- Select Config File --</option>';
foreach (db::read($module->id) as $item) {
  if (cache::getClientVariable($module->id.'_emulator') == '') {
    if ($item['type'] == 'file') {
      if (strpos($item['value'], '%') === false) {
        echo '<option';
        if (cache::getClientVariable($module->id.'_id') == $item['id']) {
          echo ' selected';
        }
        echo ' value="'.$item['id'].'">'.$item['value'].'</option>';
      }
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
  } else {
    if ($item['type'] == 'file') {
      if (strpos($item['value'], '%') !== false) {
        $item['value'] = str_replace('%EMULATOR%', cache::getClientVariable($module->id.'_emulator'), $item['value']);
        echo '<option';
        if (cache::getClientVariable($module->id.'_id') == $item['id']) {
          echo ' selected';
        }
        echo ' value="'.$item['id'].'">'.$item['value'].'</option>';
      }
    } elseif ($item['type'] == 'folder') {
      if (strpos($item['value'], '%') !== false) {
        $item['value'] = str_replace('%EMULATOR%', cache::getClientVariable($module->id.'_emulator'), $item['value']);
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
  }
}
echo '</select>';
echo '<span class="input-group-btn"><button class="btn btn-success" disabled data-validate="form" type="submit" data-toggle="modal" href="'.DIALOG.'?action=confirmsave" data-target="#modal"><em class="fa fa-save"></em> Save</button></span>';
echo '</div><br>';
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div id="module-wrapper">';
echo '<div id="module-content" style="width: 100%"></div>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

page::end();

?>