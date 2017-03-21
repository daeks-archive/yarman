<?php

require(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

page::start();

echo '<div class="row">';
echo '<div class="col-sm-3" id="panel-left" name="panel-left">';
// RENDER LEFT SIDE
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div class="btn-toolbar" style="margin-top: -5px; padding-bottom: 10px" role="toolbar">';
echo '<div class="btn-group btn-group-sm" role="group">';
echo '<button class="btn btn-default" disabled data-toggle="modal" href="'.DIALOG.'?action=sync" data-target="#modal"><em class="fa fa-refresh"></em></button>';
echo '<button class="btn btn-default" disabled data-toggle="modal" href="'.DIALOG.'?action=dat" data-target="#modal">DAT Import</button>';
echo '</div>';
echo '<div class="btn-group btn-group-sm pull-right" role="group">';
echo '<button class="btn btn-default" disabled data-toggle="modal" href="'.DIALOG.'?action=scrape" data-target="#modal"><em class="fa fa-search"></em> Scrape</button>';
echo '<span class="btn btn-success btn-file" disabled>';
echo '<input type="file">';
echo 'Upload</span>';
echo '</div>';
echo '</div>';
// RENDER EMULATORS
echo '<select name="nav-emulator" id="nav-emulator" class="form-control" data-toggle="select" data-query="'.DIALOG.'?action=change&emulator=" data-target="#panel-right">';
$first = null;
foreach (emulator::read() as $emulator) {
  if ($first == null) {
    $first = $emulator['id'];
  }
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
echo '<br><div class="dropzone">Drop files here or click upload</div>';
echo '</div>';
echo '</div>';
echo '</div>';
// END LEFT SIDE
 
// RENDER RIGHT SIDE
echo '<div class="col-sm-9" id="panel-right" name="panel-right">';

$emulator = $first;
if (cache::getClientVariable($module->id.'_emulator') != '') {
  $emulator = cache::getClientVariable($module->id.'_emulator');
}
page::load(MODULES.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.DIALOG.'?action=change&emulator='.$emulator);

echo '</div>';
echo '</div>';

page::end();

?>