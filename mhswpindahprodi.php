<?php
// Author: Emanuel Setio Dewo
// 07 March 2006

include_once "carimhsw.php";
// *** Functions ***

// *** Parameters ***
$crmhswkey = GetSetVar('crmhswkey');
$crmhswval = GetSetVar('crmhswval');

// *** Main ***
TampilkanJudul("Mahasiswa Pindah Prodi");
CariMhsw('mhswpindahprodi');
if (!empty($_SESSION['crmhswval'])) DaftarMhsw('mhswpindahprodi.det', "gos=", 1);

?>
