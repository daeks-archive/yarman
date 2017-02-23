<?php
    
  class utils
  {
  
    public static function msort($array, $cols)
    {
      $colarr = array();
      foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) {
          $colarr[$col]['_'.$k] = strtolower($row[$col]);
        }
      }
      $eval = 'array_multisort(';
      foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
      }
      $eval = substr($eval, 0, -1).');';
      eval($eval);
      $ret = array();
      foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
          $k = substr($k, 1);
          if (!isset($ret[$k])) {
            $ret[$k] = $array[$k];
          }
          $ret[$k][$col] = $array[$k][$col];
        }
      }
      return $ret;
    }
        
  }
    
?>