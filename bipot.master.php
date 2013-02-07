<?php
// Author: Emanuel Setio Dewo
// 11 Feb 2006

function DftrBipotMaster() {
  $ka = (empty($_REQUEST['sub1']))? DftrBipotIsi() : $_REQUEST['sub1']();
  $ki = AmbilDaftarBipotMaster();
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=0 width=100%>
  <td valign=top width=300 class=kolkir>$ki</td>
  <td valign=top class=kolkan>$ka</td>
  </table></p>";
}
function AmbilDaftarBipotMaster() {
  $filter = AmbilFilterBipotMaster();
  $daftar = '';
  if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']))
    $daftar = DftrBipot();
  
  return $filter.$daftar;
}
function AmbilFilterBipotMaster() {
  global $arrID, $mnux, $tok;
  $optprid = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], '', 'ProgramID');
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], '', 'ProdiID');
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='bipot' value='0'>
  <tr><td colspan=2 class=ul><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Program</td><td class=ul><select name='prid'>$optprid</select></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi'>$optprodi</select></td></tr>
  <tr><td colspan=2><input type=submit name='Jalankan' value='Jalankan'></td></tr>
  </form></table></p>";
  return $a;
}
function DftrBipot() {
  global $mnux, $tok, $arrID;
  $s = "select *
    from bipot
    where KodeID='$_SESSION[KodeID]' and ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]'
    order by Tahun desc";
  $r = _query($s);
  
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td colspan=5 class=ul><a href='?mnux=$mnux&tok=$tok&sub1=BipotMasterEdt&md=1'>Tambah Master Biaya & Potongan</a></td></tr>
  <tr><th></th>
    <th class=ttl>Tahun</th><th class=ttl>Nama Master</th>
    <th class=ttl title='Default'>Def</th>
    <th class=ttl title='Tidak aktif'>NA</th>
    <th></th>
  </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $d = ($w['Def'] == 'Y')? 'class=ul' : 'class=nac';
    if ($w['BIPOTID'] == $_SESSION['bipotid']) {
      $_ki = "<img src='img/kanan.gif'>";
      $_ka = "<img src='img/kiri.gif'>";
    }
    else {
      $_ki = '';
      $_ka = '';
    }
    
    $a .= "<tr>
      <td $c>$_ki</td>
      <td $c><a href='?mnux=$mnux&tok=$tok&sub1=BipotMasterEdt&md=0&bipotid=$w[BIPOTID]'  title='Edit Master'><img src='img/edit.png'>
      $w[Tahun]</a></td>
      <td $c><a href='?mnux=$mnux&tok=$tok&sub=&bipotid=$w[BIPOTID]' title='Lihat Detail'>
        $w[Nama]</a></td>
      <td $d align=center><img src='img/$w[Def].gif'></td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      <td $c>$_ka</td>
      </tr>";
  }
  return "$a</table></p>";
}
function BipotMasterEdt() {
  global $arrID, $mnux, $tok;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('bipot', "BIPOTID", $_REQUEST['bipotid'], '*');
    $jdl = "Edit Master Biaya & Potongan";
  }
  else {
    $w = array();
    $w['BIPOTID'] = 0;
    $w['Tahun'] = '';
    $w['Nama'] = '';
    $w['Catatan'] = '';
    $w['Def'] = 'N';
    $w['NA'] = 'N';
    $w['SP'] = 'N';
    $jdl = "Tambah Master Biaya & Potongan";
  }
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $Def = ($w['Def'] == 'Y')? 'checked' : '';
  $SP = ($w['SP'] == 'Y') ? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  CheckFormScript("Tahun,Nama");
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub1' value='BipotMasterSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='bipotid' value='$w[BIPOTID]'>
  
  <tr><th class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Kode Tahun</td><td class=ul><input type=text name='Tahun' value='$w[Tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Nama Master</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Catatan</td><td class=ul><textarea name='Catatan' cols=30 rows=3>$w[Catatan]</textarea></td></tr>
  <tr><td class=inp1>Default?</td><td class=ul><input type=checkbox name='Def' value='Y' $Def></td></tr>
  <tr><td class=inp1>Semester Pendek?</td><td class=ul><input type=checkbox name='SP' value='Y' $SP></td></tr>
  <tr><td class=inp1>Tidak Aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $NA></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok&$snm=$sid'\"></td></tr>
  </form></table></p>";
  return $a;
}
function BipotMasterSav() {
  $md = $_REQUEST['md']+0;
  $Tahun = sqling($_REQUEST['Tahun']);
  $Nama = sqling($_REQUEST['Nama']);
  $Catatan = sqling($_REQUEST['Catatan']);
  $Def = (empty($_REQUEST['Def']))? 'N' : $_REQUEST['Def'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $SP = (empty($_REQUEST['SP']))? 'N' : $_REQUEST['SP'];
  // Simpan
  if ($md == 0) {
    $BIPOTID = $_REQUEST['bipotid'];
    $s = "update bipot set Nama='$Nama', Tahun='$Tahun', Catatan='$Catatan', 
      Def='$Def', NA='$NA', SP='$SP',
      LoginEdit='$_SESSION[_Login]', TglEdit=now()
      where BIPOTID='$BIPOTID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into bipot (Tahun, Nama, KodeID, ProgramID, ProdiID, Catatan, 
      Def, NA, SP,
      TglBuat, LoginBuat)
      values('$Tahun', '$Nama', '$_SESSION[KodeID]', '$_SESSION[prid]',
      '$_SESSION[prodi]', '$Catatan', 
      '$Def', '$NA', '$SP',
      now(), '$_SESSION[_Login]')";
    $r = _query($s);
    // Ambil Last_Insert_ID
    $s_last = "select LAST_INSERT_ID() as ID";
    $r_last = _query($s_last);
    $w_last = _fetch_array($r_last);
    $BIPOTID = $w_last['ID'];
  }
  
  // Apakah diset menjadi default?
  if ($Def == 'Y') {
    $sd = "update bipot set Def='N' 
      where ProgramID='$_SESSION[prid]' and ProdiID='$_SESSION[prodi]'
      and BIPOTID<>$BIPOTID";
    //echo $sd;
    $rd = _query($sd);
  }
  return DftrBipotIsi();
}
function DftrBipotIsi() {
  global $mnux, $tok;
  if (!empty($_SESSION['prid']) && !empty($_SESSION['prodi']))
    $a = DftrBipotIsi1();
  else $a = '';
  return $a;
}
function HdrBipotIsi($JDL='', $TrxID) {
  global $mnux, $tok;
  if ($_SESSION['_LevelID'] == 1) {
    $del = "<th class=ttl>Del</th>";
  }
  return "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=10><b>$JDL</b></td></tr>
    <tr><th class=ttl>#</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Saat<br />Penarikan</th>
    <th class=ttl title='Otomatis'>Oto?</th>
    <th class=ttl title='Menggunakan Script'>Scr</th>
    <th class=ttl>Berapa<br />Kali Sesi?</th>
    <th class=ttl>Status<br />Awal</th>
    <th class=ttl>Status<br />Mhsw</th>
    <th class=ttl>Grade<br />Nilai USM</th>
    <th class=ttl>NA</th>
    </tr>";
}
function DftrBipotIsi1() {
  global $mnux, $tok;
  $arrbenar = GetFields('bipot', 'BIPOTID', $_SESSION['bipotid'], "ProgramID, ProdiID");
  if (($arrbenar['ProgramID'] == $_SESSION['prid']) and ($arrbenar['ProdiID'] == $_SESSION['prodi'])) {
    $s = "select b2.*, bn.Nama, format(b2.Jumlah, 0) as JML,
      t.Nama as NMTRX, s.Nama as SAAT
      from bipot2 b2
      left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
      left outer join saat s on b2.SaatID=s.SaatID
      left outer join trx t on b2.TrxID=t.TrxID
      where b2.BIPOTID='$_SESSION[bipotid]' and KodeID='$_SESSION[KodeID]'
      order by b2.TrxID, b2.Prioritas, b2.GradeNilai";
    $r = _query($s);
    $ftr = "</table></p>";
    $TrxID = -100;
    $cnt = 0;
    $a = BuatMenuBipotIsi();
    while ($w = _fetch_array($r)) {
      
      // Buat header & footer
      if ($TrxID != $w['TrxID']) {
        $TrxID = $w['TrxID'];
        if ($cnt > 0) $a .= $ftr;
        $a .= HdrBipotIsi($w['NMTRX'], $TrxID);
      }
      // menggunakan script?
      $scr = ($w['GunakanScript']=='Y')? "<img src='img/gear.gif' width=20 title='$w[NamaScript]'>" : "&nbsp;";
      // Tampilkan data
      $cnt++;
      $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
      $a .= "<tr>
      <td $c nowrap><a href='?mnux=$mnux&tok=$tok&sub1=BipotIsiEdt&md=0&bipot2=$w[BIPOT2ID]&trxid=$w[TrxID]&trxnama=$w[NMTRX]'><img src='img/edit.png'>
      $w[Prioritas] </a></td>
      <td $c>$w[Nama]</td>
      <td $c align=right>$w[JML]</td>
      <td $c>$w[SAAT]</td>
      <td $c align=center><img src='img/$w[Otomatis].gif'></td>
      <td $c align=center>$scr</td>
      <td $c align=right>$w[KaliSesi]</td>
      <td $c>$w[StatusAwalID]</td>
      <td $c>$w[StatusMhswID]</td>
      <td $c><img src='img/$w[GunakanGradeNilai].gif'> $w[GradeNilai]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
    }
    $a .= "</table></p>";
  }
  else $a = '';
  return $a;
}

