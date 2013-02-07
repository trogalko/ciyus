<?php
// Author: Emanuel Setio Dewo
// 18 July 2006

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
  <form action='_proses_cek_biaya.php' method=POST>
  <input type=hidden name='gos' value='ProsesHitung'>
  <tr><td>Tahun Akademik</td>
    <td><input type=text name='tahun' value='$_SESSION[tahun]' size=6 maxlength=5></td></tr>
  <tr><td>Program Studi</td>
    <td><select name='prodi'>$optprodi</select></td></tr>
  <tr><td colspan=2><input type=submit name='Proses' value='Proses'></td></tr>
  </form></table></p>";
}
function ProsesHitung() {
  $s = "select KHSID, MhswID
    from khs
    where TahunID='$_SESSION[tahun]'
      and ProdiID='$_SESSION[prodi]'
    order by KHSID";
  $r = _query($s);
  $jml = _num_rows($r); $n = 0;
  echo "<hr><p>Jumlah data: <font size=+1>$jml</font></p>";
  echo "<p><table class=box cellpadding=4 cellspacing=1>
        <tr><th class=ttl>MHSWID</th><th class=ttl>Balance 20061</th><th class=ttl>Balance 20062</th>";
  while ($w = _fetch_array($r)) {
    $khs = GetFields('khs', 'tahunid', 20061, "Biaya , Bayar , Tarik , Potongan");
    $balance = $khs['Bayar'] - $khs['Biaya'] + $khs['Potongan'] - $khs['Tarik'];
    $bal = GetFields('bipotmhsw bm', 'tahunID = 20062 and MhswID',$w['MhswID'], "(bm.Jumlah * bm.Besar) as TOT,
      format(bm.Jumlah * bm.Besar, 0) as TOTS,
      format(bm.Dibayar, 0) as BYR, format(bm.Besar, 0) as BSR" );
    $balance2 = $bal['TOT'] - $bal['BSR'];
    echo "<tr><td class=ul>$w[MhswID]</td><td class=ul>" . number_format($balance) ."</td><td class=ul>". number_format($balance2) ."</td></tr>" ; 
  }
  echo "</table></p>";
}
// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Hitung Keuangan Mahasiswa");
TampilkanHeaderProses();
$gos();
?>
