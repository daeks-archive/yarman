<?php

require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();

echo '<div class="row">';
echo '<div class="col-sm-4" id="panel-left" name="panel-left">';
// RENDER LEFT SIDE
echo '<div class="row">';
echo '<div class="col-sm-8">';
// RENDER EMULATORS
echo '<div class="input-group">';
echo '<span class="input-group-btn"><button class="btn btn-default" data-toggle="modal" href="'.DIALOG.'?action=syncemulator" data-target="#modal"><em class="fa fa-refresh"></em></button></span>';
echo '<select name="nav-emulator" id="nav-emulator" class="form-control" data-toggle="select" data-query="'.CONTROLLER.'?action=change&emulator=" data-target="#nav-romlist">';
echo '<option value="" selected>-- Select Emulator --</option>';
foreach (emulator::read() as $emulator) {
  echo '<option';
  if (cache::getClientVariable($module->id.'_emulator') == $emulator['id']) {
    echo ' selected';
  }
  echo ' value="'.$emulator['id'].'">'.$emulator['name'];
  if (isset($emulator['count']) && $emulator['count'] != '' && $emulator['count'] > 0) {
    echo ' ('.$emulator['count'].')';
  }
  echo '</option>';
}
echo '</select>';
echo '<span class="input-group-btn"><button class="btn btn-default" data-toggle="modal" href="'.DIALOG.'?action=export" data-target="#modal"><em class="fa fa-download"></em></button></span>';
echo '</div>';
echo '</div>';
// RENDER FILTER
echo '<div class="col-sm-4">';
echo '<div class="input-group">';
echo '<span class="input-group-addon"><i class="fa fa-filter fa-fw"></i></span>';
echo '<select name="nav-filter" id="nav-filter" class="form-control" data-toggle="select" data-query="'.CONTROLLER.'?action=filter&type=" data-target="#nav-romlist">';
echo '<option';
if (cache::getClientVariable($module->id.'_filter') == '') {
  echo ' selected';
}
echo ' value="">All</option>';
echo '<option';
if (cache::getClientVariable($module->id.'_filter') == 'nodata') {
  echo ' selected';
}
echo ' value="nodata">No Data</option>';
echo '<option';
if (cache::getClientVariable($module->id.'_filter') == 'noimage') {
  echo ' selected';
}
echo ' value="noimage">No Image</option>';
echo '<option';
if (cache::getClientVariable($module->id.'_filter') == 'novideo') {
  echo ' selected';
}
echo ' value="novideo">No Video</option>';
echo '</select></div>';
echo '</div>';
echo '</div>';

// RENDER ROMLIST
echo '<br><select name="nav-romlist" id="nav-romlist" class="form-control" data-toggle="select" data-query="'.DIALOG.'?action=render&tab=metadata&id=" data-target="#panel-right">';
$first = null;
$romlist = null;
if (cache::getClientVariable($module->id.'_emulator') != '') {
  $romlist = emulator::read(cache::getClientVariable($module->id.'_emulator'));
  if (cache::getClientVariable($module->id.'_filter') != '') {
    foreach ($romlist as $rom) {
      $data = rom::read($rom['id']);
      if (cache::getClientVariable($module->id.'_filter') == 'nodata') {
        if (!isset($data) || isset($data) && !isset($data['name']) || isset($data) &&  $data['name'] == '') {
          if ($first == null) {
            $first = $rom;
          }
          echo '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
        }
      } elseif (cache::getClientVariable($module->id.'_filter') == 'noimage') {
        if (!isset($data) || isset($data) && !isset($data['image']) || isset($data) &&  $data['image'] == '') {
          if ($first == null) {
            $first = $rom;
          }
          echo '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
        }
      } elseif (cache::getClientVariable($module->id.'_filter') == 'novideo') {
        if (!isset($data) || isset($data) && !isset($data['video']) || isset($data) && $data['video'] == '') {
          if ($first == null) {
            $first = $rom;
          }
          echo '<option value="'.$rom['id'].'">'.$rom['name'].'</option>';
        }
      }
    }
  } else {
    $first = $romlist[0];
    foreach ($romlist as $rom) {
      echo '<option';
      if (cache::getClientVariable($module->id.'_id') == $rom['id']) {
        echo ' selected';
      }
      echo ' value="'.$rom['id'].'">'.$rom['name'].'</option>';
    }
  }
}
echo '</select>';
echo '</div>';
// END LEFT SIDE
 
// RENDER RIGHT SIDE
echo '<div class="col-sm-8" id="panel-right" name="panel-right">';
if (cache::getClientVariable($module->id.'_emulator') != '' && cache::getClientVariable($module->id.'_id') != '') {
  if (cache::getClientVariable($module->id.'_tab') != '') {
    echo metadata::render(cache::getClientVariable($module->id.'_tab'), cache::getClientVariable($module->id.'_emulator'), cache::getClientVariable($module->id.'_id'));
  } else {
    echo metadata::render('metadata', cache::getClientVariable($module->id.'_emulator'), cache::getClientVariable($module->id.'_id'));
  }
} else {
  if (cache::getClientVariable($module->id.'_emulator') != '') {
    if ($first != null) {
      cache::setClientVariable($module->id.'_id', $first['id']);
      echo metadata::render('metadata', cache::getClientVariable($module->id.'_emulator'), $first['id']);
    }
  }
}
echo '</div>';
echo '</div>';

page::end();

?>