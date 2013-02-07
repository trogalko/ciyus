<?php
  
//Created By Sugeng
//Juli 2006

//Function
function TampilkanCariDosen() {
  global $arrID;
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $ck_nama = ($_SESSION['dsnurt'] == 'Nama') ? 'checked' : '';
  $ck_login = ($_SESSION['dsnurt'] == 'Login') ? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='inq.dosen'>
  <input type=hidden name='dsnpage' value='1'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=ul>Cari Dosen:</td>
  <td class=ul><input type=text name='dsncr' value='$_SESSION[dsncr]' size=10 maxlengh=10>
    <input type=submit name='dsnkeycr' value='Nama'>
    <input type=submit name='dsnkeycr' value='Login'>
    <input type=submit name='dsnkeycr' value='Reset'></td></tr>
  <tr><td class=ul>Urut berdasarkan:</td><td class=ul>
    <input type=radio name='dsnurt' value='Nama' $ck_nama> Nama,
    <input type=radio name='dsnurt' value='Login' $ck_login>Login/NIP
    <input type=submit name='Urutkan' value='Urutkan'></td></tr>
  <tr><td class=ul>Homebase :</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  </form></table></p>";
}
function DftrDsn() {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  TampilkanCariDosen();
  
  $whr = array();
  if (!empty($_SESSION['dsnkeycr']) && !empty($_SESSION['dsncr'])) {
    $whr[] = "$_SESSION[dsnkeycr] like '%$_SESSION[dsncr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['prodi'])) ? '' : "and Homebase = '$_SESSION[prodi]'";
  
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=inq.dosen&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen
    where KodeID='$_SESSION[KodeID]' $where $hom
    order by $_SESSION[dsnurt]";
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Login/NIP</th>
      <th class=ttl>NIDN</th>
	  <th class=ttl>Nama</th>
	  <th class=ttl>Gelar</th>
	  <th class=ttl>Program Studi</th>
	  <th class=ttl>Homebase</th>
	  <th class=ttl>Telepon</th>
	  <th class=ttl>Alamat</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=inq.dosen&gos=DsnDetail&dsnid==Login=\"><img src='img/edit.png' border=0>
      =Login=</a></td>
      <td class=cna=NA=>=NIDN=&nbsp;</td>
	  <td class=cna=NA= nowrap>=Nama=</a></td>
	  <td class=cna=NA=>=Gelar=</td>
	  <td class=cna=NA=>=ProdiID=</td>
	  <td class=cna=NA=>=Homebase=</td>
	  <td class=cna=NA=>=Telephone=</td>
	  <td class=cna=NA=>=Alamat=</td>
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

function TampilkanMenuDetailDosen($w) {
  $arrMenuDosen = array('Data Pribadi->DsnDetail->DtlPribadi',
    'Alamat->DsnDetail->DtlAlmt',
    'Akademik->DsnDetail->DtlAkademik',
    'Jabatan->DsnDetail->DtlJabatan',
    'Pengajaran->DsnDetail->DtlPengajaran',
    //'Penelitian->DsnDetail->DtlPenelitian',
    //'Pengabdian->DsnDetail->DtlPengabdian',
	  'Pendidikan->DsnDetail->TampilDetailPendidikan',
	  'Pekerjaan->DsnDetail->TampilDetailKerjaDsn');
	
  echo "<p><table class=menu cellspacing=1 cellpadding=4><tr>";
  $_SESSION['dsndtl'] = (empty($_SESSION['dsndtl']))? 'DtlPribadi' : $_SESSION['dsndtl'];
  for ($i = 0; $i < sizeof($arrMenuDosen); $i++) {
    $mn = explode('->', $arrMenuDosen[$i]);
    $c = ($mn[2] == $_SESSION['dsndtl'])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c><a href='?mnux=inq.dosen&gos=$mn[1]&dsnid=$w[Login]&dsndtl=$mn[2]'>$mn[0]</a></td>";
  }
  echo "</tr></table></p>";
}	  

function TampilkanHeaderDosen($w) {
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>NIP/Login:</td><td class=ul><b>$w[Login]</b></td></tr>
  <tr><td class=inp1>Nama:</td><td class=ul><b>$w[Nama]</b>, $w[Gelar]</td></tr>
  <tr><td class=ul>Pilihan:</td><td class=ul><a href='?mnux=inq.dosen&gos=&sub='>Kembali ke Daftar Dosen</a></td></tr>
  </table></p>";
}

function DsnDetail() {
  $w = GetFields('dosen', "Login", $_SESSION['dsnid'], '*');
  TampilkanHeaderDosen($w);
  TampilkanMenuDetailDosen($w);
  if (!empty($_SESSION['dsndtl'])) $_SESSION['dsndtl']();
}

function DtlPribadi(){
  $m = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '*');
  $jdl = "Detail Data Pribadi";
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>Login/NIP</td><td class=ul>$m[Login]</td></tr>
  <tr><td class=inp>Nomer Induk Dosen Nasional</td><td class=ul>$m[NIDN]</td></tr>
  <tr><td class=inp>Nama Dosen</td><td class=ul>$m[Nama]</td></tr>
  <tr><td class=inp>Gelar</td><td class=ul>$m[Gelar]</td></tr>
  <tr><td class=inp>Telephone</td><td class=ul>$m[Telephone]</td></tr>
  <tr><td class=inp>Handphone</td><td class=ul>$m[Handphone]</td></tr>
  <tr><td class=inp>Email</td><td class=ul>$m[Email]</td></tr>
  <tr><td class=inp>Prodi</td><td class=ul>$m[ProdiID]</td></tr>
  </table></p>";
}

function DtlAlmt(){
  $m = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '*');
  $jdl = "Alamat Pribadi";
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>No KTP</td><td class=ul>$m[KTP]</td></tr>
  <tr><td class=inp>No Telephone</td><td class=ul>$m[Telephone]</td></tr>
  <tr><td class=inp>Ho HP</td><td class=ul>$m[Handphone]</td></tr>
  <tr><td class=inp>Email</td><td class=ul>$m[Email]</td></tr>
  <tr><td class=inp>Alamat</td><td class=ul>$m[Alamat]</td></tr>
  <tr><td class=inp>Kota</td><td class=ul>$m[Kota]</td></tr>
  <tr><td class=inp>Propinsi</td><td class=ul>$m[Propinsi]</td></tr>
  <tr><td class=inp>Negara</td><td class=ul>$m[Negara]</td></tr>
  </table></p>";
}

