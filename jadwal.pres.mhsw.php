<?php
// Author: Emanuel Setio Dewo
// 28 March 2006

include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

// Simpan presensi mhsw
$nilai = GetaField('jenispresensi', 'JenisPresensiID', $_REQUEST['JenisPresensiID'], 'Nilai');
$s = "update presensimhsw set JenisPresensiID='$_REQUEST[JenisPresensiID]', Nilai='$nilai'
  where PresensiMhswID=$_REQUEST[PresensiMhswID] ";
$r = _query($s);


// Update nilai Mhsw
$PM = GetFields("presensimhsw", "PresensiMhswID", $_REQUEST['PresensiMhswID'], "*");
$NilaiPresensi = GetaField("presensimhsw", 
  "JadwalID='$PM[JadwalID]' and MhswID", $PM['MhswID'], "sum(Nilai)")+0;

$sn = "update krs 
  set _Presensi=$NilaiPresensi 
  where MhswID='$PM[MhswID]'
    and JadwalID='$PM[JadwalID]' ";
$rn = _query($sn);
//echo $sn;

$_REQUEST['Pesan'] = "Sudah disimpan: $sn";
echo "<script>window.close()</script>";
include_once "disconnectdb.php";
//include_once "pesan.html.php";
?>
