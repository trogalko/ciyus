<?php
session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function go() {
  
  $_SESSION['DB-Pos']++;
  $pos = $_SESSION['DB-Pos']+0;
  $max = $_SESSION['DB-Max']+0;
  $sql = $_SESSION['DB-SQL' . $pos];
  $persen = ($max == 0)? 0 : number_format($pos/$max*100);

  if (!empty($sql)) {
    
    $r = _query($sql);
    
    echo "<p>Processing: Database Sisfokampus</p>
    <p>Position: <b>$pos/$max</b></p>
    <p><font size=+4>$persen %</font></p>";
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    echo "<p>Proses Import Database telah Selesai</p>";
    echo "<a href='install.php?instl=param&foc=frmKonf&step=3' target=_parent>Lanjutkan</a>";
  }
}
?>
