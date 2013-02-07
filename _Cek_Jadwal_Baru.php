<?php
// Author: Emanuel Setio Dewo

function TampilkanPesan() {
  echo "<p>Script ini akan melakukan pengecekan data jadwal baru pada tabel <b>_jadwal</b>:
  Apakah data jadwal tersebut sudah ada di tabel <b>jadwal</b> atau belum.
  Jika sudah ada, maka data di tabel _jadwal tersebut akan dihapus.</p>
  <p>Setelah data di tabel <b>_jadwal</b> bersih dari duplikasi di <b>jadwal</b>, 
  maka Anda dapat meng-export data dari _jadwal ke mhsw dengan sql.</p>
  
  <p>Tekan tombol berikut ini untuk memproses data: 
  <input type=button name='Proses' value='Proses Data _jadwal' onClick=\"location='_Cek_Jadwal_Baru.php?gos=ProsesJadwalBaru'\"></p>";
}
function ProsesJadwalBaru() {
  $s = "select TahunID, JadwalID, MKID, MKKode, HariID, NamaKelas, JenisJadwalID, ProgramID, ProdiID, DosenID
    from _jadwal
    order by ProgramID, ProdiID, MKKode";
  $r = _query($s); $jml = _num_rows($r);
  echo "<p>Berikut adalah daftar Jadwal yang dihapus dari tabel <b>_mhsw</b> dari total <font size=+1>$jml</font>.</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $ada = GetaField('jadwal', 
      "TahunID='$w[TahunID]'
      and ProgramID='$w[ProgramID]'
      and ProdiID='$w[ProdiID]'
      and MKKode='$w[MKKode]'
      and HariID='$w[HariID]'
      and NamaKelas='$w[NamaKelas]'
      and JenisJadwalID",
      $w['JenisJadwalID'], 'JadwalID');
    if (!empty($ada)) 
    {
      echo "<li>$w[ProdiID] &raquo; $ada. $w[MKKode] - $w[ProgramID]-$w[ProdiID] ($w[JadwalID])
      </li>";
      $s1 = "delete from _jadwal where JadwalID='$w[JadwalID]' ";
      $r1 = _query($s1);
    }
    else {
      // yg tidak dihapus:
      //echo "<li>$w[ProdiID] &raquo; $w[MKKode] </li>";
    }
  }
  echo "</ol>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Data Jadwal Baru");
TampilkanJudul("Cek Data Jadwal Baru - Migrasi Jadwal");
$gos();
?>