function BuatMenuBipotIsi() {
  global $mnux, $tok;
  $s = "select * from trx order by TrxID";
  $r = _query($s);
  $a = "<p>";
  $arr = array();
  while ($w = _fetch_array($r)) {
    $arr[] = "<a href='?mnux=$mnux&tok=$tok&sub1=BipotIsiEdt&md=1&trxid=$w[TrxID]&trxnama=$w[Nama]'>Tambah $w[Nama]</a>";
  }
  $a .= implode(' | ', $arr);
  $a .= " | <a href='?mnux=$mnux&tok=$tok&sub1=BipotCopy'>Salin Dari Tahun Lain</a> |
    <a href='bipot.cetak.php?gos=DetailBIPOT&bipotid=$_SESSION[bipotid]' target=_blank>Cetak</a>";
  return $a."</p>";
}
function BipotIsiEdt() {
  global $mnux, $tok;
  $fakultas = substr($_SESSION['prodi'], 0, 1);

  $md = $_REQUEST['md'] +0;
  // Jika Edit
  if ($md == 0) {
    $bipot2 = $_REQUEST['bipot2'];
    $w = GetFields('bipot2', "BIPOT2ID", $bipot2, '*');
    $jdl = "Edit $_REQUEST[trxnama]";
  }
  // Jika tambah
  else {
    $w = array();
    $w['BIPOTID'] = $_SESSION['bipotid'];
    $w['BIPOTNamaID'] = 0;
    $w['Prioritas'] = 0;
    $w['TrxID'] = $_REQUEST['trxid'];
    $w['Jumlah'] = 0;
    $w['KaliSesi'] = 0;
    $w['Otomatis'] = 'Y';
    $w['SaatID'] = 1;
    $w['StatusMhswID'] = '.A.';
    $w['StatusPotonganID'] = '';
    $w['StatusAwalID'] = '.B.';
    $w['GunakanGradeNilai'] = 'N';
    $w['GradeNilai'] = '';
    $w['GunakanScript'] = 'N';
    $w['NamaScript'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah $_REQUEST[trxnama]";
  }
  // setup
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $OTO = ($w['Otomatis'] == 'Y')? 'checked' : '';
  $GunakanGradeNilai = ($w['GunakanGradeNilai'] == 'Y')? 'checked' : '';
  $grdnilai = GetCheckboxes('pmbgrade', "GradeNilai", "GradeNilai", 'GradeNilai', $w['GradeNilai'], '.');
  $GunakanScript = ($w['GunakanScript'] == 'Y')? 'checked' : '';
  $optnama = GetOption2('bipotnama', 'Nama', 'Nama', $w['BIPOTNamaID'], "TrxID=$_REQUEST[trxid]", 'BIPOTNamaID');
  $optsaat = GetOption2('saat', "concat(SaatID, '. ', Nama)", 'SaatID', $w['SaatID'], '', 'SaatID');
  //GetCheckboxes($table, $key, $Fields, $Label, $Nilai='', $Separator=',') {
  $stamhsw = GetCheckboxes('statusmhsw', "StatusMhswID",
    "concat(StatusMhswID, ' - ', Nama) as STA", 'STA', $w['StatusMhswID'], '.');
  $staawal = GetCheckboxes('statusawal', 'StatusAwalID',
    "concat(StatusAwalID, ' - ', Nama) as STA", 'STA', $w['StatusAwalID'], '.');

  // Tuliskan
  CheckFormScript("BIPOTNamaID,Jumlah,SaatID");
  return "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub1' value='BipotIsiSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='trxid' value='$_REQUEST[trxid]'>
  <input type=hidden name='bipot2' value='$w[BIPOT2ID]'>

  <tr><td class=ul colspan=2><b>$jdl</td></tr>
  <tr><td class=inp1>Prioritas Pembayaran</td>
    <td class=ul><input type=text name='Prioritas' value='$w[Prioritas]' size=4 maxlength=3></td></tr>
  <tr><td class=inp1>Nama $_REQUEST[trxnama]</td><td class=ul><select name='BIPOTNamaID'>$optnama</select></td></tr>
  <tr><td class=inp1>Jumlah Rp.</td><td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20 maxlength=15></td></tr>
  <tr><td class=inp1>Berapa kali sesi?</td><td class=ul><input type=text name='KaliSesi' value='$w[KaliSesi]' size=5 maxlength=5> Isikan 0 jika tidak ditentukan.</td></tr>
  <tr><td class=inp1>Dikenakan otomatis?</td><td class=ul><input type=checkbox name='Otomatis' value='Y' $OTO></td></tr>
  <tr><td class=inp1>Dikenakan saat</td><td class=ul><select name='SaatID'>$optsaat</select></td></tr>
  <tr><td class=inp1>Status Awal</td><td class=ul>$staawal</td></tr>
  <tr><td class=inp1>Status Mahasiswa</td><td class=ul>$stamhsw</td></tr>
  <tr><td class=inp1 rowspan=2>Grade Nilai USM</td>
    <td class=ul><input type=checkbox name='GunakanGradeNilai' value='Y' $GunakanGradeNilai> Cek Grade Nilai USM?</td></tr>
    <tr><td class=ul>$grdnilai</td></tr>
  <tr><td class=inp1>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $NA></td></tr>

  <tr><td class=inp1>Gunakan Script External?</td>
    <td class=ul><input type=checkbox name='GunakanScript' value='Y' $GunakanScript><br />
    Nama Script: <input type=text name='NamaScript' value='$w[NamaScript]' size=30 maxlength=200></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok'\"></td></tr>
  </form></table></p>";
}
function BipotIsiSav() {
  $md = $_REQUEST['md']+0;
  $trxid = $_REQUEST['trxid'];
  $Prioritas = $_REQUEST['Prioritas']+0;
  $BIPOTNamaID = $_REQUEST['BIPOTNamaID'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $KaliSesi = $_REQUEST['KaliSesi']+0;
  $Otomatis = (empty($_REQUEST['Otomatis']))? 'N' : $_REQUEST['Otomatis'];
  $SaatID = $_REQUEST['SaatID'];
  // Ambil Status Awal
  $_staawal = array();
  $_staawal = $_REQUEST['StatusAwalID'];
  $StatusAwalID = (empty($_staawal))? '' : '.'. implode('.', $_staawal) .'.';
  // Ambil Status Mhsw
  $_stamhsw = array();
  $_stamhsw = $_REQUEST['StatusMhswID'];
  $StatusMhswID = (empty($_stamhsw))? '' : '.'. implode('.', $_stamhsw) .'.';
  
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $GunakanScript = (empty($_REQUEST['GunakanScript']))? 'N' : $_REQUEST['GunakanScript'];
  $NamaScript = ($GunakanScript == 'Y')? sqling($_REQUEST['NamaScript']) : '';
  $GunakanGradeNilai = (empty($_REQUEST['GunakanGradeNilai']))? 'N' : $_REQUEST['GunakanGradeNilai'];
  // Grade Nilai
  if ($GunakanGradeNilai == 'Y') {
    $_grdnilai = array();
    $_grdnilai = $_REQUEST['GradeNilai'];
    $GradeNilai = (empty($_grdnilai))? '' : '.'. implode('.', $_grdnilai) .'.';
  }
  else $GradeNilai = '';
  
  // Simpan
  //$adakah = GetaField('bipot2', 'Bipot')
  if ($md == 0) {
    $s = "update bipot2 set Prioritas='$Prioritas',
      BIPOTNamaID='$BIPOTNamaID', Jumlah='$Jumlah',
      KaliSesi='$KaliSesi', Otomatis='$Otomatis', SaatID='$SaatID',
      StatusAwalID='$StatusAwalID', StatusMhswID='$StatusMhswID',
      GunakanScript='$GunakanScript', NamaScript='$NamaScript',
      GunakanGradeNilai='$GunakanGradeNilai', GradeNilai='$GradeNilai',
      NA='$NA'
      where BIPOT2ID='$_REQUEST[bipot2]' ";
  }
  else {
    $s = "insert into bipot2
      (BIPOTID, Prioritas, BIPOTNamaID, Jumlah, KaliSesi, Otomatis, SaatID,
      StatusAwalID, StatusMhswID, NA, TrxID,
      GunakanGradeNilai, GradeNilai,
      GunakanScript, NamaScript)
      values('$_SESSION[bipotid]', '$Prioritas',
      '$BIPOTNamaID', '$Jumlah', '$KaliSesi', '$Otomatis', '$SaatID',
      '$StatusAwalID', '$StatusMhswID', '$NA', '$trxid',
      '$GunakanGradeNilai', '$GradeNilai',
      '$GunakanScript', '$NamaScript')";
  }
  //echo $s;
  $r = _query($s);
  return DftrBipotIsi();
}
function BipotCopy() {
  global $mnux, $tok;
  $bipotid = $_SESSION['bipotid'];
  $bipot = GetaField('bipot', 'BIPOTID', $bipotid, "concat(Tahun, ' - ', Nama)");
  // Ambil Daftar
  $s = "select b.BIPOTID, b.Tahun, b.Nama
    from bipot b
    where b.KodeID='$_SESSION[KodeID]'
      and b.ProgramID='$_SESSION[prid]'
      and b.ProdiID='$_SESSION[prodi]'
      and b.BIPOTID<>$bipotid
      and b.NA='N'
    order by b.Nama";
  $r = _query($s);
  $opt = "<option value=''> </option>";
  while ($w = _fetch_array($r)) {
    $opt .= "<option value='$w[BIPOTID]'>$w[Tahun] - $w[Nama]</option>";
  }

  $a = "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='data'>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='tok' value='$tok'>
  <input type=hidden name='sub1' value='BipotCopySav'>
  <input type=hidden name='bipotid' value='$bipotid'>
  <tr><td class=ul colspan=2>Anda akan menyalin dari Master BIPOT:</td></tr>
  <tr><td class=inp>Dari Master :</td><td class=ul><select name='CopyID'>$opt</select></td></tr>
  <tr><td class=inp>Ke Master :</td><td class=ul><b>$bipot</b></td></tr>
  <tr><td class=ul colspan=2>Proses penyalinan ini akan melakukan:
  <ol>
    <li>Menghapus semua biaya & potongan dari master biaya & potongan ini.</li>
    <li>Menyalin semua biaya & potongan dari master biaya & potongan lain.</li>
  </ol>
  </td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Copy' value='Delete & Copy'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&tok=$tok'\"></td></tr>
  </form>
  </table></p>";
  return $a;
}
function BipotCopySav() {
  $bipotid = $_REQUEST['bipotid'];
  $CopyID = $_REQUEST['CopyID'];
  // Kosongkan bipot2 dari tujuan
  $s = "delete from bipot2 where BIPOTID='$bipotid' ";
  $r = _query($s);
  // Ambil data dari bipot2
  $s1 = "select * from bipot2 where BIPOTID='$CopyID' ";
  $r1 = _query($s1);
  while ($w1 = _fetch_array($r1)) {
    $s2 = "insert into bipot2(BIPOTID, BIPOTNamaID,
      TrxID, Prioritas, Jumlah, KaliSesi,
      Otomatis, SaatID, 
      StatusMhswID, StatusPotonganID, StatusAwalID,
      GunakanGradeNilai, GradeNilai,
      GunakanScript, NamaScript,
      LoginBuat, TglBuat)
      values ('$bipotid', '$w1[BIPOTNamaID]',
      '$w1[TrxID]', '$w1[Prioritas]', '$w1[Jumlah]', '$w1[KaliSesi]',
      '$w1[Otomatis]', '$w1[SaatID]',
      '$w1[StatusMhswID]', '$w1[StatusPotonganID]', '$w1[StatusAwalID]',
      '$w1[GunakanGradeNilai]', '$w1[GradeNilai]',
      '$w1[GunakanScript]', '$w1[NamaScript]',
      '$_SESSION[_Login]', now())";
    $r2 = _query($s2);
  }
  return DftrBipotIsi();
}
?>
