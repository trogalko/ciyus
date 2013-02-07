<?php
// Author: Emanuel Setio Dewo
// 23 March 2006

// *** Parameters ***
$srcpmbid = GetSetVar('srcpmbid');

// *** Main ***
$JDL = "Setup Cicilan Calon Mahasiswa";
TampilkanJudul($JDL);
TampilkanPencarianCAMA('pmb.cicilan');
if (empty($_REQUEST['srcpmbid'])) {
  //TampilkanJudul($JDL);
  //TampilkanPencarianCAMA('pmb.cicilan');
}
else {
  $pmbid = $_REQUEST['srcpmbid'];
  $_SESSION['pmbid'] = $pmbid;
  $pmb = GetFields('pmb', 'PMBID', $pmbid, "PMBID, Nama, PMBPeriodID, NIM, GradeNilai");
  if (!empty($pmb)) {
    if (empty($pmb['NIM'])) include_once "mhswbaru.cicilan.php";
    else {
      echo ErrorMsg("Tidak Dapat Diubah Lagi",
        "Calon Mahasiswa sudah diterima menjadi mahasiswa.<br />
        Data sudah tidak dapat diubah lagi.");
    }
  }
}
?>
