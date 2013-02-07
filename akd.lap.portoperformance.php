<?php
// Author: Emanuel Setio Dewo
// 05 Sept 2006
// http://www.sisfokampus.net

// *** Functions ***
function DaftarJadwal() {
  $s = "select j.*, 
    time_format(JamMulai, '%H:%i') as JM, time_format(JamSelesai, '%H:%i') as JS,
    concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwal j
      left outer join dosen d on j.DosenID=d.Login
    where j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
    order by j.HariID, j.JamMulai";
  $r = _query($s); $n = 0; $h = 256;
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>Jam Kuliah</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Cetak</th>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($h != $w['HariID']) {
      $h = $w['HariID'];
      $hari = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
      echo "<tr><td class=ul colspan=15><a name='$h'><font size=+1>$hari</font></a></td></tr>";
      echo $hdr;
    }
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[JM]-$w[JS]</td>
    <td class=ul>$w[RuangID]&nbsp;</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul>$w[JenisJadwalID]</td>
    <td class=ul>$w[SKS]</td>
    <td class=ul>$w[DSN]</td>
    <td class=ul align=center><a href='akd.lap.portoperformance.go.php?id=$w[JadwalID]' target=_blank><img src='img/printer.gif'></a></td>
    </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? "DaftarJadwal" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Penilaian Porto Folio dan Performance Mahasiswa");
TampilkanTahunProdiProgram('akd.lap.portoperformance');
if (!empty($tahun) && !empty($prodi)) $gos();
?>
