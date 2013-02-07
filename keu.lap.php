<?php
// Author: Emanuel Setio Dewo
// 05 Mey 2006

function DftrLapKeu() {
  global $arrKeuLap;
  $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th><th class=ttl>Nama Laporan</th>
    <th class=ttl>Script</th></tr>";
  for ($i=0; $i<sizeof($arrKeuLap); $i++) {
    $n++;
    $lap = explode('->', $arrKeuLap[$i]);
    echo "<tr><td class=inp>$n</td>
      <td class=ul><a href='?mnux=keu.lap.$lap[1]&bck=akd.lap'>$lap[0]</a></td>
      <td class=ul>$lap[1]</td></tr>";
  }
  echo "</table></p>";
}

// *** Parameters & Variables ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$hut = GetSetVar('hut', -1);
$arrKeuLap = array("Daftar Pembayaran Mhsw Baru->barubayar1",
  "Rekapitulasi Jumlah Calon Mahasiswa->rekapcama",
  "Rincian Kewajiban & Pembayaran Mhsw->rinciwajibbayar",
  "Setoran Mahasiswa->setormhsw",
  "Daftar Mhsw Berhutang->mhswhutang&hut=-1",
  "Daftar Mhsw Kelebihan Bayar->mhswhutang&hut=1",
  "Daftar Rincian Tagihan dan Pembayaran Mhsw->rincimahasiswa",
  "Daftar Kewajiban Per Angkatan->wajibangkatan"
  );
$gos = (empty($_REQUEST['gos']))? "DftrLapKeu" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Laporan Keuangan Mahasiswa");
$gos();
?>
