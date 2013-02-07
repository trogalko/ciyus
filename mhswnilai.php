<?php
// Author: Emanuel Setio Dewo
// 22 March 2006

include_once "krs.lib.php";

// *** Function2 ***
function DftrNilai($mhsw, $datatahun, $khs) {
  $s = "select k.*, j.*,
      sk.Nama as SK, sk.Ikut, sk.Hitung
    from krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID
      left outer join statuskrs sk on k.StatusKRSID=sk.StatusKRSID
    where k.KHSID='$khs[KHSID]' and k.NA='N'
    order by j.MKKode";
  $r = _query($s);
  $nmr = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>#</th>
    <th class=ttl rowspan=2>Kode</th>
    <th class=ttl rowspan=2>Matakuliah</th>
    <th class=ttl rowspan=2>Kelas</th>
    <th class=ttl rowspan=2>SKS</th>
    <th class=ttl rowspan=2>Jen</th>
    <th class=ttl colspan=5>Tugas</th>
    <th class=ttl rowspan=2 title='Presensi'>Pres</th>
    <th class=ttl rowspan=2 title='Ujian Tengah Semester'>UTS</th>
    <th class=ttl rowspan=2 title='Ujian Akhir Semester'>UAS</th>
    <th class=ttl rowspan=2 title='Nilai Akhir'>Akhir</th>
    <th class=ttl rowspan=2 title='Grade Nilai'>Grade</th>
    <th class=ttl rowspan=2 title='Bobot Nilai'>Bobot</th>
    <th class=ttl rowspan=2 title='Status Matakuliah'>Status</th>
    </tr>
    <tr><th class=ttl>1</th>
      <th class=ttl>2</th>
      <th class=ttl>3</th>
      <th class=ttl>4</th>
      <th class=ttl>5</th>
      </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['Ikut'] == 'Y')? 'class=ul' : 'class=nac';
    $nmr++;
    $nil = '';
    for ($i=1; $i<=5; $i++) {
      $nilai = $w['Tugas'.$i];
      $nil .= "<td $c align=right>$nilai</td>";
    }
    echo "<tr><td class=inp title='$w[KRSID]'>$nmr</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[NamaKelas]&nbsp;</td>
      <td $c>$w[SKSAsli]</td>
      <td $c>$w[JenisJadwalID]</td>
      $nil
      <td $c align=right>$w[Presensi]</td>
      <td $c align=right>$w[UTS]</td>
      <td $c align=right>$w[UAS]</td>
      <td $c align=right>$w[NilaiAkhir]</td>
      <td $c align=center>$w[GradeNilai]</td>
      <td $c align=right>$w[BobotNilai]</td>
      <td $c>$w[SK]</td>
      </tr>";
  }
  echo "</table></p>";
}


// *** Parameters ***
if ($_SESSION['_LevelID'] == 120) {
  $mhswid = $_SESSION['_Login'];
}
else {
  $mhswid = GetSetVar('mhswid');
}
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'DftrNilai' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Nilai Semester Mahasiswa");
TampilkanCariMhsw('mhswnilai');

if (!empty($mhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID",
    "m.MhswID", $mhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD,
    sm.Nama as SM, sm.Nilai as SMNilai, sm.Keluar");
  if (empty($mhsw)) {
    echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      "Tidak ada mahasiswa dengan NPM: <b>$mhswid</b>");
  }
  else {
    $datatahun = GetFields('tahun',
      "KodeID='$_SESSION[KodeID]' and ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and TahunID",
      $tahun, '*');
    $khs = GetFields("khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID",
      "k.MhswID='$mhswid' and k.TahunID", $tahun,
      "k.*, sm.Nama as SM, sm.Nilai as SMNilai, sm.Keluar");
    if (empty($khs) || empty($datatahun)) {
      echo ErrorMsg("Mahasiswa Tidak Terdaftar",
        "<p>Ada dua kemungkinan kesalahan:</p>
        <ol>
        <li>Mahasiswa <b>$mhsw[Nama]</b> ($mhswid) tidak terdaftar untuk
        sesi/semester <b>$tahun</b>.</li>
        <li>Fakultas/Jurusan belum mengaktifkan tahun akademik: <b>$tahun</b>.
        </ol>");
    }
    else {
      // Cek maksimum SKS
      if (($khs['Sesi'] <=1) and ($khs['MaxSKS'] == 0)) {
        $MaxSKS = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'DefSKS')+0;
        $khs['MaxSKS'] = $MaxSKS;
        // Simpan
        $s = "update khs set MaxSKS=$MaxSKS where KHSID=$khs[KHSID] ";
        $r = _query($s);
      }
      HeaderKRSMhsw($mhsw, $datatahun, $khs);
      $gos($mhsw, $datatahun, $khs);
    }
  }
}
?>
