<?php
// Author: Sugeng
// 09 Juni 2008

function DftrJabatanMaster() {
  $ka = (empty($_REQUEST['sub1']))? DftrPejabat() : $_REQUEST['sub1']();
  $ki = DftrJabatan();

  echo "<p><table class=bsc cellspacing=1 cellpadding=0 width=100% align=center>
  <td valign=top align=center class=kolkir>$ki</td>
  <td valign=top align=center class=kolkan>$ka</td>
  </table></p>";
}
function DftrJabatan() {
  global $mnux, $tok, $arrID;
  $s = "select *, date_format(TglAwalJabatan, '%d-%m-%Y') as TGLAWAL, date_format(TglAkhirJabatan, '%d-%m-%Y') as TGLAKHIR
    from periodejabatan
    where KodeID='$_SESSION[KodeID]'
    order by TahunJabatan desc";
  $r = _query($s);
  
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td colspan=8 class=ul><a href='?mnux=$mnux&tok=$tok&sub1=JabatanMasterEdt&md=1'>Tambah Periode Jabatan</a></td></tr>
  <tr>
  <th class=ttl>&nbsp;</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Nama Periode</th>
    <th class=ttl>Tanggal Awal</th>
    <th class=ttl>Tanggal Berakhir</th>
    <th class=ttl title='Default'>Def</th>
    <th class=ttl title='Tidak aktif'>NA</th>
  <th class=ttl>&nbsp;</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $d = ($w['Def'] == 'Y')? 'class=ul' : 'class=nac';
    if ($w['PeriodeJabatanID'] == $_SESSION['jbtnperiod']) {
      $_ki = "<img src='img/kanan.gif'>";
      $_ka = "<img src='img/kiri.gif'>";
    }
    else {
      $_ki = '';
      $_ka = '';
    }
    
    $a .= "<tr>
      <td $c>$_ki</td>
      <td $c nowrap><a href='?mnux=$mnux&tok=$tok&sub1=JabatanMasterEdt&md=0&jbtnperiod=$w[PeriodeJabatanID]' title='Edit Periode Jabatan'><img src='img/edit.png'> $w[TahunJabatan]</a></td>
      <td $c nowrap><a href='?mnux=$mnux&tok=$tok&sub=&jbtnperiod=$w[PeriodeJabatanID]' title='Lihat Detail'>$w[Nama]</a></td>
      <td $c align=center>$w[TGLAWAL]</td>
      <td $c align=center>$w[TGLAKHIR]</td>
      <td $d align=center><img src='img/$w[Def].gif'></td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      <td $c>$_ka</td>
      </tr>";
  }
  return "$a</table></p>";
}
function JabatanMasterEdt() {
  global $arrID, $mnux, $tok;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('periodejabatan', "PeriodeJabatanID", $_REQUEST['jbtnperiod'], '*');
    $jdl = "Edit Periode Jabatan";
  }
  else {
    $w = array();
    $w['PeriodeJabatanID'] = 0;
    $w['TahunJabatan'] = '';
    $w['Nama'] = '';
    $w['TglAwalJabatan'] = date('Y-m-d');
    $w['TglAkhirJabatan'] = date('Y-m-d');
    $w['Catatan'] = '';
    $w['Def'] = 'N';
    $w['NA'] = 'N';
    $jdl = "Tambah Periode Jabatan";
  }
  $TglAwalJabatan  = GetDateOption($w['TglAwalJabatan'], 'TglAwalJabatan');
  $TglAkhirJabatan = GetDateOption($w['TglAkhirJabatan'], 'TglAkhirJabatan');
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $Def = ($w['Def'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  CheckFormScript("Tahun,Nama");
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub1' value='JabatanMasterSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='jbtnperiod' value='$w[PeriodeJabatanID]'>
  
  <tr><th class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Kode Tahun</td><td class=ul><input type=text name='Tahun' value='$w[TahunJabatan]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Nama Periode Jabatan</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Tanggal Awal Jabatan</td><td class=ul>$TglAwalJabatan</td></tr>
  <tr><td class=inp1>Tanggal Akhir Jabatan</td><td class=ul>$TglAkhirJabatan</td></tr>
  <tr><td class=inp1>Catatan</td><td class=ul><textarea name='Catatan' cols=30 rows=3>$w[Catatan]</textarea></td></tr>
  <tr><td class=inp1>Default?</td><td class=ul><input type=checkbox name='Def' value='Y' $Def></td></tr>
  <tr><td class=inp1>Tidak Aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $NA></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok&$snm=$sid'\"></td></tr>
  </form></table></p>";
  return $a;
}
function JabatanMasterSav() {
  $md = $_REQUEST['md']+0;
  $Tahun = sqling($_REQUEST['Tahun']);
  $Nama = sqling($_REQUEST['Nama']);
  $TglAwalJabatan  = "$_REQUEST[TglAwalJabatan_y]-$_REQUEST[TglAwalJabatan_m]-$_REQUEST[TglAwalJabatan_d]";
  $TglAkhirJabatan = "$_REQUEST[TglAkhirJabatan_y]-$_REQUEST[TglAkhirJabatan_m]-$_REQUEST[TglAkhirJabatan_d]";
  $Catatan = sqling($_REQUEST['Catatan']);
  $Def = (empty($_REQUEST['Def']))? 'N' : $_REQUEST['Def'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $PeriodeJabatanID = $_REQUEST['jbtnperiod'];
    $s = "update periodejabatan set Nama='$Nama', TahunJabatan='$Tahun', Catatan='$Catatan', 
      Def='$Def', NA='$NA', TglAwalJabatan='$TglAwalJabatan', TglAkhirJabatan='$TglAkhirJabatan',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where PeriodeJabatanID='$PeriodeJabatanID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into periodejabatan (TahunJabatan, Nama, KodeID, Catatan, 
      Def, NA, TglAwalJabatan, TglAkhirJabatan,
      TglBuat, LoginBuat)
      values('$Tahun', '$Nama', '$_SESSION[KodeID]', '$Catatan', 
      '$Def', '$NA', '$TglAwalJabatan', '$TglAkhirJabatan',
      now(), '$_SESSION[_Login]')";
    $r = _query($s);
    // Ambil Last_Insert_ID
    $s_last = "select LAST_INSERT_ID() as ID";
    $r_last = _query($s_last);
    $w_last = _fetch_array($r_last);
    $PeriodeJabatanID = $w_last['ID'];
  }
  
  // Apakah diset menjadi default?
  if ($Def == 'Y') {
    $sd = "update periodejabatan set Def='N' 
      where PeriodeJabatanID<>$PeriodeJabatanID";
    $rd = _query($sd);
  }
  return DftrPejabat();
}

