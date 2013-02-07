<?php
// Author: Emanuel Setio Dewo
// 05 March 2006

include_once "carimhsw.php";
// *** Functions ***

// *** Parameters ***
$crmhswkey = GetSetVar('crmhswkey');
$crmhswval = GetSetVar('crmhswval');

// *** Main ***
TampilkanJudul("Keuangan Mahasiswa");
CariMhsw('mhswkeu', 'mhswkeu');
if (!empty($_SESSION['crmhswval'])) DaftarMhsw('mhswkeu.det', "mhswkeu");
?>
