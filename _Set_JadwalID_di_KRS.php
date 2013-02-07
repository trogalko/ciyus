<?php
// Author: Emanuel Setio Dewo
// 2006-09-16

include_once "sisfokampus.php";
HeaderSisfoKampus("Set KHSID di KRS");

// *** Functions ***
function TampilkanPesan() {
  echo "<p>Script ini akan mengeset field JadwalID di tabel _krs.
  Setelah di set tabel _krs siap untuk diexport ke tabel krs.</p>
  
  <p><form action='?'>
  <input type=hidden name='gos' value='ProsesKHSID'>
  Tahun: <input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
  <input type=submit name='Proses' value='Proses'>
  </form>";
}
function ProsesKHSID() {
//
// *** Hilangkan INSTR(...) jika semua data!!!
//
  $prodi = '10';
  $s = "select JadwalID, MKID, MKKode, NamaKelas, SKS, ProdiID, ProgramID
    from jadwal
    where TahunID='$_SESSION[tahun]' and JenisJadwalID='K'
      and INSTR(ProgramID, '.REG.') > 0
      and INSTR(ProdiID, '.$prodi.') > 0
    order by HariID";
  $r = _query($s);
  echo "<p>Berikut adalah daftar mhsw yg diset.</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    //, MKID='$w[MKID]', SKS='$w[SKS]'
    $prodi = TRIM($w['ProdiID'], '.');
    $s1 = "update krs 
      set JadwalID='$w[JadwalID]'  
      where TahunID='$_SESSION[tahun]' 
        and MKKode='$w[MKKode]'
        and Catatan='$w[NamaKelas]'
        and JadwalID=0
        and LEFT(MhswID, 2) ='$prodi'
      ";
    //echo "<pre>$s1</pre>";
    $r1 = _query($s1);
    $kena = _affected_rows($r1);
    echo "<li>$w[JadwalID] &raquo; $kena</li>";
  }
  echo "</ol>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun', '20061');
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Set JadwalID, MKID, SKS di tabel _krs");
$gos();

?>
