<?php
// Author: Emanuel Setio Dewo
// 31 Okt 2006
include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Denda & Pembayaran");
// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Proses Export Jadwal");
$gos();

function TampilkanPesan() {
  echo "<p>Akan ditampilkan mahasiswa yg memiliki pembayaran 10% lebih besar dari pada BPS+BPP SKS-nya</p>
  <form action='?'>
  <input type=hidden name='gos' value='TampilkanMhsw'>
  Tahun Akd: <input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
  <input type=submit name='Tampilkan' value='Tampilkan'>
  </form>"; 
}
function TampilkanMhsw() {
  TampilkanPesan();
  $s = "select k.*, m.Nama
    from khs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.TahunID='$_SESSION[tahun]'
      and k.Bayar > k.Biaya
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>#</th>
  <th class=ttl>NPM</th>
  <th class=ttl>Nama</th>
  <th class=ttl>Total<br />Biaya</th>
  <th class=ttl>Total<br />Bayar</th>
  <th class=ttl>BPS</th>
  <th class=ttl>SKS</th>
  <th class=ttl>BPP+SKS</th>
  <th class=ttl>10% Denda</th>
  <th class=ttl>Cek</th>
  ";
  while ($w = _fetch_array($r)) {
    $n++;
    $_biaya = number_format($w['Biaya']);
    $_bayar = number_format($w['Bayar']);
    // BPS = 11, BPP SKS = 5
    $_bps = GetaField('bipotmhsw', "TahunID='$_SESSION[tahun]' and MhswID='$w[MhswID]' and BIPOTNamaID",
      11, "Jumlah * Besar");
    $_sks = GetaField('bipotmhsw', "TahunID='$_SESSION[tahun]' and MhswID='$w[MhswID]' and BIPOTNamaID",
      5, "Jumlah * Besar");
    $_bi2 = $_bps + $_sks;
    $_bi10 = ($_bi2 * 0.1); //$_bi2 + ;
    $bi2 = number_format($_bi2);
    $bi10 = number_format($_bi10);
    //$bpm = GetaField("bayarmhsw", "TahunID='$_SESSION[tahun]' and MhswID='$w[MhswID]' and Jumlah", 
    //  $_bi10, "BayarMhswID");
    $ketemu = ($w['Bayar'] - $w['Biaya'] == $_bi10)? "Ketemu" : "&times;";
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$_biaya</td>
    <td class=ul align=right><b>$_bayar</td>
    <td class=ul align=right>". number_format($_bps) ."</td>
    <td class=ul align=right>". number_format($_sks) ."</td>
    <td class=ul align=right>$bi2</td>
    <td class=ul align=right>$bi10</td>
    <td class=ul>$ketemu</td>
    </tr>";
  }
  echo "</table></p>";
}
?>
