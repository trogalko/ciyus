<?php
// Author: Emanuel Setio Dewo
// 22 Maret 2007

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');
$_urutan = GetSetVar('_urutan', 0);
$UkuranHeader = GetSetVar('UkuranHeader', 'Besar');
$gos = (empty($_REQUEST['gos']))? "DetailMKMhsw" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Edit Matakuliah Mhsw Klinik (Manual)");
CekHakAksesProdi('11');
$UkuranHeader = GetSetVar('UkuranHeader', 'Besar');
TampilkanPencarianMhsw('klinik.mkmhsw', 'DetailMKMhsw', 1);

if (!empty($_SESSION['crmhswid'])) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot b on m.BIPOTID=b.BIPOTID", 
    'm.MhswID', $_SESSION['crmhswid'], "m.*, prd.Nama as PRD, prg.Nama as PRG, b.Nama as BPT");
  if (empty($mhsw))
    echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      "Mahasiswa dengan NPM: <b>$_SESSION[crmhswid]</b> tidak ditemukan.");
  else {
    include_once "mhsw.hdr.php";
    $HeaderMhsw = "TampilkanHeader".$_SESSION['UkuranHeader'];
    echo $HeaderMhsw($mhsw, 'klinik.mkmhsw', 'DftrMKMhsw', 1);
    echo "<p><a href='?mnux=$_SESSION[mnux]&gos=MKMhswEdt&md=1'>Tambah Matakuliah Mahasiswa</a></p>";
    $gos($mhsw);
  }
}
else echo Konfirmasi("Isikan NPM Mahasiswa",
    "Isikan NPM Mahasiswa untuk melihat data matakuliah mahasiswa.");


