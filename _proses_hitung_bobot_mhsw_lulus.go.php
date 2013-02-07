<?php
// Author: Sugeng

session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "krs.lib.php";
ProsesHitungBobot();

// *** functions ***
function ProsesHitungBobot() {
  $_SESSION["POS"]++;
  $pos = $_SESSION["POS"];
  $max = $_SESSION["MAX"];
  
  if (!empty($_SESSION["MhswID-$pos"])) {
    $MhswID = $_SESSION["MhswID-$pos"];
    GetKRSnya($MhswID);
    $persen = ($max == 0)? "0" : number_format($pos/$max*100, 2);
    echo "<p>Progress: <font size=+4>$persen</font> %<br />
    Processing: <font size=+1>$MhswID</font>
    <hr size=1 color=silver>
    <pre>$s</pre>";
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 0);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";

}

function GetKRSnya($MhswID){
  $s = "select KRSID, GradeNilai from krs
        where MhswID = '$MhswID'";
  $r = _query($s);
  
  while ($w = _fetch_array($r)){
    $Bobot = GetaField('nilai', "Nama", $w['GradeNilai'], "Bobot");
    
    $s0 = "update krs set BobotNilai = '$Bobot' where KRSID = '$w[KRSID]'";
    $r0 = _query($s0);
    
  }
}
?>
