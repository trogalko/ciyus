<?php
function jabatan() {
  include "jabatan.jenis.php";
  $sub = (empty($_REQUEST['sub']))? 'DftrJenisJabatan' : $_REQUEST['sub'];
  $sub();
}
function detailJabatan() {
  include "jabatan.detail.php";
  $sub = (empty($_REQUEST['sub']))? 'DftrJabatanMaster' : $_REQUEST['sub'];
  $sub();
}

// *** Konst ***
$arrJabatan = array(
  "Master Jabatan->jabatan",
  "Detail Jabatan->detailJabatan");

// *** Parameters ***
$jbtnperiod = GetSetVar('jbtnperiod');
$tok = (empty($_REQUEST['tok']))? "jabatan" : $_REQUEST['tok'];

// *** Main ***
TampilkanJudul("Master Jabatan Perguruan Tinggi");
TampilkanPilihanInstitusi($mnux);

if (!empty($tok) && !empty($_SESSION['KodeID'])) {
  TampilkanSubMenu('jabatan', $arrJabatan, 'tok', $tok);
  $tok();
}
?>