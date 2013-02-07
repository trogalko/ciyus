<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Happy Birthday Mother

// *** Functions ***
function TampilkanJenisUjian() {
  global $arrUjian;  
  $optJU = "";
  for ($i=0; $i < sizeof($arrUjian); $i++) {
    $nm = $arrUjian[$i];
    $ck = ($i == $_SESSION['ujian'])? 'selected' : '';
    $optJU .= "<option value='$i' $ck>$nm</option> \n";
  }
  $optprid = GetOption2("program", "concat(ProgramID, ' - ', Nama)", 
    'ProgramID', $_SESSION['prid'], '', 'ProgramID');
  $optprodi = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)",
    "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $optjenjad = GetOption2('jenisjadwal', "concat(JenisJadwalID, ' - ', Nama)", "JenisJadwalID", $_SESSION['jenjad'], '', "JenisJadwalID");
  echo <<<END
  <p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='jadwal.ujian'>
  <tr><td class=ul colspan=4><b>$_SESSION[KodeID]</b></td></tr>
  <tr><td class=inp>Tahun</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td>
    <td class=inp>Jenis Ujian</td><td class=ul><select name='ujian'>$optJU</select></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid'>$optprid</select></td>
    <td class=inp>Program Studi</td><td class=ul><select name='prodi'>$optprodi</select></td></tr>
  <tr><td class=inp>Jenis Jadwal</td><td class=ul><select name='jenjad' onChange='this.form.submit()'>$optjenjad</select></td>
    <td colspan=2><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table>
