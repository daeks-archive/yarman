<?php

  class panel
  {
    
    public static function start($title = '', $type = 'default')
    {
      echo '<div class="panel panel-'.$type.'">';
      echo '<div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div>';
      echo '<div class="panel-body">';
    }
            
    public static function end()
    {
      echo '</div>';
      echo '</div>';
    }
    
  }

?>