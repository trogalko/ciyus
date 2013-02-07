<?php
session_start();
include "db.mysql.php";
include "connectdb.php";

  echo "<body bgcolor=#EEFFFF>";
  include_once "dwo.lib.php";
  include_once "parameter.php"; 
  include_once "mhswkeu.sav.php";
  $dat = $_SESSION['ADUP'.$_SESSION['ADUPPOS']];
  $arr = explode('~', $dat);
  var_dump($_SESSION['ADUPPOSX']); exit;
  //echo $_SESSION['ADUP'.$_SESSION['ADUPPOS']];
  $s = "update mhsw set 
									NamaAyah = '$arr[1]', AgamaAyah='$arr[2]', PendidikanAyah='$arr[3]', 
                  PekerjaanAyah='$arr[4]', HidupAyah='$arr[5]', NamaIbu='$arr[6]', AgamaIbu='$arr[7]', 
                  PendidikanIbu='$arr[8]', PekerjaanIbu='$arr[9]', HidupIbu='$arr[10]'
									where Login = '$arr[0]'";
	$r = _query($s);
   
  echo "#$_SESSION[ADUPPOS] &raquo; <font size=4>" . $arr[0] . "</font><hr />";
  if ($_SESSION['ADUPPOS'] < $_SESSION['ADUPPOSX']) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses upload sudah <b>SELESAI</b>.</p>";
  $_SESSION['ADUPPOS']++;

include_once "disconnectdb.php";
?>