END;
}
function DftrUjian() {
  global $arrUjian;
  $_jj = (empty($_SESSION['jenjad']))? '' : "and j.JenisJadwalID='$_SESSION[jenjad]' ";
  switch ($_SESSION['ujian']) {
    case 0 : $ord = "j.HariID, j.JamMulai, j.MKKode, j.NamaKelas";
             break;
    case 1 : $ord = "j.UTSTanggal, j.UTSJamMulai, j.MKKode, j.NamaKelas";
             break;
    case 2 : $ord = "j.UASTanggal, j.UASJamMulai, j.MKKode, j.NamaKelas";
             break;
  }
  $s = "select j.*
    from jadwal j
    where j.TahunID='$_SESSION[tahun]' and j.NamaKelas<>'KLINIK'
      and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0 $_jj 
      and j.JadwalSer = 0 
	  order by $ord";
  $r = _query($s);
  $tgl = '';
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>Edit</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>ID</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Mhsw</th>
    <th class=ttl title='Minimal Kehadiran Mhsw'>Min</th>
    <th class=ttl>Dosen</th>
    <th class=ttl title='Daftar Hadir Ujian'><img src='img/printer.gif'></th>
    <th class=ttl title='Daftar Mhsw Tidak Boleh Ujian'><img src='img/printer.gif'></th> 
    </tr>";
  $nmujian = $arrUjian[$_SESSION['ujian']];
  while ($w = _fetch_array($r)) {
    if ($tgl != $w[$nmujian."Tanggal"]) {
      $tgl = $w[$nmujian."Tanggal"];
      $Tanggal = ($tgl == "0000-00-00")? "-" : FormatTanggal($tgl);
    }
    else {
      $Tanggal = "\"";
    }
    if ($w[$nmujian."Tanggal"] == "0000-00-00") {
      $c = "class=nac";
      $Tanggal = "x";
      $dhu = "&nbsp;";
      $JAM = "&nbsp;";
    }
    else {
      $c = "class=ul";
      $dhu = "<a href='jadwal.cetakdh.php?JadwalID=$w[JadwalID]&ctk=$_SESSION[ujian]' title='Daftar Hadir Ujian' target=_blank>DHU</a>";
      $xdhu = ($_SESSION['ujian'] == 2) ? "<a href='jadwal.cetakdh.php?JadwalID=$w[JadwalID]&ctk=2&hak=1' title='Mhsw Tidak Ujian' target=_blank>MTU</a>" : '&nbsp;';
      $JAM = substr($w[$nmujian."JamMulai"], 0, 5) . "-" . substr($w[$nmujian."JamSelesai"], 0, 5);
    }
    // Array dosen
    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", '<br />');
    $RuangID = $w[$nmujian."RuangID"];
    if (empty($RuangID)) $RG = '';
    else {
      $arrrg = explode(',', $RuangID);
      for ($i=0; $i < sizeof($arrrg); $i++) {
        $arrrg[$i] = substr($arrrg[$i], 0, strpos($arrrg[$i], ':'));
      }
      $RG = implode(',', $arrrg);
    }
    
    echo "<tr>
      <td class=ul><a href='?mnux=jadwal.ujian&gos=JUEdt&JadwalID=$w[JadwalID]&ujian=$_SESSION[ujian]'><img src='img/edit.png'></a></td>
      <td $c align=center>$Tanggal</td>
      <td $c>$JAM</td>
      <td $c>$RG&nbsp;</td>
      <td class=inp>$w[JadwalID]</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[NamaKelas]&nbsp;</td>
      <td $c>$w[JenisJadwalID]</td>
      <td $c align=right>$w[SKS]</td>
      <td $c align=right>$w[JumlahMhsw]/$w[Kapasitas]</td>
      <td $c align=right title='Min $w[KehadiranMin]x dari $w[Kehadiran]x tatap muka'>$w[KehadiranMin]/$w[Kehadiran]</td>
      <td $c>$dosen</td>
      <td $c>$dhu</td>
      <td $c>$xdhu</td>
      </tr>";
  }
  echo "</table></p>";
}
function JUEdt() {
  global $arrUjian;
  $JadwalID = $_REQUEST['JadwalID'];
  $w = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  $nmujian = $arrUjian[$_SESSION['ujian']];
  $nmhari = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
  // Array dosen
  $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);
  $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
    "Login", "Nama", '<br />');
  // Tanggal, Jam, Ruang
  $_TGL = $w[$nmujian."Tanggal"];
  $_TGL = ($_TGL == "0000-00-00")? date('Y-m-d') : $_TGL;
  $TGL = GetDateOption($_TGL, 'TGL');
  $JM = ($w[$nmujian."JamMulai"] == "00:00:00")? "08:00" : $w[$nmujian."JamMulai"];
  $JS = ($w[$nmujian."JamSelesai"] == "00:00:00")? "09:55" : $w[$nmujian."JamSelesai"];
  //$RG = GetOption2("ruang", "concat(RuangID, ' - ', Nama, ' (Kaps: ', KapasitasUjian, ')')", "RuangID",
  //  $w[$nmujian."RuangID"], '', 'RuangID');
  $NamaRuang = $w[$nmujian."RuangID"];
  $EditRuangUjian = GetEditRuangUjian($nmujian, $w);
  CariRuangScript();
  echo <<<END
  <p><table class=box cellspacing=1>
  <form action='?' name='data' method=POST>
  <input type=hidden name='JadwalID' value='$JadwalID'>
  <input type=hidden name='ujian' value='$_SESSION[ujian]'>
  <input type=hidden name='gos' value='JUSav'>
  <tr><th class=ttl colspan=2>Edit Jadwal $nmujian</th>
      <th class=ttl>Ruang Ujian</th></tr>
  <tr><td class=inp>No. Jadwal</td><td class=ul>$JadwalID</td>
      <td class=ul rowspan=9 valign=top>$EditRuangUjian</td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$w[MKKode] - $w[Nama]</td></tr>
  <tr><td class=inp>Kelas</td><td class=ul>$w[NamaKelas] ($w[JenisJadwalID])</td></tr>
  <tr><td class=inp>Jadwal Kuliah</td><td class=ul>$nmhari, $w[JamMulai]-$w[JamSelesai]</td></tr>
  <tr><td class=inp>Dosen Pengampu</td><td class=ul>$dosen</td></tr>
  <tr><td class=inp>Tanggal Ujian</td><td class=ul>$TGL</td></tr>
  <tr><td class=inp>Jam Ujian</td><td class=ul>
    <input type=text name='JM' value='$JM' size=5 maxlength=5> -
    <input type=text name='JS' value='$JS' size=5 maxlength=5></td></tr>
  
  <tr><td class=inp>Persentase Kehadiran Minimal Mahasiswa</td>
    <td class=ul><input type=text name='KehadiranMin' value='$w[KehadiranMin]' size=3 maxlength=3> %
    dari <b>$w[Kehadiran]</b>x tatap muka</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=button name='Batal' value='Batal' onClick="location='?mnux=jadwal.ujian'"></td></tr>
  </table></p>
