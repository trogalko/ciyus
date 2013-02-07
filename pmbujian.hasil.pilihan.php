<?php
// Author: Emanuel Setio Dewo
// 04 Feb 2006

// Peyimpan nilai ujian
include_once "db.mysql.php";
include_once "connectdb.php";

$pil = explode('.', $_REQUEST['Pilihanku']);
$s = "update pmb set ProdiID='$pil[0]' where PMBID='$_REQUEST[PMBID]' ";
$r = _query($s);

include_once "disconnectdb.php";
//include_once "pesan.html.php";
?>