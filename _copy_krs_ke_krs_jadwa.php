<?php
// Author: Emanuel Setio Dewo
// 26 Sept 2006
// www.sisfokampus.net

// Digunakan untuk menyalin semua KRS dari sebuah jadwal (Kuliah) ke jadwal lain (Responsi)

include_once "sisfokampus.php";
HeaderSisfoKampus("Import Data KRS dari Jadwal ke Jadwal Lain");

// *** Functions ***
function TampilkanHeaderJadwal() {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='gos' value='ProsesCopyKRS'>
  <tr><td class=inp>ID Jadwal 1</td><td class=ul><input type=text name='_JadwalID1' value='$_SESSION[_JadwalID1]' size=5 maxlength=10></td></tr>
  <tr><td class=inp>ID Jadwal 2</td><td class=ul><input type=text name='_JadwalID2' value='$_SESSION[_JadwalID2]' size=5 maxlength=10></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'></td></tr>
  </form></table></p>";
}
function ProsesCopyKRS() {
  $_JadwalID1 = $_REQUEST['_JadwalID1'];
  $_JadwalID2 = $_REQUEST['_JadwalID2'];
  $jdwl1 = GetFields('jadwal', 'JadwalID', $_JadwalID1, '*');
  $jdwl2 = GetFields('jadwal', 'JadwalID', $_JadwalID2, '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='gos' value='ProsesCopyKRS1'>
  <input type=hidden name='_JadwalID1' value='$_JadwalID1'>
  <input type=hidden name='_JadwalID2' value='$_JadwalID2'>
  <tr><td class=ttl colspan=2>KRS dari Jadwal yg Akan Disalin</td></tr>
  <tr><td class=inp>ID</td><td class=ul>$_JadwalID1</td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl1[MKKode] - $jdwl1[Nama] ($jdwl1[SKS])</td></tr>
  <tr><td class=inp>Kelas</td><td class=ul>$jdwl1[NamaKelas] ($jdwl1[JenisJadwalID])</td></tr>
  <tr><td class=inp>Jadwal</td><td class=ul>$jdwl1[HariID], $jdwl1[JamMulai]-$jdwl1[JamSelesai]</td></tr>
  <tr><td class=inp>Jumlah KRS</td><td class=ul>$jdwl1[JumlahMhsw]</td></tr>
  
  <tr><td class=ttl colspan=2>Disalin ke Jadwal</td></tr>
  <tr><td class=inp>ID</td><td class=ul>$_JadwalID2</td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl2[MKKode] - $jdwl2[Nama] ($jdwl2[SKS])</td></tr>
  <tr><td class=inp>Kelas</td><td class=ul>$jdwl2[NamaKelas] ($jdwl2[JenisJadwalID])</td></tr>
  <tr><td class=inp>Jadwal</td><td class=ul>$jdwl2[HariID], $jdwl2[JamMulai]-$jdwl2[JamSelesai]</td></tr>
  <tr><td class=inp>Jumlah KRS</td><td class=ul>$jdwl2[JumlahMhsw]</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Proses' value='Proses Copy'></td></tr>
  </form>
  </table></p>";
}
function ProsesCopyKRS1() {
  $_JadwalID1 = $_REQUEST['_JadwalID1'];
  $_JadwalID2 = $_REQUEST['_JadwalID2'];
  $jdwl2 = GetFields('jadwal', 'JadwalID', $_JadwalID2, '*');
  $s = "select *
    from krs
    where JadwalID='$_JadwalID1'
    order by MhswID";
  $r = _query($s); $n = 0;
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $ada = GetaField("krs", "MhswID='$w[MhswID]' and JadwalID", $_JadwalID2, 'KRSID');
    // Jika belum terdaftar, maka daftarkan
    if (empty($ada)) {
      $n++;
      $str = "insert into krs
        (KHSID, MhswID, TahunID, JadwalID,
        MKID, MKKode, SKS, HargaStandar, Harga,
        Catatan, LoginBuat, TanggalBuat)
        values ($w[KHSID], '$w[MhswID]', '$w[TahunID]', $_JadwalID2,
        '$jdwl2[MKID]', '$jdwl2[MKKode]', '$jdwl2[SKS]', 
        '$jdwl2[HargaStandar]', '$jdwl2[Harga]',
        'COPY DARI JadwalID: $_JadwalID1', 'DEWO', now()
        )";
      $rstr = _query($str);
    }
    // Tidak terdaftar
    else $str = "<font color=RED>Sudah</font>";
    echo "<li>$w[MhswID] &raquo; $str</li>";
  }
  // Hitung Jumlah Mhsw di Jadwal2
  $jml = GetaField("krs", "JadwalID", $_JadwalID2, "count(*)")+0;
  $sx = "update jadwal set JumlahMhsw=$jml where JadwalID=$_JadwalID2";
  $rx = _query($sx);
  echo "Telah tercopy <font size=+2>$n</font> KRS</td></tr>";
}

// *** Parameters ***
$_JadwalID1 = GetSetVar('_JadwalID1');
$_JadwalID2 = GetSetVar('_JadwalID2');
$gos = (empty($_REQUEST['gos']))? "TampilkanHeaderJadwal" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Copy KRS dari Jadwal1 ke Jadwal2");
$gos();
?>
