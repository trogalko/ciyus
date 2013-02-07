<?php
  // Sisfo Kampus 2 (Kurikulum Berbasis Kompetensi)
  // Author: Emanuel Setio Dewo
  // Email: setio_dewo (@sisfokampus.net, @telkom.net)
  // Start: 2005-12-04

  session_start();
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  include "cekparam.php";
  include "parameter.php";
?>

<HTML>
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META content="Emanuel Setio Dewo" name=author>
  <META content="Sisfo Kampus" name=description>

  <?php
    include_once "print.css.php";
  ?>
  </HEAD>
<BODY>
  <?php
    //include "header.php";
    if (!empty($_REQUEST['slnt'])) {
      include_once $_REQUEST['slnt'].'.php';
      $_REQUEST['slntx']();
    }
    if (!empty($_SESSION['_Session'])) {
      //echo "<p>Login: <b>$_SESSION[_Nama]</b></p>";
      //include "menusis.php";
    }

    if (file_exists($_SESSION['mnux'].'.php')) {
      include_once $_SESSION['mnux'].'.php';
      include_once "disconnectdb.php";
    }
    else echo ErrorMsg('Fatal Error', "Modul tidak ditemukan. Hubungi Administrator!!!<hr size=1 color=silver>
    Pilihan: <a href='?mnux=donothing'>Kembali</a>");
  ?>
</BODY>

</HTML>