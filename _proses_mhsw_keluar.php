<?php
// Author: Emanuel Setio Dewo
// 25 Nov 2006
// www.sisfokampus.net
// Email: setio.dewo@gmail.com

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Hitung SKS Mahasiswa");

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Set Data Mhsw Keluar");
TampilkanHeaderProses();
$gos();

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p>Anda akan memproses data Mhsw Keluar.<br />
    Anda harus telah menyalin data dari tabel <b>MSMHK</b> ke tabel <b>_mhswkeluar</b>.<br />
    Gunakan script importer: <b>mhsw-keluar.mig</b>.
    <hr>
    Silakan tekan tombol ini untuk mulai memproses: 
    <input type=button name='Proses' value='Mulai Proses' onClick=\"window.location='?gos=ProsesKeluar'\">";
}
function ProsesKeluar() {
  $s = "select *
    from _mhswkeluar
    order by MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["KEL-MhswID-$n"] = $w['MhswID'];
    $_SESSION["KEL-StatusMhswID-$n"] = $w['StatusMhswID'];
    $_SESSION["KEL-SKKeluar-$n"] = $w['SKKeluar'];
    $_SESSION["KEL-TglSKKeluar-$n"] = $w['TglSKKeluar'];
    $_SESSION["KEL-Tahun-$n"] = $w['Tahun'];
    $_SESSION["KEL-NoIjazah-$n"] = $w['NoIjazah'];
    $_SESSION["KEL-TglIjazah-$n"] = $w['TglIjazah'];
  }
  $_SESSION["KEL-POS"] = 0;
  $_SESSION["KEL-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>" . number_format($_SESSION["KEL-MAX"]). " data.</font></p>";
  // IFRAME
  echo "<p><IFRAME src='_proses_mhsw_keluar.go.php' frameborder=0 height=400 width=500>
  </IFRAME></p>";
}
?>