function DtlAkademik(){
  $m = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '*');
  $jdl = "Akademik Dosen";
  $Homebase = GetaField('prodi','ProdiID',$m['Homebase'],'Nama');
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>Mulai Bekerja</td><td class=ul>$m[TglBekerja]</td></tr>
  <tr><td class=inp>Status Dosen</td><td class=ul>$m[StatusDosen]</td></tr>
  <tr><td class=inp>Status Kerja</td><td class=ul>$m[StatusKerja]</td></tr>
  <tr><td class=inp>Prodi Homebase</td><td class=ul>$Homebase</td></tr>
  <tr><td class=inp>Mengajar di Prodi</td><td class=ul>$m[ProdiID]</td></tr>
  <tr><td class=inp>Kode Institusi Induk</td><td class=ul>$m[InstitusiInduk]</td></tr>
  <tr><td class=inp>Lulus Perguruan Tinggi</td><td class=ul>$m[LulusanPT]</td></tr>
  <tr><td class=inp>Jenjang Pendidikan Tertinggi</td><td class=ul>$m[Jenjang]</td></tr>
  <tr><td class=inp>Keilmuan</td><td class=ul>$m[keilmuan]</td></tr>
  </table></p>";
}

function DtlJabatan(){
  $m = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '*');
  $jdl = "Jabatan Dosen";
  $Jabatan = GetaField('Jabatan','JabatanID',$m['JabatanID'],'Nama');
  $JabatanD = GetaField('jabatandikti','JabatanDiktiID',$m['JabatanDiktiID'],'Nama');
  $Ikatan = GetaField('ikatan','IkatanID',$m['IkatanID'],'Nama');
  
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=ul colspan=2><b>Jabatan</b></td></tr>
  <tr><td class=inp>Jabatan Akademik</td><td class=ul>$Jabatan</td></tr>
  <tr><td class=inp>Jabatan Dikti</td><td class=ul>$JabatanD</td></tr>
  <tr><td class=inp>Golongan</td><td class=ul>$m[GolonganID]</td></tr>
  <tr><td class=inp>Ikatan</td><td class=ul>$Ikatan</td></tr>
  <tr><td class=ul colspan=2><b>Bank<b></td></tr>
  <tr><td class=inp>Nama Bank</td><td class=ul>$m[NamaBank]</td></tr>
  <tr><td class=inp>Nama Akun</td><td class=ul>$m[NamaAkun]</td></tr>
  <tr><td class=inp>Nomor Akun</td><td class=ul>$m[NomorAkun]</td></tr>
  </table></p>";
}

