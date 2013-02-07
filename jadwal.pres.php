<?php
// Author: Emanuel Setio Dewo
// 27 March 2006
// http://www.sisfokampus.net

include_once "jadwal.lib.php";

// *** Functions ***
function GetOptDsnJdwl($jdwl) {
  $arr = array();
  $arr[] = "<option value=''>--- Pengampu ---</option>";
  $arr[] = "<option value='$jdwl[DosenID]' selected>$jdwl[DSN]</option>";
  
  $s = "select jd.DosenID, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwaldosen jd
    left outer join dosen d on jd.DosenID=d.Login
    where jd.JadwalID='$jdwl[JadwalID]'
    order by d.Nama";
  
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arr[] = "<option value='$w[DosenID]'>$w[DSN]</option>";
  }
  return implode("\n", $arr);
}
function TampilkanHeaderJadwal($jdwl) {
  $optdsn = GetOptDsnJdwl($jdwl);
  $Tanggal = GetDateOption($_SESSION['Tanggal'], 'Tanggal');
  $btn = "<input type=button name='Kembali' value='Kembali ke Jadwal Kuliah' onClick=\"location='?mnux=jadwal'\">
    <input type=button name='TambahPres' value='Tambah Presensi dgn Form Lengkap' onClick=\"location='?mnux=jadwal.pres&md=1&JadwalID=$jdwl[JadwalID]&gos=PresAdd'\">
    <input type=button name='Reset' value='Refresh Data' onClick=\"location='?mnux=jadwal.pres&JadwalID=$jdwl[JadwalID]'\">";
  $kls = (empty($jdwl['NamaKelas']))? "&nbsp;" : "($jdwl[NamaKelas])";
  $Pertemuan = GetaField('presensi', "JadwalID", $jdwl['JadwalID'], "max(Pertemuan)")+1;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama] $kls ($jdwl[JENIS])</td>
    <td class=inp>SKS</td><td class=ul>$jdwl[SKS] dari $jdwl[SKSAsli] SKS</td></tr>
  <tr><td class=inp>Waktu Kuliah</td><td class=ul>$jdwl[HR], $jdwl[JM] ~ $jdwl[JS]</td>
    <td class=inp>Tempat</td><td class=ul>$jdwl[KMP], $jdwl[RuangID]</td></tr>
  <tr><td class=inp>Dosen Pengampu</td><td class=ul>$jdwl[DSN]</td>
    <td class=inp>Kehadiran</td><td class=ul>$jdwl[Kehadiran]/$jdwl[RencanaKehadiran], Minimal: $jdwl[KehadiranMin]</tr>
  
  <form action='?' method=POST name='data'>
  <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
  <input type=hidden name='gos' value='PresAddSav'>
  <input type=hidden name='md' value=1>
  <tr><td class=inp>Tambah Presensi</td><td class=ul colspan=3>Pertemuan ke-<input type=text name='Pertemuan' value='$Pertemuan' size=3 maxlength=3>
    Tanggal: $Tanggal
    Pengampu: <select name='DosenID'>$optdsn</select>
    <input type=submit name='Tambah' value='Tambahkan'>
    </td></tr>
  </form>
  
  <tr><td class=ul colspan=4>Pilihan: $btn</td></tr>
  </table></p>";
}
function DftrPres($jdwl) {
  $s = "select pres.*, d.Nama, d. Gelar, concat(d.Nama, ', ', d.Gelar) as DSN,
    time_format(pres.JamMulai, '%H:%i') as JM, time_format(pres.JamSelesai, '%H:%i') as JS,
    date_format(pres.Tanggal, '%d/%m/%Y') as TGL
    from presensi pres
      left outer join dosen d on pres.DosenID=d.Login
    where pres.JadwalID=$jdwl[JadwalID]
    order by Pertemuan, pres.Tanggal";
  $r = _query($s);
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>Ke</th>
    <th class=ttl colspan=2>Tanggal</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Dosen</th>
    <th class=ttl title='Presensi Mhsw'>Mhsw</th>
    <th class=ttl title='Jumlah Mhsw Hadir'>Jml Hadir</th>
    <th class=ttl title='Jumlah Mhsw Sakit'>Jml Sakit</th>
    <th class=ttl title='Jumlah Mhsw Ijin'>Jml Ijin</th>
    <th class=ttl title='Jumlah Mhsw Mangkir'>Jml Mangkir</th>
    <th class=ttl title='Hapus Presensi ini'>Hapus</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    /*
    $JumH = Getafield('presensimhsw', "JenisPresensiID = 'H' and PresensiID", $w['PresensiID'], "count(MhswID)");
    $JumI = Getafield('presensimhsw', "JenisPresensiID = 'I' and PresensiID", $w['PresensiID'], "count(MhswID)");
    $JumM = Getafield('presensimhsw', "JenisPresensiID = 'M' and PresensiID", $w['PresensiID'], "count(MhswID)");
    $JumS = Getafield('presensimhsw', "JenisPresensiID = 'S' and PresensiID", $w['PresensiID'], "count(MhswID)");
    */
    $_ssum = "select JenisPresensiID, count(MhswID) as Jumlah
      from presensimhsw
      where PresensiID='$w[PresensiID]'
      group by JenisPresensiID";
    $_rsum = _query($_ssum);
    $arrJumlah = array();
    while ($_wsum = _fetch_array($_rsum)) {
      $_pid = $_wsum['JenisPresensiID'];
      $_jum = $_wsum['Jumlah'];
      $arrJumlah[$_pid] = $_jum+0;
    }
    $c = (sizeof($arrJumlah) == 0)? "class=inp2" : "class=ul";
    echo "<tr $ingat>
      <td class=inp>$w[Pertemuan]</td>
      <td $c><a href='?mnux=jadwal.pres&gos=PresAdd&JadwalID=$w[JadwalID]&PresensiID=$w[PresensiID]&md=0'><img src='img/edit.png'></a></td>
      <td $c>$w[TGL]</td>
      <td $c>$w[JM]~$w[JS]</td>
      <td $c>$w[DSN]</td>
      <td $c align=center><a href='?mnux=jadwal.pres&gos=PresMhsw&JadwalID=$w[JadwalID]&PresensiID=$w[PresensiID]'><img src='img/check.gif'></a></td>
      <td $c align=right>" . ($arrJumlah['H']+0) . "$JumH</td>
      <td $c align=right>" . ($arrJumlah['S']+0) . "$JumS</td>
      <td $c align=right>" . ($arrJumlah['I']+0) . "$JumI</td>
      <td $c align=right>" . ($arrJumlah['M']+0) . "</td>
      <td $c align=center><a href='?mnux=jadwal.pres&gos=PresDel&PresensiID=$w[PresensiID]'><img src='img/del.gif'></a></td>
      </tr>";
  }
  echo "</table></p>";
}
function PresAdd($jdwl) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $PresensiID = $_REQUEST['PresensiID'];
    $p = GetFields('presensi', "PresensiID", $PresensiID, 
      "*, time_format(JamMulai, '%H:%i') as JM, time_format(JamSelesai, '%H:%i') as JS");
    $jdl = "Edit Presensi";
  }
  else {
    $p = array();
    $p['PresensiID'] = 0;
    $p['JadwalID'] = $jdwl['JadwalID'];
    $p['Pertemuan'] = GetaField('presensi', 'JadwalID', $jdwl['JadwalID'], "max(Pertemuan)")+1;
    $p['Tanggal'] = date('Y-m-d');
    $p['JM'] = $jdwl['JM'];
    $p['JS'] = $jdwl['JS'];
    $p['Catatan'] = '';
    $p['DosenID'] = '';
    $jdl = "Tambah Presensi";
  }
  $opttgl = GetDateOption($p['Tanggal'], 'Tanggal');
  //$arrdosen = explode('.', TRIM($jdwl['DosenID'], '.'));
  //$strdosen = implode(',', $arrdosen);
  //$optdsn = GetOption2('dosen', "concat(Nama, ', ', Gelar)", 'Nama', $p['DosenID'], "Login in ($strdosen)", 'Login');
  $optdsn = GetOptDsnJdwl($jdwl);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='data'>
  <input type=hidden name='mnux' value='jadwal.pres'>
  <input type=hidden name='gos' value='PresAddSav'>
  <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
  <input type=hidden name='PresensiID' value='$p[PresensiID]'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Pertemuan ke-</td><td class=ul><input type=text name='Pertemuan' value='$p[Pertemuan]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>Dosen Pengampu</td><td class=ul><select name='DosenID'>$optdsn</select></td></tr>
  
  <tr><td class=inp>Tanggal</td><td class=ul>$opttgl</td></tr>
  <tr><td class=inp>Jam Mulai-Selesai</td><td class=ul>
    <input type=text name='JamMulai' value='$p[JM]' size=5 maxlegth=5>
    <input type=text name='JamSelesai' value='$p[JS]' size=5 maxlegth=5></td></tr>
  <tr><td class=inp>Catatan</td>
    <td class=ul><textarea name='Catatan' cols=30 rows=5>$p[Catatan]</textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=Reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=jadwal.pres&JadwalID=$jdwl[JadwalID]'\"></td></tr>
  </form></table></p>";
}
function PresAddSav($jdwl) {
  $md = $_REQUEST['md']+0;
  $PresensiID = $_REQUEST['PresensiID']+0;
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $DosenID = $_REQUEST['DosenID'];
  $Pertemuan = $_REQUEST['Pertemuan']+0;
  $JM = (empty($_REQUEST['JamMulai']))? $jdwl['JamMulai'] : $_REQUEST['JamMulai'];
  $JS = (empty($_REQUEST['JamSelesai']))? $jdwl['JamSelesai'] : $_REQUEST['JamSelesai'];
  $Catatan = sqling($_REQUEST['Catatan']);
  if (!empty($DosenID)) {
    if ($md == 0) {
      $s = "update presensi set Pertemuan=$Pertemuan, DosenID='$DosenID',
        Tanggal='$Tanggal', JamMulai='$JM', JamSelesai='$JS', Catatan='$Catatan',
        LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
        where PresensiID='$PresensiID' ";
      $r = _query($s);
    }
    else {
      $s = "insert into presensi(JadwalID, TahunID, Pertemuan,
        DosenID, Tanggal,
        JamMulai, JamSelesai, Catatan,
        LoginBuat, TanggalBuat)
        values ($jdwl[JadwalID], '$jdwl[TahunID]', $Pertemuan,
        '$DosenID', '$Tanggal',
        '$JM', '$JS', '$Catatan',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
      UpdateJmlKehadiran($jdwl['JadwalID']);      
    }
  }
  else echo ErrorMsg("Tidak dapat Ditambahkan",
    "Dosen pengampu belum diset. Data pertemuan tidak ditambahkan.");
  DftrPres($jdwl);
}
function UpdateJmlKehadiran($JadwalID) {
  // update kehadiran
  $jml = GetaField('presensi', 'JadwalID', $JadwalID, "count(*)")+0;
  $s = "update jadwal set Kehadiran=$jml where JadwalID=$JadwalID";
  $r = _query($s);
}

