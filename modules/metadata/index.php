<?php
  
  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  page::start();
  
  echo '<div class="row">';
  echo '<div class="col-sm-4" id="panel-left" name="panel-left">';
  // RENDER LEFT SIDE
  echo '<div class="row">';
  echo '<div class="col-sm-8">';
  // RENDER EMULATORS
  echo '<select name="nav-emulator" id="nav-emulator" class="form-control" data-toggle="select" data-query="'.CONTROLLER.'?action=change&emulator=" data-target="#nav-romlist">';
  echo '<option value="" selected>-- Select Emulator --</option>';
  foreach (emulator::readAll() as $emulator){
    echo '<option';
    if (cache::getClientVariable($module->id.'_emulator') == $emulator['id']) {
      echo ' selected';
    }
    echo ' value="'.$emulator['id'].'">'.$emulator['name'].' ('.$emulator['count'].')</option>';
  }
  echo '</select>';
  echo '</div>';
  // RENDER FILTER
  echo '<div class="col-sm-4">';
  echo '<div class="input-group">';
  echo '<span class="input-group-addon"><i class="fa fa-filter fa-fw"></i></span>';
  echo '<select name="filter" id="filter" class="form-control">';
  echo '<option selected value="all">All</option>';
  echo '</select></div>';
  echo '</div>';
  echo '</div>';
  
  // RENDER ROMLIST
  echo '<br><select name="nav-romlist" id="nav-romlist" class="form-control" data-toggle="select" data-query="'.DIALOG.'?action=render&tab=metadata&id=" data-target="#panel-right">';
  $first = null;
  if (cache::getClientVariable($module->id.'_emulator') != '') {
    $romlist = emulator::readRomlist(cache::getClientVariable($module->id.'_emulator'));
    $first = $romlist[0];
    foreach ($romlist as $rom) {
      echo '<option';
      if (cache::getClientVariable($module->id.'_id') == $rom) {
        echo ' selected';
      }
      echo ' value="'.$rom.'">'.$rom.'</option>';
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
      echo metadata::render('metadata', cache::getClientVariable($module->id.'_emulator'), $first);
    }
  }
  echo '</div>';
  echo '</div>';
  
  page::end();
  
?>