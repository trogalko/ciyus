<?php
// Peyimpan nilai ujian
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

$LulusUjian = (empty($_REQUEST['LulusUjian']))? 'N' : $_REQUEST['LulusUjian'];
$Catatan = sqling($_REQUEST['Catatan']);
$s = "update pmb set NilaiUjian='$_REQUEST[NilaiUjian]', LulusUjian='$LulusUjian', Catatan='$Catatan'
  where PMBID='$_REQUEST[PMBID]' ";
$r = _query($s);

include_once "disconnectdb.php";
//include_once "pesan.html.php";
?>
