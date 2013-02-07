<?php
// Author : Emanuel Setio Dewo
// 21 April 2006

include_once "krs.lib.php";

// *** Functions ***
function DftrSetara($mhsw) {
  $s = "select krs.*,
    mk.MKKode, mk.Nama
    from krs krs
      left outer join mk mk on krs.MKID=mk.MKID
    where krs.Setara='Y'
      and krs.MhswID='$mhsw[MhswID]'
    order by mk.MKKode";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=8><b>Daftar Matakuliah Hasil Penyetaraan</b> 
      <a href='?mnux=setara&mhswid=$mhsw[MhswID]&gos=SetaraEdt&md=1'>[+ Tambah]</a></td></tr>
    <tr><th class=ttl>No</th><th class=ttl>Kode</th><th class=ttl>Nama</th>
      <th class=ttl>SKS</th><th class=ttl>Nilai</th><th class=ttl>Bobot</th>
      </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
      <td class=ul nowrap><a href='?mnux=setara&mhswid=$mhsw[MhswID]&gos=SetaraEdt&md=0&krsid=$w[KRSID]'><img src='img/edit.png'>
        $w[MKKode]</a></td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$w[SKS]</td>
      <td class=ul align=right>$w[GradeNilai]</td>
      <td class=ul align=right>$w[BobotNilai]</td>
      </tr>";
  }
  echo "</table></p>";
}
function SetaraEdt($mhsw) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('krs', 'KRSID', $_REQUEST['krsid'], '*');
    $jdl = "Edit Penyetaraan";
  }
  else {
    $w = array();
    $w['KRSID'] = 0;
    $w['MhswID'] = $mhsw['MhswID'];
    $w['TahunID'] = $_SESSION['tahun'];
    $w['MKID'] = 0;
    $w['SKS'] = 0;
    $w['GradeNilai'] = '';
    $w['SetaraKode'] = '';
    $w['SetaraNama'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Penyetaraan";
  }
  $kurid = GetaField("kurikulum", "ProdiID='$mhsw[ProdiID]' and NA", 'N', "KurikulumID");
  $optmk = GetOption2("mk", "concat(MKKode, ' - ', Nama, ' (', SKS, ')')", "MKKode", $w['MKID'], "KurikulumID=$kurid", "MKID");
  $optnil = GetOption2("nilai", "concat(Nama, ' (', Bobot, ')')", "Nama", $w['GradeNilai'], "ProdiID='$mhsw[ProdiID]'", "Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='setara'>
  <input type=hidden name='gos' value='SetaraSav'>
  <input type=hidden name='krsid' value='$w[KRSID]'>
  <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
  <input type=hidden name='md' value='$md'>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul><select name='MKID'>$optmk</select></td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$w[TahunID]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Nilai</td><td class=ul><select name='GradeNilai'>$optnil</select></td></tr>
  <tr><td class=ul colspan=2><b>Matakuliah Asal</b></td></tr>
  <tr><td class=inp>Kode</td><td class=ul><input type=text name='SetaraKode' value='$w[SetaraKode]' size=10 maxlength=20></td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul><input type=text name='SetaraNama' value='$w[SetaraNama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Grade Nilai</td><td class=ul><input type=text name='SetaraGrade' value='$w[SetaraGrade]' size=4 maxlength=5></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=setara'\"></td></tr>
  </form></table></p>"; 
}
function SetaraSav($mhsw) {
  $MKID = $_REQUEST['MKID'];
    $mk = GetFields("mk", "MKID", $MKID, "*");
  $tahun = $_REQUEST['tahun'];
  $GradeNilai = $_REQUEST['GradeNilai'];
  $nl = GetFields("nilai", "ProdiID='$mhsw[ProdiID]' and Nama", $GradeNilai, "*");
  $SetaraKode = sqling($_REQUEST['SetaraKode']);
  $SetaraNama = sqling($_REQUEST['SetaraNama']);
  $SetaraGrade = sqling($_REQUEST['SetaraGrade']);
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $s = "update krs set MKID='$MKID', SKS='$mk[SKS]',
      TahunID='$tahun', GradeNilai='$GradeNilai', BobotNilai='$nl[Bobot]',
      SetaraKode='$SetaraKode', SetaraNama='$SetaraNama', SetaraGrade='$SetaraGrade',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where KRSID='$_REQUEST[krsid]' ";
    $r = _query($s);
  }
  else {
    $s = "insert into krs (MhswID, MKID, SKS, TahunID,
      GradeNilai, BobotNilai, Setara,
      SetaraKode, SetaraNama, SetaraGrade,
      LoginBuat, TanggalBuat)
      values ('$mhsw[MhswID]', '$MKID', '$mk[SKS]', '$tahun',
      '$GradeNilai', '$nl[Bobot]', 'Y', 
      '$SetaraKode', '$SetaraNama', 'SetaraGrade',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  DftrSetara($mhsw);
}


// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? "DftrSetara" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Penyetaraan Matakuliah Mahasiswa");
TampilkanCariMhsw('setara');
if (!empty($gos) && !empty($mhswid)) {
  $mhsw = GetFields("mhsw", "MhswID", $mhswid, "*");
  if (!empty($mhsw)) $gos($mhsw);
}
?>
