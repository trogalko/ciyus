<?php
// Author: Emanuel Setio Dewo
// 25 Sept 2006
// www.sisfokampus.net

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Import Pembayaran dari Tabel _pembayaran");

// *** Functions ***
function TampilkanTahun() {
  TampilkanJudul("Proses Hitung Jumlah MK & SKS Mahasiswa");
  echo "<p><form action='?' method=POST>
  <input type=hidden name='gos' value='Hitung'>
  Tahun: <input type=text name='tahun' value='$_SESSION[tahun]'>
  <input type=submit name='Proses' value='Proses Hitung'>
  </form></p>";
}
function Hitung() {
  $s = "select TahunID, KHSID, MhswID, StatusMhswID
    from khs
    where TahunID='$_SESSION[tahun]'
    and ProdiID = '10'
    order by MhswID";
  $r = _query($s);
  $jml = _num_rows($r); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['HTG-KHSID-'.$n] = $w['KHSID'];
    $_SESSION['HTG-MhswID-'.$n] = $w['MhswID'];
    $_SESSION['HTG-Status-'.$n] = $w['StatusMhswID'];
  }
  $_SESSION['HTG-TOTAL'] = $jml;
  $_SESSION['HTG-POS'] = 0;
  $_jml = number_format($jml);
  echo "<p>Ada <font size=+1>$_jml</font> data.</p>";
  
  echo "<p><IFRAME SRC='_proses_hitung_jumlah_mk_sks_mhsw.php?gos=Hitung1' frameborder=0 height=400 width=100%>
  </IFRAME></p>";
}
function Hitung1() {
  $_SESSION['HTG-POS']++;
  $pos = $_SESSION['HTG-POS'];
  $max = $_SESSION['HTG-TOTAL'];
  $KHSID = $_SESSION['HTG-KHSID-'.$pos];
  $MhswID = $_SESSION['HTG-MhswID-'.$pos];
  $Status = $_SESSION['HTG-Status-'.$pos];
  $arr = GetFields("krs", "StatusKRSID='A' and KHSID", $KHSID, "count(*) as JumlahMK, sum(SKS) as TotalSKS");
  $persen = number_format($pos/$_SESSION['HTG-TOTAL'] * 100, 2);
  // Simpan data
  $JumlahMK = $arr['JumlahMK']+0;
  $TotalSKS = $arr['TotalSKS']+0;
  if (($Status =='P') && ($JumlahMK > 0)) {
    $_Status = ", StatusMhswID='A' ";
  }
  elseif (($Status == 'A') && ($JumlahMK == 0)) {
    $_Status = ", StatusMhswID='P' ";
  }
  else $_Status = '';
  $s = "update khs set JumlahMK=$JumlahMK, TotalSKS=$TotalSKS $_Status
    where KHSID=$KHSID";
  $r = _query($s);
  echo "<p>$persen %</p>
    <p>$pos. $MhswID ($KHSID) [$Status] &raquo; $arr[JumlahMK] ($arr[TotalSKS])</p>
    <p><pre>$s</pre></p>";
  
  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Selesai. <a href='?'>Kembali</a></p>";
}

// *** Parameters ***
$arrID['Kode'] = 'UKRIDA';
$_SESSION['KodeID'] = 'UKRIDA';
$_SESSION['_ProdiID'] = '10,11,21,22,24,31,32,41,50,60';
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? "TampilkanTahun" : $_REQUEST['gos'];

// *** Main ***
if (!empty($gos)) $gos();
?>