END;
}
/*
<tr><td class=inp>Ruang Ujian</td><td class=ul><input type=text name='RuangID' value='$NamaRuang' size=30 maxlength=100>
    <a href='javascript:cariruang(data)'>Cari</a>
  </td></tr>
*/
function GetEditRuangUjian($ujn, $jdwl) {
  $_rg = $jdwl[$ujn.'RuangID'];
  $arr = array();
  $arr = explode(',', $_rg);
  $a = '';
  for ($i = 0; $i < 5; $i++) {
    $n = $i+1;
    $det = explode(':', $arr[$i]);
    $a .= "<tr><td class=inp1>Ruang</td>
      <td class=ul><input type=text name='RuangID$n' value='$det[0]' size=10></td>
      <td class=inp1>Kapasitas</td>
      <td class=ul><input type=text name='Kapasitas$n' value='$det[1]' size=3></td>
      </tr>";
  }
  return "<table class=bsc cellspacing=1>$a</table>";
}
function CariRuangScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function cariruang(frm){
    lnk = "cariruang.php?prodi=" + $_SESSION[prodi] + "&arrrg="+frm.RuangID.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </SCRIPT>
EOF;
}
function JUSav() {
  global $arrUjian;
  $JadwalID = $_REQUEST['JadwalID'];
  $nmujian = $arrUjian[$_SESSION['ujian']];
  $TGL = "$_REQUEST[TGL_y]-$_REQUEST[TGL_m]-$_REQUEST[TGL_d]";
  $JM = str_replace('.', ':', $_REQUEST['JM']);
  $JS = str_replace('.', ':', $_REQUEST['JS']);
  $RG = $_REQUEST['RuangID'];
  $KehadiranMin = $_REQUEST['KehadiranMin']+0;
  $rg = array();
  for ($i=1; $i<=5; $i++) {
    $_rg = $_REQUEST['RuangID'.$i];
    if (!empty($_rg)) {
      $kap = $_REQUEST['Kapasitas'.$i]+0;
      $rg[] = $_rg . ':' . $kap;
    } 
  }
  $RuangID = implode(',', $rg);
  // construct sql
  $s = "update jadwal set " . $nmujian . "Tanggal='$TGL', " .
    $nmujian . "JamMulai='$JM', ".
    $nmujian . "JamSelesai='$JS', ".
    $nmujian . "RuangID='$RuangID',
    KehadiranMin=$KehadiranMin
    where JadwalID=$JadwalID";
  $r = _query($s);
  DftrUjian();
}


// *** Parameters ***
//$arrUjian = array(0=>"", 1=>"UTS", 2=>"UAS");
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$ujian = GetSetVar('ujian');
$jenjad = GetSetVar('jenjad');
$gos = (empty($_REQUEST['gos']))? "DftrUjian" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Jadwal " . $arrUjian[$ujian]);
TampilkanJenisUjian();
if (!empty($tahun) && !empty($prodi) && !empty($prid) && !empty($ujian)) {
  $gos();
}
?>