// *** Functions ***
function CekHakAksesProdi($prd) {
  $hak = TRIM($_SESSION['_ProdiID'], ',');
  $arrhak = explode(',', $hak);
  $ada = array_search($prd, $arrhak);
  if ($ada === false) die(ErrorMsg('Tidak Berhak', 
    "Anda tidak berhak mengakses modul ini"));
}
function DetailMKMhsw($mhsw) {
  $arrUrutan = array('krs.TahunID', 'krs.MKKode');
  TampilkanUrutanMKMhsw();
  $urut = $arrUrutan[$_SESSION['_urutan']];
  $s = "select krs.*, mk.Nama
    from krs
      left outer join mk on krs.MKID=mk.MKID
    where krs.MhswID='$mhsw[MhswID]'
      and krs.NA='N'
    order by $urut";
  $r = _query($s); $n = 0;
  echo "<p><table class=box>";
  echo "<tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>RS</th>
    <th class=ttl>Grade</th>
    <th class=ttl>Bobot</th>
    <th class=ttl>Hapus</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul><a href='?mnux=klinik.mkmhsw&gos=MKMhswEdt&md=0&KRSID=$w[KRSID]'><img src='img/edit.png'></a> $w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[TahunID]</td>
      <td class=ul>$w[RuangID]&nbsp;</td>
      <td class=ul>$w[GradeNilai]&nbsp;</td>
      <td class=ul align=right>&nbsp;$w[BobotNilai]</td>
      <td class=ul align=center><a href='?mnux=$_SESSION[mnux]&gos=MKMhswDel&KRSID=$w[KRSID]'><img src='img/del.gif'></a></td>
    </tr>";
  }
  echo "</table></form>";
}
function TampilkanUrutanMKMhsw() {
  $arrUrutan = array('Semester', 'Kode Matakuliah');
  $str = '';
  foreach ($arrUrutan as $key => $val) {
    $sel = ($key == $_SESSION['_urutan'])? 'selected' : '';
    $str .= "<option value='$key' $sel>$val</option>";
  }
  echo "<p><table class=box>
  <form name='frmUrutan' action='?' method=POST>
  <tr><td class=inp>Urut berdasarkan:</td>
    <td class=ul><select name='_urutan' onChange='this.form.submit()'>$str</select></td></tr>
  </form>
  </table></p>";
}
function MKMhswEdt($mhsw) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $KRSID = $_REQUEST['KRSID']+0;
    $w = GetFields('krs', 'KRSID', $KRSID, "*");
    if (empty($w))
      die(ErrorMsg("Data Tidak Ditemukan",
        "Data KRS <b>#$KRSID</b> tidak ditemukan.<br />
        Kemungkinan data sudah dihapus.
        <hr size=1 color=silver>
        Pilihan: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>"));
    $jdl = "Edit KRS Mahasiswa Klinik";
  }
  else {
    $KRSID = 0;
    $w = array();
    $w['TahunID'] = $_SESSION['tahun'];
    $jdl = "Tambah KRS Mahasiswa Klinik (Manual)";
  }
  $kurid = GetaField("kurikulum", "NA='N' and ProdiID", '11', "KurikulumID")+0;
  $optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama)",
    "MKKode", $w['MKID'], "ProdiID='11' and KurikulumID='$kurid'", "MKID");
  $optrs = GetOption2('rumahsakit', "concat(RSID, ' - ', Nama)",
    'RSID', $w['RuangID'], '', 'RSID');
  $optgrd = GetOption2('nilai', "concat(Nama, ' (', Bobot, ')')",
    'Bobot desc', $w['GradeNilai'], "ProdiID='11'", 'Nama');
  echo "<p><table class=box>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='MKMhswSAV'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value=1>
  <input type=hidden name='KRSID' value='$KRSID'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Semester</td>
      <td class=ul><input type=text name='TahunID' value='$w[TahunID]'></td></tr>
  <tr><td class=inp>Matakuliah</td>
      <td class=ul><select name='MKID'>$optmk</td></tr>
  <tr><td class=inp>Rumah Sakit</td>
      <td class=ul><select name='RuangID'>$optrs</select></td></tr>
  <tr><td class=inp>Nilai Akhir</td>
      <td class=ul><input type=text name='NilaiAkhir' value='$w[NilaiAkhir]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Grade Nilai</td>
      <td class=ul><select name='GradeNilai'>$optgrd</select></td></tr>
  
  <tr><td class=ul colspan=2>
      <input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]'\"></td></tr>
  
  </form></table></p>";
}
function MKMhswSAV($mhsw) {
  $md = $_REQUEST['md']+0;
  $TahunID = $_REQUEST['TahunID'];
  $MKID = $_REQUEST['MKID'];
  $mk = GetFields('mk', 'MKID', $MKID, "MKKode, Nama, SKS");
  $MKKode = $mk['MKKode'];
  $SKS = $mk['SKS'];
  $RuangID = $_REQUEST['RuangID'];
  $NilaiAkhir = $_REQUEST['NilaiAkhir']+0;
  $GradeNilai = $_REQUEST['GradeNilai'];
  $BobotNilai = GetaField('nilai', "ProdiID='11' and Nama", $GradeNilai, "Bobot")+0;
  
  if ($md == 0) {
    $KRSID = $_REQUEST['KRSID']+0;
    $s = "update krs
      set TahunID='$TahunID', MKID='$MKID', MKKode='$MKKode', SKS='$SKS',
      NilaiAkhir='$NilaiAkhir', GradeNilai='$GradeNilai', BobotNilai='$BobotNilai',
      RuangID='$RuangID', CatatanError='Manual', Final='Y',
      LoginBuat='$_SESSION[_Login]', TanggalBuat=now()
      where KRSID='$KRSID' ";
    $r = _query($s);
    echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=';</script>";
    //echo "<pre>$s</pre>";
  }
  else {
    $s = "insert into krs
      (MhswID, TahunID, MKID, MKKode, SKS,
      NilaiAkhir, GradeNilai, BobotNilai,
      RuangID, CatatanError, Final,
      LoginBuat, TanggalBuat)
      values
      ('$mhsw[MhswID]', '$TahunID', '$MKID', '$MKKode', '$SKS',
      '$NilaiAkhir', '$GradeNilai', '$BobotNilai',
      '$RuangID', 'Manual', 'Y',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=';</script>";
  }
}
function MKMhswDel($mhsw) {
  $KRSID = $_REQUEST['KRSID']+0;
  $krs = GetFields('krs', 'KRSID', $KRSID, "*");
  $NamaMK = GetaField('mk', 'MKID', $krs['MKID'], "Nama");
  $NamaRS = GetaField('rumahsakit', 'RSID', $krs['RuangID'], 'Nama');
  echo Konfirmasi("Konfirmasi Penghapusan Data",
    "Benar Anda akan menghapus data ini?
    <p><table class=box>
    <tr><td class=inp>KRS #</td>
        <td class=ul>$KRSID</td></tr>
    <tr><td class=inp>Matakuliah</td>
        <td class=ul>$krs[MKKode] - $NamaMK ($krs[SKS] SKS)</td></tr>
    <tr><td class=inp>Rumah sakit</td>
        <td class=ul>$krs[RuangID] - $NamaRS</td></tr>
    <tr><td class=inp>Nilai</td>
        <td class=ul>$krs[GradeNilai] ($krs[BobotNilai]), $krs[NilaiAkhir]</td></tr>
    </table></p>
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=$_SESSION[mnux]&gos='>Batal</a> |
    <a href='?mnux=$_SESSION[mnux]&gos=MKMhswDel1&KRSID=$KRSID'>Hapus Data</a>");
}
function MKMhswDel1($mhsw) {
  $KRSID = $_REQUEST['KRSID'];
  $s = "delete from krs where KRSID='$KRSID' ";
  $r = _query($s);
  echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=';</script>";
}
?>
