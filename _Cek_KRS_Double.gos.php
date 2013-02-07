<?php
// Pemrosesan
// Author: Emanuel Setio Dewo
// 18 July 2006
// www.sisfokampus.net

session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";

//if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
prc();

function prc() {
  $_SESSION['CEK-KRS-POS']++;
  $pos = $_SESSION['CEK-KRS-POS'];
  $jml = $_SESSION['CEK-KRS-JML'];
  
  $MhswID = $_SESSION["CEK-KRS-MHSWID-$pos"];
  $ProdiID = $_SESSION["CEK-KRS-PRODIID-$pos"];
  $KHSID = $_SESSION["CEK-KRS-KHSID-$pos"];
  $persen = ($jml == 0)? 0 : number_format($pos/$jml*100);
  
  if (!empty($MhswID)) {
    echo "<p><font size=+3>$persen%</font><br />
    $pos &raquo; $MhswID</p>";
    CekDataKRS($MhswID, $ProdiID, $KHSID);
  }
  if ($pos <= $jml) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 10);</script>";
  }
  else {
    $dihapus = $_SESSION["CEK-KRS-DIHAPUS"];
    echo "<hr><p>Proses Selesai<br />
    Jumlah dihapus: <font size=+4>$dihapus</font></p>";
  }
}
function CekDataKRS($MhswID, $ProdiID, $KHSID) {
  $s = "select krs.KRSID, j.HariID, krs.MKID
    from krs
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.KHSID='$KHSID'
    order by j.HariID, krs.MKID";
  $r = _query($s);
  $sebel = '';
  while ($w = _fetch_array($r)) {
    $strpsn = '';
    $skrg = "$w[HariID]-$w[MKID]";
    if ($sebel == $skrg) {
      $strpsn = "<font size=+2 color=maroon>Dihapus</font>";
      $_SESSION["CEK-KRS-DIHAPUS"]++;
      $sx = "delete from krs where KRSID='$w[KRSID]' ";
      $rx = _query($sx);
    }
    echo "<li>$w[MKID] $skrg $strpsn</li>";
    $sebel = $skrg;
  }
  // Hitung KRS
  $jml = GetFields("krs left outer join jadwal j on krs.JadwalID=j.JadwalID", 
    "krs.StatusKRSID='A' and j.JenisJadwalID='K' and krs.KHSID", $KHSID, "sum(krs.SKS) as TotalSKS, count(*) as JumlahMK");
  $TotalSKS = $jml['TotalSKS']+0;
  $JumlahMK = $jml['JumlahMK']+0;
  $sr = "update khs set TotalSKS=$TotalSKS, JumlahMK=$JumlahMK where KHSID='$KHSID' ";
  $rr = _query($sr);
  echo "<p>Summary &raquo; Total SKS: <b>$TotalSKS</b>, Jumlah MK: <b>$JumlahMK</b></p>";
}
?>