function DtlPengajaran(){
  $m = GetFields('dosen', 'Login', $_REQUEST['dsnid'], '* ');
  $jdl = "Jabatan Dosen";
  $Jabatan = GetaField('Jabatan','JabatanID',$m['JabatanID'],'Nama');
  $JabatanD = GetaField('jabatandikti','JabatanDiktiID',$m['JabatanDiktiID'],'Nama');
  $Ikatan = GetaField('ikatan','IkatanID',$m['IkatanID'],'Nama');
  
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=ul colspan=2><b>Jabatan</b></td></tr>
  <tr><td class=inp>Jabatan Akademik</td><td class=ul>$Jabatan</td></tr>
  <tr><td class=inp>Jabatan Dikti</td><td class=ul>$JabatanD</td></tr>
  <tr><td class=inp>Golongan</td><td class=ul>$m[GolonganID]</td></tr>
  <tr><td class=inp>Ikatan</td><td class=ul>$Ikatan</td></tr>
  <tr><td class=ul colspan=2><b>Bank<b></td></tr>
  <tr><td class=inp>Nama Bank</td><td class=ul>$m[NamaBank]</td></tr>
  <tr><td class=inp>Nama Akun</td><td class=ul>$m[NamaAkun]</td></tr>
  <tr><td class=inp>Nomor Akun</td><td class=ul>$m[NomorAkun]</td></tr>
  </table></p>";
}

