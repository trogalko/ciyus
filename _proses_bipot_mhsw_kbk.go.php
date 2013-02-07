<?php
session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
//include "krs.lib.php";
include_once "mhswkeu.sav.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function prosesbipot() {
  $prodi = $_SESSION['prodi'];
  $_SESSION['KBK-Pos-'. $prodi]++;
  $pos = $_SESSION['KBK-Pos-'. $prodi];
  $max = $_SESSION['KBK-Max-'. $prodi];
  $KHSID = $_SESSION['KBK-KHSID-'. $prodi. $pos];
  $MhswID = $_SESSION['KBK-MhswID-'. $prodi. $pos];
  $JUM = $_SESSION['KBK-JUM-' . $prodi. $pos];
  $persen = ($max == 0)? 0 : number_format($pos/$max*100);
  
  $_REQUEST['mhswid'] = $MhswID;
  $_REQUEST['khsid'] = $KHSID;
  $_REQUEST['pmbmhswid'] = 1;  
  
  if (!empty($MhswID)) {
    PrcBIPOTSesi();
    echo "<p>Processing: <b>$_REQUEST[mhswid]</b></p>
    <p>Position: <b>$pos/$max</b></p>
    <p>Jumlah Mahasiswa: <b>$_REQUEST[khsid]</b></p>
    <p><font size=+4>$persen %</font></p>";
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";
}
?>
