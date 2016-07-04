<?php
  // Toolbox to hold basic functions that can be recalled from various sources
  class xTools{
    function jsonSheet($sheet){
      $json = file_get_contents($sheet);
      $json = json_decode($json,true);
      foreach ($json['feed']['entry'] as $k => $v) {
        if (stripos($v['gsx$'.$x]['$t'], $key) !== false) {
          $r = trim($v['gsx$'.$x]['$t']);
          $t = true;
        }
      }
    }
    // Get our switch
    function get($x,$y=null,$z=null) {
      $dir = $_SERVER['DOCUMENT_ROOT'].'/lp/includes/templates/';
      // Custom switchs, defaults to pulling the first line in the given .//html/$x.html file
      switch ($x) {
        // DON'T EDIT.
        // Simple get->('javascript') call to place all JS in global js file...
        // Individual js should go in ./js/ or on the page itself.
        // All Global JS is managed via Tag Manger. Not here, not in the html file, so go Here:
        case 'javascript':
        case 'js':
          // TODO: Make logic to know which ID to use.
          $gtmid = $_SESSION['google_tag_manager_id'] = isset($_SESSION['google_tag_manager_id'])
            ? $_SESSION['google_tag_manager_id'] : GTM_ID;
          $return = str_replace('{$GTM_ID}', $gtmid, file_get_contents($dir."google-tag-manager.html") );
        break;

        // Pulls the full testimonials file.
        // TODO: make more intuitive for entering quick multiple entries.
        case 'testimonials':
          $return = file_get_contents("./html/$x.html");
        break;

        // get->('trustpilot','list', $options)
        case 'trustpilot':
          include("../../includes/trustpilot.inc");
          $t = $trustpilot;
          $y = array_merge($t['default'], $t['library'][$y]);
          if( is_array($z) )
            $y = array_merge($y, $z);
          $z = "";
          foreach ($y as $key => $value) {
            $z .= "data-$key='$value' ";
          }
          $return = str_replace('{$z}', $z, $t['html']);
        break;

        default:
          $return = $this->getKeyFile($x);
        break;
      }
      return $return;
    }
    function __indexArray($a, $index = 'id'){
      foreach ($a as $row => $p) {
        if (is_numeric($row)){
          $array[$p[$index]] = $p;
        }
      }
      return $array;
    }
    public function getSheetArray($url,$i=null){
      $sheet = $this->getSpreadSheet( $url );
      return $this->__indexArray($sheet);
    }
    function __getSpreadSheetKey($url, $type=null){
      switch ($type) {
        default:
          $url = parse_url($url);
          $url = str_replace("/edit", '', $url);
          $url = str_replace("/spreadsheets/d/", '', $url['path']);
        break;
      }
      return $url;
    }
    function getSpreadSheet($google_sheet_id_key=null){
      $google_sheet_id_key = $this->__getSpreadSheetKey( $google_sheet_id_key );
      $cache               = XO_DIR."/cache/$google_sheet_id_key.json";
      if($google_sheet_id_key){
        if(!file_exists($cache)
          || (time() - filemtime($cache) >= 60 * 60 * 72)
          || (isset($_GET['x4']) && $_GET['x4'] == 'pull' )
        ){
          $url      = 'http://spreadsheets.google.com/feeds/list/'.$google_sheet_id_key.'/od6/public/values?alt=json';
          $file     = file_get_contents($url);
          if($file){
            file_put_contents($cache, $file);                                         # Cache the contents into our file.
          }else{
            $file   = file_get_contents($cache);                                      # Read from Cache
          }
        }else{
          $file     = file_get_contents($cache);
        }
        $json       = json_decode($file,true);
        foreach($json['feed']['entry'] as $key => $value) {
          foreach ($value as $k => $v) {
            if( stripos($k,'gsx$') !== false ){
              $k = str_replace('gsx$', '', $k);
              $t = $v['$t'];
              $sheet[$key][$k] = $t;
              $sheet[$k] = $value['gsx$'.$k]['$t'] ;
              $sheet[$key][$k] = $t;
            }
          }
        }
        return $sheet;
      }
    }
    // By defining ?key=, this pull the first line that matches the key
    function getKeyFile($x,$t=false){
      $key  = isset($_GET['key']) ? $_GET['key'] : false;
      $x    = str_replace('_', '', $x);
      $json = file_get_contents('https://spreadsheets.google.com/feeds/list/1Sz9ai8EzNzfs_WMh1AVqgpjefj_3_KFEhBFNhds_uyo/od6/public/values?alt=json');
      $json = json_decode($json,true);
      foreach ($json['feed']['entry'] as $k => $v) {
        if (stripos($v['gsx$'.$x]['$t'], $key) !== false) {
            $r = trim($v['gsx$'.$x]['$t']);
            $t = true;
        }
      }
      if(!$t)
        $r = $json['feed']['entry'][0]['gsx$'.$x]['$t'];
      return $r;
    }
  }