function TampilDetailKerjaDsn(){
  include_once "class/dwolister.class.php";  
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=inq.dosen&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpekerjaan
    where DosenID='$_SESSION[dsnid]' $where
    order by DosenPekerjaanID";
  //$NamaPT = GetaField('perguruantinggi','PerguruanTinggiID','=PerguruanTinggiID=','Nama');
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
  	 <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Detail</th>
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
      <td class=cna=NA=><a href=\"?mnux=inq.dosen&gos=DsnDetail&md=0&dsnid=$_SESSION[dsnid]&dsndtl=DtlPekerjaan\"><img src='img/edit.png' border=0>
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

function TampilDetailPendidikan(){
  include_once "class/dwolister.class.php";  
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=dosen&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosenpendidikan left outer join perguruantinggi pt on pt.PerguruanTinggiID = dosenpendidikan.PerguruanTinggiID 
  				  left outer join jenjang j on j.JenjangID = dosenpendidikan.JenjangID
    where DosenID='$_SESSION[dsnid]' $where
    order by dosenpendidikan.jenjangID";
  $lst->fields = "dosenpendidikan.*, pt.Nama as Nama, j.Nama as jnama ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
  	<tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Detail</th>
	  <th class=ttl>Gelar</th>
	  <th class=ttl>Jenjang</th>
	  <th class=ttl>Tanggal Lulus Ijazah</th>
	  <th class=ttl>Nama Perguruan Tinggi</th>
	  <th class=ttl>Negara</th>
	  <th class=ttl>Bidang Ilmu</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=inq.dosen&gos=DsnDetail&dsnid=$_SESSION[dsnid]&dsndtl=DtlPendidikan\"><img src='img/edit.png' border=0>
      </a></td>
	  <td class=cna=NA= nowrap>=Gelar=</a></td>
	  <td class=cna=NA=>=jnama=</td>
	  <td class=cna=NA=>=TanggalIjazah=</td>
	  <td class=cna=NA=>=Nama=</td>
	  <td class=cna=NA=>=NamaNegara=</td>
	  <td class=cna=NA=>=BidangIlmu=</td>
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

function DtlPendidikan(){
  $m = GetFields('dosenpendidikan', 'KodeLogin', $_REQUEST['dsnid'], '*');
  $jdl = "Pendidikan Dosen";
  
  $Jenjang = GetaField('jenjang','JenjangID',$m['JenjangID'],'Nama');
  $PT = GetaField('perguruantinggi','PerguruanTinggiID',$m['PerguruanTinggiID'],'Nama');
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>Gelar</td><td class=ul>$m[Gelar]</td></tr>
  <tr><td class=inp>Jenjang</td><td class=ul>$Jenjang</td></tr>
  <tr><td class=inp>Tanggal Lulus Ijazah</td><td class=ul>$m[TanggalIjazah]</td></tr>
  <tr><td class=inp>Nama Perguruan Tinggi</td><td class=ul>$PT</td></tr>
  <tr><td class=inp>Negara</td><td class=ul>$m[Negara]</td></tr>
  <tr><td class=inp>Bidang Ilmu</td><td class=ul>$m[BidangIlmu]</td></tr>
  </table></p>";
}

function DtlPekerjaan(){
  $m = GetFields('dosenpekerjaan', 'DosenID', $_REQUEST['dsnid'], '*');
  $jdl = "Pekerjaan Dosen";
  
  //$Jenjang = GetaField('jenjang','JenjangID',$m['JenjangID'],'Nama');
  //$PT = GetaField('perguruantinggi','PerguruanTinggiID',$m['PerguruanTinggiID'],'Nama');
  
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>$jdl</b></td></tr>
  <tr><td class=inp>Nama Institusi</td><td class=ul>$m[Institusi]</td></tr>
  <tr><td class=inp>Jabatan</td><td class=ul>$m[Jabatan]</td></tr>
  <tr><td class=inp>Alamat Institusi</td><td class=ul>$m[Alamat]</td></tr>
  <tr><td class=inp>Kota</td><td class=ul>$m[Kota]</td></tr>
  <tr><td class=inp>Kodepos</td><td class=ul>$m[Kodepos]</td></tr>
  <tr><td class=inp>Telepon</td><td class=ul>$m[Telepon]</td></tr>
  <tr><td class=inp>Fax</td></td><td class=ul>$m[Fax]</td></tr>
  </table></p>";
}

//Parameter
$dsnsub = GetSetVar('dsndtl');
$dsnurt = GetSetVar('dsnurt', 'Login');
$dsnid = GetSetVar('dsnid');
$dsncr = GetSetVar('dsncr');
$dsnkeycr = GetSetVar('dsnkeycr');
$dsnpage = GetSetVar('dsnpage');
$phomebase = GetSetVar('prodi');

if ($dsnkeycr == 'Reset') {
  $dsncr = '';
  $_SESSION['dsncr'] = '';
  $dsnkeycr = '';
  $_SESSION['dsnkeycr'] = '';
}

$gos = (empty($_REQUEST['gos']))? 'DftrDsn' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Inquiry Dosen");
$gos();

  
?>
