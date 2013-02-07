<?php
// Author: Emanuel Setio Dewo
// 18 Juli 2006
// www.sisfokampus.net

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Total KHS Mahasiswa");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table cellspacing=1 cellpadding=4>
  <form action='_proseskhsmhsw.php' method=GET>
  <input type=hidden name='gos' value='ProsesKHS'>
  <tr><td>Program Studi</td>
    <td><select name='prodi'>$optprodi</select>
    <input type=submit name='Proses' value='Proses'></td>
  </form></table></p>";
}
function ProsesKHS() {
  $s = "select MhswID, ProgramID, ProdiID
    from mhsw
    where ProdiID='$_SESSION[prodi]'
    order by MhswID";
  $r = _query($s);
  $jml = _num_rows($r); $n = 0;
  echo "<p>Total Mhsw: <font size=+2>$jml</font></p>";
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['PRC-MhswID-'.$_SESSION['prodi'].$n] = $w['MhswID'];
    $_SESSION['PRC-ProgramID-'.$_SESSION['prodi'].$n] = $w['ProgramID'];
    $_SESSION['PRC-ProdiID-'.$_SESSION['prodi'].$n] = $w['ProdiID'];
  }
  // parameter
  $_SESSION['PRC-Max-'. $_SESSION['prodi']] = $n;
  $_SESSION['PRC-POS-'. $_SESSION['prodi']] = 0;
  
  // IFRAME
  echo "<p><IFRAME src='_proseskhsmhsw.go.php?gos=prckhs' frameborder=0 height=300 width=100%>
  </IFRAME></p>";
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Total KHS Mahasiswa");
TampilkanHeaderProses();
$gos();
?>
