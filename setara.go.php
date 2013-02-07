<?php
function TampilkanPesan() {
  echo "<p>Script ini akan menyalin data dari tabel mhswssetarapindahan ke tabel krs.
  Data yg telah disalin tidak akan disalin ulang.</p>";
}

function ExportKRSSetara() {
  $mhswstrID = $_REQUEST['mhswstrID'];
  $prodiID = $_REQUEST['prodit'];
  $s = "select mps.*, mp.*, n.Bobot as BOBOT, mps.GradeNilai as GRADE
    from mhswpindahansetara mps 
      left outer join nilai n on n.Nama = mps.GradeNilai
      left outer join mhswpindahan mp on mp.MhswPindahanID = mps.MhswPindahanID
    where mps.sudah=0 
    and mps.MhswPindahanID = $mhswstrID
    and n.ProdiID = $prodiID";
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
        GradeNilai, BobotNilai, Final,
        Catatan,
        LoginBuat, TanggalBuat)
        values ('$w[KHSID]', '$w[MhswID]', '00000', '$w[JadwalID]',
        '$w[MKID]', '$w[MKKode]', '$w[SKS]', 'A',
        $w[Harga], '$w[HargaStandar]',
        '$w[GRADE]', '$w[BOBOT]', 'Y',
        '$w[Catatan]',
        '$w[LoginBuat]', now())
      ";
      //echo "<pre>$s1</pre>";
      $r1 = _query($s1);
    
      // Set bahwa sudah di export
      $s2 = "update mhswpindahansetara set sudah=1 where KRSID=$w[KRSID]";
      $r2 = _query($s2);
      echo "<pre>$s2</pre>";
    //}
    //else echo "<li>$w[MhswID] &raquo; $w[KRSID]</li>";
  }
  $s3 = "update mhswpindahan set Sudah=1 where MhswPindahanID=$mhswstrID";
  $r3 = _query($s3);
  $up = GetFields('mhswpindahan', "MhswPindahanID", $mhswstrID, "*");
  $s4 = "update mhsw set BatasStudi='$up[BatasStudi]', TotalSKSPindah='$up[JumlahSetara]', SKPenyetaraan='$up[SKPenyetaraan]',
         TglSKPenyetaraan='$up[TglSKPenyetaraan]', ProdiAsalPT='$up[ProdiAsalPT]', AsalPT='$up[AsalPT]', IPKAsalPT='$up[IPKAsalPT]'
         where MhswID='$up[MhswID]'";
  $r4 = _query($s4);
  echo "</ol>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

// *** Main ***
//include_once "sisfokampus.php";
//HeaderSisfoKampus("Proses Export KRS");
TampilkanJudul("Proses Export KRS");
$gos();
?>
