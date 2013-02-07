<?php

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Hitung BIPOT Mahasiswa KBK");

// *** Functions ***
function TampilkanHeaderProses() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table cellspacing=1 cellpadding=4>
  <form action='_proses_bipot_mhsw_kbk.php' method=POST>
  <input type=hidden name='gos' value='ProsesHitung'>
  <tr><td>Periode Akademik</td>
    <td><input type=text name='tahunak' value='$_SESSION[tahunak]' size=6 maxlength=5></td></tr>
  <tr><td>Angkatan</td>
    <td><input type=text name='tahun1' value='$_SESSION[tahun1]' size=6 maxlength=4></td></tr>
  <tr><td>Program Studi</td>
    <td><select name='prodi'>$optprodi</select></td></tr>
  <tr><td colspan=2><input type=submit name='Proses' value='Proses'></td></tr>
  </form></table></p>";
  
  echo "<p><table cellpadding=4 cellspacing=1>
        <tr><td class=inp>Keterangan</td><td class=wrn>Pastikan Mahasiswa yang akan diproses BIPOT-nya telah memiliki Master BIPOT</td></tr></table></p>";
}
function ProsesHitung() {
  $whr = '';
  if (!empty($_SESSION['prodi'])) $whr = "and k.ProdiID='$_SESSION[prodi]' ";
  
  $s = "SELECT k.KHSID, m.MhswID, m.ProdiID, count( k.TahunID ) as JUM
        FROM khs k
        LEFT OUTER JOIN mhsw m ON k.MhswID = m.MhswID
        WHERE left( m.TahunID, 4 ) = '$_SESSION[tahun1]'
          AND k.TahunID = '$_SESSION[tahunak]'
          $whr
        GROUP BY k.MhswID";

  $r = _query($s);
  $jml = _num_rows($r); $n = 0;
  echo "<hr><p>Jumlah data: <font size=+1>$jml</font></p>";
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['KBK-MhswID-'. $_SESSION['prodi'] . $n] = $w['MhswID'];
    $_SESSION['KBK-KHSID-' . $_SESSION['prodi'] . $n] = $w['KHSID'];
    $_SESSION['KBK-JUM-'. $_SESSION['prodi'] . $n] = $w['JUM'];
  }
  $_SESSION['KBK-Max-'. $_SESSION['prodi']] = $jml;
  $_SESSION['KBK-Pos-'. $_SESSION['prodi']] = 0;
  
  // IFRAME
  echo "<p><IFRAME src='_proses_bipot_mhsw_kbk.go.php?gos=prosesbipot' frameborder=0 height=400 width=300>
  </IFRAME></p>";
}

// *** Parameters ***
$tahun1 = GetSetVar('tahun1', 2007);
$prodi = GetSetVar('prodi');
$tahunak = GetSetVar('tahunak');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Bipot Mahasiswa KBK");
TampilkanHeaderProses();
$gos();
?>
