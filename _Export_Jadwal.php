<?php
// Author: Emanuel Setio Dewo
// 2006-09-16

function TampilkanPesan() {
  echo "<p>Script ini akan menyalin data dari tabel _jadwal ke tabel jadwal.
  Data yg telah disalin tidak akan disalin ulang.</p>
  <p>Tekan tombol berikut untuk memulai proses: <input type=button name='Proses' value='Proses Copy' onClick=\"location='?gos=Exportjadwal'\"></p>";
}

function ExportJadwal() {
  $s = "select *
    from _jadwal
    where Sudah=0";
  $r = _query($s); $jml = _num_rows($r);
  echo "<p>Jumlah data: <font size=+1>$jml</font></p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    echo "<li>$w[ProdiID] &raquo; $w[MKKode]
    </li>";
    // Export
    $s1 = "insert into jadwal (KodeID, TahunID, ProdiID, ProgramID,
      NamaKelas, MKID, JenisJadwalID, MKKode, Nama, 
      HariID, JamMulai, JamSelesai, SKSAsli, SKS,
      DosenID, RencanaKehadiran, Kehadiran, JumlahMhsw,
      Kapasitas, RuangID,
      TglBuat, LoginBuat)
      
      values ('$w[KodeID]', '$w[TahunID]', '$w[ProdiID]', '$w[ProgramID]',
      '$w[NamaKelas]', '$w[MKID]', '$w[JenisJadwalID]', '$w[MKKode]', '$w[Nama]',
      '$w[HariID]', '$w[JamMulai]', '$w[JamSelesai]', '$w[SKSAsli]', '$w[SKS]',
      '$w[DosenID]', '$w[RencanaKehadiran]', '$w[Kehadiran]', '$w[JumlahMhsw]',
      '$w[Kapasitas]', '$w[RuangID]',
      now(), '$w[LoginBuat]')
    ";
    //echo "<pre>$s1</pre>";
    $r1 = _query($s1);
    
    // Set bahwa sudah di export
    $s2 = "update _jadwal set Sudah=1 where JadwalID=$w[JadwalID]";
    $r2 = _query($s2);
  }
  echo "</ol>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

// *** Main ***
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Export Jadwal");
TampilkanJudul("Proses Export Jadwal");
$gos();

?>
