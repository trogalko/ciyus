<?php
// Author: Emanuel Setio Dewo
// 28 June 2006
// www.sisfokampus.net

// *** Functions ***
function DaftarJadwal() {
  $s = "select j.*,
    concat(d.Nama, ', ', d.Gelar) as DSN, JumlahMhsw, 
    time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join dosen d on j.DosenID=d.Login
    where j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProgramID, '.$_SESSION[prid].') > 0
      and INSTR(j.ProdiID, '.$_SESSION[prodi].') > 0
    order by j.HariID, j.JamMulai, j.NamaKelas";
  $r = _query($s);
  $hdr = "<tr><th class=ttl>ID</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Jml Mhsw</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Assisten</th>
    </tr>";
  $hr = 'abcdefghijklmnopqr';
  echo "<p><table class=box cellspacing=1>";
  while ($w = _fetch_array($r)) {
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
      echo "<tr><td class=ul colspan=8><b>$_hr</b></td></tr>";
      echo $hdr;
    }
    $AssistenDosen = GetAssistenDosen($w['JadwalID']);
    echo "<tr>
      <td class=inp>$w[JadwalID]</td>
      <td class=ul>$w[JM]~$w[JS]</td>
      <td class=ul>$w[RuangID]&nbsp;</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]</td>
      <td class=ul>$w[JenisJadwalID]</td>
      <td class=ul align=right>$w[JumlahMhsw]</td>
      <td class=ul align=right>$w[SKS]</td>
      <td class=ul>$w[DSN]</td>
      <td class=ul>$AssistenDosen&nbsp;</td>
      </tr>";
  }
  echo "</table></p>";
}
function GetAssistenDosen($jdwlid) {
  $s = "select jd.JadwalDosenID, d.Nama as DSN
    from jadwaldosen jd
      left outer join dosen d on jd.DosenID=d.Login
    where jd.JadwalID=$jdwlid
    order by d.Nama";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = "$w[DSN]";
  }
  return implode(', ', $a);
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? "DaftarJadwal" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Jadwal Kuliah");
TampilkanTahunProdiProgram('inq.jadwal');
if (!empty($tahun) && !empty($prid) && !empty($prodi))
  $gos();
?>
