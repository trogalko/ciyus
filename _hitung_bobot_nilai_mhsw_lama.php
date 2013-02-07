<?php
// Author: Emanuel Setio Dewo
// 22 Sept 2006

include_once "sisfokampus.php";
HeaderSisfoKampus("Mengecek Pembayaran Bahasa Inggris");

// *** Functions ***
function DaftarJadwal() {
  
  $s = "select KRSID, GradeNilai from krs where
      MhswID = '241996029'
      order by krs.TahunID";
      
  $r = _query($s);
  echo "<p>Berikut adalah daftar jadwal tahun: $_SESSION[tahun], Prodi: $_SESSION[prodi]</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $Bobot = GetaField("Nilai", "Nama", $w['GradeNilai'], "Bobot");
    $sx = "update krs set BobotNilai=$Bobot where KRSID=$w[KRSID] ";
    $rx = _query($sx);
  }
  echo "</ol>";
}

// *** Parameters ***
$arrID['Kode'] = 'UKRIDA';
$_SESSION['KodeID'] = 'UKRIDA';
$_SESSION['_ProdiID'] = '10,21,22,24,31,32,41,50,99';
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Import Pembayaran dari Tabel _pembayaran");
TampilkanTahunProdiProgram('', 'DaftarJadwal');
if (!empty($gos)) $gos();
$_SESSION['_ProdiID'] = '';
?>
