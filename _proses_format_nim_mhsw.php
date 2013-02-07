<?php

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Membalik NIM Mahasiswa");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table cellspacing=1 cellpadding=4>
  <form action='_proses_format_nim_mhsw.php' method=POST>
  <input type=hidden name='gos' value='ProsesFormat'>
  <tr><td>Angkatan</td>
    <td><input type=text name='tahun' value='$_SESSION[tahun1]' size=6 maxlength=4></td></tr>
  <tr><td>Program Studi</td>
    <td><select name='prodi'>$optprodi</select></td></tr>
  <tr><td colspan=2><input type=submit name='Proses' value='Proses'></td></tr>
  </form></table></p>";
}
function ProsesFormat() {
  $whr = '';
  if (!empty($_SESSION['prodi'])) $whr = "and ProdiID='$_SESSION[prodi]' ";
  $s = "select MhswID, ProdiID from mhsw 
        where left(MhswID, 4) = '$_SESSION[tahun1]'
        $whr
        order by MhswID ASC";
        
  $r = _query($s);
  $jml = _num_rows($r); $n = 0;
  echo "<hr><p>Jumlah data: <font size=+1>$jml</font></p>";
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['FRMT-MhswID-'. $w['prodi'] . $n] = $w['MhswID'];
  }
  $_SESSION['FRMT-Max-'. $w['prodi']] = $jml;
  $_SESSION['FRMT-Pos-'. $w['prodi']] = 0;
  
  // IFRAME
  echo "<p bgcolor=#ff77ff><IFRAME src='_proses_format_nim_mhsw.go.php?gos=formatnim' frameborder=0 height=400 width=600>
  </IFRAME></p>";
}

// *** Parameters ***
$tahun1 = GetSetVar('tahun1', 2007);
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Format NIM Mahasiswa");
TampilkanHeaderProses();
$gos();
?>
