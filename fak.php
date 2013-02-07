<?php
// Author: Emanuel Setio Dewo
// 2005-12-18

// *** Functions ***
function DftrFak() {
  $s = "select f.*
    from fakultas f
    where f.KodeID='$_SESSION[KodeID]'
    order by f.FakultasID";
  $r = _query($s);
  $cs = 6;
  $optkd = GetOption2('identitas', "concat(Kode, ' - ', Nama)", 'Kode', $_SESSION['KodeID'], '', 'Kode');
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='fid' value=''>
    <input type=hidden name='mnux' value='fak'>
    <tr><td colspan=$cs class=ul><b>Institusi:</b> <select name='KodeID' onChange='this.form.submit()'>$optkd</select></td></tr>
    </form>

    <tr><td colspan=$cs class=ul><a href='?mnux=fak&gos=FakEdt&md=1'>Tambah Fakultas</a> |
    <a href='?mnux=fak&gos=ProdiEdt&md=1'>Tambah Program Studi</a> |
    <a href='fak.cetak.php'>Cetak</a></td></tr>";
  $a.= "<tr><th></th><th colspan=2 class=ttl>Kode</th>
    <th class=ttl>Fakultas</th><th class=ttl>NA</th>
    <th></th></tr>";
  while ($w = _fetch_array($r)) {
    if ($w['FakultasID'] == $_SESSION['fid']) {
      $c = 'class=inp1';
      $i1 = "<img src='img/kanan.gif'>";
      $i2 = "<img src='img/kiri.gif'>";
    }
    else {
      $c = 'class=ul';
      $i1 = ''; $i2 = '';
    }
    
    $a .= "<tr><td $c>$i1</td>
    <td $c>$w[FakultasID]</td>
    <td $c><a href='?mnux=fak&fid=$w[FakultasID]&gos=FakEdt&md=0'><img src='img/edit.png' border=0></a></td>
    <td $c><a href='?mnux=fak&fid=$w[FakultasID]'>$w[Nama]</a></td>
    <td class=cna$w[NA] align=center><img src='img/book$w[NA].gif'></td>
    <td $c>$i2</td></tr>";
  }
  return $a. "</table></p>";
}
function FakEdt() {
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('fakultas', 'FakultasID', $_REQUEST['fid'], '*');
    $jdl = 'Edit Fakultas';
    $_fid = "<input type=hidden name='FakultasID' value='$w[FakultasID]'><b>$w[FakultasID]</b>";
  }
  else {
    $w = array();
    $w['FakultasID'] = '';
    $w['Nama'] = '';
    $w['Pejabat'] = '';
    $w['Jabatan'] = '';
    $w['NA'] = 'N';
    $jdl = 'Tambah Fakultas';
    $_fid = "<input type=text name='FakultasID' size=15 maxlength=20>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  CheckFormScript("FakultasID, Nama");
  return "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='fak'>
  <input type=hidden name='gos' value='FakSav'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th colspan=2 class=ttl>$jdl</th></tr>
  <tr><td class=inp1>Kode Fakultas &nbsp;</td><td class=ul>$_fid</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp1>Pejabat Fakultas</td><td class=ul><input type=text name='Pejabat' value='$w[Pejabat]' size=50 maxlength=200></td></tr>
  <tr><td class=inp1>Nama Jabatan</td><td class=ul><input type=text name='Jabatan' value='$w[Jabatan]' size=50 maxlength=200></td></tr>
  <tr><td class=inp1>Tidak Aktif?&nbsp;</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=fak'\"></td></tr>
  </form></table></p>";
}
function FakSav() {
  $md = $_REQUEST['md'] +0;
  $FakultasID = $_REQUEST['FakultasID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Pejabat = sqling($_REQUEST['Pejabat']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update fakultas set Nama='$Nama', NA='$NA', Pejabat='$Pejabat', Jabatan='$Jabatan'
      where FakultasID='$FakultasID' ";
    $r = _query($s);
    return DftrProdi();
  }
  else {
    $ada = GetFields('fakultas', 'FakultasID', $FakultasID, '*');
    if (empty($ada)) {
      $s = "insert into fakultas(FakultasID, KodeID, Nama, Pejabat, Jabatan, NA)
        values('$FakultasID', '$_SESSION[KodeID]', '$Nama', '$Pejabat', '$Jabatan', '$NA')";
      $r = _query($s);
      return DftrProdi();
    }
    else return ErrorMsg("Kesalahan", "Fakultas dengan Kode: <b>$FakultasID</b> telah digunakan oleh
      Fakultas: <b>$ada[Nama]</b>.<br>
      Gunakan kode lain.");
  }
}

// *** Prodi ***
function DftrProdi() {
  if (!isset($_SESSION['fid'])) return '';
  else {
    $s = "select p.*, j.Nama as JEN
      from prodi p 
      left outer join jenjang j on p.JenjangID=j.JenjangID
      where FakultasID='$_SESSION[fid]' order by ProdiID";
    $r = _query($s);
    $fak = GetFields('fakultas', 'FakultasID', $_SESSION['fid'], '*');
    $a = "<p><table class=box cellspacing=1 cellpadding=4>
      <tr><td>$fak[FakultasID]</td><td colspan=4><b>$fak[Nama]</td></tr>
      <tr><th colspan=2 class=ttl>Kode</th>
      <th class=ttl>Program Studi</th>
      <th class=ttl>Jenjang</th>
      <th class=ttl>Format NIM</th>
      <th class=ttl>Dpt Pindah<br />ke Prodi</th>
      <th class=ttl>Default<br />Kehadiran</th>
      <th class=ttl>Batas<br />Studi</th>
      <th class=ttl>NA</th></tr>";
    while ($w = _fetch_array($r)) {
      $c = ($w['NA'] == 'Y') ? 'class=nac' : 'class=ul';
      $a .= "<tr><td $c>$w[ProdiID]</td>
        <td $c><a href='?mnux=fak&gos=ProdiEdt&md=0&prid=$w[ProdiID]'><img src='img/edit.png' border=0></a></td>
        <td $c>$w[Nama]</td>
        <td $c>$w[JEN]</td>
        <td $c>$w[FormatNIM]</td>
        <td $c>$w[DapatPindahProdi]&nbsp;</td>
        <td $c align=right>$w[DefKehadiran]</td>
        <td $c align=right>$w[BatasStudi]/$w[JumlahSesi]</td>
        <td $c align=center><img src='img/book$w[NA].gif' border=0></td>
        </tr>";
    }
    return $a."</table></p>";
  }
}
function ProdiEdt() {
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('prodi', 'ProdiID', $_REQUEST['prid'], '*');
    $_prid = "<input type=hidden name='prid' value='$w[ProdiID]'><b>$w[ProdiID]</b>";
    $jdl = 'Edit Program Studi';
  }
  else {
    $w = array();
    $w['ProdiID'] = '';
    $w['Nama'] = '';
    $w['Nama_en'] = '';
    $w['JenjangID'] = '';
    $w['Gelar'] = '';
    $w['FakultasID'] = $_SESSION['fid'];
    $w['FormatNIM'] = '';
    $w['DapatPindahProdi'] = '';
    $w['ProdiDiktiID'] = '';
    $w['NamaSesi'] = "Semester";
    $w['CekPrasyarat'] = 'Y';
    $w['DefSKS'] = 0;
    $w['DefKehadiran'] = 16;
    $w['TotalSKS'] = 0;
    $w['BatasStudi'] = 0;
    $w['JumlahSesi'] = 0;
    $w['Akreditasi'] = '';
    $w['NoSKDikti'] = '';
    $w['TglSKDikti'] = date('Y-m-d');
    $w['NoSKBAN'] = '';
    $w['TglSKBAN'] = date('Y-m-d');
    $w['PajakHonorDosen'] = 10;
    $w['Pejabat'] = '';
    $w['Jabatan'] = '';
    $w['NA'] = 'N';
    $_prid = "<input type=text name='prid' size=30 maxlength=20>";
    $jdl = 'Tambah Program Studi';
  }
  CheckFormScript("prid,Nama,FormatNIM,DefSKS");
  CariProdiDikti();
  $NamaProdi = GetaField('prodidikti', 'ProdiDiktiID', $w['ProdiDiktiID'], 'Nama');
  $TglSKDikti = GetDateOption($w['TglSKDikti'], 'TglSKDikti');
  $TglSKBAN = GetDateOption($w['TglSKBAN'], 'TglSKBAN');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $cp = ($w['CekPrasyarat'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $opt = GetOption2('fakultas', "concat(FakultasID, '. ', Nama)", 'FakultasID', $w['FakultasID'], '', 'FakultasID');
  $optjen = GetOption2('jenjang', "concat(JenjangID, ' - ', Nama)", 'JenjangID', $w['JenjangID'], '', 'JenjangID');
  //GetCheckboxes($table, $key, $Fields, $Label, $Nilai='', $Separator=',') {
  $pind = GetCheckboxes("prodi", "ProdiID", "concat(ProdiID, ' - ', Nama) as NM",
    'NM', $w['DapatPindahProdi'], '.');

  return "<p><table class=box cellspacing=1 cellpadding=4>
  <form name='data' action='?' method=POST onSubmit=\"return CheckForm(data)\">
  <input type=hidden name='mnux' value='fak'>
  <input type=hidden name='gos' value='ProdiSav'>
  <input type=hidden name='md' value='$md'>

  <tr><th colspan=2 class=ttl>$jdl</th></tr>
  <tr><td class=inp>Program Studi</td><td class=ul>$_prid</td></tr>
  <tr><td class=inp>Fakultas</td><td class=ul><select name='FakultasID'>$opt</select></td></tr>
  <tr><td class=inp>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Nama Inggris</td><td class=ul><input type=text name='Nama_en' value='$w[Nama_en]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Jenjang</td><td class=ul><select name='JenjangID'>$optjen</select></td></tr>
  <tr><td class=inp>Gelar</td><td class=ul><input type=text name='Gelar' value='$w[Gelar]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Format NIM</td><td class=ul><input type=text name='FormatNIM' value='$w[FormatNIM]' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Dapat Pindah ke Prodi</td><td class=ul>$pind</td></tr>
  <tr><td class=inp>Nama Sesi</td><td class=ul><input type=text name='NamaSesi' value='$w[NamaSesi]' size=30 maxlength=50> Misal: Semester, Cawu.</td></tr>
  <tr><td class=inp>Cek Prasyarat</td><td class=ul><input type=checkbox name='CekPrasyarat' value='Y' $cp></td></tr>
  <tr><td class=inp>Total SKS Lulus</td><td class=ul><input type=text name='TotalSKS' value='$w[TotalSKS]' size=5 maxlength=4></td></tr>
  <tr><td class=inp>Default SKS</td><td class=ul><input type=text name='DefSKS' value='$w[DefSKS]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>Default Jumlah Kehadiran</td><td class=ul><input type=text name='DefKehadiran' value='$w[DefKehadiran]' size=4 maxlength=3></td></tr>
  <tr><td class=inp>Kode Prodi Dikti</td><td class=ul><input type=text name='ProdiDiktiID' value='$w[ProdiDiktiID]' size=10 maxlength=20>
    <input type=text name='NamaProdi' value='$NamaProdi' size=30 maxlength=50>
    <a href='javascript:cariprodidikti(data)'>Cari</a></td></tr>
  <tr><td class=inp>Pajak Honor Dosen</td><td class=ul><input type=text name='PajakHonorDosen' value='$w[PajakHonorDosen]' size=3 maxlength=3></td></tr>
  <tr><td class=inp>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  
  <tr><td class=ul colspan=2><b>Pejabat Jurusan</b></td></tr>
  <tr><td class=inp>Nama Pejabat</td><td class=ul><input type=text name='Pejabat' value='$w[Pejabat]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Nama Jabatan</td><td class=ul><input type=text name='Jabatan' value='$w[Jabatan]' size=40 maxlength=50></td></tr>
  
  <tr><td class=ul colspan=2><b>Batas Studi</b></td></tr>
  <tr><td class=inp>Batas Studi</td><td class=ul><input type=text name='BatasStudi' value='$w[BatasStudi]' size=3 maxlength=2> Sesi/Semester</td></tr>
  <tr><td class=inp>Jml Sesi/tahun</td><td class=ul><input type=text name='JumlahSesi' value='$w[JumlahSesi]' size=3 maxlength=2> per tahun</td></tr>
  
  <tr><td class=ul colspan=2><b>Surat Keputusan</b></td></tr>
  <tr><td class=inp>No SK Dikti</td><td class=ul><input type=text name='NoSKDikti' value='$w[NoSKDikti]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal SK Dikti</td><td class=ul>$TglSKDikti</td></tr>
  <tr><td class=inp>No SK BAN</td><td class=ul><input type=text name='NoSKBAN' value='$w[NoSKBAN]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal SK BAN</td><td class=ul>$TglSKBAN</td></tr>
  <tr><td class=inp>Akreditasi</td><td class=ul><input type=text name='Akreditasi' value='$w[Akreditasi]' size=5 maxlength=10></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=fak&$snm=$sid'\"></td></tr>
  </form></table></p>";
}
function ProdiSav() {
  $md = $_REQUEST['md']+0;
  $prid = $_REQUEST['prid'];
  $Nama = sqling($_REQUEST['Nama']);
  $Nama_en = sqling($_REQUEST['Nama_en']);
  $JenjangID = $_REQUEST['JenjangID'];
  $Gelar = sqling($_REQUEST['Gelar']);
  $FormatNIM = sqling($_REQUEST['FormatNIM']);
  $FakultasID = $_REQUEST['FakultasID'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $arrpindah = array();
  $arrpindah = $_REQUEST['ProdiID'];
  $pindah = (empty($arrpindah))? '' : '.'.implode('.', $arrpindah).'.';
  $NamaSesi = sqling($_REQUEST['NamaSesi']);
  $CekPrasyarat = (empty($_REQUEST['CekPrasyarat']))? "N" : $_REQUEST['CekPrasyarat'];
  $TotalSKS = $_REQUEST['TotalSKS']+0;
  $DefSKS = $_REQUEST['DefSKS']+0;
  $DefKehadiran = $_REQUEST['DefKehadiran']+0;
  $BatasStudi = $_REQUEST['BatasStudi']+0;
  $JumlahSesi = $_REQUEST['JumlahSesi']+0;
  $ProdiDiktiID = $_REQUEST['ProdiDiktiID'];
  $Akreditasi = $_REQUEST['Akreditasi'];
  $Pejabat = sqling($_REQUEST['Pejabat']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $NoSKDikti = sqling($_REQUEST['NoSKDikti']);
  $TglSKDikti = "$_REQUEST[TglSKDikti_y]-$_REQUEST[TglSKDikti_m]-$_REQUEST[TglSKDikti_d]";
  $NoSKBAN = sqling($_REQUEST['NoSKBAN']);
  $TglSKBAN = "$_REQUEST[TglSKBAN_y]-$_REQUEST[TglSKBAN_m]-$_REQUEST[TglSKBAN_d]";
  $PajakHonorDosen = $_REQUEST['PajakHonorDosen']+0;
  if ($md == 0) {
    $s = "update prodi set Nama='$Nama', Nama_en='$Nama_en', FormatNIM='$FormatNIM',
      JenjangID='$JenjangID', DapatPindahProdi='$pindah', FakultasID='$FakultasID',
      Gelar='$Gelar',
      NamaSesi='$NamaSesi', CekPrasyarat='$CekPrasyarat',
      TotalSKS=$TotalSKS, DefSKS=$DefSKS, DefKehadiran=$DefKehadiran, NA='$NA',
      ProdiDiktiID='$ProdiDiktiID', Akreditasi='$Akreditasi',
      BatasStudi='$BatasStudi', JumlahSesi='$JumlahSesi',
      Pejabat='$Pejabat', Jabatan='$Jabatan',
      NoSKDikti='$NoSKDikti', TglSKDikti='$TglSKDikti',
      NoSKBAN='$NoSKBAN', TglSKBAN='$TglSKBAN',
      PajakHonorDosen='$PajakHonorDosen'
      where ProdiID='$prid' ";
    $r = _query($s);
    return DftrProdi();
  }
  else {
    $ada = GetFields('prodi', 'ProdiID', $prid, '*');
    if (empty($ada)) {
      $s = "insert into prodi(ProdiID, Nama, Nama_en, KodeID, FormatNIM, FakultasID, NamaSesi, CekPrasyarat,
        JenjangID, Gelar, DapatPindahProdi, TotalSKS, DefSKS, DefKehadiran, BatasStudi, JumlahSesi, 
        Pejabat, Jabatan, NA, ProdiDiktiID,
        Akreditasi, NoSKDikti, TglSKDikti,
        NoSKBAN, TglSKBAN, PajakHonorDosen)
        values('$prid', '$Nama', '$Nama_en', '$_SESSION[KodeID]', '$FormatNIM', '$FakultasID', '$NamaSesi', '$CekPrasyarat',
        '$JenjangID', '$Gelar', '$pindah', '$TotalSKS', '$DefSKS', '$DefKehadiran', '$BatasStudi', '$JumlahSesi', 
        '$Pejabat', '$Jabatan', '$NA', '$ProdiDiktiID',
        '$Akreditasi', '$NoSKDikti', '$TglSKDikti',
        '$NoSKBAN', '$TglSKBAN', '$PajakHonorDosen')";
      $r = _query($s);
      return DftrProdi();
    }
    else return ErrorMsg('Kesalahan', "Kode Program Studi: <b>$ProdiID</b> telah digunakan oleh
      Program Studi: <b>$ada[Nama]</b>.<br>
      Gunakan kode program studi yang lain.");
  }
}
function CariProdiDikti() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function cariprodidikti(frm){
    lnk = "cariprodidikti.php?ProdiDiktiID="+frm.ProdiDiktiID.value+"&Cari="+frm.NamaProdi.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}

// *** Default ***
function DefFak() {
  $gos = (empty($_REQUEST['gos']))? 'DftrProdi' : $_REQUEST['gos'];
  $ka = $gos();
  $ki = DftrFak();

  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td valign=top>$ki</td>
  <td valign=top>$ka</td></tr>
  </table></p>";
}

// *** Parameters ***
$fid = GetSetVar('fid');
$KodeID = GetSetVar('KodeID', $_Identitas);


// *** Main ***
TampilkanJudul("Fakultas - Program Studi");
DefFak();
?>
