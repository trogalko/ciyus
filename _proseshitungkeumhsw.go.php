<?php
session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "mhswkeu.lib.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function prckeu() {
  $prodi = $_SESSION['prodi'];
  $_SESSION['HTG-Pos-'. $prodi]++;
  $pos = $_SESSION['HTG-Pos-'. $prodi];
  $max = $_SESSION['HTG-Max-'. $prodi];
  $MhswID = $_SESSION['HTG-MhswID-'. $prodi. $pos];
  $KHSID = $_SESSION['HTG-KHSID-' . $prodi. $pos];
  $persen = ($max == 0)? 0 : number_format($pos/$max*100);
  
  if (!empty($MhswID)) {
    echo "<p>Processing: <b>$MhswID</b></p>
    <p>Position: <b>$pos/$max</b></p>
    <p><font size=+4>$persen %</font></p>";
    // Hitung
    HitungBiayaBayarMhsw($MhswID, $KHSID);
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";
}

?>
