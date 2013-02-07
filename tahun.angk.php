<?php
// Author: Emanuel Setio Dewo
// 22 March 2006
// Simpan hanya angkatan

include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

if (!empty($_REQUEST['TahunID']) && !empty($_REQUEST['ProgramID']) && !empty($_REQUEST['ProdiID'])) {
  $s = "update tahun
    set HanyaAngkatan='$_REQUEST[HanyaAngkatan]'
    where TahunID='$_REQUEST[TahunID]' and ProgramID='$_REQUEST[ProgramID]'
      and ProdiID='$_REQUEST[ProdiID]' and KodeID='$_REQUEST[KodeID]' ";
  $r = _query($s);
  $_REQUEST['Pesan'] = "Telah Disimpan";
} else $_REQUEST['Pesan'] = "<font color=red><b>Tidak disimpan</b></font>";


include_once "disconnectdb.php";
include_once "pesan.html.php";
?>