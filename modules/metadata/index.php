<?php
  
  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  page::start();
  
  echo '<div class="row">';
  echo '<div class="col-sm-4">';
  // RENDER LEFT SIDE
  echo '<div class="row">';
  echo '<div class="col-sm-8">';
  // RENDER SYSTEMS
  echo '<select name="sys" id="sys" class="form-control" data-toggle="select" data-query="'.CONTROLLER.'?action=change&system=" data-target="#romlist">';
  echo '<option value="" selected>-- Select System --</option>';
  foreach (roms::getSystems() as $sys){
    echo '<option';
    if(cache::getClientVariable($module->id.'_system') == $sys['id']) {
      echo ' selected';
    }
    echo ' value="'.$sys['id'].'">'.$sys['name'].'</option>';
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
  echo '<br><select name="romlist" id="romlist" class="form-control" data-toggle="select" data-query="'.DIALOG.'?action=tab&tab=metadata&id=" data-target="#tab">';
  $first = null;
  if(cache::getClientVariable($module->id.'_system') != '') {
    $romlist = roms::getRoms(cache::getClientVariable($module->id.'_system'));
    $first = $romlist[0];
    foreach ($romlist as $rom) {
      echo '<option';
      if(cache::getClientVariable($module->id.'_id') == $rom) {
        echo ' selected';
      }
      echo ' value="'.$rom.'">'.$rom.'</option>';
    }
  }
  echo '</select>';
  echo '</div>';
  // END LEFT SIDE
   
  // RENDER RIGHT SIDE
  echo '<div class="col-sm-8" id="tab" name="tab">';
  if(cache::getClientVariable($module->id.'_system') != '' && cache::getClientVariable($module->id.'_id') != '') {
    if(cache::getClientVariable($module->id.'_tab') != '') {
      if(cache::getClientVariable($module->id.'_tab') == 'metadata') {
        echo metadata::renderMetadata(cache::getClientVariable($module->id.'_system'), cache::getClientVariable($module->id.'_id'));
      }
      if(cache::getClientVariable($module->id.'_tab') == 'media') {
        echo metadata::renderMedia(cache::getClientVariable($module->id.'_system'), cache::getClientVariable($module->id.'_id'));
      }
    } else {
      echo metadata::renderMetadata(cache::getClientVariable($module->id.'_system'), cache::getClientVariable($module->id.'_id'));
    }
  } else {
    if(cache::getClientVariable($module->id.'_system') != '') {
      echo metadata::renderMetadata(cache::getClientVariable($module->id.'_system'), $first);
    }
  }
  echo '</div>';
  echo '</div>';
  
  page::end();
  
?>