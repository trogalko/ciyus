<?php
  // Sisfo Kampus 2 (Kurikulum Berbasis Kompetensi)
  // Author: Emanuel Setio Dewo
  // Email: setio_dewo (@sisfokampus.net, @telkom.net)
  // Start: 2005-12-04

  session_start();
  include "db.mysql.php";
  if (!file_exists("connectdb.php")) {
    header("Location: install.php");
    exit;
  } else {
    include "connectdb.php";
  }
  include "dwo.lib.php";
  include "parameter.php";
  include "cekparam.php";
  $mdlid = GetSetVar('mdlid');
?>
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META content="Emanuel Setio Dewo" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="index.css" rel="stylesheet" type="text/css">
  <link href="styles_menu.css" rel="stylesheet" type="text/css">
  
  <script type="text/javascript" src="menu/dropdowntabs.js"></script>
  <script src="jquery.js" language="javascript" type="text/javascript"></script>
  <script src="boxcenter.js" language="javascript" type="text/javascript"></script>
  <script src="jquery.autocomplete.js" language="javascript" type="text/javascript"></script>
  <script src="jtip.js" language="javascript" type="text/javascript"></script>  
    
  <link rel="stylesheet" type="text/css" href="menu/ddcolortabs.css" />
  <?php
    if (!empty($_REQUEST['slnt'])) {
      include_once $_REQUEST['slnt'].'.php';
      $_REQUEST['slntx']();
    }
    
    if (isset($_REQUEST['GODONLOT'])) {
      $_meta = "<META HTTP-EQUIV=\"refresh\" content=\"1; URL=http://localhost/$_REQUEST[GODONLOT]?f=$_REQUEST[f]\">";
      echo $_meta;
    }
  ?>
  </HEAD>
<BODY>
  <?php
    //include "connectdb.php";
    
    include "header.php";
    if (!empty($_SESSION['_Session'])) {
      $NamaLevel = GetaField('level', 'LevelID', $_SESSION['_LevelID'], 'Nama');
      if (empty($_REQUEST['BypassMenu'])) include "menusis.php";
      if (!empty($_SESSION['mdlid'])) {
        $_strMDLID = GetaField('mdl', "MdlID", $_SESSION['mdlid'], "concat(MdlGrpID, ' � ', Nama)");
        echo "<div class=MenuDirectory>Menu: $_strMDLID</div>";
      }
       echo "<div class=NamaLogin>Login: <b>$_SESSION[_Nama]</b> ($NamaLevel) � <a href='?mnux=&slnt=loginprc&slntx=lout'>Logout</a></div>";
    }
    echo "<div class=isi>";
    if (file_exists($_SESSION['mnux'].'.php')) {
      // cek apakah berhak mengakses? Harus dicek 1 per 1 karena mungkin 1 modul tersedia bagi banyak level
      $sboleh = "select * from mdl where Script='$_SESSION[mnux]'";
      $rboleh = _query($sboleh); $ktm = -1;
      if (_num_rows($rboleh) > 0) {
        while ($wboleh = _fetch_array($rboleh)) {
          $pos = strpos($wboleh['LevelID'], ".$_SESSION[_LevelID].");
          if ($pos === false) {}
          else $ktm = 1;
        }
        if ($ktm <= 0) {
          echo ErrorMsg("Anda Tidak Berhak",
            "Anda tidak berhak mengakses modul ini.<br />
            Hubungi Sistem Administrator untuk memperoleh informasi lebih lanjut.
            <hr size=1>
            Pilihan: <a href='?mnux=&slnt=loginprc&slntx=lout'>Logout</a>");
        }
        else include_once $_SESSION['mnux'].'.php';
      } else include_once $_SESSION['mnux'].'.php';
      include_once "disconnectdb.php";
    }
    else echo ErrorMsg('Fatal Error', "Modul tidak ditemukan. Hubungi Administrator!!!<hr size=1 color=silver>
    Pilihan: <a href='?mnux=donothing'>Kembali</a>");
    echo "</div>";
  ?>
  <div class='footer'>
  <center>Powered by <a href="http://stikes.banisaleh.ac.id" title="Stikes Banisaleh">Sisfo Stikes Banisaleh</a></center>
  </div>
</BODY>

</HTML>
