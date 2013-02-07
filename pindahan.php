<?php
// Author: Emanuel Setio Dewo
// 10 Juli 2006
// www.sisfokampus.net

// *** Functions ***
function DaftarPindahan() {
  $_whr = array();
  if (!empty($_SESSION['prid'])) $_whr[] = "mp.ProgramID='$_SESSION[prid]' ";
  if (!empty($_SESSION['prodi'])) $_whr[] = "mp.ProdiID='$_SESSION[prodi]' ";
  $whr = implode(' and ', $_whr);
  $whr = (empty($whr))? '' : "and $whr";
  $s = "select mp.*, pt.Nama as NamaPT
    from mhswpindahan mp
      left outer join perguruantinggi pt on mp.AsalPT=pt.PerguruanTinggiID
    where mp.NA='N' and mp.TahunID='$_SESSION[tahun]' 
      $whr
    order by mp.MhswPindahanID desc";
  $r = _query($s);
  echo "<p><a href='?mnux=pindahan&gos=PindEdt&md=1'>Pendataan Mahasiswa Pindahan</a></p>";
  echo "<p><table class=box cellspacing=1>
  <tr><th class=ttl colspan=2>ID</th>
  <th class=ttl># PMB</th>
  <th class=ttl>NIM</th>
  <th class=ttl>Nama</th>
  <th class=ttl colspan=2>Asal Perguruan Tinggi</th>
  <th class=ttl>Prodi Asal</th>
  <th class=ttl>IPK</th>
  <th class=ttl colspan=2>Penyetaraan</th>
  <th class=ttl>Proses</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    if (empty($w['MhswID'])) {
      $c = "class=ul";
      $edt = "<a href='?mnux=pindahan&md=0&gos=PindEdt&MhswPindahanID=$w[MhswPindahanID]'><img src='img/edit.png'></a>";
      $str = "<a href='?mnux=pindahan.setara&MPID=$w[MhswPindahanID]'><img src='img/edit.png'></a>";
    }
    else {
      $c = "class=nac";
      $edt = "&nbsp;";
      $str = "&nbsp;";
    }
    $prs = (!empty($w['MhswID']) && $w['Sudah'] != 1) ? "<a href='?mnux=setara.go&gos=ExportKRSSetara&mhswstrID=$w[MhswPindahanID]&prodit=$w[ProdiID]' title='Proses'><img src='img/gear.gif'></a>" : "&nbsp;";
    echo "<tr><td class=inp>$w[MhswPindahanID]</td>
      <td $c align=center>$edt</td>
      <td $c>$w[MhswID]</td>
      <td $c>$w[MhswID]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[AsalPT]</td>
      <td $c>$w[NamaPT]</td>
      <td $c>$w[ProdiAsalPT]</td>
      <td $c align=right>$w[IPKAsalPT]</td>
      <td $c align=right>$w[JumlahSetara]</td>
      <td $c align=center width=5>$str</td>
      <td $c>$prs</td>
      </tr>";
  }
  echo "</table></p>";
}
function CariPTScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function caript(frm){
    lnk = "cariperguruantinggi.php?PerguruanTinggiID="+frm.AsalPT.value+"&Cari="+frm.NamaPT.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function PindEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mhswpindahan', 'MhswPindahanID', $_REQUEST['MhswPindahanID'], '*');
    $jdl = "Edit Data Mhsw Pindahan";
  }
  else {
    $w = array();
    $w['MhswPindahanID'] = 0;
    $w['Nama'] = '';
    $w['MhswID'] = '';
    $w['BatasStudi'] = '';
    $w['TahunID'] = $_SESSION['tahun'];
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['ProgramID'] = $_SESSION['prid'];
    $w['Alamat'] = '';
    $w['Kota'] = '';
    $w['RT'] = '';
    $w['RW'] = '';
    $w['KodePos'] = '';
    $w['Propinsi'] = '';
    $w['Negara'] = '';
    $w['Telepon'] = '';
    $w['Handphone'] = '';
    $w['Email'] = '';
    $w['AsalPT'] = '';
    $w['ProdiAsalPT'] = '';
    $w['MhswIDAsalPT'] = '';
    $w['IPKAsalPT'] = 0;
    $w['SKPenyetaraan'] = '';
    $w['TglSKPenyetaraan'] = '';
    $jdl = "Tambah Data Mhsw Pindahan";
  }
  $TglSKPenyetaraan = GetDateOption($w['TglSKPenyetaraan'], 'TglSKPenyetaraan');
  $PT = GetaField('perguruantinggi','PerguruanTinggiID',$w['AsalPT'],"concat(Nama,',',Kota)");
  $LulusAsalPT = ($w['LulusAsalPT'] == 'Y')? 'checked' : '';
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['ProdiID'], '', 'ProdiID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $w['ProgramID'], '', 'ProgramID');
  CariPTScript();
  CheckFormScript("Nama,Alamat,Kota,AsalPT,ProdiAsalPT");
  echo "<p><table class=box cellspacing=1>
  <form action='?' name='data' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='gos' value='PindSav'>
  <input type=hidden name='MhswPindahanID' value='$w[MhswPindahanID]'>
  <input type=hidden name='tahun' value='$_SESSION[tahun]'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Mhsw</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>NIM</td><td class=ul><input type=text name='MhswID' value='$w[MhswID]' size=15 maxlength=20></td></tr>
  <tr><td class=inp>Batas Studi</td><td class=ul><input type=text name='BatasStudi' value='$w[BatasStudi]' size=15 maxlength=20></td></tr>
  <tr><td class=inp>Masuk ke Program</td><td class=ul><select name='prid'>$optprg</select></td></tr>
  <tr><td class=inp>Masuk ke Program Studi</td><td class=ul><select name='prodi'>$optprd</select></td></tr>
  <tr><td class=inp>Alamat</td><td class=ul><input type=text name='Alamat' value='$w[Alamat]' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Kota / Kode Pos</td><td class=ul><input type=text name='Kota' value='$w[Kota]' size=30 maxlength=50> /
    <input type=text name='KodePos' value='$w[KodePos]' size=15 maxlength=50></td></tr>
  <tr><td class=inp>RT / RW</td><td class=ul><input type=text name='RT' value='$w[RT]' size=5 maxlength=5> /
    <input type=text name='RW' value='$w[RW]' size=5 maxlength=5></td></tr>
  <tr><td class=inp>Propinsi / Negara</td>
    <td class=ul><input type=text name='Propinsi' value='$w[Propinsi]' size=20 maxlength=50> /
    <input type=text name='Negara' value='$w[Negara]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>Telepon / Handphone</td>
    <td class=ul><input type=text name='Telepon' value='$w[Telepon]' size=20 maxlength=50> /
    <input type=text name='Handphone' value='$w[Handphone]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>Email</td><td class=ul><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  
  <tr><td class=inp>Kode P.T</td><td class=ul><input type=text name='AsalPT' value='$w[AsalPT]' size=10 maxlength=10></td></tr></td></tr>
  <tr><td class=inp>Nama P.T</td><td class=ul><input type=text name='NamaPT' value='$PT' size=50> <a href='javascript:caript(data)'>Cari</a></td></tr>
  
  <tr><td class=inp>Prodi Asal</td><td class=ul><input type=text name='ProdiAsalPT' value='$w[ProdiAsalPT]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>NPM Asal</td><td class=ul><input type=text name='MhswIDAsalPT' value='$w[MhswIDAsalPT]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>IPK Asal</td><td class=ul><input type=text name='IPKAsalPT' value='$w[IPKAsalPT]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>No SK Penyetaraan</td><td class=ul><input type=text name='SKPenyetaraan' value='$w[SKPenyetaraan]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal SK Penyetaraan</td><td class=ul>$TglSKPenyetaraan</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pindahan'\"></td></tr>
  </form></table></p>";
}
function PindSav() {
  $md = $_REQUEST['md']+0;
  $MhswPindahanID = $_REQUEST['MhswPindahanID']+0;
  $tahun = $_REQUEST['tahun'];
  $Nama = sqling($_REQUEST['Nama']);
  $BatasStudi = sqling($_REQUEST['BatasStudi']);
  $MhswID = sqling($_REQUEST['MhswID']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $RT = sqling($_REQUEST['RT']);
  $RW = sqling($_REQUEST['RW']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Handphone']);
  $AsalPT = $_REQUEST['AsalPT'];
  $ProdiAsalPT = sqling($_REQUEST['ProdiAsalPT']);
  $MhswIDAsalPT = sqling($_REQUEST['MhswIDAsalPT']);
  $IPKAsalPT = $_REQUEST['IPKAsalPT']+0;
  $MhswIDAsalPT = sqling($_REQUEST['MhswIDAsalPT']);
  $TglSKPenyetaraan = "$_REQUEST[TglSKPenyetaraan_y]-$_REQUEST[TglSKPenyetaraan_m]-$_REQUEST[TglSKPenyetaraan_d]";
  $SKPenyetaraan = sqling($_REQUEST['SKPenyetaraan']);
  if ($md == 0) {
    $s = "update mhswpindahan set Nama='$Nama', MhswID='$MhswID', BatasStudi='$BatasStudi', Alamat='$Alamat', Kota='$Kota',
      RT='$RT', RW='$RW', KodePos='$KodePos', Propinsi='$Propinsi', Negara='$Negara',
      Telepon='$Telepon', Handphone='$Handphone', Email='$Email',
      ProdiID='$_SESSION[prodi]', ProgramID='$_SESSION[prid]', MhswIDAsalPT='$MhswIDAsalPT',
      AsalPT='$AsalPT', ProdiAsalPT='$ProdiAsalPT', MhswIDAsalPT='$MhswIDAsalPT', IPKAsalPT='$IPKAsalPT',
      LoginEdit='$_SESSION[_Login]',TglSKPenyetaraan='$TglSKPenyetaraan', SKPenyetaraan='$SKPenyetaraan', TanggalEdit=now()
      where MhswPindahanID=$MhswPindahanID ";
    $r = _query($s);
    $s0 = "update mhswpindahansetara set MhswID='$MhswID' where MhswPindahanID=$MhswPindahanID ";
    $r0 = _query($s0);
  }
  else {
    $s = "insert into mhswpindahan
      (TahunID, Nama, MhswID, Alamat, Kota, RT, RW, KodePos, StatusMhswID,
      Propinsi, Negara, Telepon, Handphone,
      Email, AsalPT, ProdiAsalPT, IPKAsalPT,
      ProdiID, ProgramID, MhswIDAsalPT,
      LoginBuat, TanggalBuat)
      values
      ('$tahun', '$Nama', '$MhswID', '$Alamat', '$Kota', '$RT', '$RW', '$KodePos', 'A',
      '$Propinsi', '$Negara', '$Telepon', '$Handphone',
      '$Email', '$AsalPT', '$ProdiAsalPT', '$IPKAsalPT',
      '$_SESSION[prodi]', '$_SESSION[prid]', '$MhswIDAsalPT',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  //echo "<pre>$MhswPindahanID</pre>";
  echo "<script>window.location = '?mnux=pindahan';</script>";
}


// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? "DaftarPindahan" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Pendataan & Penyetaraan Mhsw Pindahan");
TampilkanTahunProdiProgram('pindahan', '');
if (!empty($tahun) && !empty($prodi)) $gos();
?>
