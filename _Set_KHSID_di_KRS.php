<?php
// Author: Emanuel Setio Dewo
// 2006-09-16

include_once "sisfokampus.php";
HeaderSisfoKampus("Set KHSID di KRS");

// *** Functions ***
function TampilkanPesan() {
  echo "<p>Script ini akan mengeset field KHSID di tabel _krs.
  Setelah di set tabel _krs siap untuk diexport ke tabel krs.</p>
  
  <p><form action='?'>
  <input type=hidden name='gos' value='ProsesKHSID'>
  Tahun: <input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
  <input type=submit name='Proses' value='Proses'>
  </form>";
}
function ProsesKHSID() {
//
// Hilangkan filter ProdiID='' jika ingin semua data!!!
//
  $s = "select KHSID, MhswID
    from khs
    where TahunID='$_SESSION[tahun]'
      and ProdiID in ('10', '11')
    order by MhswID";
  $r = _query($s);
  echo "<p>Berikut adalah daftar mhsw yg diset.</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $s1 = "update _krs 
      set KHSID='$w[KHSID]' 
      where TahunID='$_SESSION[tahun]' 
        and KHSID=0
        and MhswID='$w[MhswID]' ";
    $r1 = _query($s1);
    $kena = _affected_rows($r1);
    echo "<li>$w[MhswID] &raquo; $kena</li>";
  }
  echo "</ol>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun', '20061');
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Set KHSID di tabel _krs");
$gos();

?>
