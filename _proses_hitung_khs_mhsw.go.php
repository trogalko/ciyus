<?php
session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "krs.lib.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function hitungkhs() {
  $prodi = $_SESSION['prodi'];
  $_SESSION['TES-Pos-'. $prodi]++;
  $pos = $_SESSION['TES-Pos-'. $prodi];
  $max = $_SESSION['TES-Max-'. $prodi];
  $MhswID = $_SESSION['TES-MhswID-'. $prodi. $pos];
  $JUM = $_SESSION['TES-JUM-' . $prodi. $pos];
  $persen = ($max == 0)? 0 : number_format($pos/$max*100);
  
  if (!empty($MhswID)) {

    echo "<p>Processing: <b>$MhswID</b></p>
    <p>Position: <b>$pos/$max</b></p>
    <p>Jumlah KHS: <b>$JUM</b></p>
    <p><font size=+4>$persen %</font></p>";
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";
}

function deldata($mhsw){
  $s = "delete from khs where MhswID = '$mhsw' and TahunID <> '20071'";
  $up = "update khs set sesi = 1 where MhswID = '$mhsw' and TahunID = '20071'";
  
  $r = _query($s);
  $rup = _query($up);
}

?>
