<?php
// Author: Emanuel Setio Dewo
// 15 March 2006

include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

$JadwalID = $_REQUEST['JadwalID']+0;
if ($JadwalID == 0) die("Terjadi kesalahan. Data tidak disimpan.");

$krsid = array();
$krsid = $_REQUEST['krsid'];
for ($i = 0; $i < sizeof($krsid); $i++) {
  $_krsid = $krsid[$i];
  $Tugas1 = $_REQUEST['Tugas1_'.$krsid[$i]]+0;
  $Tugas2 = $_REQUEST['Tugas2_'.$krsid[$i]]+0;
  $Tugas3 = $_REQUEST['Tugas3_'.$krsid[$i]]+0;
  $Tugas4 = $_REQUEST['Tugas4_'.$krsid[$i]]+0;
  $Tugas5 = $_REQUEST['Tugas5_'.$krsid[$i]]+0;
  $Presensi = $_REQUEST['Presensi_'.$krsid[$i]]+0;
  $StatusKRSID = $_REQUEST['StatusKRSID_'.$_krsid];
  $UTS = $_REQUEST['UTS_'.$krsid[$i]]+0;
  $UAS = $_REQUEST['UAS_'.$krsid[$i]]+0;
  $s = "update krs set Tugas1=$Tugas1, Tugas2=$Tugas2, Tugas3=$Tugas3, Tugas4=$Tugas4, Tugas5=$Tugas5,
    Presensi=$Presensi, UTS=$UTS, UAS=$UAS, StatusKRSID='$StatusKRSID'
    where KRSID=$_krsid";
  $r = _query($s);
}
echo "Sudah disimpan.";
echo "<script>window.close()</script>";

/*
// ------
$krsid = $_REQUEST['krsid'];
$Tugas1 = $_REQUEST['Tugas1']+0;
$Tugas2 = $_REQUEST['Tugas2']+0;
$Tugas3 = $_REQUEST['Tugas3']+0;
$Tugas4 = $_REQUEST['Tugas4']+0;
$Tugas5 = $_REQUEST['Tugas5']+0;
$Presensi = $_REQUEST['Presensi']+0;
$UTS = $_REQUEST['UTS']+0;
$UAS = $_REQUEST['UAS']+0;

$jdwlid = GetaField('krs', 'KRSID', $krsid, 'JadwalID');
$jdwl = GetFields('jadwal', 'JadwalID', $jdwlid, 'Final');
// Jika nilai sudah final
if ($jdwl['Final'] == 'Y') {
  $_REQUEST['Pesan'] = "<font color=red><b>Nilai tidak disimpan.<br />
    Nilai matakuliah ini sudah di-finalisasi.</b></font>";
  include_once "pesan.html.php";
}
else {
  $s = "update krs set Tugas1='$Tugas1', Tugas2='$Tugas2', Tugas3='$Tugas3',
  Tugas4='$Tugas4', Tugas5='$Tugas5',
  Presensi='$Presensi', UTS='$UTS', UAS='$UAS'
  where KRSID=$krsid ";
  $r = _query($s);
  $_REQUEST['Pesan'] = "Nilai sudah disimpan";
  include_once "pesan.html.php";
  echo "<script>window.close()</script>";
}
*/
include_once "disconnectdb.php";
?>
