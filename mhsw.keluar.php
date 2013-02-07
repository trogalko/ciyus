<?php
// Author: Emanuel Setio Dewo
// 06 Sept 2006
// www.sisfokampus.net

include_once "carimhsw.php";
// *** Functions ***


// *** Parameters ***
$crmhswkey = GetSetVar('crmhswkey');
$crmhswval = GetSetVar('crmhswval');


// *** Main ***
TampilkanJudul("Mahasiswa Keluar/DO");
CariMhsw('mhsw.keluar');
if (!empty($_SESSION['crmhswval'])) DaftarMhsw('mhsw.keluar.go', "mhsw.keluar");
?>
