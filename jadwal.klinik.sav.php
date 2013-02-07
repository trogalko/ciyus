<?php
// Author: Emanuel Setio Dewo
// 10 April 2006

function JdwlSav() {
  $md = $_REQUEST['md']+0;
  $tahun = $_REQUEST['tahun'];
  $ProdiID = $_REQUEST['ProdiID'];
  $ProgramID = $_REQUEST['ProgramID'];
  $MKID = $_REQUEST['MKID'];
  $DosenID = $_REQUEST['DosenID'];
  $mk = GetFields('mk', 'MKID', $MKID, '*');
  $TglMulai = "$_REQUEST[TglMulai_y]-$_REQUEST[TglMulai_m]-$_REQUEST[TglMulai_d]";
  $TglSelesai = "$_REQUEST[TglSelesai_y]-$_REQUEST[TglSelesai_m]-$_REQUEST[TglSelesai_d]";
  // Durasi
  $_RencanaKehadiran = $_REQUEST['_RencanaKehadiran']+0;
  $RencanaKehadiran = $_REQUEST['RencanaKehadiran']+0;
  // Jika ada perubahan durasi, maka ubah TglSelesainya
  if ($_RencanaKehadiran != $RencanaKehadiran) {
    $_hari = $RencanaKehadiran * 7;
    $tgl1 = mktime(0, 0, 0, $_REQUEST['TglMulai_m'], $_REQUEST['TglMulai_d']+$_hari, $_REQUEST['TglMulai_y']);
    $TglSelesai = date('Y-m-d', $tgl1);
  }
  
  $RSID = $_REQUEST['RSID'];
  $Harga = $_REQUEST['Harga']+0;
  $Kapasitas = $_REQUEST['Kapasitas']+0;
  if ($md == 0) {
    $s = "update jadwal set MKID='$MKID', MKKode='$mk[MKKode]', Nama='$mk[Nama]',
      DosenID='$DosenID',
      SKS='$mk[SKS]', SKSAsli='$mk[SKS]', Harga='$Harga', Kapasitas='$Kapasitas',
      RuangID='$RSID', TglMulai='$TglMulai', TglSelesai='$TglSelesai', RencanaKehadiran='$Durasi',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where JadwalID='$_REQUEST[JadwalID]' ";
    $r = _query($s);
    // update harga KRS
    $s1 = "update krs set Harga=$Harga where JadwalID=$_REQUEST[JadwalID] ";
    $r1 = _query($s1);
  }
  else {
    $s = "insert into jadwal (MKID, MKKode, Nama, TahunID, JenisJadwalID,
      KodeID, ProgramID, ProdiID, NamaKelas, DosenID,
      RuangID, SKS, SKSAsli, HargaStandar, Harga, Kapasitas,
      TglMulai, TglSelesai, RencanaKehadiran,
      LoginBuat, TglBuat)
      values ('$MKID', '$mk[MKKode]', '$mk[Nama]', '$tahun', 'K',
      '$_SESSION[KodeID]', '.$ProgramID.', '.$ProdiID.', 'KLINIK', '$DosenID',
      '$RSID', '$mk[SKS]', '$mk[SKS]', 'N', '$Harga', '$Kapasitas',
      '$TglMulai', '$TglSelesai', '$Durasi',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
}
?>
