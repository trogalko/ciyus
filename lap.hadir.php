<?php
// Author: Emanuel Setio Dewo
// 18 April 2006

// *** Functions ***
function TampilkanPilihanJenis() {
  global $urutanjenis;
  $DariTgl = GetDateOption($_SESSION['DariTgl'], 'DariTgl');
  $SampaiTgl = GetDateOption($_SESSION['SampaiTgl'], 'SampaiTgl');
  $a = '';
  for ($i=0; $i<sizeof($urutanjenis); $i++) {
    $sel = ($i == $_SESSION['_urutanjenis'])? 'selected' : '';
    $v = explode('~', $urutanjenis[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='lap.hadir'>
  <tr><td class=inp>Kode Dosen : </td>
  <td class=ul><input type=text value='$_SESSION[kdosen]' name='kdosen'></td>
  <td class=inp>Cetak Berdasarkan : </td>
  <td class=ul><select name='_urutanjenis' onChange='this.form.submit()'>$a</select></td></tr>
  <tr><td class=inp>Periode Absensi : </td>
    <td class=ul>$DariTgl</td>
    <td class=inp>Sampai Tanggal : </td>
    <td class=ul>$SampaiTgl</td></tr>
  
  <tr><td class=ul><input type=submit name=submit value=Kirim></td></tr>
  </form></table></p>";
}

function TampilkanJadwal($tahun, $prid, $prodi) {
  global $urutanjenis;
  $_u = explode('~', $urutanjenis[$_SESSION['_urutanjenis']]);
        $_key = $_u[1];
  $cek = (empty($_key))? '' : "and j.JenisJadwalID = '$_key'";
  $kd = (empty($_SESSION['kdosen']))? '' : "and j.DosenID = '$_SESSION[kdosen]'";
  
  $s = "select j.*
    from jadwal j 
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
      and j.NamaKelas <> 'KLINIK'
      $cek
      $kd
    order by j.MKKode";
  $r = _query($s);
  // Menu
  $trg = "target=_blank";
  echo "<p><table class=bsc cellspacing=1>
    <tr><td>Matakuliah »</td><td><a href='lap.hadir.rekap.php?tahun=$tahun&prid=$prid&prodi=$prodi' $trg>Rekap Kehadiran per MK</a> |
    <a href='lap.hadir.rinci.php?tahun=$tahun&prid=$prid&prodi=$prodi' $trg>Rincian Kehadiran per MK</a>
    </td></tr>
    <tr><td>Dosen »</td><td><a href='lap.hadir.rekap.dosen.php?tahun=$tahun&prid=$prid&prodi=$prodi' $trg>Rekap Kehadiran Dosen</a> |
    <a href='lap.hadir.rinci.dosen.php?tahun=$tahun&prid=$prid&prodi=$prodi' $trg>Rincian Kehadiran Dosen</a>
    </td></tr>
    <tr><td>Mahasiswa »</td><td><a href='lap.hadir.mhswtidakujian.php?tahun=$tahun&prodi=$prodi&prid=$prid' target=_blank>Mahasiswa yang Tidak Memenuhi Absensi untuk Ujian</a></td></tr>
    </table></p>";
  // Tampilkan data
  echo "<p><table class=box cellspacing=1>";
  echo "<tr><th class=ttl>ID</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>K/R</th>
    <th class=ttl>Kode Dosen</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Hadir</th>
    <th class=ttl>Rencana</th>
    <th class=ttl>Persen</th>
    <th class=ttl>Pres<br />Mhsw</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $_prsn = ($w['RencanaKehadiran'] == 0)? '0' : $w['Kehadiran']/$w['RencanaKehadiran']*100;
    $prsn = number_format($_prsn, 0);
    // Dosen
    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", ', ');
    echo "<tr>
      <td class=inp>$w[JadwalID]</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]&nbsp;</td>
      <td class=ul>$w[JenisJadwalID]</td>
      <td class=ul>$w[DosenID]</td>
      <td class=ul>$dosen</td>
      <td class=ul align=right>$w[Kehadiran]</td>
      <td class=ul align=right>$w[RencanaKehadiran]</td>
      <td class=ul align=right>$prsn %</td>
      <td class=ul align=center><a href='lap.hadir.mhsw.php?JadwalID=$w[JadwalID]&prn=0'><img src='img/printer.gif'></a></td>
      </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$DariTgl_m = GetSetVar('DariTgl_m', date('m'));
$DariTgl_d = GetSetVar('DariTgl_d', date('d'));
$DariTgl_y = GetSetVar('DariTgl_y', date('Y'));
$DariTgl = "$DariTgl_y-$DariTgl_m-$DariTgl_d";
$_SESSION['DariTgl'] = $DariTgl;

$SampaiTgl_d = GetSetVar('SampaiTgl_d', date('d'));
$SampaiTgl_m = GetSetVar('SampaiTgl_m', date('m'));
$SampaiTgl_y = GetSetVar('SampaiTgl_y', date('Y'));
$SampaiTgl = "$SampaiTgl_y-$SampaiTgl_m-$SampaiTgl_d";
$_SESSION['SampaiTgl'] = $SampaiTgl;
$urutanjenis = array(0=>"Semua~", 1=>"Kuliah~K", 2=>"Responsi~R");
$_urutanjenis = GetSetVar('_urutanjenis', 0);
$kdosen = GetSetVar('kdosen');

// *** Main ***
TampilkanJudul("Laporan Kehadiran");
TampilkanTahunProdiProgram("lap.hadir", "", '', '', 1);
TampilkanPilihanJenis();
TampilkanJadwal($tahun, $prid, $prodi);
$gos = $_REQUEST['gos'];
if (!empty($gos)) $gos();
?>
