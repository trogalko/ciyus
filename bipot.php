<?php
// Author: Emanuel Setio Dewo
// 10 Feb 2006

// *** Functions ***
function bipot() {
  include "bipot.nama.php";
  $sub = (empty($_REQUEST['sub']))? 'DftrBipotNama' : $_REQUEST['sub'];
  $sub();
}
function bipotmhsw() {
  include "bipot.master.php";
  $sub = (empty($_REQUEST['sub']))? 'DftrBipotMaster' : $_REQUEST['sub'];
  $sub();
}
function bipotdef() {
}

// *** Konst ***
$arrBipot = array("Master Biaya & Potongan->bipot",
  "Biaya & Potongan Mahasiswa->bipotmhsw");
//  "Default Biaya & Potongan->bipotdef");

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$bipotid = GetSetVar('bipotid');
$tok = (empty($_REQUEST['tok']))? "bipot" : $_REQUEST['tok'];

// *** Main ***
TampilkanJudul("Master Biaya dan Potongan Mahasiswa");
TampilkanPilihanInstitusi($mnux);

if (!empty($tok) && !empty($_SESSION['KodeID'])) {
  TampilkanSubMenu('bipot', $arrBipot, 'tok', $tok);
  $tok();
}
?>