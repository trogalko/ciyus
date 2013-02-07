<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

// *** Functions ***
function DftrBeasiswa() {
  include_once "beasiswa.dftr.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrBea';
  $sub();
}
function QueryKinerja() {
  include_once "beasiswa.ipk.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrIPK';
  $sub();
}
function JenisBeasiswa() {
  if (strpos(".1.20.50.", '.'.$_SESSION['_LevelID'].'.') === false) echo Konfirmasi1("Anda tidak dapat mengakses modul ini.");
  else {
    include_once "beasiswa.jen.php";
    $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrJenisBeasiswa';
    $sub();
  }
}


// *** Parameters ***
$arrBeasiswa = array("Daftar Penerima Beasiswa->DftrBeasiswa",
  "Query IPK Mahasiswa->QueryKinerja",
  "Jenis Beasiswa->JenisBeasiswa");
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$BeasiswaID = GetSetVar('BeasiswaID');
$beaMhswID = GetSetVar('beaMhswID');
$beaBesar = GetSetVar('beaBesar', 0);
$IPKMin = GetSetVar('IPKMin', 3);
$IPSMin = GetSetVar('IPSMin', 3);
$IPUrut = GetSetVar('IPUrut', 'IPS');
$pref = "Bea22";
$tokendef = "DftrBeasiswa";
$token = GetSetVar($pref, $tokendef);

// *** Main ***
TampilkanJudul("Beasiswa Mahasiswa");
TampilkanSubMenu('beasiswa', $arrBeasiswa, $pref, $token);
if (!empty($token)) $token();
?>
