<?php
// Author: Emanuel Setio Dewo
// 02/02/2006

include_once "dosen.hdr.php";
include_once "class/dwolister.class.php";  

// *** Functions ***
function CariDosen() {
  TampilkanFilterDosen('dosen', 1);
  FormLast();
  echo "<p><span id=lastsp class=inp1><a href='javascript:sib()'>Dosen ID Terakhir : </a></span><span class=inp1 id=inp>$_SESSION[ll]&nbsp;</span></p>";
  echo "<a href=?mnux=dosen.cetak>Cetak</a>";
  DaftarDosen('dosen', "gos=DsnEdt&md=0&dsnid==Login=", "NIDN,Nama,Gelar,Homebase,Telephone,Alamat");
}

function GetLastIDDosen($last){
	$s = "select max(d.Login) as Login from dosen d
          left outer join statusdosen sd on sd.StatusDosenID=d.StatusDosenID
        where sd.StatusDosenID = '$last'";
	$r = _query($s);
	$w = _fetch_array($r);
	$_SESSION['ll'] = $w['Login'];
	return $w['Login'];
}

function FormLast(){
  $optstts = GetOption2('statusdosen', "concat(StatusDosenID, ' - ', Nama)", "StatusDosenID", $_SESSION['last'], '', "StatusDosenID");
  $LAST = GetLastIDDosen($_SESSION['last']);
  echo "<script type=\"text/javascript\">
        function sib(){
          $('#last').fadeIn('slow');
          $('#inp').hide();
          $('#lastsp').hide();
        }
        </script>";
  echo "<div id='last' style='display:none'>
        <form action='?' method='POST'>
        <input type=hidden name='mnux' value='dosen'>
        <table class=box cellpadding=4 cellspacing=1>
        <tr><th class=ttl>Status Dosen</th><th class=ttl>DosenID Terakhir</th></tr>
        <tr><td class=ul><select name=last onchange='this.form.submit()'>$optstts</select></td><td class=ul align=center>$LAST</td></tr>
        </table></form></div>";
}

function DsnAdd($mnux='dosen', $gos='DsnAddSav', $sub='') {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '*');
    $jdl = "Edit Data Pribadi";
    $_strdsnid = "<input type=hidden name='dsnid' value='$w[Login]'><b>$w[Login]</b>";
  }
  else {
    $w = array();
    $w['Login'] = '';
    $w['NIDN'] = '';
    $w['Nama'] = '';
    $w['Gelar'] = '';
    $w['Telephone'] = '';
    $w['Handphone'] = '';
    $w['Email'] = '';
    $w['ProdiID'] = '';
    $w['TempatLahir'] = '';
    $w['TanggalLahir'] = date('Y-m-d');
    $w['KelaminID'] = 'P';
    $w['AgamaID'] = 'K';
    $w['NA'] = 'N';
    $Homebase = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', '', '', 'ProdiID');
    $hm = "<tr><td class=inp1>Prodi Homebase</td><td class=ul><select name='Homebase'>$Homebase</select></td></tr>";
    $jdl = "Tambah Dosen";
    $_strdsnid = "<input type=text name='dsnid' size=20 maxlength=20>";
  }
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $optprodi = GetCheckboxes("prodi", "ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", "NM", $w['ProdiID'], '.');
  $TglLahir = GetDateOption($w['TanggalLahir'], 'TglLahir');
  $optagm = GetOption2('agama', "concat(Agama, ' - ', Nama)", 'Agama', $w['AgamaID'], '', 'Agama');
  $radkel = GetRadio("select Nama, Kelamin from kelamin order by Nama", "Kelamin", "Nama", "Kelamin", $w['KelaminID'], ", ");    
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <input type=hidden name='sub' value='$sub'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>Login/NIP</td><td class=ul>$_strdsnid</td></tr>
  <tr><td class=inp1>NIDN</td><td class=ul><input type=text name='NIDN' value='$w[NIDN]' size=20 maxlength=50>
    Nomer Induk Dosen Nasional</td></tr>
  <tr><td class=inp1>Nama Dosen</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Gelar</td><td class=ul><input type=text name='Gelar' value='$w[Gelar]' size=$0 maxlength=100></td></tr>
  <tr><td class=inp1>Tempat/Tanggal Lahir</td>
    <td class=ul><input type=text name='TempatLahir' value='$w[TempatLahir]' size=30 maxlength=50>
    Tanggal: $TglLahir</td></tr>
  <tr><td class=inp1>Jenis Kelamin</td><td class=ul>$radkel</td></tr>
  <tr><td class=inp1>Agama</td><td class=ul><select name='AgamaID'>$optagm</select></td></tr>
  <tr><td class=inp1># Telepon</td><td class=ul><input type=text name='Telephone' value='$w[Telephone]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1># Ponsel</td><td class=ul><input type=text name='Handphone' value='$w[Handphone]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>E-mail</td><td class=ul><input type=text name='Email' value='$w[Email]' size=40 maxlength=50></td></tr>
  $hm
  <tr><td class=inp1>Program Studi</td><td class=ul>$optprodi</td></tr>
  <tr><td class=inp1>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=dosen&gos=&$snm=$sid'\"></td></tr>
  </form></table></p>";
}
function SetPasswordDosen($tgl) {
  $tmp = explode('-', $tgl);
  
  $tanggal = '';
  $bulan   = '';
  $tahun   = '';
  
  $tanggal = $tmp[2];
  $bulan   = $tmp[1];
  $tahun   = $tmp[0];
  
  $thn2digit = substr($tahun, -2);
  
  $pass = "$tanggal" . "$bulan" . "$thn2digit";
  
  return $pass;
}

