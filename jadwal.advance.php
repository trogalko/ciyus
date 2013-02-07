<?php
// Author: Emanuel Setio Dewo
// 02 Feb 2006

// *** Functions ***
function DftrJdwl() {
  global $arrID;
  $arrVld = GetFields('tahun',
    "ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
    $_SESSION['tahun'], '*');
  if (empty($arrVld)) {
    echo ErrorMsg("Tahun Akademik Belum Dibuat",
    "Tahun Akademik <b>$_SESSION[tahun]</b> untuk Program <b>$_SESSION[prid]</b> dan Program Studi <b>$_SESSION[prodi]</b> belum dibuat.<br />
    Hubungi Kepala Akademik/Jurusan.");
  }
  else {
    TampilkanMenuJadwal();
    TampilkanJadwal();
  }
}
function TampilkanMenuJadwal(){
  echo "<p><a href='?mnux=jadwal&md=1&gos=JdwlEdt&md=1'>Tambah Jadwal</a></p>";
}
function TampilkanJadwal() {
  $hdrjdwl = "<tr><th class=ttl>ID</th>
    <th class=ttl>Waktu</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl colspan=2>Kelas</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Jml<br />Mhsw</th>
    <th class=ttl>
  ";
  $s = "select j.*,
    time_format(j.JamMulai, '%H:%i') as Mulai,
    time_format(j.JamSelesai, '%H:%i') as Selesai,
    d.Nama as NamaDosen, concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwal j
      left outer join mk mk on j.MKID=mk.MKID
      left outer join dosen d on j.DosenID=d.Login
    where j.KodeID='$_SESSION[KodeID]' and j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
      and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
    order by j.HariID, j.JamMulai";
  $r = _query($s);
  // Tampilkan daftar jadwal
  $hari = -1;
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($hari != $w['HariID']) {
      $hari = $w['HariID'];
      $NamaHari = GetaField('hari', 'HariID', $hari, 'Nama');
      echo "<tr><td class=ul colspan=7><b>$NamaHari</b></td></tr>";
      echo $hdrjdwl;
    }
    $c = "class=ul";
    echo "<tr>
      <td class=inp1>$w[JadwalID]</td>
      <td $c>$w[Mulai]-$w[Selesai]</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td><td $c>$w[NamaKelas]&nbsp;</td>
      <td $c>$w[SKS] ($w[SKSAsli])</td>
      <td $c><abbr title='$w[DSN]'>$w[NamaDosen]</abbr></td>
      <td $c>$w[JumlahMhsw]</td>
      </tr>";
  }
  echo "</table></p>";
}
function ResetArrJadwal() {
  $w = array();
  $w['JadwalID'] = 0;
  $w['JadwalRef'] = 0;
  $w['KodeID'] = $_SESSION['KodeID'];
  $w['TahunID'] = $_SESSION['tahun'];
  $w['ProdiID'] = '.'.$_SESSION['prodi'].'';
  $w['ProgramID'] = $_SESSION['prid'];
  //echo "Prodi: $w[ProdiID], Program: $_SESSION[prid]";
  $w['NamaKelas'] = '';
  $w['MKID'] = 0;
  $w['JadwalJenisID'] = 0;
  $w['MKKode'] = '';
  $w['Nama'] = '';
  $w['HariID'] = 1;
  $w['JamMulai'] = '08:00';
  $w['JamSelesai'] = '09:59';
  $w['SKSAsli'] = 0;
  $w['SKS'] = 0;
  $w['DosenID'] = '';
  $w['RencanaKehadiran'] = 0;
  $w['Kehadiran'] = 0;
  $w['JumlahMhsw'] = 0;
  $w['RuangID'] = '';
  $w['HargaStandar'] = 'Y';
  $w['Harga'] = 0;
  $w['NA'] = 'N';
  return $w;
}
function AmbilArrJadwal() {
  $w = array();
  $w['JadwalID'] = $_REQUEST['JadwalID'];
  $w['JadwalRef'] = $_REQUEST['JadwalRef'];
  $w['KodeID'] = $_SESSION['KodeID'];
  $w['TahunID'] = $_SESSION['tahun'];
  $w['ProdiID'] = $_REQUEST['ProdiID'];
  $w['ProgramID'] = $_REQUEST['prid'];
  $w['NamaKelas'] = $_REQUEST['NamaKelas'];
  $w['MKID'] = $_REQUEST['MKID'];
  $w['JadwalJenisID'] = $_REQUEST['JadwalJenisID'];
  $w['MKKode'] = $_REQUEST['MKKode'];
  $w['Nama'] = $_REQUEST['Nama'];
  $w['HariID'] = $_REQUEST['HariID'];
  $w['JamMulai'] = $_REQUEST['JamMulai'];
  $w['JamSelesai'] = $_REQUEST['JamSelesai'];
  $w['SKSAsli'] = $_REQUEST['SKSAsli']+0;
  $w['SKS'] = $_REQUEST['SKS']+0;
  $w['DosenID'] = $_REQUEST['DosenID'];
  $w['RencanaKehadiran'] = $_REQUEST['RencanaKehadiran']+0;
  $w['Kehadiran'] = $_REQUEST['Kehadiran']+0;
  $w['JumlahMhsw'] = $_REQUEST['JumlahMhsw'];
  $w['RuangID'] = $_REQUEST['RuangID'];
  $w['HargaStandar'] = $_REQUEST['HargaStandar'];
  $w['Harga'] = $_REQUEST['Harga'];
  $w['NA'] = $_REQUEST['NA'];
  return $w;
}
function JdwlEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('jadwal', "JadwalID", $_REQUEST['jadwalid'], '*');
    $jdl = "Edit Jadwal";
  }
  else {
    $w = ($_REQUEST['gagal'] == 1)? AmbilArrJadwal() : ResetArrJadwal();
    $jdl = "Tambah Jadwal";
  }
  $w['JadwalRef'] = (empty($_REQUEST['JadwalRef']))? $w['JadwalRef'] : $_REQUEST['JadwalRef'];
  //GetCheckboxes($table, $key, $Fields, $Label, $Nilai='', $Separator=',') {
  $hakprodi = TRIM($_SESSION['_ProdiID'], ',');
  $optprodi = GetCheckboxes("prodi", "ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", "NM", $w['ProdiID'], '.', "ProdiID in ($hakprodi)");
  $optprid = GetCheckboxes("program", "ProgramID",
    "concat(ProgramID, ' - ', Nama) as NM", "NM", $w['ProgramID'], ',');
  $opthari = GetOption2('hari', "Nama", "HariID", $w['HariID'], '', 'HariID');
  $optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama, ' (', SKS, ' SKS)')",
    'MKKode', $w['MKID'], "ProdiID='$_SESSION[prodi]'", 'MKID');
  $optdsn = GetOption2('dosen', "concat(Nama, ', ', Gelar)",
    'Login', $w['DosenID'], "INSTR(ProdiID, '.$_SESSION[prodi].')", 'Login');
  $ckHargaStandar = ($w['HargaStandar'] == 'Y')? 'checked' : '';
  // Tampilkan form
  CariRuangScript();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='data' method=POST>
  <input type=hidden name='JadwalID' value='$w[JadwalID]'>
  <input type=hidden name='JadwalRef' value='$w[JadwalRef]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='mnux' value='jadwal'>
  <input type=hidden name='gos' value='JdwlSav'>
  <input type=hidden name='prodi' value='$_SESSION[prodi]'>

  <tr><th class=ttl colspan=2>$jdl</th></tr>

  <tr><td class=inp1>Berlaku untuk<br />Program :</td><td class=ul>$optprid</td></tr>
  <tr><td class=inp1>Berlaku untuk<br />Program Studi :</td><td class=ul>$optprodi</td></tr>
  <tr><td class=inp1>Hari :</td><td class=ul><select name='HariID'>$opthari</select></td></tr>
  <tr><td class=inp1>Jam Kuliah :</td><td class=ul>
    <input type=text name='JamMulai' value='$w[JamMulai]' size=5 maxlength=5> s/d
    <input type=text name='JamSelesai' value='$w[JamSelesai]' size=5 maxlength=5>
    </td></tr>
  <tr><td class=inp1>Matakuliah :</td><td class=ul><select name='MKID'>$optmk</select></td></tr>
  <tr><td class=inp1>Nama Kelas :</td><td class=ul><input type=text name='NamaKelas' value='$w[NamaKelas]' size=10 maxlength=20></td></tr>
  <tr><td class=inp1>Ruang Kuliah :</td><td class=ul><input type=text name='RuangID' value='$w[RuangID]' size=40 maxlength=255>
    <a href='javascript:cariruang(data)'>Cari</a></td></tr>
  <tr><td class=inp1>Dosen Pengampu :</td><td class=ul><select name='DosenID'>$optdsn</select></td></tr>
  <tr><td class=inp1>Rencana Jml Kehadiran :</td><td class=ul><input type=text name='RencanaKehadiran' value='$w[RencanaKehadiran]' size=3 maxlength=3></td></tr>
  <tr><td class=inp1>Harga :</td><td class=ul><input type=checkbox name='HargaStandar' value='Y' $ckHargaStandar> Apakah harga standar?<hr size=1 color=silver />
    Jika tidak, harganya adalah: Rp. <input type=text name='Harga' value='$w[Harga]' size=15 maxlength=15></td></tr>
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=jadwal'\"></td></tr>
  </table></p>";
}
function CariRuangScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function cariruang(frm){
    lnk = "cariruang.php?prodi="+frm.prodi.value+"&arrrg="+frm.RuangID.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </SCRIPT>
EOF;
}
function JdwlSav() {
  $md = $_REQUEST['md'];
  $JadwalID = $_REQUEST['JadwalID'];
  $JadwalRef = $_REQUEST['JadwalRef'];
  $KodeID = $_SESSION['KodeID'];
  $TahunID = $_SESSION['tahun'];
  // array prodi
  $arrProdiID = $_REQUEST['ProdiID'];
  $ProdiID = (empty($arrProdiID))? '' : '.'.implode('.', $arrProdiID).'.';
  // array program
  $arrProgramID = $_REQUEST['ProgramID'];
  $ProgramID = (empty($arrProgramID))? '' : '.'.implode('.', $arrProgramID).'.';

  $NamaKelas = $_REQUEST['NamaKelas'];
  $MKID = $_REQUEST['MKID'];
  $matakuliah = GetFields('mk', 'MKID', $MKID, '*');
  $JadwalJenisID = $_REQUEST['JadwalJenisID'];
  $MKKode = $matakuliah['MKKode'];
  $Nama = $matakuliah['Nama'];
  $HariID = $_REQUEST['HariID'];
  $JamMulai = $_REQUEST['JamMulai'];
  $JamSelesai = $_REQUEST['JamSelesai'];
  $SKSAsli = $matakuliah['SKS'];
  $SKS = (empty($_REQUEST['SKS']))? $SKSAsli : $_REQUEST['SKS'];

  $DosenID = $_REQUEST['DosenID'];
  $RencanaKehadiran = $_REQUEST['RencanaKehadiran']+0;
  $Kehadiran = $_REQUEST['Kehadiran']+0;
  $JumlahMhsw = $_REQUEST['JumlahMhsw'];
  $RuangID = $_REQUEST['RuangID'];
  $HargaStandar = (empty($_REQUEST['HargaStandar']))? 'N' : $_REQUEST['HargaStandar'];
  $Harga = $_REQUEST['Harga'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];

  // Simpan
  if ($md == 0) {
    $s = "update jadwal set JadwalRef='$JadwalRef',
      ProdiID='$ProdiID', ProgramID='$ProgramID', NamaKelas='$NamaKelas', MKID='$MKID',
      JadwalJenisID='$JadwalJenisID', MKKode='$MKKode', Nama='$Nama',
      HariID='$HariID', JamMulai='$JamMulai', JamSelesai='$JamSelesai',
      SKSAsli='$SKSAsli', SKS='$SKS', DosenID='$DosenID',
      RencanaKehadiran='$RencanaKehadiran', RuangID='$RuangID',
      HargaStandar='$HargaStandar', Harga='$Harga', NA='$NA'
      where JadwalID='$JadwalID' ";
    $r = _query($s);
    DftrJdwl();
  }
  else {
    $s = "insert into jadwal (JadwalRef, ProdiID, ProgramID, KodeID, TahunID,
      NamaKelas, MKID, JadwalJenisID, MKKode, Nama,
      HariID, JamMulai, JamSelesai,
      SKSAsli, SKS, DosenID,
      RencanaKehadiran, RuangID,
      HargaStandar, Harga, NA)
      values ('$JadwalRef', '$ProdiID', '$ProgramID', '$KodeID', '$TahunID',
      '$NamaKelas', '$MKID', '$JadwalJenisID', '$MKKode', '$Nama',
      '$HariID', '$JamMulai', '$JamSelesai',
      '$SKSAsli', '$SKS', '$DosenID',
      '$RencanaKehadiran', '$RuangID',
      '$HargaStandar', '$Harga', '$NA') ";
    $r = _query($s);
    TampilkanPesan("Sudah Disimpan. <hr size=1 color=silver>");
    DftrJdwl();
  }

}
function TampilkanPesan($msg) {
echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  win2 = window.open("pesan.html.php?Pesan=$msg", "", "width=600, height=600, scrollbars, status");
  win2.creator = self;
  </SCRIPT>
EOF;
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
//$tahun = GetaField('tahun', "KodeID='$_SESSION[KodeID]' and ProgramID='$prid' and NA='N' and ProdiID", $prodi, 'TahunID');
//$_SESSION['tahun'] = $tahun;
$gos = (empty($_REQUEST['gos']))? 'DftrJdwl' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Penjadwalan Kuliah $thnid");
TampilkanTahunProdiProgram('jadwal', '');
if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']) && !empty($_SESSION['KodeID']) && !empty($tahun)) {
  $gos();
}
?>
