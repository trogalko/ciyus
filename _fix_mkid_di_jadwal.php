<?php
// Author: Emanuel Setio Dewo
// 05 Okt 2006

// Dalam rangka ngakurin data jadwal

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Total KHS Mahasiswa");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='gos' value='ProsesMK'>
  <tr><td class=inp>Tahun</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program Studi</td>
    <td><select name='prodi'>$optprodi</select>
    <input type=submit name='Proses' value='Proses'></td>
  </form></table></p>";
}
function ProsesMK() {
  $s = "select *
    from mk
    where ProdiID='$_SESSION[prodi]'
    order by MKID";
  $r = _query($s);
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    // update jadwal
    $s1 = "update jadwal set MKID='$w[MKID]', Nama='$w[Nama]', SKS='$w[SKS]'
      where TahunID='$_SESSION[tahun]' and MKKode='$w[MKKode]' ";
    //$r1 = _query($s1);
    // update krs
    $s2 = "update krs set MKID='$w[MKID]', SKS='$w[SKS]'
      where TahunID='$_SESSION[tahun]' and MKKode='$w[MKKode]' ";
    $r2 = _query($s2);
    echo "<li>$w[MKID] &raquo; $w[MKKode] - $s2
      </li>";
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
