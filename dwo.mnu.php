<?php
function GetUserModule(){
    global $strCantQuery;
    $_LevelID = $_SESSION['_LevelID'];
    $_LoginID = $_SESSION['_LoginID'];
    $_arr = array();
    $strLevelID = '.'.$_LevelID.'.';
    $_sql = "select mg.MdlGrpID as GM
        from mdl m
        left outer join mdlgrp mg on m.MdlGrpID=mg.MdlGrpID
        where m.Web='Y' and LOCATE('$strLevelID', m.LevelID)>0 and m.NA='N' and mg.NA='N'
        group by mg.Urutan";
    $_sqlx = "select mg.mdlgrp, m.Level 
        from usermodul um
        right join modul m on um.ModulID=m.ModulID
        where m.InMenu='Y' and um.UserID='$_LoginID' or LOCATE($_LevelID, m.Level) group by m.GroupModul";
    $_res = mysql_query($_sql) or die("Gagal: $_sql<br>".mysql_error());
    while ($w = mysql_fetch_array($_res)) {
        $_arr[] = $w['GM'];
    }
    return $_arr;
}

function GetModule($gm) {
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
	
        echo "<div id=$gm class='dropmenudiv_a'>";
        while ($_w1 = mysql_fetch_array($_rs1)) {
            echo "<a href=\"?mnux=$_w1[Script]&mdlid=$_w1[MdlID]&$_snm=$_sid\">$_w1[Nama]</a>\n";
	}
        echo "</div>";
}

function StartMenu($arrMdl) {
    echo "<div id='colortab' class='ddcolortabs'>";
    echo "<ul>";
    foreach ($arrMdl as $menu) {
        echo "<li class=round><a title=$menu rel=$menu><span>$menu</span></a></li>";
    }
    echo "</div>";
    echo "</ul>";
    echo "<div class=\"ddcolortabsline\">&nbsp;</div>";
}

function RunMenu(){
    echo "<script type=\"text/javascript\">";
    echo "tabdropdown.init(\"colortab\", 'auto')";
    echo "</script>";
}
?>