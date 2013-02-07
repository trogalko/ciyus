<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 18 Agustus 2006 // Selamat ulang tahun kemerdekaan RI ke-61

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Rekap Beasiswa");

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');

BuatRekapBeasiswa($tahun, $prodi);

// *** Functions ***
function BuatRekapBeasiswa($tahun, $prodi) {
  echo "<p align=center><font size=+1>Rekap Pemohon Beasiswa $tahun</font></p>";
  
  // Data
  $s = "select b.Nama, sum(bm.Besar) as BSR, sum(bm.Disetujui) as S7, count(bm.BeasiswaMhswID) as JML
    from beasiswamhsw bm
      left outer join beasiswa b on bm.BeasiswaID=b.BeasiswaID
    where bm.TahunID='$tahun' and bm.NA='N'
    group by bm.BeasiswaID";
  $r = _query($s); $n = 0; $_bsr = 0; $_s7 = 0; $_jml = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>Jenis Beasiswa</th>
    <th class=ttl>Jml Pemohon</th>
    <th class=ttl>Permohonan</th>
    <th class=ttl>Disetujui</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $BSR = number_format($w['BSR']);
    $S7 = number_format($w['S7']);
    $_bsr += $w['BSR'];
    $_s7 += $w['S7'];
    $JML = number_format($w['JML']);
    $_jml += $w['JML'];
    echo "<tr><td class=inp>$n</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$JML</td>
      <td class=ul align=right>$BSR</td>
      <td class=ul align=right>$S7</td>
      </tr>";
  }
  $bsr = number_format($_bsr);
  $s7 = number_format($_s7);
  $jml = number_format($_jml);
  echo "<tr><td class=ul colspan=2 align=right>Jumlah :</td>
  <td class=ul align=right><b>$jml</b></td>
  <td class=ul align=right><b>$bsr</b></td>
  <td class=ul align=right><b>$s7</b></td></tr>
  </table></p>";
}
?>
