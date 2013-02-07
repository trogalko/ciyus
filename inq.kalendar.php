<?php
// Author: Emanuel Setio Dewo
// 28 June 2006
// www.sisfokampus.net

// *** Functions ***
function KalendarAkd() {
  $t = GetFields('tahun', "ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
    $_SESSION['tahun'], '*');
  if (empty($t))
    echo ErrorMsg("Kalendar Akademik Tidak Ada",
      "Kalendar Akademik <font size=+1>$_SESSION[tahun]</font> tidak ada atau
      belum dibuat.<br />
      Hubungi MIS/IT untuk informasi lebih lanjut");
  else {
    $_prd = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
    $_prg = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
    $TglKRSMulai = FormatTanggal($t['TglKRSMulai']);
    $TglKRSSelesai = FormatTanggal($t['TglKRSSelesai']);
    $TglUbahKRSMulai = FormatTanggal($t['TglUbahKRSMulai']);
    $TglUbahKRSSelesai = FormatTanggal($t['TglUbahKRSSelesai']);
    $TglCuti = FormatTanggal($t['TglCuti']);
    $TglMundur = FormatTanggal($t['TglMundur']);
    $TglKembaliUangKuliah = FormatTanggal($t['TglKembaliUangKuliah']);
    $TglBayarMulai = FormatTanggal($t['TglBayarMulai']);
    $TglBayarSelesai = FormatTanggal($t['TglBayarSelesai']);
    $TglKuliahMulai = FormatTanggal($t['TglKuliahMulai']);
    $TglKuliahSelesai = FormatTanggal($t['TglKuliahSelesai']);
    $TglUTSMulai = FormatTanggal($t['TglUTSMulai']);
    $TglUTSSelesai = FormatTanggal($t['TglUTSSelesai']);
    $TglUASMulai = FormatTanggal($t['TglUASMulai']);
    $TglUASSelesai = FormatTanggal($t['TglUASSelesai']);
    $TglNilai = FormatTanggal($t['TglNilai']);
    
    echo "<p><table class=box cellspacing=1>
    <tr><td class=ul colspan=2><font size=+1>Kalendar Akademik</font></th></tr>
    <tr><td class=inp>Tahun Akademik</td>
      <td class=ul>$t[TahunID]</td></tr>
    <tr><td class=inp>Nama Tahun</td>
      <td class=ul>$t[Nama]</td></tr>
    <tr><td class=inp>Program</td>
      <td class=ul>$_prg</td></tr>
    <tr><td class=inp>Program Studi</td>
      <td class=ul>$_prd</td></tr>
    <tr><td class=inp>Tidak Aktif?</td><td class=ul><img src='img/book$t[NA].gif'></td></tr>
      
    <tr><td class=ul colspan=2><font size=+1>KRS</font></td></tr>
    <tr><td class=inp>Mulai KRS</td>
      <td class=ul>$TglKRSMulai</td></tr>
    <tr><td class=inp>Selesai KRS</td>
      <td class=ul>$TglKRSSelesai</td></tr>
    <tr><td class=inp>Mulai Ubah KRS</td><td class=ul>$TglUbahKRSMulai</td></tr>
    <tr><td class=inp>Selesai Ubah KRS</td><td class=ul>$TglUbahKRSSelesai</td></tr>
    <tr><td class=inp>Batas Pengajuan Cuti</td><td class=ul>$TglCuti</td></tr>
    <tr><td class=inp>Batas Pengajuan Mundur Kuliah</td><td class=ul>$TglMundur</td></tr>
    <tr><td class=inp>Batas Pengambilan Kelebihan Uang Kuliah</td><td class=ul>$TglKembaliUangKuliah</td></tr>
    
    <tr><td class=ul colspan=2><font size=+1>Masa Pembayaran</font></td></tr>
    <tr><td class=inp>Mulai Pembayaran</td><td class=ul>$TglBayarMulai</td></tr>
    <tr><td class=inp>Selesai Pembayaran</td><td class=ul>$TglBayarSelesai</td></tr>
    
    <tr><td class=ul colspan=2><font size=+1>Periode Perkuliahan</font></td></tr>
    <tr><td class=inp>Mulai Kuliah</td><td class=ul>$TglKuliahMulai</td></tr>
    <tr><td class=inp>Selesai Kuliah</td><td class=ul>$TglKuliahSelesai</td></tr>
    
    <tr><td class=ul colspan=2><font size=+1>Periode Ujian Tengah Semester</font></td></tr>
    <tr><td class=inp>Mulai UTS</td><td class=ul>$TglUTSMulai</td></tr>
    <tr><td class=inp>Selesai UTS</td><td class=ul>$TglUTSSelesai</td></tr>
    
    <tr><td class=ul colspan=2><font size=+1>Periode Ujian Akhir Semester</font></td></tr>
    <tr><td class=inp>Mulai UAS</td><td class=ul>$TglUASMulai</td></tr>
    <tr><td class=inp>Selesai UAS</td><td class=ul>$TglUASSelesai</td></tr>
    <tr><td class=inp>Batas Akhir Penilaian</td><td class=ul>$TglNilai</td></tr>
    <tr><td class=inp>Catatan</td>
      <td class=ul>$t[Catatan]&nbsp;</td></tr>
    
    </table></p>";
  }
}

// *** Parameters ***
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? "KalendarAkd" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Kalendar Akademik");
TampilkanTahunProdiProgram('inq.kalendar');
if (!empty($tahun) && !empty($prid) && !empty($prodi))
  $gos();
?>
