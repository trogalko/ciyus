<?php
// Author: Emanuel Setio Dewo
// 25 Nov 2006
// www.sisfokampus.net

session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "krs.lib.php";
ProsesKeluar();

// *** functions ***
function ProsesKeluar() {
  $_SESSION["KEL-POS"]++;
  $pos = $_SESSION["KEL-POS"];
  $max = $_SESSION["KEL-MAX"];
  
  if (!empty($_SESSION["KEL-MhswID-$pos"])) {
    $MhswID = $_SESSION["KEL-MhswID-$pos"];
    $StatusMhswID = $_SESSION["KEL-StatusMhswID-$pos"];
    $SKKeluar = $_SESSION["KEL-SKKeluar-$pos"];
    $TglSKKeluar = $_SESSION["KEL-TglSKKeluar-$pos"];
    $Tahun = $_SESSION["KEL-Tahun-$pos"];
    $s = "update mhsw
      set StatusMhswID='$StatusMhswID',
        SKKeluar='$SKKeluar',
        TglSKKeluar='$TglSKKeluar',
        TahunKeluar='$Tahun'
      where MhswID='$MhswID' ";
    $r = _query($s);
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
?>