function DftrPejabat() {
  global $mnux, $tok;
  
  $s = "SELECT pj.*, jj.Nama as NAMAJABATAN FROM pejabat pj LEFT OUTER JOIN jenisjabatan jj ON pj.JenisJabatanID = jj.JenisJabatanID
        WHERE pj.PeriodeJabatanID='$_SESSION[jbtnperiod]'
        AND pj.KodeID='$_SESSION[KodeID]'";
  
  $r = _query($s);
  if ($_SESSION['_LevelID'] == 1) {
    $del = "<th class=ttl>Del</th>";
  }
  
  $a = "<p><a href='?mnux=$mnux&tok=$tok&sub1=PejabatEdt&md=1'>Tambah</a> | <a href='?mnux=$mnux&tok=$tok&sub1=PejabatCopy'>Salin Dari Periode Lain</a></p>";
  
  $a .= "<table class=box cellpadding=4 cellspacing=1>
        <tr><th class=ttl>No.</th><th class=ttl>Jabatan</th><th class=ttl>Nama Pejabat</th><th class=ttl>Menjabat di Fakultas</th><th class=ttl>Menjabat di Prodi</th>$del</tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $nmprodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
    $nmfakul = GetaField('fakultas', 'FakultasID', $w['FakultasID'], 'Nama');
    
    if (!empty($del)) $lnk = "<td class=ul><a href='?mnux=$mnux&tok=$tok&sub1=jbtDel&jbtn=$w[JabatanID]'><img src='img/del.gif'></a></td>";
    
    $a .= "<tr><td class=inp1>$n</td><td class=ul>$w[NAMAJABATAN]</td><td class=ul>$w[Nama]</td><td class=ul>$nmfakul&nbsp;</td><td class=ul>$nmprodi&nbsp;</td>$lnk</tr>";
  }
  
  $a .= "</table>";
  return $a;
}

