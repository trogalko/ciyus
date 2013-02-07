<?php
// Author: Emanuel Setio Dewo
// 19 May 2006 (Pengganti prc.ipk.batch.x.php
// http://www.sisfokampus.net

session_start();
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

function PRCMUNDUR() {
  echo "<body bgcolor=#EEFFFF>";
  $_SESSION['HM-POS']++;
  $pos = $_SESSION['HM-POS'];
  $max = $_SESSION['HM-JML'];
  $mhswid = $_SESSION['HM-MhswID-'.$pos];
  $tahun1 = $_SESSION['HM-tahun1'];
  echo "Processing: <font size=+1>$mhswid</font><hr size=1 color=silver>";
  // Ambil data KHS Mhsw
  $s = "select KHSID, TahunID
    from khs
    where MhswID='$mhswid'
      and TahunID = '$tahun1'
    order by TahunID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arr = GetFields('krsprc', "GradeNilai not in ('', '-') and TahunID <= '$tahun1' and MhswID", $mhswid, "sum(SKS) as TSKS, sum(SKS * BobotNilai) as KXN");
    $arr['TSKS'] += 0; 
    $si = "update mhsw set TotalSKS_='$arr[TSKS]' where MhswID='$mhswid'";
    $ri = _query($si);
    
    echo "Tahun: $w[TahunID], Jumlah SKS : $arr[TSKS] <br />";
  }
  // refresh page
  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<p>Proses Hitung IPK Mundur telah <font size=+1>Selesai</font></p>";
}
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
include_once "disconnectdb.php";
?>