function DsnAddSav($gos='DsnEdt') {
  $md = $_REQUEST['md']+0;
  $Login = sqling($_REQUEST['dsnid']);
  $Homebase = $_REQUEST['Homebase'];
  $NIDN = sqling($_REQUEST['NIDN']);
  $Nama = sqling($_REQUEST['Nama']);
  $TempatLahir = sqling($_REQUEST['TempatLahir']);
  $TanggalLahir = "$_REQUEST[TglLahir_y]-$_REQUEST[TglLahir_m]-$_REQUEST[TglLahir_d]";
  $Gelar = sqling($_REQUEST['Gelar']);
  $Telephone = sqling($_REQUEST['Telephone']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Email']);
  $KelaminID = $_REQUEST['Kelamin'];
  $AgamaID = $_REQUEST['AgamaID'];
  $ProdiID = $_REQUEST['ProdiID'];
  $_ProdiID = (empty($ProdiID))? '' : '.'.implode('.', $ProdiID).'.';
  
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update dosen set NIDN='$NIDN', Nama='$Nama',
      TempatLahir='$TempatLahir', TanggalLahir='$TanggalLahir', 
      Gelar='$Gelar', Telephone='$Telephone', Handphone='$Handphone',
      KelaminID='$KelaminID', AgamaID='$AgamaID',
      Email='$Email', ProdiID='$_ProdiID', NA='$NA'
      where Login='$Login' ";
    $r = _query($s);
    $gos();
  }
  else {
    $ada = GetFields('dosen', "Login", $Login, '*');
    if (empty($ada)) {
      $pass = setPasswordDosen($TanggalLahir);
      $s = "insert into dosen (Login, NIDN, Nama, TempatLahir, TanggalLahir,
        AgamaID, KelaminID, Password, Homebase,
        KodeID, Gelar, Telephone, Handphone,
        Email, ProdiID, NA)
        values ('$Login', '$NIDN', '$Nama', '$TempatLahir', '$TanggalLahir',
        '$AgamaID', '$KelaminID', PASSWORD('$pass'), '$Homebase',
        '$_SESSION[KodeID]', '$Gelar', '$Telephone', '$Handphone',
        '$Email', '$_ProdiID', '$NA')";
      $r = _query($s);
      $_SESSION['dsnid'] = $_REQUEST['dsnid'];
      $_SESSION['dsnsub'] = "DsnEdtPribadi";
      $_SESSION['dsncr'] = $_REQUEST['dsnid'];
      $_SESSION['dsnkeycr'] = "Login";
      echo "<script>window.location = '?mnux=dosen';</script>";
    }
    else echo ErrorMsg("Gagal", "Data dosen tidak dapat disimpan karena NIP: <b>$Login</b> sudah dipakai oleh:
      <b>$ada[Nama]</b>.<br>
      Gunakan NIP lain.<hr size=1 color=silver>
      Pilihan: <a href='?mnux=dosen&gos=DsnAdd&md=1'>Tambah Dosen</a> |
      <a href='?mnux=dosen&gos='>Kembali ke Daftar Dosen</a>");
  }
}
// *** Edit Dosen ***
function TampilkanHeaderDosenEdit($w) {
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>NIP/Login:</td><td class=ul><b>$w[Login]</b></td></tr>
  <tr><td class=inp1>Nama:</td><td class=ul><b>$w[Nama]</b>, $w[Gelar]</td></tr>
  <tr><td class=ul>Pilihan:</td><td class=ul><a href='?mnux=dosen&gos=&sub='>Kembali ke Daftar Dosen</a></td></tr>
  </table></p>";
}
function TampilkanMenuEditDosen($w) {
  $arrMenuDosen = array('Data Pribadi->DsnEdt->DsnEdtPribadi',
    'Alamat->DsnEdt->DsnAlmt',
    'Akademik->DsnEdt->DsnEdtAkademik',
    'Jabatan->DsnEdt->DsnEdtJabatan',
    'Pengajaran->DsnEdt->DsnEdtPengajaran',
    'Penelitian->DsnEdt->TampilTelitiDsn',
    //'Pengabdian->DsnEdt->DsnEdtPengabdian',
    'Pendidikan->DsnEdt->TampilPendidikan',
    'Pekerjaan->DsnEdt->TampilKerjaDsn');
  
  echo "<p><table class=menu cellspacing=1 cellpadding=4><tr>";
  $_SESSION['dsnsub'] = (empty($_SESSION['dsnsub']))? 'DsnEdtPribadi' : $_SESSION['dsnsub'];
  for ($i = 0; $i < sizeof($arrMenuDosen); $i++) {
    $mn = explode('->', $arrMenuDosen[$i]);
    $c = ($mn[2] == $_SESSION['dsnsub'])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c><a href='?mnux=dosen&gos=$mn[1]&dsnid=$w[Login]&dsnsub=$mn[2]'>$mn[0]</a></td>";
  }
  echo "</tr></table></p>";
}
function DsnEdt() {
  $w = GetFields('dosen', "Login", $_SESSION['dsnid'], '*');
  TampilkanHeaderDosenEdit($w);
  TampilkanMenuEditDosen($w);
  if (!empty($_SESSION['dsnsub'])) $_SESSION['dsnsub']();
}
function DsnEdtPribadi() {
  $_REQUEST['md']+0;
  DsnAdd('dosen', 'DsnEdtPribadiSav', 'DsnEdtPribadi');
}
function DsnEdtPribadiSav() {
  DsnAddSav('DsnEdt');
}

