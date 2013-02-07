<?php
// Author: Emanuel Setio Dewo
// 18 June 2006
// www.sisfokampus.net

include "sisfokampus.php";
HeaderSisfoKampus("Detail Pembayaran Matakuliah Klinis");

echo "<SCRIPT>window.resizeTo(500, 500)</SCRIPT>";
$KRSID = $_REQUEST['KRS'];
$krs = GetFields('krs', 'KRSID', $KRSID, '*');

echo "<p><center><font size=+2>Detail Pembayaran Matakuliah</font><br />
  <font size=+3>$krs[MKKode]</font></center></p>";
$s = "select bm.*
  from bayarmhsw bm
  where bm.BayarMhswRef='$KRSID'
  order by bm.Tanggal";
$r = _query($s);
if (_num_rows($r) == 0) echo ErrorMsg("Belum Ada Pembayaran", 
  "Mahasiswa belum melakukan pembayaran untuk matakuliah ini.<hr size=1 color=silver>
  <input type=button name='Tutup' value='Tutup' onClick=\"javascript:window.close()\">");
else {
  $n = 0; $ttl = 0;
  echo "<p align=center><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>No BPM</th>
    <th class=ttl>Tanggal<br />Bank</th>
    <th class=ttl>Tanggal<br />Entry</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl>Keterangan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $TGL = FormatTanggal($w['Tanggal']);
    $TGLE = FormatTanggal($w['TanggalBuat']);
    $JML = number_format($w['Jumlah']);
    $ttl += $w['Jumlah'];
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[BayarMhswID]</td>
    <td class=ul>$TGL</td>
    <td class=ul>$TGLE</td>
    <td class=ul align=right>$JML</td>
    <td class=ul>$w[Keterangan]&nbsp;</td>
    </tr>";
  }
  $_ttl = number_format($ttl);
  echo "<tr><td class=ul colspan=4 align=right>Total :</td>
  <td class=ul align=right><font size=+1>$_ttl</font></td></tr>
  </table></p>
  <p align=center><input type=button name='Tutup' value='Tutup' onClick=\"javascript:window.close()\"></p>";
}
?>
