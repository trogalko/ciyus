<?php
// Author: Emanuel Setio Dewo
// 27 June 2006
// www.sisfokampus.net

// *** Functions ***
function DftrLapAkd() {
  global $arrAkdLap;
  $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th><th class=ttl>Nama Laporan</th>
    <th class=ttl>Script</th></tr>";
  for ($i=0; $i<sizeof($arrAkdLap); $i++) {
    $n++;
    $lap = explode('->', $arrAkdLap[$i]);
    echo "<tr><td class=inp>$n</td>
      <td class=ul><a href='?mnux=klinik.lap.$lap[1]&bck=klinik.lap'>$lap[0]</a></td>
      <td class=ul>$lap[1]</td></tr>";
  }
  echo "</table></p>";
}

// *** Parameters & Variables ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$arrAkdLap = array("Deposit Mahasiswa->deposit",
  "Rekap Deposit Mahasiswa->depositrekap",
  "Daftar BPM Mhsw Klinik->daftarbpm",
  "Mhsw Belum Bayar->belumbayar",
  "Matakuliah Semester ini->kuliah"
  );
$gos = (empty($_REQUEST['gos']))? "DftrLapAkd" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Laporan Kepaniteraan");
$gos();
?>
