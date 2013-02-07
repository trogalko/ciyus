<?php
// Author: Emanuel Setio Dewo
// Sisfo Kampus 2006
// 22 Nov 2006

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Hitung SKS Mahasiswa");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table cellspacing=1 cellpadding=4>
  <form action='_proses_hitung_presensi_mhsw.php' method=POST>
  <input type=hidden name='gos' value='ProsesHitung'>
  <tr><td>Tahun Akademik</td>
    <td><input type=text name='tahun' value='$_SESSION[tahun]' size=6 maxlength=5></td></tr>
  <tr><td>Program Studi</td>
    <td><select name='prodi'>$optprodi</select></td></tr>
  <tr><td colspan=2><input type=submit name='Proses' value='Proses'></td></tr>
  </form></table></p>";
}
function ProsesHitung() {
  $whr = '';
  if (!empty($_SESSION['prodi'])) $whr = "and ProdiID='$_SESSION[prodi]' ";
  $s = "select KHSID, MhswID
    from khs
    where TahunID='$_SESSION[tahun]'
      $whr
    order by KHSID";
  $r = _query($s);
  $jml = _num_rows($r); $n = 0;
  echo "<hr><p>Jumlah data: <font size=+1>$jml</font></p>";
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['HTG-MhswID-'. $_SESSION['prodi'] . $n] = $w['MhswID'];
    $_SESSION['HTG-KHSID-' . $_SESSION['prodi'] . $n] = $w['KHSID'];
  }
  $_SESSION['HTG-Max-'. $_SESSION['prodi']] = $jml;
  $_SESSION['HTG-Pos-'. $_SESSION['prodi']] = 0;
  
  // IFRAME
  echo "<p><IFRAME src='_proses_hitung_presensi_mhsw.go.php?gos=hitungpresensi' frameborder=0 height=400 width=300>
  </IFRAME></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Hitung Presensi Mahasiswa");
TampilkanHeaderProses();
$gos();

?>
