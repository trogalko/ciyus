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
      <td class=ul><a href='?mnux=$lap[1]&bck=baa.lap'>$lap[0]</a></td>
      <td class=ul>$lap[1]</td></tr>";
  }
  echo "</table></p>";
}

// *** Parameters & Variables ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$arrKeuLap = array("Daftar Pembayaran Mhsw Baru->keu.lap.barubayar1",
  "Rekapitulasi Jumlah Mahasiswa Baru->keu.lap.rekapcama",
  "Laporan Jumlah Mahasiswa Generate NIM->baa.lap.jumlahmhswnim",
  "Rincian Kewajiban & Pembayaran Mhsw->keu.lap.rinciwajibbayar",
  "Daftar Pembayaran BPM Mahasiswa->keu.lap.bpm",
  "Daftar Mhsw Berhutang->keu.lap.mhswhutang&hut=-1",
  "Daftar Mhsw Kelebihan Bayar->keu.lap.mhswhutang&hut=1",
  "Daftar Alamat Mahasiswa->baa.lap.mhsw.alamat",
  "Daftar Alamat Excel->baa.khs.alamat"
  );
$gos = (empty($_REQUEST['gos']))? "DftrLapKeu" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Laporan Keuangan Mahasiswa");
$gos();
?>
