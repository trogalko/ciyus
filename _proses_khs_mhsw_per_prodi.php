<?php
// Author: Emanuel Setio Dewo
// 05 Okt 2006

// Dalam rangka ngakurin data krs hasil import & dibuat KHS-nya.

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Total KHS Mahasiswa");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='gos' value='ProsesKRS'>
  <tr><td class=inp>Tahun</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program Studi</td>
    <td><select name='prodi'>$optprodi</select>
    <input type=submit name='Proses' value='Proses'></td>
  </form></table></p>";
}
function ProsesKRS() {
  $prodi = ($_SESSION['prodi'] == '61')? '01' : $_SESSION['prodi'];
  $s = "select k.MhswID, sum(k.SKS) as TotalSKS, count(*) as JumlahMK
    from krs k
    where k.TahunID='$_SESSION[tahun]'
      and LEFT(k.MhswID, 2)='$prodi'
    group by k.MhswID";
  $r = _query($s);
  $jml = _num_rows($r);
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $ada = GetFields('khs', "MhswID='$w[MhswID]' and TahunID", $_SESSION['tahun'], '*');
    if (empty($ada)) {
      $str = "Diproses";
      
    }
    else {
      $str = "Sudah: $ada[KHSID], $ada[JumlahMK] - $ada[TotalSKS]";
      $KHSID = $ada['KHSID'];
    }
    // update KRS
    $s1 = "update krs set KHSID=$KHSID where MhswID='$w[MhswID]' and TahunID='$_SESSION[tahun]' ";
    $r1 = _query($s1);
    echo "<li>$w[MhswID] &raquo; $w[JumlahMK] - $w[TotalSKS] &raquo; $str</li>";
  }
  echo "</ol>";
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses KHS Mhsw dari KRS-nya");
TampilkanHeaderProses();
if (!empty($prodi) && !empty($prodi)) $gos();

?>
