<?php
// Author: Emanuel Setio Dewo
// 18 April 2006

// *** Functions ***
function DftrMKMhsw($mhsw) {
	global $urutan;
	$_u = explode('~', $urutan[$_SESSION['_urutan']]);
  $_key = $_u[1];
  $s = "select krs.*, mk.MKKode, mk.Nama as NamaMK
    from krs krs
      left outer join mk on krs.MKID=mk.MKID
    where krs.MhswID='$mhsw[MhswID]'
    order by $_key, krs.BobotNilai desc";
  $r = _query($s); $n = 0;
  echo "<p><a href='?mnux=mhsw.mk&gos=MKMhswEdt&md=1'>Tambah Matakuliah</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th><th class=ttl>Kode</th><th class=ttl>Nama</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Setaraan</th><th class=ttl>SKS</th>
    <th class=ttl>Grade</th><th class=ttl>Bobot</th>
    <th class=ttl>Hapus</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? "class=nac" : "class=ul";
    echo "<tr><td class=inp><a href='?mnux=mhsw.mk&gos=MKMhswEdt&md=0&krsid=$w[KRSID]'>$n
      <img src='img/edit.png'></a></td>
    <td $c>$w[MKKode]</td>
    <td $c>$w[NamaMK]</td>
    <td $c>$w[TahunID]</td>
    <td $c align=center><img src='img/$w[Setara].gif'></td>
    <td $c align=right>$w[SKS]</td>
    <td $c align=center>$w[GradeNilai]</td>
    <td $c align=right>$w[BobotNilai]</td>
    <td class=ul align=center><a href='?mnux=mhsw.mk&gos=MKMhswDel&krsid=$w[KRSID]'><img src='img/del.gif'></a></td>
    </tr>";
  }
  echo "</table></p>";
}
function MKMhswEdt($mhsw) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields("krs", "KRSID", $_REQUEST['krsid'], '*');
    $jdl = "Edit Matakuliah Mahasiswa";
  }
  else {
    $w = array();
    $w['KRSID'] = 0;
    $w['MKID'] = 0;
    $w['TahunID'] = '';
    $w['GradeNilai'] = '';
    $w['SKS'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Matakuliah Mahasiswa";
  }
  $kurid = GetaField("kurikulum", "NA='N' and KodeID='$_SESSION[KodeID]' and ProdiID", $mhsw['ProdiID'], "KurikulumID");
  $optmk = GetOption2("mk", "concat(MKKode, ' - ', Nama, ' (', SKS, ' SKS)')", "MKKode",
    $w['MKID'], "KurikulumID='$kurid'", "MKID");
  $optgrd = GetOption2("nilai", "concat(Nama, '   ', '(', Bobot, ')', ' ', NilaiMin, ' - ', NilaiMax)", "Bobot desc", $w['GradeNilai'], "ProdiID='$mhsw[ProdiID]'", 'Nama');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='mkmhsw' method=POST>
  <input type=hidden name='mnux' value='mhsw.mk'>
  <input type=hidden name='gos' value='MKMhswSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='krsid' value='$w[KRSID]'>
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul><select name='MKID'>$optmk</select></td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='TahunID' value='$w[TahunID]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>SKS</td><td class=ul><input type=text name='SKS' value='$w[SKS]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Grade Nilai</td><td class=ul><select name='GradeNilai'>$optgrd</select></td></tr>
  <tr><td class=inp>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhsw.mk&gos=DftrMKMhsw'\"></td></tr>
  </form></table></p>";
}
function MKMhswSav($mhsw) {
  $md = $_REQUEST['md']+0;
  $TahunID = $_REQUEST['TahunID'];
  $KRSID = $_REQUEST['krsid'];
  $MKID = $_REQUEST['MKID'];
  $SKS = $_REQUEST['SKS'];
  $mk = (!empty($SKS)) ? GetFields('mk', "MKID", $MKID, "*") : $SKS;
  $GradeNilai = $_REQUEST['GradeNilai'];
  $BobotNilai = GetaField('nilai', "ProdiID='$mhsw[ProdiID]' and Nama", $GradeNilai, "Bobot");
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update krs set MKID='$MKID', MKKode='$mk[MKKode]', SKS='$mk[SKS]', TahunID='$TahunID',
      GradeNilai='$GradeNilai', BobotNilai='$BobotNilai', NA='$NA', Final='Y', 
      LoginEdit='$_SESSION[_Login]' and TanggalEdit=now()
      where KRSID='$KRSID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into krs (TahunID, MKID, MKKode, SKS, MhswID,
      GradeNilai, BobotNilai, NA, StatusKRSID, Final, SKS,
      LoginBuat, TanggalBuat)
      values ('$TahunID', '$MKID', '$mk[MKKode]', '$mk[SKS]', '$mhsw[MhswID]',
      '$GradeNilai', '$BobotNilai', '$NA', 'A', 'Y',
      '$_SESSION[_Login]', now())";
    $r = _query($s); 
  } 
  DftrMKMhsw($mhsw);
}
function MKMhswDel($mhsw) {
  $krsid = $_REQUEST['krsid'];
  if (!empty($krsid)) {
    $krs = GetFields("krs krs left outer join mk mk on krs.MKID=mk.MKID",
      "krs.KRSID", $krsid, "krs.*, mk.MKKode, mk.Nama");
    echo Konfirmasi("Konfirmasi Penghapusan",
      "<p>Benar Anda akan menghapus data matakuliah mahasiswa ini?</p>
      <table class=box cellspacing=1 cellpadding=4>
      <tr><td class=inp>Matakuliah</td><td class=ul>$krs[MKKode] - $krs[Nama] ($krs[SKS] SKS)</td></tr>
      <tr><td class=inp>Nilai</td><td class=ul>$krs[GradeNilai] ($krs[BobotNilai])</td></tr>
      <tr><td class=inp>Tahun Akd</td><td class=ul>$krs[TahunID]</td>
      <tr><td class=inp>Penyetaraan?</td><td class=ul><img src='img/$krs[Setara].gif'></td></tr>
      </table>
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=mhsw.mk&gos=MKMhswDel1&konfirm=1&krsid=$krsid'>Hapus</a> |
      <a href='?mnux=mhsw.mk&gos='>Batal</a>");
  }
  else {
    echo ErrorMsg("Gagal Hapus", "Tidak ada yang dihapus");
  }
}
function MKMhswDel1($mhsw) {
  $krsid = $_REQUEST['krsid'];
  if (!empty($krsid)) {
    $s = "delete from krs where KRSID='$krsid' ";
    $r = _query($s);
    echo Konfirmasi("Telah Dihapus",
      "Data matakuliah mahasiswa telah dihapus.");
  }
  DftrMKMhsw($mhsw);
}

function TampilkanOrderBy() {
  global $urutan;
  $a = '';
  for ($i=0; $i<sizeof($urutan); $i++) {
    $sel = ($i == $_SESSION['_urutan'])? 'selected' : '';
    $v = explode('~', $urutan[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhsw.mk'>
  <input type=hidden name='gos' value='DftrMKMhsw'>
  <tr><td class=inp>Urut berdasarkan: </td>
  <td class=ul><select name='_urutan' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

// *** Parameters ***
$urutan = array(0=>"Kode Matakuliah~mk.MKKODE", 1=>"Periode~krs.TahunID");
  
$_urutan = GetSetVar('_urutan', 1);
$crmhswid = GetSetVar('crmhswid');
$UkuranHeader = GetSetVar('UkuranHeader', 'Besar');
$gos = (empty($_REQUEST['gos']))? "DftrMKMhsw" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Edit Matakuliah Mahasiswa");
TampilkanPencarianMhsw('mhsw.mk', 'DftrMKMhsw', 1);
TampilkanOrderBy();
if (!empty($crmhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on prg.ProgramID=m.ProgramID
    left outer join prodi prd on prd.ProdiID=m.ProdiID", 
    'm.MhswID', $crmhswid, 
    "m.*, prg.Nama as PRG, prd.Nama as PRD");
  if (!empty($mhsw)) {
    include_once "mhsw.hdr.php";
    $HeaderMhsw = "TampilkanHeader".$UkuranHeader;
    echo $HeaderMhsw($mhsw, 'mhsw.mk', 'DftrMKMhsw', 1);
    $gos($mhsw);
  }
  else echo ErrorMsg("Gagal",
    "Data mahasiswa dengan NPM <b>$crmhswid</b> tidak ditemukan.");
}
?>
