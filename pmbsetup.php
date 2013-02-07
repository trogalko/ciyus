<?php
// Author: Emanuel Setio Dewo
// 2005-12-17

// *** Functions for Period ***
function Per() {
  include_once "pmbsetup.per.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrPer';
  $sub();
}
// *** Function for Percentage ***
function Persen() {
  include_once "pmbsetup.persen.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrProdi';
  $sub();
}
// *** Function for Harga ***
function Hrg() {
  include_once "pmbsetup.hrg.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrHrg';
  $sub();
}
// *** Function for USM ***
function Usm() {
  include_once "pmbsetup.usm.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DefUSM';
  $sub();
}
function UsmProd() {
  include_once "pmbsetup.usmprod.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DefUSMProd';
  $sub();
}
function Sek() {
  $NamaSekolah = GetSetVar('NamaSekolah');
  $KotaSekolah = GetSetVar('KotaSekolah');
  include_once "pmbsetup.sek.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrSek';
  $sub();
}
function JenSek() {
  include_once "pmbsetup.jensek.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrJenSek';
  $sub();
}
function Stawal() {
  include_once "pmbsetup.stawal.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrStawal';
  $sub();
}
function Syarat() {
  include_once "pmbsetup.syarat.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'DftrSyarat';
  $sub();
}

// *** Catatan2 ***
$CatatanStatusAwal = "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul><b>Beli Formulir</b></td><td class=ul>Jika harus Beli Formulir, maka akan dilakukan pemeriksaan terhadap bukti setoran dari pembelian formulir.</td></tr>
  <tr><td class=ul><b>Jalur Khusus</b></td><td class=ul>Hanya dapat didaftarkan oleh Kepala PMB.</td></tr>
  <tr><td class=ul><b>Tanpa Test</b></td><td class=ul>Calon mahasiswa tidak perlu mengikuti test/USM.</td></tr>
  </table></p>";

// *** Parameters ***
$pref = 'pmb';
$mnux = 'pmbsetup';
$tokendef = 'Per';
$arrpmb = array('Periode->Per',
  'Harga Formulir->Hrg', 'Komponen USM->Usm',
  'USM Prodi->UsmProd',
  'Status Awal->Stawal',
  'Asal Sekolah->Sek', 'Jenis Sekolah->JenSek',
  'Syarat2->Syarat');
$token = GetSetVar($pref, $tokendef);

// *** Extra Parameters ***
$pmbperiod = GetSetVar('pmbperiod');
$KodeID = GetSetVar('KodeID');
$periodpg = GetSetVar('periodpg', 1);

// *** Main ***
TampilkanJudul('Setup PMB');
TampilkanSubMenu($mnux, $arrpmb, $pref, $token);
if (!empty($token)) $token();
?>
