<?php
// Author: Emanuel Setio Dewo
// 05 Okt 2006

// Perbaiki MKID di tabel _KRS

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Total KHS Mahasiswa");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='gos' value='ProsesMK'>
  <tr><td class=inp>Tahun</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program Studi</td>
    <td><select name='prodi'>$optprodi</select>
    <input type=submit name='Proses' value='Proses'></td>
  </form></table></p>";
}
function ProsesMK() {
  $s = "select *
    from mk
    where ProdiID='$_SESSION[prodi]'
    order by MKID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["FIX-MKID-$n"] = $w['MKID'];
    $_SESSION["FIX-MKKode-$n"] = $w['MKKode'];
    $_SESSION["FIX-SKS-$n"] = $w['SKS'];
  }
  $_SESSION["FIX-POS"] = 0;
  $_SESSION["FIX-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>$n</font> data.</p>";
  echo "<p><IFRAME src='_fix_mkid_di_krs.go.php?gos=FixMKID' frameborder=0 height=400 width=300>
  </IFRAME></p>";
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Perbaiki MKID di _KRS");
TampilkanHeaderProses();
if (!empty($prodi) && !empty($prodi)) $gos();
?>
