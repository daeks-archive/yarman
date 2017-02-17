<?php

  class metadata {
  
    public static function start($tab) {
      // RENDER RIGHT MENU
      $data = '<div class="row">';
      $data .= '<div class="col-sm-12">';

      // RENDER RIGHT HEADER
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-8">';
      $data .= '<ul class="nav nav-tabs">';
      $data .= '<li ';
      if($tab == 'metadata') {
        $data .= 'class="active"';
      }
      $data .= '><a href="#" data-toggle="async" data-query="'.DIALOG.'?action=tab&tab=metadata" data-target="#tab">Metadata</a></li>';
      $data .= '<li ';
      if($tab == 'media') {
        $data .= 'class="active"';
      }
      $data .= '><a href="#" data-toggle="async" data-query="'.DIALOG.'?action=tab&tab=media" data-target="#tab">Media</a></li>';
      $data .= '</ul>';
      $data .= '</div>';
      $data .= '<div class="col-sm-4">';
      $data .= '<div class="btn-toolbar btn-group-sm" role="toolbar">';
      $data .= '<button class="btn btn-success disabled" type="button"><em class="fa fa-play"></em> Start Game</button>';
      $data .= '<div class="btn-group btn-group-sm pull-right">';
      $data .= '<button class="btn btn-primary disabled" type="button">Save</button>';
      $data .= '<button class="btn btn-default disabled" type="button">Scrape</button>';
      $data .= '<button class="btn btn-danger disabled" type="button"><em class="fa fa-trash"></em> Delete</button>';
      $data .= '</div>';
      $data .= '</div>';
      $data .= '</div>';
      $data .= '</div>';
      $data .= '<br>';
      // END RENDER RIGHT MENU
      return $data;
    }
    
    public static function end() {
      $data = '</div>';
      $data .= '</div>';
      // END RNDER RIGHT
      return $data;
    }
  
    public static function renderMetadata($system, $id) {
      $rom = roms::getMetadata($system, $id);
      $fields = db::read('fields');
      
      $data = self::start('metadata');

      $data .= '<div class="row">';
      $data .= '<div class="col-sm-12">';
      
      $data .= '<form id="data" name="data" role="form">';
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-8">';
      $data .= form::getField($fields, 'name', (isset($rom['name'])?$rom['name']:''));
      $data .= form::getField($fields, 'genre', (isset($rom['genre'])?$rom['genre']:''));
      $data .= form::getField($fields, 'developer', (isset($rom['developer'])?$rom['developer']:''));
      $data .= form::getField($fields, 'publisher', (isset($rom['publisher'])?$rom['publisher']:''));
      $data .= '</div>';
      $data .= '<div class="col-sm-4">';
      if(pathinfo((isset($rom['image'])?$rom['image']:''), PATHINFO_BASENAME) != '') {
        $data .= '<a href="#" class="thumbnail">';
        $data .= '<img src="/media.php?sys='.$system.'&file='.urlencode(pathinfo($rom['image'], PATHINFO_BASENAME)).'">';
        $data .= '</a>';
      }
      $data .= '</div>';
      $data .= '</div>';
      
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-6">';
      $data .= form::getField($fields, 'releasedate', (isset($rom['releasedate'])?$rom['releasedate']:''));
      $data .= '</div>';
      $data .= '<div class="col-sm-6">';
      $data .= form::getField($fields, 'players', (isset($rom['players'])?$rom['players']:''));
      $data .= '</div>';
      $data .= '</div>';
      
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-12">';
      $data .= form::getField($fields, 'desc', (isset($rom['desc'])?$rom['desc']:''));
      $data .= '</div>';
      $data .= '</div>';
      
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-6">';
      $data .= form::getField($fields, 'playcount', (isset($rom['playcount'])?$rom['playcount']:''));
      $data .= '</div>';
      $data .= '<div class="col-sm-6">';
      $data .= form::getField($fields, 'lastplayed', (isset($rom['lastplayed'])?$rom['lastplayed']:''));
      $data .= '</div>';
      $data .= '</div>';

      //foreach (utils::msort(db::read('fields'), array('id' => SORT_ASC)) as $field){
      //  $data .= form::getField($field, $rom[$field['id']]);
      //}
      $data .= '</form>';
      
      $data .= '</div>';
      $data .= '</div>';

      $data .= self::end();
      
      return $data;
    }
    
    public static function renderMedia($system, $id) {
      $rom = roms::getMetadata($system, $id);
      $fields = db::read('fields');
      
      $data = self::start('media');

      $data .= '<div class="row">';
      $data .= '<div class="col-sm-12">';
      
      $data .= '<form id="data" name="data" role="form">';
      $data .= '<div class="row">';
      $data .= '<div class="col-sm-8">';
      $data .= form::getField($fields, 'image', (isset($rom['image'])?$rom['image']:''));
      $data .= form::getField($fields, 'video', (isset($rom['video'])?$rom['video']:''));
      $data .= form::getField($fields, 'marquee', (isset($rom['marquee'])?$rom['marquee']:''));
      $data .= form::getField($fields, 'thumbnail', (isset($rom['thumbnail'])?$rom['thumbnail']:''));
      $data .= '</div>';
      $data .= '<div class="col-sm-4">';
      if(pathinfo((isset($rom['image'])?$rom['image']:''), PATHINFO_BASENAME) != '') {
        $data .= '<a href="#" class="thumbnail">';
        $data .= '<img src="/media.php?sys='.$system.'&file='.urlencode(pathinfo($rom['image'], PATHINFO_BASENAME)).'">';
        $data .= '</a>';
      }
      $data .= '</div>';
      $data .= '</div>';
      $data .= '</form>';
      
      $data .= '</div>';
      $data .= '</div>';

      $data .= self::end();
      
      return $data;
    }
  
  }

?>