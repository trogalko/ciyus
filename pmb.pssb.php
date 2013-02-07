<?php
// Author: Emanuel Setio Dewo
// 4 April 2006

// *** Functions ***
function TampilkanPSSBTahun() {
  global $arrID;
  $TglDUMulai = GetDateOption($_SESSION['TglDUMulai'], 'TglDUMulai');
  $TglDUSelesai = GetDateOption($_SESSION['TglDUSelesai'], 'TglDUSelesai');
  $TglBayar = GetDateOption($_SESSION['TglBayar'], 'TglBayar');
  $TglTangan = GetDateOption($_SESSION['TglTangan'], 'TglTangan');
  $snm = session_name(); $sid = session_id();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmb.pssb'>
  <input type=hidden name='$snm' value='$sid'>

  <tr><td class=ul colspan=4><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp>Tahun PSB</td><td class=ul colspan=3><input type=text name='tahunpssb' value='$_SESSION[tahunpssb]' size=4 maxlength=4>
    <input type=submit name='Tampilkan' value='Set Parameter & Tampilkan'></td></tr>
  <tr><td class=inp>Mulai Daftar Ulang</td><td class=ul>$TglDUMulai</td>
    <td class=inp>Tgl Akhir Pembayaran</td><td class=ul>$TglBayar</td>
    </tr>
  <tr><td class=inp>Selesai Daftar Ulang</td><td class=ul>$TglDUSelesai</td>
    <td class=inp>Tgl Tanda Tangan</td><td class=ul>$TglTangan</td>
    </tr>
  </form>
  </table></p>";
}
function DftrPSSB() {
  // Akan ditampilkan atau langsung print?
  $prn = '';
  // Tampilkan Menu
  echo "<p><a href='?mnux=pmb.pssb&gos=PSSBEDT&md=1'>Tambah Data PSB</a> |
    <a href='pmb.pssb.daftar.php?tahunpssb=$_SESSION[tahunpssb]' target=_blank>Cetak Peserta PSB</a></p>";
  $snm = session_name(); $sid = session_id();
  // Tampilkan
  $TOT = GetaField('pssb p
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    left outer join program prg on p.ProgramID=prg.ProgramID', "p.TahunID='$_SESSION[tahunpssb]' and p.KodeID", $_SESSION['KodeID'], 'count(PSSBID)');
  echo "<p><table class=box><tr><td class=ul>Jumlah Calon Mahasiswa PSB : <b>$TOT</b></td></tr></table></p>";
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['pssbpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=pmb.pssb&pssbpage==PAGE='>=PAGE=</a>";

  $lst->tables = "pssb p
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    left outer join program prg on p.ProgramID=prg.ProgramID
    where p.TahunID='$_SESSION[tahunpssb]' and p.KodeID='$_SESSION[KodeID]'
    order by p.PSSBID desc";
  $lst->fields = "p.*,
    prd.Nama as PRD, prg.Nama as PRG";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='pmb.pssb.cetak.php' name='LST' target=_blank>
    <input type=hidden name='tahunpssb' value='$_SESSION[tahunpssb]'>
    <input type=hidden name='prn' value='$prn'>
    <input type=hidden name='TglDUMulai' value='$_SESSION[TglDUMulai]'>
    <input type=hidden name='TglDUSelesai' value='$_SESSION[TglDUSelesai]'>
    <input type=hidden name='TglBayar' value='$_SESSION[TglBayar]'>
    <input type=hidden name='TglTangan' value='$_SESSION[TglTangan]'>
    <tr><th class=ttl>No.</th>
    <th class=ttl>No PSB</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Telp/HP</th>
    <th class=ttl>Diskon<br />%</th>
    <th class=ttl title='Cetak batch'>Batch<br />
      <input type=submit name='Cetak' value='Cetak'></th>
    <th class=ttl title='Surat Pemberitahuan'>Surat</th>
    <th class=ttl title='Hapus'>Hapus</th>
    </tr>";
  $lst->footerfmt = "</form></table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp1>=NOMER=</td>
    <td class=ul><a href='?mnux=pmb.pssb&gos=PSSBEDT&pssbid==PSSBID='><img src='img/edit.png'>
    =PSSBID=</a></td>
    <td class=ul>=Nama=</td>
    <td class=ul>=ProgramID= - =PRD=</td>
    <td class=ul>=Telepon=/=Handphone=</td>
    <td class=ul align=right>=Diskon=</td>
    <td class=ul align=center><input type=checkbox name='pssbid[]' value='=PSSBID=' checked></td>
    <td class=ul align=center><a href='pmb.pssb.cetak.php?pssbid[]==PSSBID=&tahunpssb=$_SESSION[tahunpssb]&TglDUMulai=$_SESSION[TglDUMulai]&TglDUSelesai=$_SESSION[TglDUSelesai]&TglBayar=$_SESSION[TglBayar]&TglTangan=$_SESSION[TglTangan]&prn=$prn' target=_blank><img src='img/printer.gif'></a></td>
    <td class=ul align=center><a href='?mnux=pmb.pssb&gos=PSSBDEL&pssbid==PSSBID='><img src='img/del.gif'></a></td>
    </tr>";
  echo $lst->TampilkanData();
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "</p>";
}
function PSSBDEL() {
  $PSSBID = $_REQUEST['pssbid'];
  $Nama = GetaField('pssb', 'PSSBID', $PSSBID, 'Nama');
  echo Konfirmasi("Konfirmasi Penghapusan Data",
    "Benar Anda akan menghapus data PSSB <font size=+1>$PSSBID</font> 
    dengan nama calon: <font size=+1>$Nama</font> ?
    <hr size=1 color=silver>
    Opsi: <a href='?mnux=$_SESSION[mnux]&gos=PSSBDEL1&pssbid=$PSSBID'>Hapus Data</a> | 
    <a href='?mnux=$_SESSION[mnux]'>Batal</a>");
}
function PSSBDEL1() {
  $PSSBID = $_REQUEST['pssbid'];
  $s = "delete from pssb where PSSBID='$PSSBID' ";
  $r = _query($s);
  echo Konfirmasi1("PSSB <font size=+1>$PSSBID</font> Sudah Dihapus");
  DftrPSSB();
}
function CariSekolahScript() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function carisekolah(frm){
    lnk = "carisekolah.php?SekolahID="+frm.AsalSekolah.value+"&Cari="+frm.NamaSekolah.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  function caript(frm){
    lnk = "cariperguruantinggi.php?PerguruanTinggiID="+frm.AsalPT.value+"&Cari="+frm.NamaPT.value;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function PSSBEDT() {
  $PSSBMinimalFields = "Nama,AsalSekolah,Kelamin,Agama,ProgramID,ProdiID";
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('pssb', 'PSSBID', $_REQUEST['pssbid'], '*');
    $jdl = "Edit Data PSSB";
  }
  else {
    $w = array();
    $w['PSSBID'] = "<font color=red>[Autonumber]</font>";
    $w['TahunID'] = $_SESSION['tahunpssb'];
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['Nama'] = '';
    $w['ProgramID'] = '';
    $w['ProdiID'] = '';
    $w['Kelamin'] = 'P';
    $w['TempatLahir'] = '';
    $w['TglLahir'] = date('Y-m-d');
    $w['Agama'] = '';
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
    $w['AsalSekolah'] = '';
    $w['Diskon'] = 0;
    $jdl = "Tambah Data PSSB";
  }
  CheckFormScript($PSSBMinimalFields);
  CariSekolahScript();

  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", "ProgramID", $w['ProgramID'], '', 'ProgramID');
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", "ProdiID", $w['ProdiID'], '', 'ProdiID');
  $optkelamin = GetRadio("select * from kelamin where NA='N'", 'Kelamin', 'Nama', 'Kelamin', $w['Kelamin'], '&nbsp;');
  $optTglLhr = GetDateOption($w['TanggalLahir'], 'TanggalLahir');
  $optAgama = GetOption2('agama', "concat(Agama, ' - ', Nama)", '', $w['Agama'], '', 'Agama');
  $NamaSekolah = GetaField('asalsekolah', 'SekolahID', $w['AsalSekolah'], "concat(Nama, ', ', Kota)");

  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form name='data' action='?' method=POST onSubmit=\"return CheckForm(this);\">
  <input type=hidden name='mnux' value='pmb.pssb'>
  <input type=hidden name='gos' value='PSSBSAV'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='PSSBID' value='$w[PSSBID]'>

  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=ul colspan=2><b>Data Pribadi</td></tr>
  <tr><td class=inp>No. PSSB</td><td class=ul>$w[PSSBID]</td></tr>
  <tr><td class=inp>Nama Lengkap</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Jenis Kelamin</td><td class=ul>$optkelamin</td></tr>
  <tr><td class=inp>Agama</td><td class=ul><select name='Agama'>$optAgama</select></td></tr>
  <tr><td class=inp>Tempat Lahir</td><td class=ul><input type=text name='TempatLahir' value='$w[TempatLahir]' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Tanggal Lahir</td><td class=ul>$optTglLhr</td></tr>
  <tr><td class=inp>Kode Sekolah</td><td class=ul><input type='readonly' name='AsalSekolah' value='$w[AsalSekolah]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>Nama Sekolah</td><td class=ul><input type=text name='NamaSekolah' value='$NamaSekolah' size=50 maxlength=255>
    <a href='javascript:carisekolah(data)'>Cari</a></td></tr>

  <tr><td class=ul colspan=2><b>Pilihan</b></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='ProgramID'>$optprg</select></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='ProdiID'>$optprd</select></td></tr>

  <tr><td class=ul colspan=2><b>Alamat Lengkap</td></tr>
  <tr><td class=inp>Alamat</td><td class=ul><input type=text name='Alamat' value='$w[Alamat]' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Kota</td><td class=ul><input type=text name='Kota' value='$w[Kota]' size=30 maxlength=50> Kode Pos <input type=text name='KodePos' value='$w[KodePos]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Propinsi</td><td class=ul><input type=text name='Propinsi' value='$w[Propinsi]' size=30 maxlength=50> Negara <input type=text name='Negara' value='$w[Negara]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Telepon</td><td class=ul><input type=text name='Telepon' value='$w[Telepon]' size=20 maxlength=50>
    Handphone <input type=text name='Handphone' value='$w[Handphone]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>E-mail</td><td class=ul><input type=text name='Email' value='$w[Email]' size=50 maxlength=50></td></tr>
  
  <tr><td class=ul colspan=2><b>Diskon</b></td></tr>
  <tr><td class=inp>Diskon SPP</td><td class=ul><input type=text name='Diskon' value='$w[Diskon]' size=3 maxlength=4></td></tr>

  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pmb.pssb'\"></td></tr>
  </form></table></p>";
}
function PSSBSAV() {
  $md = $_REQUEST['md']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $Kelamin = $_REQUEST['Kelamin'];
  $Agama = $_REQUEST['Agama'];
  $TempatLahir = $_REQUEST['TempatLahir'];
  $TanggalLahir = "$_REQUEST[TanggalLahir_y]-$_REQUEST[TanggalLahir_m]-$_REQUEST[TanggalLahir_d]";
  $AsalSekolah = $_REQUEST['AsalSekolah'];
  $ProgramID = $_REQUEST['ProgramID'];
  $ProdiID = $_REQUEST['ProdiID'];
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Email']);
  $Diskon = $_REQUEST['Diskon']+0;
  // Simpan
  if ($md == 0) {
    $s = "update pssb set Nama='$Nama', Kelamin='$Kelamin', Agama='$Agama',
      TempatLahir='$TempatLahir', TanggalLahir='$TanggalLahir',
      AsalSekolah='$AsalSekolah', ProgramID='$ProgramID', ProdiID='$ProdiID',
      Alamat='$Alamat', Kota='$Kota', KodePos='$KodePos',
      Propinsi='$Propinsi', Negara='$Negara',
      Telepon='$Telepon', Handphone='$Handphone', Email='$Email',
      Diskon='$Diskon',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where PSSBID='$_REQUEST[PSSBID]' ";
    $r = _query($s);
  }
  else {
    $PSSBID = GetNextPSSBID($_SESSION['tahunpssb']);
    $s = "insert into pssb (TahunID, KodeID, PSSBID,
      Nama, Kelamin, Agama,
      TempatLahir, TanggalLahir,
      AsalSekolah, ProgramID, ProdiID,
      Alamat, Kota, KodePos,
      Propinsi, Negara,
      Telepon, Handphone, Email, Diskon,
      LoginBuat, TanggalBuat)
      values ('$_SESSION[tahunpssb]', '$_SESSION[KodeID]', '$PSSBID',
      '$Nama', '$Kelamin', '$Agama',
      '$TempatLahir', '$TanggalLahir',
      '$AsalSekolah', '$ProgramID', '$ProdiID',
      '$Alamat', '$Kota', '$KodePos',
      '$Propinsi', '$Negara',
      '$Telepon', '$Handphone', '$Email', '$Diskon',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  // Kembali ke daftar PSSB
  DftrPSSB();
}


// *** Parameters ***
$tahunpssb = GetSetVar('tahunpssb');
$pssbpage = GetSetVar('pssbpage', 0);
// Tanggal Daftar Ulang Mulai
$TglDUMulai_y = GetSetVar('TglDUMulai_y', date('Y'));
$TglDUMulai_m = GetSetVar('TglDUMulai_m', date('m'));
$TglDUMulai_d = GetSetVar('TglDUMulai_d', date('d'));
$TglDUMulai = "$TglDUMulai_y-$TglDUMulai_m-$TglDUMulai_d";
$_SESSION['TglDUMulai'] = $TglDUMulai;
// Tanggal Daftar Ulang Selesai
$TglDUSelesai_y = GetSetVar('TglDUSelesai_y', date('Y'));
$TglDUSelesai_m = GetSetVar('TglDUSelesai_m', date('m'));
$TglDUSelesai_d = GetSetVar('TglDUSelesai_d', date('d'));
$TglDUSelesai = "$TglDUSelesai_y-$TglDUSelesai_m-$TglDUSelesai_d";
$_SESSION['TglDUSelesai'] = $TglDUSelesai;
// Tanggal Terakhir Pembayaran
$TglBayar_y = GetSetVar('TglBayar_y', date('Y'));
$TglBayar_m = GetSetVar('TglBayar_m', date('m'));
$TglBayar_d = GetSetVar('TglBayar_d', date('d'));
$TglBayar = "$TglBayar_y-$TglBayar_m-$TglBayar_d";
$_SESSION['TglBayar'] = $TglBayar;
// Tanggal tanda tangan
$TglTangan_y = GetSetVar('TglTangan_y', date('Y'));
$TglTangan_m = GetSetVar('TglTangan_m', date('m'));
$TglTangan_d = GetSetVar('TglTangan_d', date('d'));
$TglTangan = "$TglTangan_y-$TglTangan_m-$TglTangan_d";
$_SESSION['TglTangan'] = $TglTangan;

$gos = (empty($_REQUEST['gos']))? 'DftrPSSB' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Pendataan PSB");
TampilkanPSSBTahun();
if (!empty($tahunpssb)) $gos();
?>