function DsnEdtAkademik() {
  if (!empty($_REQUEST['dsnsub1'])) {
    $_REQUEST['dsnsub1']();
  }
  $w = GetFields('dosen', 'Login', $_SESSION['dsnid'], '*');
  $TglBekerja = GetDateOption($w['TglBekerja'], 'TglBekerja');
  $StatusDosen = GetOption2('statusdosen', "concat(StatusDosenID, ' - ', Nama)", 'StatusDosenID', $w['StatusDosenID'], '', 'StatusDosenID');
  $StatusKerja = GetOption2('statuskerja', "concat(StatusKerjaID, ' - ', Nama)", 'StatusKerjaID', $w['StatusKerjaID'], '', 'StatusKerjaID');
  $Homebase = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Homebase'], '', 'ProdiID');
  $_ProdiID = GetRadioProdi($w['ProdiID'], 'ProdiID');
  $Jenjang = GetOption2('jenjang', "concat(JenjangID, ' - ', Nama)", 'JenjangID', $w['JenjangID'], '', 'JenjangID');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dosen'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='DsnEdtAkademik'>
  <input type=hidden name='dsnsub1' value='DsnEdtAkademikSav'>
  <input type=hidden name='dsnid' value='$w[Login]'>
  
  <tr><td class=ul colspan=2><b>Profile</b></td></tr>
  <tr><td class=inp1>Mulai Bekerja</td><td class=ul>$TglBekerja</td></tr>
  <tr><td class=inp1>Status Dosen</td><td class=ul><select name='StatusDosenID'>$StatusDosen</select></td></tr>
  <tr><td class=inp1>Status Kerja</td><td class=ul><select name='StatusKerjaID'>$StatusKerja</select></td></tr>
  <tr><td class=inp1>Prodi Homebase</td><td class=ul><select name='Homebase'>$Homebase</select></td></tr>
  <tr><td class=inp1>Mengajar di Prodi</td><td class=ul>$_ProdiID</td></tr>
  <tr><td class=inp1>Kode Instansi Induk</td><td class=ul><input type=text name='InstitusiInduk' value='$w[InstitusiInduk]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Lulus Perg. Tinggi</td><td class=ul><input type=text name='LulusanPT' value='$w[LulusanPT]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Jenjang Pendidikan Tertinggi</td><td class=ul><select name='JenjangID'>$Jenjang</select></td></tr>
  <tr><td class=inp1>Keilmuan</td><td class=ul><input type=text name='Keilmuan' value='$w[Keilmuan]' size=40 maxlength=100></td></tr>  
  
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table>";
}
function GetOptGol($prodi='', $gol='', $kat='') {
  $a = '<option>-</option>';
  if (!empty($prodi)) {
    $s = "select GolonganID, KategoriID, Pangkat
      from golongan 
    where ProdiID='$prodi'
      order by GolonganID, KategoriID";
    $r = _query($s);
    while ($w = _fetch_array($r)) {
      $sel = (($w['GolonganID'] == $gol) && ($w['KategoriID'] == $kat))? 'selected' : '';
      $a .= "<option value='$w[GolonganID]~$w[KategoriID]' $sel>$w[GolonganID]-$w[KategoriID] : $w[Pangkat]</option>";
    }
  }
  return $a;
}
function DsnEdtJabatan() {
  if (!empty($_REQUEST['dsnsub1'])) {
    $_REQUEST['dsnsub1']();
  }
  $w = GetFields('dosen', 'Login', $_SESSION['dsnid'], '*');
  $Jabatan = GetOption2('jabatan', "concat(JabatanID, ' - ', Nama)", 'JabatanID', $w['JabatanID'], '', 'JabatanID');
  $JabatanDikti = GetOption2('jabatandikti', "concat(JabatanDiktiID, ' - ', Nama)", 'JabatanDiktiID', $w['JabatanDiktiID'], '', 'JabatanDiktiID');
  //$Golongan = GetOption2('golongan', "concat(GolonganID, ' - ', KategoriID, ' - ', Pangkat)", 'GolonganID, KategoriID', $w['GolonganID'], "ProdiID='$w[Homebase]'", "concat(GolonganID,'~',KategoriID)");
  $optgol = GetOptGol($w['Homebase'], $w['GolonganID'], $w['KategoriID']);
  $optikt = GetOption2("ikatan", "concat(IkatanID, ' - ', Nama, ' (', format(Besar, 0), ')')", "IkatanID", $w['IkatanID'], '', 'IkatanID');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dosen'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='DsnEdtJabatan'>
  <input type=hidden name='dsnsub1' value='DsnEdtJabatanSav'>
  <input type=hidden name='dsnid' value='$w[Login]'>
    
  <tr><td class=ul colspan=2><b>Jabatan</b></td></tr>
  <tr><td class=inp1>Jabatan Akademik</td><td class=ul><select name='JabatanID'>$Jabatan</select></td></tr>
  <tr><td class=inp1>Jabatan Dikti</td><td class=ul><select name='JabatanDiktiID'>$JabatanDikti</select></td></tr>
  <tr><td class=inp1>Golongan</td><td class=ul><select name='GolonganID'>$optgol</select></td></tr>
  <tr><td class=inp1>Tunjangan Ikatan</td><td class=ul><select name='IkatanID'>$optikt</select></td></tr>
  
  <tr><td class=ul colspan=2><b>Bank</b></td></tr>
  <tr><td class=inp1>Nama Bank</td><td class=ul><input type=text name='NamaBank' value='$w[NamaBank]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Nama Akun</td><td class=ul><input type=text name='NamaAkun' value='$w[NamaAkun]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Nomer Akun</td><td class=ul><input type=text name='NomerAkun' value='$w[NomerAkun]' size=50 maxlength=50></td></tr>
  
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table>";
}
function DsnEdtAkademikSav() {
  $dsnid = $_REQUEST['dsnid'];
  $TglBekerja = "$_REQUEST[TglBekerja_y]-$_REQUEST[TglBekerja_m]-$_REQUEST[TglBekerja_d]";
  $StatusDosenID = $_REQUEST['StatusDosenID'];
  $StatusKerjaID = $_REQUEST['StatusKerjaID'];
  $Homebase = $_REQUEST['Homebase'];
  $_ProdiID = array();
  $_ProdiID = $_REQUEST['ProdiID'];
  $ProdiID = implode('.', $_ProdiID);
  $ProdiID = ".$ProdiID.";
  $LulusanPT = sqling($_REQUEST['LulusanPT']);
  $JenjangID = $_REQUEST['JenjangID'];
  $Keilmuan = sqling($_REQUEST['Keilmuan']);
  $InstitusiInduk = sqling($_REQUEST['InstitusiInduk']);
  
  $s = "update dosen
    set TglBekerja='$TglBekerja', StatusDosenID='$StatusDosenID', StatusKerjaID='$StatusKerjaID',
    Homebase='$Homebase', ProdiID='$ProdiID',
    LulusanPT='$LulusanPT', JenjangID='$JenjangID', Keilmuan='$Keilmuan', InstitusiInduk='$InstitusiInduk'
    where Login='$dsnid' ";
  $r = _query($s);
  //DsnEdtPribadi($w);
}
function DsnEdtJabatanSav() {
  $dsnid = $_REQUEST['dsnid'];
  $JabatanID = $_REQUEST['JabatanID'];
  $JabatanDiktiID = $_REQUEST['JabatanDiktiID'];
  $Gol = $_REQUEST['GolonganID'];
  if (!empty($Gol)) {
    $arrgol = explode('~', $Gol);
    $GolonganID = $arrgol[0];
    $KategoriID = $arrgol[1];
  }
  else {
    $GolonganID = '';
    $KategoriID = '';
  }
  $IkatanID = $_REQUEST['IkatanID'];
  $NamaBank = sqling($_REQUEST['NamaBank']);
  $NamaAkun = sqling($_REQUEST['NamaAkun']);
  $NomerAkun = sqling($_REQUEST['NomerAkun']);
  $s = "update dosen set JabatanID='$JabatanID', JabatanDiktiID='$JabatanDiktiID',
    GolonganID='$GolonganID', KategoriID='$KategoriID', IkatanID='$IkatanID',
    NamaBank='$NamaBank', NamaAkun='$NamaAkun', NomerAkun='$NomerAkun'
    where Login='$dsnid' ";
  $r = _query($s);
}
function DsnAlmt() {
  $w = GetFields('dosen', 'Login', $_SESSION['dsnid'], '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dosen'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='DsnAlmtSav'>
  <input type=hidden name='dsnid' value='$w[Login]'>
  <tr><td class=ul colspan=2><b>Alamat</b></td></tr>
  <tr><td class=inp>No KTP</td><td class=ul><input type=text name='KTP' value='$w[KTP]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>No Telepon</td><td class=ul><input type=text name='Telephone' value='$w[Telephone]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>No HP</td><td class=ul><input type=text name='Handphone' value='$w[Handphone]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>E-mail</td><td class=ul><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Alamat</td><td class=ul><textarea name='Alamat' cols=30 rows=3>$w[Alamat]</textarea></td></tr>
  <tr><td class=inp>Kota</td><td class=ul><input type=text name='Kota' value='$w[Kota]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Kode Pos</td><td class=ul><input type=text name='KodePos' value='$w[KodePos]' size=30 maxlength=30></td></td>
  <tr><td class=inp>Propinsi</td><td class=ul><input type=text name='Propinsi' value='$w[Propinsi]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Negara</td><td class=ul><input type=text name='Negara' value='$w[Negara]' size=30 maxlength=30></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'></td></tr>
  </form></table></p>";
}
function DsnAlmtSav() {
  $dsnid = $_REQUEST['dsnid'];
  $KTP = sqling($_REQUEST['KTP']);
  $Telephone = $_REQUEST['Telephone'];
  $Handphone = $_REQUEST['Handphone'];
  $Email = sqling($_REQUEST['Email']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  // Simpan
  $s = "update dosen set KTP='$KTP', Telephone='$Telephone',
    Handphone='$Handphone', Email='$Email', Alamat='$Alamat',
    Kota='$Kota', KodePos='$KodePos', Propinsi='$Propinsi', Negara='$Negara'
    where Login='$dsnid' ";
  $r = _query($s);
  DsnAlmt();
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

function DsnEdtPendidikan() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w_ = GetFields('dosenpendidikan', 'DosenPendidikanID', $_REQUEST['DosenPendidikanID'], '*');
	  $PT = GetFields('perguruantinggi','PerguruanTinggiID',$w_['PerguruanTinggiID'],'Nama,PerguruanTinggiID');
	  $jdl = "Update Pendidikan Dosen";   
  }
  else {
    $jdl = "Tambah Pendidikan Dosen";
    $w_ = array();
	}
  
  $w = GetFields('dosen','Login',$_SESSION['dsnid'],'*');
  $Tglijazah = GetDateOption($w_['TanggalIjazah'], 'Tglijazah');
  $Jenjang = GetOption2('jenjang', "concat(JenjangID, ' - ', Nama)", 'JenjangID', $w_['JenjangID'], '', 'JenjangID');
  //$Nomor = GetOption2('jenjang','JenjangID','JenjangID',$w_['Nomor'],'','JenjangID');
  $NoBenua = GetOption2('benua',"concat(KodeBenua,' - ', NamaBenua)",'KodeBenua',$w_['KodeBenua'],'','KodeBenua');
  $Negara = GetOption2('negara','NamaNegara','NamaNegara',$w_['NamaNegara'],'','NamaNegara');
    
  CariPTScript();
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='data'>
  <input type=hidden name='mnux' value='dosen'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='TampilPendidikan'>
  <input type=hidden name='dsnsub1' value='DsnEdtPendidikanSav'>
  <input type=hidden name='dsnid' value='$_SESSION[dsnid]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='DosenPendidikanID' value='$w_[DosenPendidikanID]'>
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Nomor Urut</td><td class=ul><input type=text name='Nomor' value='$w_[Nomor]' size=4 maxlength=3></td></tr>
  <tr><td class=inp1>Gelar</td><td class=ul><input type=text name='Gelar' value='$w_[Gelar]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Jenjang</td><td class=ul><select name='JenjangID'>$Jenjang</select></td></tr>
  <tr><td class=inp1>Tanggal Lulus Ijasah</td><td class=ul>$Tglijazah</td></tr>
  <tr><td class=inp1>Kode P.T</td><td class=ul><input type=text name='AsalPT' value='$PT[PerguruanTinggiID]' size=10 maxlength=10></td></tr></td></tr>
  <tr><td class=inp1>Kode Asal P.T</td><td class=ul><input type=text name='NamaPT' value='$PT[Nama]' size=50> <a href='javascript:caript(data)'>Cari</a></td></tr>
  <tr><td class=inp1>Negara</td><td class=ul><input type=text name='NamaNegara' value='$w_[NamaNegara]' size=30 maxlength=50></td></tr>
  <tr><td class=inp1>Benua</td><td class=ul><select name='KodeBenua'>$NoBenua</select></td></tr>
  <tr><td class=inp1>Bidang Ilmu</td><td class=ul><input type=text name='BidangIlmu' value='$w_[BidangIlmu]' size=40 maxlength=></td></tr>
  <tr><td class=inp1>Prodi DIKTI</td><td class=ul><input type=text name='Prodidosen' value='$w_[Prodi]' size=40 maxlength=></td></tr>
  
  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=dosen&gos=DsnEdt&dsnid=1001&dsnsub=TampilPendidikan'\"></td></tr>
  </form></table>";
  //<tr><td class=inp1>Negara</td><td class=ul><select name='NamaNegara'>$Negara</select></td></tr>
}

function DsnEdtPendidikanSav() {
  $md = $_REQUEST['md']+0;
  $Tglijazah_d = $_REQUEST['Tglijazah_d'];
  $Tglijazah_m = $_REQUEST['Tglijazah_m'];
  $Tglijazah_y = $_REQUEST['Tglijazah_y'];
  
  $dsnid = $_REQUEST['dsnid'];
  $Nomor = $_REQUEST['Nomor']+0;
  $DosenPendidikanID = $_REQUEST['DosenPendidikanID'];
  $Tglijazah = "$Tglijazah_y-$Tglijazah_m-$Tglijazah_d";
  $Gelar = $_REQUEST['Gelar'];
  $JenjangID = $_REQUEST['JenjangID'];
  $PTID = $_REQUEST['AsalPT'];
  $NamaNegara = $_REQUEST['NamaNegara'];
  $Nomor = $_REQUEST['Nomor'];
  $BidangIlmu = sqling($_REQUEST['BidangIlmu']);
  $KodeBenua = $_REQUEST['KodeBenua'];
  $ProdiDikti = $_REQUEST['Prodidosen'];
  
  if ($md == 0){
    $s = "update dosenpendidikan
      set Nomor=$Nomor, TanggalIjazah='$Tglijazah', Gelar='$Gelar', JenjangID='$JenjangID',
      PerguruanTinggiID='$PTID', NamaNegara='$NamaNegara',
      BidangIlmu='$BidangIlmu', KodeBenua = '$KodeBenua', Prodi = 'ProdiDikti',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where DosenPendidikanID=$DosenPendidikanID";
	  $r = _query($s);
  }
	else {
	  $in = "insert into dosenpendidikan (DosenID, Nomor, TanggalIjazah, Gelar, JenjangID,
      PerguruanTinggiID, NamaNegara, BidangIlmu, KodeBenua,
      LoginBuat, TanggalBuat)
  	  values ('$dsnid', $Nomor, '$Tglijazah', '$Gelar', '$JenjangID',
      '$PTID', '$NamaNegara', '$BidangIlmu', '$KodeBenua',
      '$_SESSION[_Login]', now())";
	  $r = _query($in); 
	}
  TampilPendidikan1();
}
function TampilKerjaDsn() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilKerjaDsn1" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilKerjaDsn1(){
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=dosen&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpekerjaan
    where DosenID='$_SESSION[dsnid]' $where
    order by DosenPekerjaanID";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
  	<td class=ul colspan=9><a href='?mnux=dosen&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsub=TampilKerjaDsn&dsnsub1=DsnEdtPekerjaan'>Tambah Pekerjaan</td></tr>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Edit</th>
	  <th class=ttl>Jabatan</th>
	  <th class=ttl>Nama Institusi</th>
	  <th class=ttl>Alamat Institusi</th>
	  <th class=ttl>Kota</th>
	  <th class=ttl>Kodepos</th>
	  <th class=ttl>Telepon</th>
	  <th class=ttl>Fax</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=dosen&gos=DsnEdt&md=0&dpid==DosenPekerjaanID=&dsnid=$_SESSION[dsnid]&dsnsub=TampilKerjaDsn&dsnsub1=DsnEdtPekerjaan\"><img src='img/edit.png' border=0>
      </a></td>
	  <td class=cna=NA= nowrap>=Jabatan=</a></td>
	  <td class=cna=NA=>=Institusi=</td>
	  <td class=cna=NA=>=Alamat=</td>
	  <td class=cna=NA=>=Kota=</td>
	  <td class=cna=NA=>=Kodepos=</td>
	  <td class=cna=NA=>=Telepon=</td>
	  <td class=cna=NA=>=Fax=</td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}
