<?php
// Author: Emanuel Setio Dewo
// 13 Desember 2005


  $sid = session_id();
  $agent = $_SERVER['HTTP_USER_AGENT'];
  //if (strpos($agent, 'Win') === false) $strpos = 'absolute'; else $strpos = 'relative';
  //echo "<div style='position: $strpos; height:18; border: 1px silver solid; background-color: #DEFDEF'>";
  echo "<div class=menudropdown> \n";
  //include_once "menu.css";
  //include_once "start.menu.js";
?>

<SCRIPT TYPE="text/javascript" SRC="start.menu.js" CHARSET="ISO-8859-1">
</SCRIPT>
<?
  include_once "dwo.mnu.php";
  // Display All Menu
  $_modul = array();
  $_modul = GetUserModul();
  //$_modul[] = 'Tema';
  $_modul[] = 'Aku';
  StartMenu($_modul);
  // Buat menu utama

  for ($i=0; $i < count($_modul)-1; $i++) {
    DisplayMenuItem($_modul[$i]);
  }

  //DisplayMenuItem();
  
  //DisplayThemeMenu('sysfo.php');
  DisplayBasicMenu('?');
  EndMenu();
?>

<script type="text/javascript" src="end.menu.js" charset="ISO-8859-1">
</script>
</div>
