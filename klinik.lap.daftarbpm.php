<?php
// Author: Emanuel Setio Dewo
// 05/03/2007

// *** Functions ***
function CetakDaftarBPM() {
  if (empty($_SESSION['tahun'])) die(ErrorMsg("Tahun Akademik Kosong", 
    "Tentukan Tahun Akademik terlebih dahulu untuk melihat laporan."));
  
  $s = "select bm.*, m.Nama
    from bayarmhsw bm
      left outer join mhsw m on bm.MhswID=m.MhswID
    where m.ProdiID='11'
      and bm.TahunID='$_SESSION[tahun]'
      and (bm.Jumlah + bm.JumlahLain) > 0
    order by bm.BayarMhswID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>#</th>
    <th class=ttl>No. BPM</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Lain2</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $tgl = FormatTanggal($w['Tanggal']);
    $jml = number_format($w['Jumlah']);
    $jml2 = number_format($w['JumlahLain']);
    $c = ($w['Proses'] == 1)? "class=ul" : "class=nac";
    $c = ($jml + $jml2 > 0)? $c : "class=nac";
    echo "<tr>
      <td class=inp>$n</td>
      <td $c>$w[BayarMhswID]</td>
      <td $c>$tgl</td>
      <td $c>$w[Keterangan]</td>
      <td $c>$w[MhswID]</td>
      <td $c>$w[Nama]</td>
      <td $c align=right>$jml</td>
      <td $c align=right>$jml2</td>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar BPM Klinik");
TampilkanTahunSaja($_SESSION['mnux'], "CetakDaftarBPM", 'klinik.lap');

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
?>
