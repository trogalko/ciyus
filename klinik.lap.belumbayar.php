<?php
// Author: Emanuel Setio Dewo
// 05/03/2007

// *** Functions ***
function CetakBelumBayar() {
  if (empty($_SESSION['tahun'])) die(ErrorMsg("Tahun Akademik Kosong", 
    "Tentukan Tahun Akademik terlebih dahulu untuk melihat laporan."));
  
  $s = "select k.*, m.Nama
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.Harga > k.Bayar
      and m.ProdiID = '11'
    order by m.MhswID";
  $r = _query($s); $n = 0;
  
  echo "<p><table class=box cellspacing=1>";
  echo "<tr><th class=ttl>#</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Harga</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl>Kurang</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $hrg = number_format($w['Harga']);
    $byr = number_format($w['Bayar']);
    $krg = $w['Harga'] - $w['Bayar'];
    $krg = number_format($krg);
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$hrg</td>
      <td class=ul align=right>$byr</td>
      <td class=ul align=right>$krg</td>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar Mhsw Klinik Belum Bayar");
TampilkanTahunSaja($_SESSION['mnux'], "CetakBelumBayar", 'klinik.lap');

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
?>
