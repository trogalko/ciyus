<?php
// Author: Emanuel Setio Dewo
// 08 June 2006
// www.sisfokampus.net

// *** Parameters ***
$prodi_asal = '10';
$prodi_panitera = '11';
$gos = (empty($_REQUEST['gos']))? 'TampilkanCariMhswLama' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Mahasiswa Baru Kepaniteraan");
$gos();


// *** Functions ***
function TampilkanCariMhswLama() {
  CheckFormScript('sked');
  global $prodi_asal;
  $_prd = GetFields('prodi', 'ProdiID', $prodi_asal, '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this);\">
  <input type=hidden name='mnux' value='mhsw.panitera.baru'>
  <input type=hidden name='gos' value='BuatPanitera'>
  
  <tr><td class=inp>NPM dari S.Ked</td>
    <td class=ul><input type=text name='sked' value='$_REQUEST[sked]' size=30 maxlength=50></td>
    <td class=ul colspan=2><input type=submit name='Buat' value='Buat NPM Panitera'></td></tr>
  </form></table></p>";
}

function BuatPanitera() {
  global $prodi_asal, $prodi_panitera;
  $sked = $_REQUEST['sked'];
  $mhsw = GetFields('mhsw', "MhswID", $sked, '*');
  if (!empty($mhsw)) {
    if ($mhsw['ProdiID'] == $prodi_asal) {
      $ada = GetFields('mhsw', "ProdiID='$prodi_panitera' and PMBID", $sked, '*');
      if (!empty($ada)) {
        die(ErrorMsg("Mhsw Sudah Terdaftar", 
	      "Mahasiswa dengan NPM S.Ked <b>$sked</b> telah terdaftar di program Kepaniteraan.
        <hr size=1 color=silver>
	      Pilihan: <a href='?mnux=mhsw.panitera.baru'>Kembali</a>"));
	    }
	  //if ($mhsw['LulusUjian'] == 'N')
	  if ($mhsw['StatusMhswID'] != 'L') 
	    die(ErrorMsg("Mhsw Belum Lulus",
		  "Mahasiswa dengan NPM S.Ked <b>$sked</b> belum lulus S.Ked.
      <hr size=1 color=silver>
	    Pilihan: <a href='?mnux=mhsw.panitera.baru'>Kembali</a>"));

    $tgl = FormatTanggal($mhsw['TanggalLulus']);
    CheckFormScript("SKMasuk");
    $TGL = GetDateOption(date('Y-m-d'), 'TGL');

	  echo Konfirmasi("Pembuatan NPM Kepaniteraan",
      "<p><form action='?' method=POST onSubmit=\"return CheckForm(this)\">
      <input type=hidden name='mnux' value='mhsw.panitera.baru'>
      <input type=hidden name='sked' value='$sked'>
      <input type=hidden name='gos' value='BuatPanitera1'>
      <table class=bsc>
		  <tr><td class=ul>NPM SKed</td>
		  <td class=ul><b>$mhsw[MhswID]</td></tr>
		  <tr><td class=ul>Nama Mhsw</td>
		  <td class=ul><b>$mhsw[Nama]</td></tr>
		  <tr><td class=ul>Lulus Tanggal</td>
		  <td class=ul><b>$tgl</td></tr>
		  <tr><td class=ul>Nilai Akhir</td>
		  <td class=ul><b>$mhsw[GradeNilai] ($mhsw[NilaiUjian])</td></tr>
		  <tr><td class=ul>SK Masuk Panitera</td>
		    <td class=ul><input type=text name='SKMasuk' size=30 maxlength=50></td></tr>
		  <tr><td class=ul>Tanggal SK Masuk</td>
		    <td class=ul>$TGL</td></tr>
		  </table></p>
		
	    Pilihan: <input type=submit name='Simpan' value='Simpan'>
	    <input type=button name='Batal' value='Batalkan Proses' onClick=\"location='?mnux=mhsw.panitera.baru&gos='\">
      </form>");
	  }
	  else echo ErrorMsg("Mhsw Bukan Dari Kedokteran",
	  "Mahasiswa dengan Nama <b>$mhsw[Nama]</b> (<b>$sked</b>) bukan dari Fakultas Kedokteran.<br />
	  Mahasiswa ini tidak dapat diproses menjadi mahasiswa Kepaniteraan.
	  <hr size=1 color=silver>
	  Pilihan: <a href='?mnux=mhsw.panitera.baru'>Kembali</a>");
  } 
  else echo ErrorMsg("Mhsw Tidak Ditemukan",
    "Mhsw dengan NPM <b>$sked</b> tidak ditemukan, atau bukan dari fakultas Kedokteran.
	  <hr size=1 color=silver>
	  Pilihan: <a href='?mnux=mhsw.panitera.baru'>Kembali</a>");
}
/*
function BuatPanitera1() {
  global $prodi_asal, $prodi_panitera, $arrID;
  $PRG = 'REG';
  $sked = $_REQUEST['sked'];
  $mhsw = GetFields('mhsw', 'MhswID', $sked, '*');

  $TahunID = GetaField('tahun', "NA='N' and ProgramID='$PRG' and ProdiID", $prodi_panitera, 'TahunID');
  $MhswID = GetNextNIM($TahunID, $mhsw);
  $BatasStudi = HitungBatasStudi($TahunID, $prodi_panitera);
  $BIPOTID = GetaField('bipot', "NA='N' and ProgramID='$PRG' and ProdiID", $prodi_panitera, "BIPOTID");
  $SKMasuk = sqling($_REQUEST['SKMasuk']);
  $TGL = "$_REQUEST[TGL_y]-$_REQUEST[TGL_m]-$_REQUEST[TGL_d]";

  $s = "insert into mhsw (MhswID, Login, LevelID, Password, PMBID,
  TahunID, KodeID, BIPOTID,
	Nama, Foto, StatusAwalID, StatusMhswID,
	ProgramID, ProdiID, PenasehatAkademik,
	Kelamin, WargaNegara, Kebangsaan, TempatLahir, TanggalLahir,
	Agama, StatusSipil, Alamat, Kota, RT, RW, KodePos,
	Propinsi, Negara, Telepon, Handphone, Email,
	AlamatAsal, KotaAsal, RTAsal, RWAsal, KodePosAsal,
	NegaraAsal, TeleponAsal,
	NamaAyah, AgamaAyah, PendidikanAyah, PekerjaanAyah, HidupAyah,
	NamaIbu, AgamaIbu, PendidikanIbu, PekerjaanIbu, HidupIbu,
	AlamatOrtu, KotaOrtu, RTOrtu, RWOrtu, KodePosOrtu,
	PropinsiOrtu, NegaraOrtu, TeleponOrtu, HandphoneOrtu, EmailOrtu,
	AsalSekolah, JenisSekolahID, AlamatSekolah, KotaSekolah,
	JurusanSekolah, NilaiSekolah, TahunLulus,
	AsalPT, MhswIDAsalPT, ProdiAsalPT, LulusAsalPT,
	TglLulusAsalPT, IPKAsalPT, BatasStudi,
	SKMasuk, TglSKMasuk,
	LoginBuat, TanggalBuat)
	values ('$MhswID', '$MhswID', 120, '$sked', '$sked',
	'$TahunID', '$_SESSION[KodeID]', '$BIPOTID',
	'$mhsw[Nama]', '$mhsw[Foto]', 'B', 'A',
	'$PRG', '$prodi_panitera', '$mhsw[PenasehatAkademik]',
	'$mhsw[Kelamin]', '$mhsw[WargaNegara]', '$mhsw[Kebangsaan]', '$mhsw[TempatLahir]', '$mhsw[TanggalLahir]',
	'$mhsw[Agama]', '$mhsw[StatusSipil]', '$mhsw[Alamat]', '$mhsw[Kota]', '$mhsw[RT]', '$mhsw[RW]', '$mhsw[KodePos]',
	'$mhsw[Propinsi]', '$mhsw[Negara]', '$mhsw[Telephone]', '$mhsw[Handphone]', '$mhsw[Email]',
	'$mhsw[AlamatAsal]', '$mhsw[KotaAsal]', '$mhsw[RTAsal]', '$mhsw[RWAsal]', '$mhsw[KodePosAsal]',
	'$mhsw[NegaraAsal]', '$mhsw[TeleponAsal]',
	'$mhsw[NamaAyah]', '$mhsw[AgamaAyah]', '$mhsw[PendidikanAyah]', '$mhsw[PekerjaanAyah]', '$mhsw[HidupAyah]',
	'$mhsw[NamaIbu]', '$mhsw[AgamaIbu]', '$mhsw[PendidikanIbu]', '$mhsw[PekerjaanIbu]', '$mhsw[HidupIbu]',
	'$mhsw[AlamatOrtu]', '$mhsw[KotaOrtu]', '$mhsw[RTOrtu]', '$mhsw[RWOrtu]', '$mhsw[KodePosOrtu]',
	'$mhsw[PropinsiOrtu]', '$mhsw[NegaraOrtu]', '$mhsw[TeleponOrtu]', '$mhsw[HandphoneOrtu]', '$mhsw[EmailOrtu]',
	'$mhsw[AsalSekolah]', '$mhsw[JenisSekolahID]', '$mhsw[AlamatSekolah]', '$mhsw[KotaSekolah]',
	'$mhsw[JurusanSekolah]', '$mhsw[NilaiSekolah]', '$mhsw[TahunLulus]',
	'$arrID[KodeHukum]', '$sked', '$mhsw[ProdiID]', '$mhsw[LulusUjian]',
	'$mhsw[TanggalLulus]', '$mhsw[IPK]', '$BatasStudi',
	'$SKMasuk', '$TGL',
	'$_SESSION[_Login]', now())";
  $r = _query($s);
  
  echo Konfirmasi("Telah Diproses",
    "Mahasiswa SKed <b>$mhsw[Nama]</b> ($sked) telah diproses menjadi mahasiswa Kepaniteraan.<br />
	NPM Kepaniteraan: <font size=+2>$MhswID</font>.<br />
	Data mahasiswa diimport dari data mahasiswa SKed. Silakan cek di Master Mahasiswa jika ada data yang berubah.
	<hr size=1 color=silver>
	Pilihan: <a href='?mnux=mhsw.panitera.baru'>Buat NPM Lain</a>");
}*/
?>
