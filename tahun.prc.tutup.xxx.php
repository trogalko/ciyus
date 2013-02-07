<?php
// Author: Emanuel Setio Dewo
// 19 May 2006
// www.sisfokampus.net

session_start();
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

function PRC() {
  echo "<body bgcolor=#EEFFFF>";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  $pss = $_SESSION['THN'.$prodi.'POS'];
  $mhswid = $_SESSION['THN'.$prodi.$pss];
  // Jika ada data
  if (!empty($mhswid)) {
    echo "<p>#<font size=+2>".$pss . "</font> &raquo; <b>$tahun</b> &raquo; " .$_SESSION['THN'.$prodi.$pss]."</p><hr>";
    $sdh = GetFields('khs', "MhswID='$mhswid' and TahunID", $tahun, "KHSID, MhswID");
    if (empty($sdh)) {
      $def = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
      $sesi = GetaField('khs', 'MhswID', $w['MhswID'], "max(Sesi)")+1;
      $mhsw = GetFields('mhsw', "MhswID", $mhswid, "BIPOTID"); 
      $sp = "insert into khs (TahunID, KodeID, ProgramID, ProdiID,
        MhswID, StatusMhswID, Sesi, BIPOTID,
        LoginBuat, TanggalBuat)
        values ('$tahun', '$_SESSION[KodeID]', '$w[ProgramID]', '$prodi',
        '$mhswid', '$def', '$sesi', '$mhsw[BIPOTID]',
        '$_SESSION[_Login]', now()  )";
      //echo "<pre>$sp</pre>";
      //$rp = _query($sp);
      echo "<p><font color=green>DIPROSES</font></p>";
    }
    else {
      echo "<p><font color=gray>Sudah pernah diproses</font></p>";
    }
  }
  // refresh page
  if ($_SESSION['THN'.$prodi.'POS'] < $_SESSION['THN'.$prodi]) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    // update data tahun
    $st = "update tahun set ProsesBuka=ProsesBuka+1
      where TahunID='$tahun' and ProgramID='$prid' and ProdiID='$prodi'";
    $rt = _query($st);
    echo "<p>Proses buka TAHUN akademik <b>$tahun</b> sudah <font size=+2>SELESAI</font></p>";
  }

  $_SESSION['THN'.$prodi.'POS']++;
}

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

include_once "disconnectdb.php";
?>
