<?php
// Author: Emanuel Setio Dewo
// 2006-09-16

function TampilkanPesan() {
  echo "<p>Script ini akan menyalin data dari tabel _krs ke tabel krs.
  Data yg telah disalin tidak akan disalin ulang.</p>
  <p>Tekan tombol berikut untuk memulai proses: <input type=button name='Proses' value='Proses Copy' onClick=\"location='?gos=Exportjadwal'\"></p>";
}

function ExportJadwal() {
  $s = "select *
    from _krs
    where Sudah=0";
  $r = _query($s); $jml = _num_rows($r);
  echo "<p>Jumlah data: <font size=+1>$jml</font></p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    //$ada = GetaField('krs', "MhswID='$w[MhswID]' and JadwalID", $w[JadwalID], 'KRSID');
    //if (empty($ada)) {
      echo "<li>$w[MhswID] &raquo; $w[MKKode]
      </li>";
      // Export
      $s1 = "insert into krs
        (KHSID, MhswID, TahunID, JadwalID, 
        MKID, MKKode, SKS, StatusKRSID,
        Harga, HargaStandar,
        GradeNilai, BobotNilai,
        Catatan,
        LoginBuat, TanggalBuat)
        values ('$w[KHSID]', '$w[MhswID]', '$w[TahunID]', '$w[JadwalID]',
        '$w[MKID]', '$w[MKKode]', '$w[SKS]', '$w[StatusKRSID]',
        $w[Harga], '$w[HargaStandar]',
        '$w[GradeNilai]', $w[BobotNilai],
        '$w[Catatan]',
        '$w[LoginBuat]', now())
      ";
      //echo "<pre>$s1</pre>";
      $r1 = _query($s1);
    
      // Set bahwa sudah di export
      $s2 = "update _krs set Sudah=1 where KRSID=$w[KRSID]";
      $r2 = _query($s2);
      echo "<pre>$s2</pre>";
    //}
    //else echo "<li>$w[MhswID] &raquo; $w[KRSID]</li>";
  }
  echo "</ol>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

// *** Main ***
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Export KRS");
TampilkanJudul("Proses Export KRS");
$gos();

?>
