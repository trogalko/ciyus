<?php
session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "krs.lib.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function FixMKID() {
  $_SESSION["FIX-POS"]++;
  $pos = $_SESSION["FIX-POS"];
  $max = $_SESSION["FIX-MAX"];
  
  $_sks_mk = ", SKS=$w[SKS]";
  $MKID = $_SESSION["FIX-MKID-$pos"];
  $MKKode = $_SESSION["FIX-MKKode-$pos"];
  $SKS = $_SESSION["FIX-SKS-$pos"];
  
  // HARAP DIPERHATIKAN TABEL YG AKAN DIUPDATE: _KRS ATAU KRS?
  $s2 = "update krs set MKID='$MKID', SKS='$SKS'
      where MKKode='$MKKode' and MKID=0";
  $r2 = _query($s2);
  $_jml = _affected_rows($r2);
  $persen = ($max > 0)? $pos / $max * 100 : 0;
  $_persen = number_format($persen, 2);
  echo "<h1>$_persen</h1> 
    <p>$s2</p>
    <p>Efektif: <font size=+1>$_jml</font></p>";
  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 0);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";
}
?>
