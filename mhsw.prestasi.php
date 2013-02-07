<?php
// Author: Emanuel Setio Dewo
// 31 Agustus 2006
// www.sisfokampus.net

include_once "carimhsw.php";

// *** Functions ***
function DaftarMhsw1() {
  DaftarMhsw('mhsw.prestasi.det', "");
}


// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$inqMhswPage = GetSetVar('inqMhswPage');
$crmhswkey = GetSetVar('crmhswkey');
$crmhswval = GetSetVar('crmhswval');
if (isset($_REQUEST['crmhswkey'])) {
  $inqMhswPage = 1;
  $_SESSION['inqMhswPage'] = $inqMhswPage;
}
$gos = (empty($_REQUEST['gos']))? 'DaftarMhsw1' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Catatan Prestasi dan Wanprestasi Mhsw");
  CariMhsw('mhsw.prestasi');
  $gos('mhsw.inq');
?>
