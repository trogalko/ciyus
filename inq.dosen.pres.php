<?php
// Author: Emanuel Setio Dewo
// Email: setio.dewo@gmail.com
// 19/12/2006
// Deskripsi: inquiry kehadiran dosen

// *** Parameters ***
if ($_SESSION['_LevelID'] == 100) {
  $dsnid = $_SESSION['_Login'];
  $_SESSION['dsnid'] = $dsnid;
}
else $dsnid = GetSetVar('dsnid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? "TampilkanPresensiDosen" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Inquiry Kehadiran Dosen");
TampilkanHeaderInquiryPresensiDosen();
$gos();

// *** Functions ***
function TampilkanHeaderInquiryPresensiDosen() {
  global $KodeID, $arrID;
  if (!empty($_SESSION['dsnid'])) {
    $DSN = GetFields('dosen', 'Login', $_SESSION['dsnid'], "Nama, Gelar");
    $NamaDsn = "$DSN[Nama], $DSN[Gelar]";
  }
  else $NamaDsn = "-";
  if ($_SESSION['_LevelID'] == 100) {
    $strDSNID = "$_SESSION[dsnid] - <font size=+1>$NamaDsn</font>";
  }
  else {
    $strDSNID = "<input type=text name='dsnid' value='$_SESSION[dsnid]' size=8 maxlength=8> <font size=+1>$NamaDsn</font>";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <tr><td class=ttl colspan=5>$KodeID - $arrID[Nama]</td></tr>
  <tr><td class=inp>Tahun Akd</td>
      <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=8 maxlength=8></td>
      <td class=inp>Dosen</td>
      <td class=ul>$strDSNID</td>
      <td class=ul><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table></p>";
}
function TampilkanPresensiDosen() {
  if (!empty($_SESSION['dsnid']) && !empty($_SESSION['tahun'])) TampilkanPresensiDosen1();
}
function TampilkanPresensiDosen1() {
  $dsn = GetFields("dosen d
    left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
    left outer join prodi prd on d.Homebase=prd.ProdiID", 
    "d.Login", $_SESSION['dsnid'], 
    "d.Login, d.Nama, d.Gelar, sd.Nama as SD, prd.Nama as HB");
  TampilkanHeaderDosen($dsn);
  TampilkanDetailKuliah($dsn);
}
function TampilkanHeaderDosen($dsn) {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='dosenpa' method=POST>
  <input type=hidden name='mnux' value='dosen.PA'>
  <tr><td class=inp>Kode Dosen</td><td class=ul><font size=+1>$dsn[Login]</font> &nbsp;</td>
    <td class=inp>Nama</td><td class=ul><font size=+1>$dsn[Nama]</font>, $dsn[Gelar]</td></tr>
  <tr><td class=inp>Status</td><td class=ul>$dsn[SD] &nbsp;</td>
    <td class=inp>Homebase</td><td class=ul>$dsn[HB] ($dsn[Homebase])</td></tr>
  </form></table></p>";
}
function TampilkanDetailKuliah($dsn, $utama=0) {
  $s = "select j.*, h.Nama as HR
    from jadwal j
      left outer join hari h on j.HariID=h.HariID
    where j.DosenID='$dsn[Login]'
    order by j.HariID, j.JamMulai";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>ID</th>
    <th class=ttl>Hari</th>
    <th class=ttl>Jam Kuliah</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Hadir</th>
    <th class=ttl title='Rencana Kehadiran'>Rencana</th>
    <th class=ttl title='Detail'>Detil</th>
    </tr>";
  $_hr = -10;
  while ($w = _fetch_array($r)) {
    if ($_hr != $w['HariID']) {
      $_hr = $w['HariID'];
      $strhr = "<b>$w[HR]</b>";
    }
    else $strhr = "<img align='right' src='img/brch.gif'>";
    $JM = substr($w['JamMulai'], 0, 5);
    $JS = substr($w['JamSelesai'], 0, 5);
    $dtl = ($w['Kehadiran'] >0)? "<a href='?mnux=$_SESSION[mnux]&gos=PresDet&jid=$w[JadwalID]'><img src='img/zoom.png'></a>" : "&nbsp;";
    echo "<tr>
    <td class=inp>$w[JadwalID]</td>
    <td class=ul>$strhr</td>
    <td class=ul>$JM - $JS</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul align=center>$w[JenisJadwalID]</td>
    <td class=ul align=right>$w[Kehadiran]</td>
    <td class=ul align=right>$w[RencanaKehadiran]</td>
    <td class=ul align=center>$dtl</td>
    </tr>";
  }
  echo "</table></p>";
}
function PresDet() {
  $jid = $_REQUEST['jid'];
  $jdwl = GetFields('jadwal', 'JadwalID', $jid, "*");
  $hr = GetaField('hari', "HariID", $jdwl['HariID'], "Nama");
  $JM = substr($w['JamMulai'], 0, 5);
  $JS = substr($w['JamSelesai'], 0, 5);
  // Tampilkan header
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>ID</td>
      <td class=ul>$jdwl[JadwalID]</td>
      <td class=inp>Matakuliah</td>
      <td class=ul>$jdwl[MKKode] - $jdwl[Nama]</td>
      </tr>
  <tr><td class=inp>Hari, Jam</td>
      <td class=ul>$hr, $JM - $JS</td>
      <td class=inp>Kelas, Jenis, SKS</td>
      <td class=ul>$jdwl[NamaKelas], $jdwl[JenisJadwalID], $jdwl[SKS] SKS</td>
      </tr>
  <tr><td class=ul colspan=4><input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]'\"></td></tr>
  </table></p>";
  // Tampilkan detail kehadiran
  $s = "select p.PresensiID, p.TahunID, p.JadwalID,
    p.Pertemuan, p.DosenID, p.Tanggal, p.JamMulai, p.JamSelesai,
    p.Hitung, p.Catatan,
    d.Nama, d.Gelar
    from presensi p
      left outer join dosen d on p.DosenID=d.Login
    where p.JadwalID='$jid'
    order by p.Pertemuan";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Mulai</th>
    <th class=ttl>Selesai</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Catatan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $tgl = FormatTanggal($w['Tanggal']);
    $JS = substr($w['JamMulai'], 0, 5);
    $JM = substr($w['JamSelesai'], 0, 5);
    $catatan = $w['Catatan'];
    $catatan = str_replace(chr(13), "<br />", $catatan);
    echo "<tr>
    <td class=inp>$w[Pertemuan]</td>
    <td class=ul>$tgl</td>
    <td class=ul>$JM</td>
    <td class=ul>$JS</td>
    <td class=ul>$w[Nama], $w[Gelar]</td>
    <td class=ul>$w[Catatan]&nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
}
?>
