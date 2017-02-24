<?php

class page
{
  public static $time;
  public static $devices = 'hidden-xs hidden-sm display-md display-lg';

  public static function start($infobox = '', $js = null, $cache = FILE_CACHE)
  {
    self::$time = microtime(true);
    echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<meta charset="utf-8">';
    echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<head>';
    
    $module = module::read();
    if ($module != null) {
      echo '<title>'.NAME.' - '.$module->name.'</title>';
    } else {
      echo '<title>'.NAME.'</title>';
    }
    
    echo '<link rel="icon" type="image/x-icon" href="favicon.ico" />';
    echo '<meta name="robots" content="noindex">';
    
    $jsinclude = array(JS, INC);
    if ($module != null) {
      array_push($jsinclude, MODULES.DIRECTORY_SEPARATOR.$module->id);
    }
    foreach ($jsinclude as $path) {
      foreach (scandir($path) as $include) {
        if (is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strpos($include, 'min') == 0  && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'JS') {
          $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR, '', $path)).URL_SEPARATOR.$include;
          if (FILE_COMPRESS && is_file($path.DIRECTORY_SEPARATOR.substr($include, 0, -3).'.min.'.substr($include, -2))) {
            $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR, '', $path)).URL_SEPARATOR.substr($include, 0, -3).'.min.'.substr($include, -2);
          }
          echo '<script type="text/javascript" src="'.URL_SEPARATOR.$ref.($cache ? '' : '?v='.time()).'"></script>';
        }
      }
    }
  
    $cssinclude = array(CSS, INC);
    if ($module != null) {
      array_push($cssinclude, MODULES.DIRECTORY_SEPARATOR.$module->id);
    }
    foreach ($cssinclude as $path) {
      foreach (scandir($path) as $include) {
        if (is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strpos($include, 'min') == 0  && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'CSS') {
          $ref = str_replace(BASE.DIRECTORY_SEPARATOR, '', $path).URL_SEPARATOR.$include;
          if (FILE_COMPRESS && is_file($path.DIRECTORY_SEPARATOR.substr($include, 0, -4).'.min.'.substr($include, -3))) {
            $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR, '', $path)).URL_SEPARATOR.substr($include, 0, -4).'.min.'.substr($include, -3);
          }
          echo '<link type="text/css" href="'.URL_SEPARATOR.$ref.($cache ? '' : '?v='.time()).'" rel="stylesheet" media="screen" />';
        }
      }
    }
        
    echo '</head>';
    echo '<body '.(isset($js)?'onload="'.$js.'"':'').'>';
    
    $module = module::read();
    $modules = module::readAll();
    
    $menuright = array();
    $menuleft = array();
    foreach ($modules as $moduleconfig) {
      $tmp = json_decode(file_get_contents($moduleconfig));
      $item = "";
      if (isset($tmp->menu)) {
        if ($tmp->menu->position == 'left') {
          if (isset($tmp->menu->dropdown)) {
            $item .= '<li class="dropdown">';
            $item .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-'.$tmp->menu->icon.' fa-fw"></i></a>';
            $item .= '<ul class="dropdown-menu">';
            foreach ($tmp->menu->dropdown as $dropdown) {
              if ($dropdown->type == 'spacer') {
                $item .= '<li role="separator" class="divider"></li>';
              } elseif ($dropdown->type == 'modal') {
                $item .= '<li><a data-toggle="modal" href="'.$dropdown->external.'" data-target="#modal">'.$dropdown->name.'</a></li>';
              }
            }
            $item .= '</ul>';
            $item .= '</li>';
          } else {
            if ($module != null && $tmp->id == $module->id) {
              $item .= '<li class="active">';
            } else {
              $item .= '<li>';
            }
            if(isset($tmp->menu->external)) {
              $item .= '<a href="'.$tmp->menu->external.'" target="_blank">';
            } else {
              $item .= '<a href="/'.basename(MODULES).URL_SEPARATOR.$tmp->id.URL_SEPARATOR.'">';
            }
            if ($tmp->menu->icon != '') {
              $item .= '<i class="fa fa-'.$tmp->menu->icon.' fa-fw"></i> ';
            }
            $item .= $tmp->name;
            $item .= '</a></li>';
          }
          if (!array_key_exists($tmp->menu->order, $menuleft)) {
            $menuleft[$tmp->menu->order] = $item;
          }
        }
        
        if ($tmp->menu->position == 'right') {
          if (isset($tmp->menu->dropdown)) {
            $item .= '<li class="dropdown">';
            $item .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-'.$tmp->menu->icon.' fa-fw"></i></a>';
            $item .= '<ul class="dropdown-menu">';
            foreach ($tmp->menu->dropdown as $dropdown) {
              if ($dropdown->type == 'spacer') {
                $item .= '<li role="separator" class="divider"></li>';
              } elseif ($dropdown->type == 'modal') {
                $item .= '<li><a data-toggle="modal" href="'.$dropdown->external.'" data-target="#modal">'.$dropdown->name.'</a></li>';
              }
            }
            $item .= '</ul>';
            $item .= '</li>';
          } else {
            $item .= '<li>';
            if(isset($tmp->menu->external)) {
              $item .= '<a href="'.$tmp->menu->external.'" target="_blank">';
            } else {
              $item .= '<a href="/'.basename(MODULES).URL_SEPARATOR.$tmp->id.URL_SEPARATOR.'">';
            }
            if ($tmp->menu->icon != '') {
              $item .= '<i class="fa fa-'.$tmp->menu->icon.' fa-fw"></i> ';
            }
            $item .= $tmp->name;
            $item .= '</a></li>';
          }
          if (!array_key_exists($tmp->menu->order, $menuright)) {
            $menuright[$tmp->menu->order] = $item;
          }
        }
      }
    }
    
    echo '<div class="navbar navbar-inverse navbar-fixed-top display-xs display-sm display-md display-lg" role="navigation">';
    echo '<div class="navbar-header">';
    //echo '<a class="navbar-brand" href="'.URL_SEPARATOR.'">'.NAME.'</a>';
    echo '<a class="navbar-brand" href="'.URL_SEPARATOR.'"><img style="max-width:30px; margin-top: -7px;" src="../../'.BRAND.'"> '.NAME.'</a>';
    echo '</div>';
    // START RIGHT
    echo '<div class="navbar-right '.self::$devices.'">';
    echo '<ul class="nav navbar-nav">';
    // RENDER RIGHT
    ksort($menuright);
    foreach ($menuright as $item) {
      echo $item;
    }
    echo '</ul>';
    echo '</div>';
    // END RIGHT
    // START LEFT
    echo '<div class="navbar-left '.self::$devices.'" stlye="display: none!important;">';
    echo '<ul class="nav navbar-nav">';
    // RENDER LEFT
    ksort($menuleft);
    foreach ($menuleft as $item) {
      echo $item;
    }
    echo '<li><div style="width:10px"></div></li>';
    echo '</ul>';
    echo '</div>';
    // END LEFT
    echo '</div>';
    echo '<div class="modal" id="modal" tabindex="-1" role="dialog"><div class="modal-dialog"><div class="modal-content" id="modal-content"></div></div></div>';
    echo '<div class="container-fluid '.self::$devices.'">';
    echo '<div id="infobox" class="infobox">'.$infobox.'</div>';
  }
          
  public static function end()
  {
    echo '</div>';
    echo '<div class="container-fluid display-xs display-sm hidden-md hidden-lg">';
    echo '<div class="display-xs hidden-sm hidden-md hidden-lg not-supported">';
    echo '<div class="alert alert-danger"><b>Mobile devices are currently not supported.</b><br>Please use a larger screen environment.</div>';
    echo '</div>';
    echo '<div class="hidden-xs display-sm hidden-md hidden-lg not-supported">';
    echo '<div class="alert alert-danger"><b>Tablet devices are currently not supported.</b><br>Please use a larger screen environment.</div>';
    echo '</div>';
    echo '</div>';
    echo '<div class="footer navbar-fixed-bottom">';
    echo '<div class="container-fluid">';
    echo '<p class="text-muted"> <i id="loading" class="fa fa-spinner fa-spin hidden"></i> (c) '.date('Y', time()).' daeks - generated in '.number_format(microtime(true) - self::$time, 5).'s</p>';
    echo '</div>';
    echo '</div>';
    echo '</body>';
    echo '</html>';
  }
}

?>