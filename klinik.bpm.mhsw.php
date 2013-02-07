<?php
// Author: Emanuel Setio Dewo
// 23 Nov 2006
// www.sisfokampus.net

include "sisfokampus.php";
include "klinik.lib.php";
HeaderSisfoKampus("Detail Pembayaran Matakuliah Klinis");

echo "<SCRIPT>window.resizeTo(500, 500)</SCRIPT>";

$MhswID = $_REQUEST['MhswID'];
$mhsw = GetFields('mhsw', 'MhswID', $MhswID, '*');

TampilkanJudul("Detail Pembayaran Mahasiswa");
TampilkanHeaderMhswKlinik($mhsw);

$s = "select bm.*
  from bayarmhsw bm
  where MhswID='$MhswID'
  order by bm.Tanggal";
$r = _query($s); $n = 0;
echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>No</th>
  <th class=ttl>B.P.M</th>
  <th class=ttl>Ke<br />Rekening</th>
  <th class=ttl>Bukti<br />Setoran</th>
  <th class=ttl>Tanggal<br />Setor</th>
  <th class=ttl>Tanggal<br />Entry</th>
  <th class=ttl>Jumlah</th>
  <th class=ttl>Keterangan</th>
  </tr>";
while ($w = _fetch_array($r)) {
  $n++;
  $Tanggal = FormatTanggal($w['Tanggal']);
  $TanggalEntry = FormatTanggal($w['TanggalBuat']);
  $JML = number_format($w['Jumlah']);
  echo "<tr>
    <td class=inp>$n</td>
    <td class=ul>$w[BayarMhswID]</td>
    <td class=ul>$w[RekeningID]&nbsp;</td>
    <td class=ul>$w[BuktiSetoran]&nbsp;</td>
    <td class=ul>$Tanggal&nbsp;</td>
    <td class=ul>$TanggalEntry&nbsp;</td>
    <td class=ul align=right>$JML&nbsp;</td>
    <td class=ul>$w[Keterangan]&nbsp;</th>
  </tr>";
}
echo "</table></p>";
echo "<p>Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()'></p>";
?>