function PejabatEdt() {
  global $mnux, $tok;
  
  $md = $_REQUEST['md'] +0;
  // Jika Edit
  if ($md == 0) {
    $PejabatID = $_REQUEST['pjbtid'];
    $w = GetFields('pejabat', "PejabatID", $PejabatID, '*');
    $jdl = "Edit Pejabat";
  }
  // Jika tambah
  else {
    $w = array();
    $w['PeriodeJabatanID'] = $_SESSION['jbtnperiod'];
    $w['Nama'] = '';
    $w['JenisJabatanID'] = '';
    $w['FakultasID'] = '';
    $w['ProdiID'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Pejabat";
  }
  // setup
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $NamaJabatan = GetOption2('jenisjabatan', "concat(Singkatan, ' - ', Nama)", 'JenisJabatanID', $w['JenisJabatanID'], '', 'JenisJabatanID');
  $Fakultas    = GetOption2('fakultas', "concat(FakultasID, ' - ', Nama)", 'FakultasID', $w['FakultasID'], '', 'FakultasID');
  $Prodi       = GetOption2('prodi', "Concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['ProdiID'], '', 'ProdiID');
  
  // Tuliskan
  CheckFormScript("Nama,NamaJabatan");
  return "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub1' value='JabatanDtlSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='PejabatID' value='$w[PejabatID]'>
  <input type=hidden name='PeriodeJabatan' value='$w[PeriodeJabatanID]'>

  <tr><th class=ttl colspan=2><b>$jdl</th></tr>
  <tr><td class=inp1>Jabatan</td><td class=ul><select name='NamaJabatan'>$NamaJabatan</select></td></tr>
  <tr><td class=inp1>Nama Pejabat</td><td class=ul><input type=text name='NamaPejabat' value='$w[Nama]' size=45></td></tr>
  <tr><td class=inp1>Menjabat di Fakultas?</td><td class=ul><select name='Fakultas'>$Fakultas</select></td></tr>
  <tr><td class=inp1>Menjabat di Prodi?</td><td class=ul><select name='Prodi'>$Prodi</select></td></tr>
  <tr><td class=inp1>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $NA></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok'\"></td></tr>
  </form></table></p>";
}
function JabatanDtlSav() {
  $md          = $_REQUEST['md']+0;
  $PeriodeJabatan = $_REQUEST['PeriodeJabatan'];
  $NamaJabatan = $_REQUEST['NamaJabatan'];
  $NamaPejabat = sqling($_REQUEST['NamaPejabat']);
  $Fakultas    = $_REQUEST['Fakultas'];
  $Prodi       = $_REQUEST['Prodi'];
  $NA          = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  
  if ($md == 0) {
    $PejabatID = $_REQUEST['PejabatID'];
    $s = "update pejabat set Nama='$NamaPejabat', 
      JenisJabatanID='$NamaJabatan', FakultasID='$Fakultas',
      ProdiID='$Prodi',
      LoginEdit='$_SESSION[_Login]', TglEdit=NOW(),
      NA='$NA'
      where PejabatID='$PejabatID'";
  }
  else {
    $s = "insert into pejabat
      (JenisJabatanID, Nama, FakultasID, ProdiID, KodeID, PeriodeJabatanID, LoginBuat, TglBuat)
      values('$NamaJabatan', '$NamaPejabat', '$Fakultas', '$Prodi', '$_SESSION[KodeID]', '$PeriodeJabatan', '$_SESSION[_Login]', NOW())";
  }
  
  if (!empty($Fakultas)) {
    $nm = GetaField('jenisjabatan', 'JenisJabatanID', $NamaJabatanID, 'Nama');
    $ti = "update fakultas set Jabatan = '$nm', Pejabat = '$NamaPejabat' where FakultasID='$Fakultas'";
    $rti = _query($ti);
  }
  
  if (!empty($Prodi)) {
    $nm = GetaField('jenisjabatan', 'JenisJabatanID', $NamaJabatanID, 'Nama');
    $tip = "update prodi set Jabatan = '$nm', Pejabat = '$NamaPejabat' where FakultasID='$Fakultas'";
    $rtip = _query($tip);
  }
  
  $r = _query($s);
  return DftrPejabat();
}
function PejabatCopy() {
  global $mnux, $tok;
  $PeriodeJabatan = $_SESSION['jbtnperiod'];
  $PRDJB = GetaField('periodejabatan', 'PeriodeJabatanID', $PeriodeJabatan, "concat(TahunJabatan, ' - ', Nama)");
  // Ambil Daftar
  $s = "select pj.PeriodeJabatanID, pj.TahunJabatan, pj.Nama
    from periodejabatan pj
    where pj.KodeID='$_SESSION[KodeID]'
      and pj.PeriodeJabatanID<>$PeriodeJabatan
      and pj.NA='N'
    order by pj.PeriodeJabatanID";
  $r = _query($s);
  $opt = "<option value=''> </option>";
  while ($w = _fetch_array($r)) {
    $opt .= "<option value='$w[PeriodeJabatanID]'>$w[TahunJabatan] - $w[Nama]</option>";
  }

  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='data'>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub1' value='JabatanCopySav'>
  <input type=hidden name='PeriodeJabatan' value='$PeriodeJabatan'>
  <tr><td class=ul colspan=2>Anda akan menyalin master data Pejabat:</td></tr>
  <tr><td class=inp>Dari Master Periode :</td><td class=ul><select name='CopyID'>$opt</select></td></tr>
  <tr><td class=inp>Ke Master Periode :</td><td class=ul><b>$PRDJB</b></td></tr>
  <tr><td class=ul colspan=2>Proses penyalinan ini akan melakukan:
  <ol>
    <li>Menghapus semua data pejabat pada periode ini.</li>
    <li>Menyalin semua data pejabat dari periode yang akan disalin.</li>
  </ol>
  </td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Copy' value='Delete & Copy'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok'\"></td></tr>
  </form>
  </table></p>";
  return $a;
}
function JabatanCopySav() {
  $PeriodeJabatan = $_REQUEST['PeriodeJabatan'];
  $CopyID = $_REQUEST['CopyID'];

  $s = "delete from pejabat where PeriodeJabatanID='$Periodejabatan' ";
  $r = _query($s);

  $s1 = "select * from pejabat where PeriodeJabatanID='$CopyID' ";
  $r1 = _query($s1);
  while ($w1 = _fetch_array($r1)) {
    $s2 = "insert into pejabat (PeriodeJabatanID, JenisJabatanID,
      Nama, FakultasID, ProdiID, KodeID, LoginBuat, TglBuat)
      values ('$PeriodeJabatan', '$w1[JenisJabatanID]',
      '$w1[Nama]', '$w1[FakultasID]', '$w1[ProdiID]', '$w1[KodeID]', '$_SESSION[_Login]', now())";
    $r2 = _query($s2);
  }
  return DftrPejabat();
}
?>
