<?php
// Pemrosesan
// Author: Emanuel Setio Dewo
// 18 July 2006
// www.sisfokampus.net

session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function prckhs() {
  $prodi = $_SESSION['prodi'];
  $_SESSION['PRC-POS-'. $prodi]++;
  $pos = $_SESSION['PRC-POS-'. $prodi];
  $max = $_SESSION['PRC-Max-'. $prodi];
  $MhswID = $_SESSION['PRC-MhswID-'. $prodi. $pos];
  $ProgramID = $_SESSION['PRC-ProgramID-'. $prodi. $pos];
  $ProdiID = $_SESSION['PRC-ProdiID-'. $prodi. $pos];
  $persen = ($max == 0)? 0 : number_format($pos/$max*100);
  
  if (!empty($MhswID)) {
    echo "<p>Processing: <b>$MhswID</b></p>
    <p>Position: <b>$pos/$max</b> &raquo; <font size=+2>$persen %</font></p>";
    // Buat KHS
    $s = "select TahunID, sum(SKS) as TotalSKS, count(*) as JumlahMK 
      from krs 
      where MhswID='$MhswID' and StatusKRSID='A'
      group by TahunID";
    $r = _query($s); $n = 0;
    while ($w = _fetch_array($r)) {
      $n++;
      $ada = GetaField('khs', "MhswID='$MhswID' and TahunID", $w['TahunID'], "KHSID");
      $TotalSKS = $w['TotalSKS']+0;
      $JumlahMK = $w['JumlahMK']+0;
      $StatusMhswID = ($JumlahMK > 0)? 'A' : 'P';
      if (empty($ada)) {
        $s0 = "insert into khs
          (TahunID, KodeID, ProgramID, ProdiID, MhswID, Sesi,
          StatusMhswID, JumlahMK, TotalSKS,
          LoginBuat, TanggalBuat)
          values
          ('$w[TahunID]', 'UKRIDA', '$ProgramID', '$ProdiID', '$MhswID', $n,
          '$StatusMhswID', $JumlahMK, $TotalSKS,
          'BATCH PROCESSING', now())";
        $r0 = _query($s0);
        $ada['KHSID'] = GetLastID();
        $str = "<font size=+1>DIBUAT <font size=+1>$n</font></font>";
      }
      else {
        $s0 = "update khs set Sesi=$n, StatusMhswID='$StatusMhswID' where KHSID=$ada";
        $r0 = _query($s0);
        $str = "<font color=red>Updated <font size=+1>$n</font></font>";
      }
      // Perbaiki KRS
      $skrs = "update krs set KHSID=$ada[KHSID] where MhswID='$MhswID' and TahunID='$w[TahunID]' and KHSID=0";
      $rkrs = _query($skrs);
      $jkrs = _affected_rows($rkrs);
      echo "$w[TahunID] &raquo; $str &raquo; $jkrs<br />";
      // Jika Semester 1, maka buat data keuangan dummy:
      //if ($n == 1) BuatKeuDummy($MhswID, $w['TahunID']); 
    }
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";
  $_SESSION['ADPOS']++;
}
function BuatKeuDummy($MhswID, $TahunID) {
  $arr = array('1~Sumbangan Lab', '3~SPP Baru', '10~Character Building', '12~Jaket');
  for ($i = 0; $i < sizeof($arr); $i++) {
    $str = explode('~', $arr[$i]);
    $s = "insert into bipotmhsw (PMBMhswID, MhswID, TahunID,
      BIPOTNamaID, Nama, TrxID,
      Jumlah, Besar,
      LoginBuat, TanggalBuat)
      values (1, '$MhswID', '$TahunID',
      '$str[0]', '$str[1]', 1,
      1, 0,
      'BATCH', now())";
    $r = _query($s);
  }
}
?>
