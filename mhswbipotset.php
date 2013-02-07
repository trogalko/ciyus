<?php
// Peyimpan nilai ujian
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

$bpt = GetFields('bipotmhsw', 'TrxID = 1 and PMBID', $_REQUEST['PMBID'], '*');
if (!empty($bpt)) {
  $d = "delete from bipotmhsw where PMBID = '$_REQUEST[PMBID]'";
  $dr = _query($d);
}

$s = "update pmb set BIPOTID='$_REQUEST[BIPOTID]'
  where PMBID='$_REQUEST[PMBID]' ";
$r = _query($s);

$_REQUEST['Pesan'] = "<p>Master Biaya & Potongan telah diset untuk mahasiswa: <b>$_REQUEST[PMBID]</b></p>";

include_once "disconnectdb.php";
//include_once "pesan.html.php";
?>