function TampilTelitiDsn() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilTelitiDsn1" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilTelitiDsn1(){
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=dosen&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpenelitian
    where DosenID='$_SESSION[dsnid]' $where
    order by DosenPenelitianID";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
  	<td class=ul colspan=9><a href='?mnux=dosen&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsub=TampilTelitiDsn&dsnsub1=DsnEdtPenelitian'>Tambah Penelitian</td></tr>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Edit</th>
	  <th class=ttl>Penelitian</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=dosen&gos=DsnEdt&md=0&dlid==DosenPenelitianID=&dsnid=$_SESSION[dsnid]&dsnsub=TampilTelitiDsn&dsnsub1=DsnEdtPenelitian\"><img src='img/edit.png' border=0>
      </a></td>
	  <td class=cna=NA= nowrap>=Penelitian=</a></td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DsnEdtPenelitian() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w = GetFields('dosenpenelitian', 'DosenPenelitianID', $_REQUEST['dlid'], '*');
	  $jdl = "Update Penelitian Dosen";   
  }
  else {
    $w = array();
    $w['DosenID'] = $_REQUEST['dsnid'];
    $w['DosenPenelitianID'] = 0;
    $jdl = "Tambah Penelitian Dosen";
	}
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  //$Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='data'>
  <input type=hidden name='mnux' value='dosen'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='TampilTelitiDsn'>
  <input type=hidden name='dsnsub1' value='DsnEdtPenelitianSav'>
  <input type=hidden name='dsnid' value='$w[DosenID]'>
  <input type=hidden name='dpid' value='$w[DosenPenelitianID]'>
  <input type=hidden name='md' value='$md'>
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Judul Penelitian</td><td class=ul><input type=text name='penelitian' value='$w[NamaPenelitian]' size=20 maxlength=100></td></tr>

    
  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=dosen&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=TampilTelitiDsn'\"></td></tr>
  </form></table>";
}
function DsnEdtPenelitianSav() {
  $md = $_REQUEST['md']+0;
  $dsnid = $_REQUEST['dsnid'];
  $Penelitian = sqling($_REQUEST['penelitian']);
  $dlid = $_REQUEST['dlid'];
  
  if ($md == 0){
    $s = "update dosenpenelitian
      set NamaPenelitian='$Penelitian', 
      where DosenPenelitianID='$dlid'";
	  $r = _query($s); 
	}
	else {
	  $in = "insert into dosenpenelitian (DosenID, NamaPenelitian)
  		values ('$dsnid', '$Penelitian')";
	  $r = _query($in); 
	}
	TampilTelitiDsn1();
}
function TampilPendidikan() {
  $dsnsub1 = (empty($_REQUEST['dsnsub1']))? "TampilPendidikan1" : $_REQUEST['dsnsub1'];
  $dsnsub1();
}
function TampilPendidikan1(){
  include_once "class/dwolister.class.php";  
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=dosen&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpendidikan left outer join perguruantinggi pt on pt.PerguruanTinggiID = dosenpendidikan.PerguruanTinggiID 
  	left outer join jenjang j on j.JenjangID = dosenpendidikan.JenjangID
    where dosenpendidikan.DosenID='$_SESSION[dsnid]' $where
    order by Nomor";
  $lst->fields = "dosenpendidikan.*, pt.Nama as Nama, j.Nama as jnama, date_format(TanggalIjazah, '%d-%m-%Y') as TGLIJZ ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
  	<td class=ul colspan=9><a href='?mnux=dosen&gos=DsnEdt&md=1&dsnid=$_SESSION[dsnid]&dsnsub=TampilPendidikan&dsnsub1=DsnEdtPendidikan'>Tambah Pendidikan</td></tr>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Edit</th>
	  <th class=ttl>Gelar</th>
	  <th class=ttl>Jenjang</th>
	  <th class=ttl>Tanggal Lulus Ijazah</th>
	  <th class=ttl>Nama Perguruan Tinggi</th>
	  <th class=ttl>Negara</th>
	  <th class=ttl>Bidang Ilmu</th>
	  <th class=ttl>Prodi DIKTI</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=Nomor=</td>
      <td class=cna=NA=><a href=\"?mnux=dosen&gos=DsnEdt&md=0&dsnid=$_SESSION[dsnid]&dsnsub=TampilPendidikan&dsnsub1=DsnEdtPendidikan&DosenPendidikanID==DosenPendidikanID=\"><img src='img/edit.png' border=0>
      </a></td>
	  <td class=cna=NA= nowrap>=Gelar=</a></td>
	  <td class=cna=NA=>=jnama=</td>
	  <td class=cna=NA=>=TGLIJZ=</td>
	  <td class=cna=NA=>=Nama=</td>
	  <td class=cna=NA=>=NamaNegara=</td>
	  <td class=cna=NA=>=BidangIlmu=</td>
	  <td class=cna=NA=>=Prodi=</td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

function DsnEdtPekerjaan() {
  $md = $_REQUEST['md']+0;
  if($md == 0){
  	$w = GetFields('dosenpekerjaan', 'DosenPekerjaanID', $_REQUEST['dpid'], '*');
	  $jdl = "Update Pekerjaan Dosen";   
  }
  else {
    $w = array();
    $w['DosenID'] = $_REQUEST['dsnid'];
    $w['DosenPekerjaanID'] = 0;
    $jdl = "Tambah Pekerjaan Dosen";
	}
  
  //$w  = GetFields('dosen','DosenID',$_SESSION['dsnid'],'*');
  $Kota = GetOption2('perguruantinggi', 'Kota', 'Kota', $w['Kota'], '', 'Kota');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST Name='data'>
  <input type=hidden name='mnux' value='dosen'>
  <input type=hidden name='gos' value='DsnEdt'>
  <input type=hidden name='dsnsub' value='TampilKerjaDsn'>
  <input type=hidden name='dsnsub1' value='DsnEdtPekerjaanSav'>
  <input type=hidden name='dsnid' value='$w[DosenID]'>
  <input type=hidden name='dpid' value='$w[DosenPekerjaanID]'>
  <input type=hidden name='md' value='$md'>
  
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp1>Nama Institusi</td><td class=ul><input type=text name='Institusi' value='$w[Institusi]' size=20 maxlength=100></td></tr>
  <tr><td class=inp1>Jabatan</td><td class=ul><input type=text name='Jabatan' value='$w[Jabatan]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Alamat Institusi</td><td class=ul><input type=text name='Alamat' value='$w[Alamat]' size=30 maxlength=100></td></tr>
  <tr><td class=inp1>Kota</td><td class=ul><input type=text name='Kota' value='$w[Kota]' maxlength=50 size=30></td></tr>
  <tr><td class=inp1>Kodepos</td><td class=ul><input type=text name='Kodepos' value='$w[Kodepos]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Telepon</td><td class=ul><input type=text name='Telepon' value='$w[Telepon]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Fax</td><td class=ul><input type=text name='Fax' value='$w[Fax]' size=10 maxlength=10></td></tr>
    
  <tr><td colspan=2><input type=submit name='submit' value='Simpan'>
    <input type=reset name='Reset' value='Reset'> 
	<input type=button name='batal' value='Batal' onClick=\"location='?mnux=dosen&gos=DsnEdt&dsnid=$_SESSION[dsnid]&dsnsub=TampilKerjaDsn'\"></td></tr>
  </form></table>";
}

function DsnEdtPekerjaanSav() {
  $md = $_REQUEST['md']+0;
  $dsnid = $_REQUEST['dsnid'];
  $Institusi = sqling($_REQUEST['Institusi']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $Kodepos = sqling($_REQUEST['Kodepos']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Fax = sqling($_REQUEST['Fax']);
  $dpid = $_REQUEST['dpid'];
  
  if ($md == 0){
    $s = "update dosenpekerjaan
      set Institusi='$Institusi', Jabatan='$Jabatan', Alamat='$Alamat',
      Kota='$Kota', Telepon='$Telepon',
      Fax='$Fax', Kodepos = '$Kodepos'
      where DosenPekerjaanID='$dpid'";
	  $r = _query($s); 
	}
	else {
	  $in = "insert into dosenpekerjaan (DosenID, Institusi, Jabatan, Alamat, Kota, Kodepos, Telepon, Fax)
  		values ('$dsnid', '$Institusi', '$Jabatan', '$Alamat', '$Kota', '$Kodepos', '$Telepon', '$Fax')";
	  $r = _query($in); 
	}
	TampilKerjaDsn1();
}
function DsnEdtPengajaran() {
  $dsnajarpage = GetSetVar('dsnajarpage', 1);
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['dsnajarpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=dosen&gos=DsnEdt&dsnajarpage==PAGE='>=PAGE=</a>";
  $lst->tables = "jadwal j
    left outer join hari h on j.HariID=h.HariID
    where j.DosenID='$_SESSION[dsnid]'
    order by j.TahunID, j.HariID, j.JamMulai desc";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "j.TahunID, j.MKKode, j.MKID, j.Nama, j.NamaKelas, j.JenisJadwalID, h.Nama as HR,
    j.JamMulai, j.JamSelesai ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>Jenis</th>
    <th class=ttl>Hari</th>
    <th class=ttl>Jam</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
	  <td class=ul>=TahunID=</td>
	  <td class=ul>=MKKode=</td>
	  <td class=ul>=Nama=</td>
	  <td class=ul>=NamaKelas=</td>
	  <td class=ul>=JenisJadwalID=</td>
	  <td class=ul>=HR=</td>
	  <td class=ul>=JamMulai= ~ =JamSelesai=</td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";

}

// *** Paramaters ***
$dsnsub = GetSetVar('dsnsub');
$dsnurt = GetSetVar('dsnurt', 'Login');
$dsnid = GetSetVar('dsnid');
$dsncr = GetSetVar('dsncr');
$dsnkeycr = GetSetVar('dsnkeycr');
$dsnpage = GetSetVar('dsnpage');
$prodi = GetSetVar('prodi');
$last = GetSetVar('last', "H");
if ($dsnkeycr == 'Reset') {
  $dsncr = '';
  $_SESSION['dsncr'] = '';
  $dsnkeycr = '';
  $_SESSION['dsnkeycr'] = '';
}
$gos = (empty($_REQUEST['gos']))? 'CariDosen' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Master Dosen");
$gos();
?>
