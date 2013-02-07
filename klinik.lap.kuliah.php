<?php
// Author: Emanuel Setio Dewo
// 05/03/2007

// *** Functions ***
function CetakKuliahKlinik() {
  $s = "select j.*
    from jadwal j
    where INSTR(j.ProdiID, '.11.')>0
      and j.NamaKelas = 'KLINIK'
    order by RuangID, TglMulai, JenisJadwalID, NamaKelas";
  $r = _query($s); $n = 0; $rg = 'qwertyuiop';
  echo "<p><table class=box cellspacing=1>";
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Mulai</th>
    <th class=ttl>Selesai</th>
    <th class=ttl>Jml Mhsw</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($rg != $w['RuangID']) {
      $rg = $w['RuangID'];
      echo "<tr><td class=ul colspan=5><font size=+1>$w[RuangID]</font></td></tr>";
      echo $hdr;
    }
    $TM = FormatTanggal($w['TglMulai']);
    $TS = FormatTanggal($w['TglSelesai']);
    $jml = number_format($w['JumlahMhsw']);
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[JenisJadwalID]</td>
      <td class=ul>$w[NamaKelas]</td>
      <td class=ul>$TM</td>
      <td class=ul>$TS</td>
      <td class=ul align=right>$jml</td>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar Kuliah Klinik");
TampilkanTahunSaja($_SESSION['mnux'], "CetakKuliahKlinik", 'klinik.lap');

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
?>
