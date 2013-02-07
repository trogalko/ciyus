<?php
// Author: Emanuel Setio Dewo
// 22 Sept 2006

include_once "sisfokampus.php";
HeaderSisfoKampus("Import Pembayaran dari Tabel _pembayaran");

// *** Functions ***
function DaftarJadwal() {
  $s = "select JadwalID, MKKode, Nama, NamaKelas
    from jadwal 
    where INSTR(ProdiID, '.$_SESSION[prodi].')>0
      and TahunID='$_SESSION[tahun]'
    order by MKKode";
  $r = _query($s);
  echo "<p>Berikut adalah daftar jadwal tahun: $_SESSION[tahun], Prodi: $_SESSION[prodi]</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $jml = GetaField('krs', "StatusKRSID='A' and JadwalID", $w['JadwalID'], "count(*)")+0;
    $jmlkrs = GetaField('krstemp', "StatusKRSID='A' and JadwalID", $w['JadwalID'], "Count(*)")+0;
    $sx = "update jadwal set JumlahMhsw=$jml, JumlahMhswKRS=$jmlkrs where JadwalID=$w[JadwalID] ";
    $rx = _query($sx);
    echo "<li>$w[JadwalID] &raquo; $w[MKKode] $w[Nama] $w[NamaKelas] <font size=+1>$jml, $jmlkrs</font></li>";
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
