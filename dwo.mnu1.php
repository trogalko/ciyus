<?php
// Author: Emanuel Setio Dewo
// 13 Desember 2005

function StartMenu($arrMenu) {
  echo "<Script Language='JavaScript1.2'><!--
    var pMenu = new PopupMenu('pMenu');
    with (pMenu) {
      startMenu('root', false, 0, 0, 28, hBar);
    ";
  for ($i =0; $i < sizeof($arrMenu); $i++) {
    echo "addItem('$arrMenu[$i]', '$arrMenu[$i]', 'sm:'); \n";
  }
}
function GetUserModul() {
  global $strCantQuery;
  $_LevelID = $_SESSION['_LevelID'];
  $_LoginID = $_SESSION['_LoginID'];
  $_arr = array();
  $strLevelID = '.'.$_LevelID.'.';
  $_sql = "select mg.MdlGrpID as GM
    from mdl m
    left outer join mdlgrp mg on m.MdlGrpID=mg.MdlGrpID
    where m.Web='Y' and LOCATE('$strLevelID', m.LevelID)>0 and m.NA='N'
    group by mg.Urutan";
  $_sqlx = "select mg.mdlgrp, m.Level 
    from usermodul um
    right join modul m on um.ModulID=m.ModulID
    where m.InMenu='Y' and um.UserID='$_LoginID' or LOCATE($_LevelID, m.Level) group by m.GroupModul";
  $_res = mysql_query($_sql) or die("Gagal: $_sql<br>".mysql_error());
  while ($w = mysql_fetch_array($_res)) {
    $_arr[] = $w['GM'];
  }
  //var_dump($_arr); exit;
  return $_arr;
}
function DisplayMenuItem($gm) {
  $_ggl = "<p>Gagal menginisialisasi menu</p><p>Failed to initialised menus</p>";
  $_Login = $_SESSION['_Login'];
  $_LevelID = $_SESSION['_LevelID'];
  
  $_snm = session_name(); $_sid = session_id();
  $_arr = array();
  $strLevel = ".$_LevelID.";

  // ambil default
	$_qy1 = "select m.*
	  from mdl m
	  where LOCATE('$strLevel', m.LevelID)>0 and m.Web='Y' and m.MdlGrpID='$gm' and m.NA='N'
	  order by m.Nama";
	$_qyx = "select md.* 
	  from modul md
	  where LOCATE('$_LevelID', md.Level)>0
	  and md.InMenu='Y'
	  and md.web='Y'
	  and md.GroupModul='$gm'
	  order by md.Modul";
	$_rs1 = mysql_query($_qy1) or die($_ggl . mysql_error());
	while ($_w1 = mysql_fetch_array($_rs1)) {
	  $_arr[] = "$_w1[Nama]->?mnux=$_w1[Script]&mdlid=$_w1[MdlID]&$_snm=$_sid";
	}
	sort($_arr);
        var_dump($_arr); exit;
	AddSubMenu($gm, $_arr);
  }
function AddSubMenu($Menu, $arr) {
  echo "startMenu('$Menu', true, 0, 20, 200, subM); \n";
  for ($i=0; $i < sizeof($arr); $i++) {
    $arrsub = explode('->', $arr[$i]);
    if (!isset($arrsub[2])) $tg = 22;
    else $tg = $arrsub[2] * 20;
    $link = ltrim($arrsub[1]);
    echo "addItem('$arrsub[0]', '$link', '', subM, $tg); \n";
  }
}
function EndMenu() {
  echo "}
  -->
  </script>";
}
function DisplayBasicMenu($action='?') {
  $snm = session_name(); $sid = session_id();
  $arr = array();
  $arr[] = "Logout->?slnt=loginprc&slntx=lout&mdlid=0";
  $arr[] = "Hal. Depan->?mnux=&mdlid=";
  $arr[] = "Preference->?mnux=usrpref&mdlid=";
  $arr[] = "Error & Bugs->?mnux=bugserror";
  AddSubMenu('Aku', $arr);
}
?>
