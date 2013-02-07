<?php
// Author: Emanuel Setio Dewo
// 09 Nov 2006
session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Data KRS Mahasiswa");

$tahun = GetSetVar('tahun');
$arrprodi = array('21', '22', '23');
$gos = (empty($_REQUEST['gos']))? "TampilanAwal" : $_REQUEST['gos'];


TampilkanJudul("Cek Data KRS Double Mhsw");
$gos();

// *** Functions ***
function TampilanAwal() {
  global $arrprodi;
  $prd = implode(', ', $arrprodi);
  echo Konfirmasi("Proses Pengecekan KRS Double",
  "Proses akan mengecek KRS Mhsw apakah KRS seorang mahasiswa double.<br />
  Jika double, maka akan dihapus salah satu.<br />
  Pengecekan dilakukan terhadap <b>Hari</b> dan <b>MKID</b>. <br />
  Prodi yg akan dicek: <b>$prd</b>.
  <hr size=1 color=silver>
  <form action='?' method=GET>
  <input type=hidden name='gos' value='ProsesKRS'>
  Tahun Akademik: <input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
  <hr size=1 color=silver>
  <input type=submit name='Simpan' value='Simpan'>
  </form>");
}
function ProsesKRS() {
  if (empty($_SESSION['tahun'])) {
    echo ErrorMsg("Tidak Dpt Diproses",
    "Tahun Akademik kosong! Proses dibatalkan.");
    TampilanAwal();
  } else ProsesBeneran();
}
function ProsesBeneran() {
  global $arrprodi;
  $strprd = '';
  foreach($arrprodi as $val) $strprd .= ",'$val'";
  $strprd = TRIM($strprd, ',');
  
  $s = "select khs.MhswID, khs.ProdiID, khs.KHSID
    from khs
    where khs.ProdiID in ($strprd) and khs.TahunID='$_SESSION[tahun]'
    order by khs.MhswID";
  $r = _query($s);
  $jml = _num_rows($r);
  $_SESSION['CEK-KRS-JML'] = $jml;
  $_SESSION['CEK-KRS-POS'] = 0;
  echo "<p>Ada terdaftar <font size=+1>$jml</font> mhsw.</p>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["CEK-KRS-MHSWID-$n"] = $w['MhswID'];
    $_SESSION["CEK-KRS-PRODIID-$n"] = $w['ProdiID'];
    $_SESSION["CEK-KRS-KHSID-$n"] = $w['KHSID']; 
  }
  $_SESSION["CEK-KRS-JML"] = $n;
  $_SESSION["CEK-KRS-DIHAPUS"] = 0;
    // IFRAME
  echo "<p><IFRAME src='_Cek_KRS_Double.gos.php' frameborder=0 height=100% width=100%>
  </IFRAME></p>";
}
?>
