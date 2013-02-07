<?php
// Author: Emanuel Setio Dewo
// 23 June 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanCariTahunMhsw($mnux='') {
  global $arrID;
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='TampilkanNilaiMhsw'>
  <tr><td class=ul colspan=2><font size=+1>$arrID[Nama]</font></td></tr>
  <tr><td class=inp>Tahun Akd</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=8 maxlength=10></td></tr>
  <tr><td class=inp>NPM Mhsw</td>
    <td class=ul><input type=text name='MhswID' value='$_SESSION[MhswID]' size=15 maxlength=20></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table></p>";
}
function TampilkanNilaiMhsw($mhsw) {
  // Tampilkan data
  $s = "select k.*, mk.Nama, j.JenisJadwalID
    from krs k
      left outer join mk mk on k.MKID=mk.MKID
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.TahunID='$_SESSION[tahun]' 
      and k.MhswID='$_SESSION[MhswID]'
      and k.StatusKRSID in ('A', 'T')
      and (j.JenisJadwalID <> 'R' or j.JenisJadwalID is null)
    order by k.MKKode, j.JenisJadwalID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Grade</th>
    <th class=ttl>Bobot</th>
    <th class=ttl>Buat</th>
    <th class=ttl>SK Koreksi</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $sk = AmbilSKNilai($w['KRSID']);
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$w[SKS]</td>
    <td class=ul align=center>$w[JenisJadwalID]&nbsp;</td>
    <td class=ul>$w[GradeNilai]</td>
    <td class=ul align=right>$w[BobotNilai]</td>
    <td class=ul align=center><a href='?mnux=koreksi.nilai&gos=BuatKoreksi&md=1&KRSID=$w[KRSID]&MhswID=$_SESSION[MhswID]&tahun=$_SESSION[tahun]'><img src='img/edit.png'></a></td>
    <td class=ul>$sk&nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
}
function AmbilSKNilai($krsid) {
  $s = "select KoreksiNilaiID, SK
    from koreksinilai
    where KRSID=$krsid
    order by KoreksiNilaiID";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = "<a href='?mnux=koreksi.nilai&KNID=$w[KoreksiNilaiID]&gos=BuatKoreksi&md=0&KRSID=$krsid'>$w[SK]</a>";
  }
  return implode(',', $a);
}
function BuatKoreksi($mhsw) {
  $md = $_REQUEST['md']+0;
  $krs = GetFields('krs', 'KRSID', $_REQUEST['KRSID'], '*');
  if ($md == 0) {
    $w = GetFields('koreksinilai', "KoreksiNilaiID", $_REQUEST['KNID'], '*');
    $jdl = "Edit SK Koreksi Nilai";
  }
  else {
    $w = array();
    $w['KoreksiNilaiID'] = 0;
    $w['Tanggal'] = date('Y-m-d');
    $w['SK'] = '';
    $w['Perihal'] = '';
    $w['TahunID'] = $krs['TahunID'];
    $w['KRSID'] = $krs['KRSID'];
    $w['MhswID'] = $mhsw['MhswID'];
    $w['MKID'] = $krs['MKID'];
    $w['GradeLama'] = $krs['GradeNilai'];
    $w['GradeNilai'] = $krs['GradeNilai'];
    $w['Pejabat'] = '';
    $w['Jabatan'] = '';
    $w['Lampiran'] = '';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah SK Koreksi Nilai";
  }
  $mk = GetFields('mk', 'MKID', $w['MKID'], "MKKode, Nama");
  $tgl = GetDateOption($w['Tanggal'], "TGL");
  $optnilai = GetOption2('nilai', "concat(Nama, ' (', Bobot, ')')", "Bobot desc", $w['GradeNilai'], "ProdiID='$mhsw[ProdiID]'", "Nama");
  // Tampilkan form
  CheckFormScript("SK,Perihal,GradeNilai,Keterangan,Pejabat,Jabatan");
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='koreksi.nilai'>
  <input type=hidden name='gos' value='KoreksiSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='KoreksiNilaiID' value='$w[KoreksiNilaiID]'>
  <input type=hidden name='TahunID' value='$w[TahunID]'>
  <input type=hidden name='KRSID' value='$w[KRSID]'>
  <input type=hidden name='MKID' value='$w[MKID]'>
  <input type=hidden name='MhswID' value='$w[MhswID]'>
  <input type=hidden name='GradeLama' value='$w[GradeLama]'>
  <tr><td class=ul colspan=2><font size=+1>$jdl</font></td></tr>
  <tr><td class=inp>Nomer SK</td><td class=ul><input type=text name='SK' value='$w[SK]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal SK</td><td class=ul>$tgl</td></tr>
  <tr><td class=inp>Perihal</td><td class=ul><input type=text name='Perihal' value='$w[Perihal]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul><font size=+1>$mk[Nama]</font> ($mk[MKKode])</td></tr>
  <tr><td class=inp>Nilai/Grade Lama</td><td class=ul><font size=+1>$w[GradeLama]</font></td></tr>
  <tr><td class=inp>Nilai/Grade Baru</td><td class=ul><select name='GradeNilai'>$optnilai</select></td></tr>
  <tr><td class=inp>Keterangan (Sebab2)</td><td class=ul><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
  <tr><td class=inp>Dibuat oleh</td><td class=ul><input type=text name='Pejabat' value='$w[Pejabat]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Jabatan</td><td class=ul><input type=text name='Jabatan' value='$w[Jabatan]' size=30 maxlenght=50></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=koreksi.nilai&gos=TampilkanNilaiMhsw'\"></td></tr>
  </form></table></p>"; 
}
function KoreksiSav() {
  $md = $_REQUEST['md'];
  $TahunID = $_REQUEST['TahunID'];
  $KRSID = $_REQUEST['KRSID'];
  $krs = GetFields('krs', 'KRSID', $KRSID, '*');
  $MKID = $_REQUEST['MKID'];
  $MhswID = $_REQUEST['MhswID'];
  $prodi = GetaField('mhsw', 'MhswID', $MhswID, 'ProdiID');
  $KoreksiNilaiID = $_REQUEST['KoreksiNilaiID'];
  $Tanggal = "$_REQUEST[TGL_y]-$_REQUEST[TGL_m]-$_REQUEST[TGL_d]";
  $SK = sqling($_REQUEST['SK']);
  $Perihal = sqling($_REQUEST['Perihal']);
  $GradeLama = $_REQUEST['GradeLama'];
  $GradeNilai = $_REQUEST['GradeNilai'];
  $BobotNilai = GetaField('nilai', "ProdiID='$prodi' and Nama", $GradeNilai, "Bobot")+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $Pejabat = sqling($_REQUEST['Pejabat']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  // Simpan SK
  if ($md == 0) {
    $s = "update koreksinilai set Tanggal='$Tanggal', TahunID='$TahunID', SK='$SK', Perihal='$Perihal',
      GradeNilai='$GradeNilai', Pejabat='$Pejabat', Jabatan='$Jabatan', Keterangan='$Keterangan',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where KoreksiNilaiID=$KoreksiNilaiID";
    $r = _query($s);
  }
  else {
    $s = "insert into koreksinilai (Tanggal, TahunID, SK, Perihal,
      KRSID, MhswID, MKID, GradeLama, GradeNilai,
      Pejabat, Jabatan, Keterangan,
      LoginBuat, TglBuat)
      values ('$Tanggal', '$TahunID', '$SK', '$Perihal',
      '$KRSID', '$MhswID', '$MKID', '$GradeLama', '$GradeNilai',
      '$Pejabat', '$Jabatan', '$Keterangan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  // Ubah KRS
  $s1 = "update krs set GradeNilai='$GradeNilai', BobotNilai='$BobotNilai', Final='Y', StatusKRSID='A' where KRSID=$KRSID";
  $r1 = _query($s1);
  // Tampilkan konfirmasi
  echo Konfirmasi("Koreksi Nilai Telah Dilakukan",
    "Koreksi nilai sudah dilakukan.<br />
    Di bawah ini akan dilakukan proses perhitungan IPK/IPS bagi mahasiswa yang bersangkutan.");
  // PROSES
  $mhsw = GetFields('mhsw', 'MhswID', $MhswID, "ProdiID, ProgramID");
  $prd = $mhsw['ProdiID'];
  $_SESSION["IPK$prd"] = 1;
  $_SESSION["IPK-MhswID$prd"."0"] = $MhswID;
  $_SESSION["IPK-KHSID$prd"."0"] = $krs['KHSID'];
  $max = $_SESSION['IPK'.$prodi];
  $_SESSION["IPK$prd".'POS'] = 0;
  echo "<p><IFRAME SRC='prc.ipk.go.php?gos=PRC2&tahun=$TahunID&prodi=$prd&prid=$mhsw[ProgramID]' width=300 height=75 frameborder=0>
  </IFRAME></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$MhswID = GetSetVar('MhswID');
$gos = (empty($_REQUEST['gos']))? '' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Koreksi Nilai Mhsw");
TampilkanCariTahunMhsw('koreksi.nilai');
if (!empty($MhswID)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID", 
    "m.MhswID", $_SESSION['MhswID'], 
    "m.*, prg.Nama as PRG, prd.Nama as PRD");
  if (empty($mhsw))
    echo ErrorMsg("Mhsw Tidak Ditemukan",
      "Mahasiswa dengan NPM: <font size=+1>$_SESSION[MhswID]</font> tidak ditemukan");
  else if (!empty($gos)) {
    include_once "mhsw.hdr.php";
    TampilkanHeaderBesar($mhsw, "koreksi.nilai", "TampilkanNilaiMhsw", 0);
    $gos($mhsw);
  }
}
?>
