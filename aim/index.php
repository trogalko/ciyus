<?php
  // Sisfo Kampus 2 (Kurikulum Berbasis Kompetensi)
  // Author: Emanuel Setio Dewo
  // Email: setio_dewo (@sisfokampus.net, @telkom.net)
  // Start: 2005-12-04

  session_start();
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  include "dwo.mhsw.php";
  //include "parameter.php";
  include "cekparam.php";
  $mnux = GetSetVar('mnux');
  $mdlid = GetSetVar('mdlid');
?>
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE>Anjungan Informasi Mahasiswa</TITLE>
  <META content="Emanuel Setio Dewo" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="index.css" rel="stylesheet" type="text/css">
  <?php
    if (!empty($_REQUEST['slnt'])) {
      include_once $_REQUEST['slnt'].'.php';
      $_REQUEST['slntx']();
    }
    
    if (isset($_REQUEST['GODONLOT'])) {
      $_meta = "<META HTTP-EQUIV=\"refresh\" content=\"1; URL=http://localhost/semarang/$_REQUEST[GODONLOT]?f=$_REQUEST[f]\">";
      echo $_meta;
    }
  ?>
  </HEAD>
<BODY>
  <?php
    include_once "header.php";
    // jika sudah login
    if (empty($_SESSION['__Login'])) LoginMhsw();
    else {
      include_once "mnu.mhsw.php";
    }
    // eksekusi modul
    if (file_exists($_SESSION['mnux'].'.php')) {
      include_once $_SESSION['mnux'].'.php';
      include_once "disconnectdb.php";
    }
  ?>
  <div class='footer'>
  <center>Powered by <a href="http://sisfo.stikes-banisaleh.com" title="Stikes Banisaleh">Sisfo Stikes Banisaleh 2012</a></center>
  </div>
</BODY>

</HTML>
