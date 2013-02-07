<?php
// Author: Emanuel Setio Dewo
// 25 Nov 2006
// www.sisfokampus.net
// Email: setio.dewo@gmail.com

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Hitung Bobot Mahasiswa Lulus/DO/Keluar");

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Hitung Bobot Mahasiswa Lama");
TampilkanHeaderProses();
$gos();

// *** Functions ***
function TampilkanHeaderProses() {
  //$optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p>Anda akan memproses Bobot Mahasiswa Lama.<br />
    Silakan tekan tombol ini untuk mulai memproses: 
    <input type=button name='Proses' value='Mulai Proses' onClick=\"window.location='?gos=ProsesHitung'\">";
}
function ProsesHitung() {
  $s = "select *
    from mhsw
    where StatusMhswID in ('L')
    order by MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["MhswID-$n"] = $w['MhswID'];
  }
  $_SESSION["POS"] = 0;
  $_SESSION["MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>" . number_format($_SESSION["MAX"]). " data.</font></p>";
  // IFRAME
  echo "<p><IFRAME src='_proses_hitung_bobot_mhsw_lulus.go.php' frameborder=0 height=400 width=500>
  </IFRAME></p>";
}
?>