// ********************
// *** Absensi Mhsw ***
// ********************

function TampilkanHeaderPresensiMhsw($jdwl, $Pres) {
  $optpres = GetOption2("jenispresensi", "concat(JenisPresensiID, ' - ', Nama)", 'JenisPresensiID', 'H', '', "JenisPresensiID");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Pertemuan ke</td><td class=ul>$Pres[Pertemuan]</td>
    <td class=inp>Tanggal</td><td class=ul>$Pres[TGL]</td>
    <td class=inp>Jam</td><td class=ul>$Pres[JM]~$Pres[JS]</td>
    <td class=inp>Pengampu</td><td class=ul>$Pres[DSN]</td>
    <td class=ul><input type=button name='Kembali' value='Daftar Pertemuan' onClick=\"location='?mnux=jadwal.pres&JadwalID=$jdwl[JadwalID]'\"></td>
  </tr>
  <tr><td class=inp colspan=3>Set semua mhsw ke: </td>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='jadwal.pres'>
    <input type=hidden name='jadwalid' value='$jdwl[JadwalID]'>
    <input type=hidden name='PresensiID' value='$Pres[PresensiID]'>
    <input type=hidden name='gos' value='SetPresensiMhsw'>
    <td class=ul colspan=7><select name='JenisPresensiID'>$optpres</select>
    <input type=submit name='Set' value='Set Status'></td>
    </form></tr>
  </table></p>";
}
function SetPresensiMhsw($jdwl) {
  $JenisPresensiID = $_REQUEST['JenisPresensiID'];
  if (!empty($JenisPresensiID)) {
    $nilai = GetaField('jenispresensi', 'JenisPresensiID', $JenisPresensiID, 'Nilai');
    $s0 = "update presensimhsw set JenisPresensiID='$JenisPresensiID', Nilai='$nilai'
      where PresensiID='$_REQUEST[PresensiID]' ";
    $r0 = _query($s0);
  }
  PresMhsw($jdwl);
}
function PresMhsw($jdwl) {
  $PresensiID = $_REQUEST['PresensiID'];
  $Pres = GetFields("presensi pres
    left outer join dosen d on pres.DosenID=d.Login", 
    "pres.PresensiID", $PresensiID, 
    "pres.*, concat(d.Nama, ', ', d.Gelar) as DSN,
    date_format(Tanggal, '%d-%m-%Y') as TGL,
    time_format(JamMulai, '%H:%i') as JM, time_format(JamSelesai, '%H:%i') as JS");
  TampilkanHeaderPresensiMhsw($jdwl, $Pres);
  CekPresensiMhsw($jdwl, $Pres);
  
  // Tampilkan Presensi
  $arrJenisPresensi = GetArrayJenisPresensi();
  $s = "select pm.*, m.Nama, m.ProdiID
    from presensimhsw pm
      left outer join mhsw m on pm.MhswID=m.MhswID
    where pm.PresensiID=$PresensiID
    order by pm.MhswID";
  $r = _query($s); $nmr = 0; $prd = '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
    }
    $nmr++;
    $c = "class=ul";
    $optpresmhsw = GetOptionPresensiMhsw($arrJenisPresensi, $w['JenisPresensiID']);
    if ($w['JenisPresensiID'] == 'M') $col = 'RED';
    elseif($w['JenisPresensiID'] == 'S') $col = 'GREEN';
    elseif($w['JenisPresensiID'] == 'I') $col = 'YELLOW';
    else $col = 'WHITE';
    echo "<form action='jadwal.pres.mhsw.php' method=POST target='_blank'>
      <input type=hidden name='PresensiMhswID' value='$w[PresensiMhswID]'>
      <tr bgcolor = $col><td class=inp>$nmr</td>
      <td $c>$w[MhswID]</td>
      <td $c>$w[Nama]</td>
      <td $c>$optpresmhsw</td>
      </tr></form>";
  }
  echo "</table></p>";
}
function GetArrayJenisPresensi() {
  $s = "select * from jenispresensi";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[JenisPresensiID]~$w[Nama]~$w[Nilai]";
  }
  return $arr;
}
function GetOptionPresensiMhsw($arr, $jen) {
  $str = '';
  for ($i=0; $i < sizeof($arr); $i++) {
    $a = explode('~', $arr[$i]);
    $ck = ($a[0] == $jen)? 'selected' : '';
    $str .= "<option value='$a[0]' $ck>$a[0] - $a[1]</option>";
  }
  return "<select name='JenisPresensiID' onChange='this.form.submit()'>".$str."</select>";
}
function CekPresensiMhsw($jdwl, $Pres) {
  // Default
  $serial = ($jdwl['JadwalSer'] == 0)? $jdwl['JadwalID'] : $jdwl['JadwalSer'];
  $jp = GetFields('jenispresensi', 'Def', 'Y', "JenisPresensiID, Nilai");
  // ambil data KRS
  $skrs = "select k.KRSID, k.MhswID 
    from krs k
      left outer join presensimhsw pm on k.MhswID=pm.MhswID and pm.PresensiID=$Pres[PresensiID]
      left outer join khs khs on k.KHSID=khs.KHSID
    where pm.PresensiMhswID is NULL
      and khs.Cetak='Y'
      and k.JadwalID='$serial'
    order by k.MhswID";
  $rkrs = _query($skrs);
  while ($wkrs = _fetch_array($rkrs)) {
    //echo "$wkrs[KRSID]. $wkrs[MhswID]<br />";
    $s = "insert into presensimhsw (JadwalID, KRSID,
      PresensiID, MhswID, 
      JenisPresensiID, Nilai)
      values ($jdwl[JadwalID], $wkrs[KRSID],
      '$Pres[PresensiID]', '$wkrs[MhswID]',
      '$jp[JenisPresensiID]', '$jp[Nilai]')";
    $r = _query($s);
  }
}
function PresDel() {
  $PresensiID = $_REQUEST['PresensiID'];
  $p = GetFields('presensi', 'PresensiID', $PresensiID, '*');
  echo Konfirmasi("Konfirmasi Penghapusan Presensi",
    "Apakah benar Anda akan menghapus presensi pada pertemuan ke <b>$p[Pertemuan]</b> ini?<br />
    Jika ya, maka semua data presensi mahasiswa pada pertemuan ini juga akan dihapus.
    <hr />
    Pilihan: <a href='?mnux=jadwal.pres&gos=PresDel1&PRID=$PresensiID'>Hapus</a> |
    <a href='?mnux=jadwal.pres&gos='>Batalkan Penghapusan</a>");
}
function PresDel1() {
  $prid = $_REQUEST['PRID']+0;
  $JadwalID = GetaField('presensi', 'PresensiID', $prid, 'JadwalID');
  // hapus presensi
  $s = "delete from presensi where PresensiID=$prid";
  $r = _query($s);
  // hapus presensi mhsw
  $s1 = "delete from presensimhsw where PresensiID=$prid";
  $r1 = _query($s1);
  // Hitung jumlah pertemuan
  $jml = GetaField('presensi', "JadwalID", $JadwalID, "count(*)")+0;
  $s2 = "update jadwal set Kehadiran=$jml where JadwalID=$JadwalID";
  $r2 = _query($s2);
  echo "<script>window.location = '?mnux=jadwal.pres'; </script>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$DosenID = GetSetVar('dosen');
$JadwalID = GetSetVar('JadwalID');
if (!empty($_REQUEST['Kirim'])) {
  $JadwalID = 0;
  $_SESSION['JadwalID'] = 0;
}
$gos = (empty($_REQUEST['gos']))? 'DftrPres' : $_REQUEST['gos'];
$Tanggal_y = GetSetVar('Tanggal_y', date('Y'));
$Tanggal_m = GetSetVar('Tanggal_m', date('m'));
$Tanggal_d = GetSetVar('Tanggal_d', date('d'));
$Tanggal = "$Tanggal_y-$Tanggal_m-$Tanggal_d";
$_SESSION['Tanggal'] = $Tanggal;

// *** Main ***
TampilkanJudul("Presensi Kuliah");
TampilkanHeaderDosenMK('jadwal.pres');
$jdwl = GetFields("jadwal j
  left outer join dosen d on j.DosenID=d.Login
  left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
  left outer join hari h on j.HariID=h.HariID
  left outer join ruang r on j.RuangID=r.RuangID
  left outer join kampus k on r.KampusID=k.KampusID", 
  'JadwalID', $JadwalID, 
  "j.*, d.Nama as NamaDosen, concat(d.Nama, ', ', d.Gelar) as DSN, 
  h.Nama as HR, k.Nama as KMP, jj.Nama as JENIS,
  time_format(j.JamMulai, '%H:%i') as JM,
  time_format(j.JamSelesai, '%H:%i') as JS");
if (!empty($jdwl)) {
  if ($jdwl['JumlahMhsw'] >= 0) {
    TampilkanHeaderJadwal($jdwl);
    $gos($jdwl);
  }
  else echo ErrorMsg("Tidak Ada Mhsw",
    "Tidak ada mahasiswa yang mendaftar KRS Matakuliah ini.<br />
    Tidak dapat mendatakan presensi dosen dan mahasiswa.");
}
?>
