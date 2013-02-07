<?php
function FilterInqMhswPerMK(){
	global $arrID;
	$optjenjad = GetOption2('jenisjadwal', "concat(JenisJadwalID, ' - ', Nama)", "JenisJadwalID", $_SESSION['jenjad'], '', "JenisJadwalID");
	echo "<p class=noprint><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='inq.pesertakuliah'>
  <input type=hidden name='gos' value='TampilkanDaftarMhsw'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Tahun Akademik</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
    <input type=submit name='Tentukan' value='Tentukan'></td></tr>
	<tr><td class=inp1>Kode MK :</td><td class=ul><input type=text name=KodeMK value='$_SESSION[KodeMK]' size=15></td></tr>
  <tr><td class=inp1>Seksi/Kelas :</td><td class=ul><input type=text name=Kelas value='$_SESSION[Kelas]' size=15></td></tr>
	<td class=inp1>Filter: </td><td class=ul><select name='jenjad' onChange='this.form.submit()'>$optjenjad</select></td></tr>
	</form></table></p>";
}

function TampilkanDaftarMhsw(){
  $_jj = (empty($_SESSION['jenjad']))? '' : "and j.JenisJadwalID='$_SESSION[jenjad]' ";
	$_SESSION['Kelas'] = strtoupper($_SESSION['Kelas']);
	$stemp = "select m.Nama as NamaM,krs.Mhswid as IDM, m.TempatLahir, m.TanggalLahir, ag.Nama 
    from krs as krs 
			left outer join mhsw m on m.MhswID = krs.MhswID
			left outer join jadwal j on j.JadwalID = krs.JadwalID
			left outer join agama ag on m.Agama = ag.Agama
        where 
		krs.MKKode = '$_SESSION[KodeMK]' 
		and j.NamaKelas = '$_SESSION[Kelas]'
		and krs.NA = 'N'
		$_jj
		order by krs.Mhswid";
		
		$s = "Select m.Nama as NamaM,krs.Mhswid as IDM, m.TempatLahir, m.TanggalLahir, ag.Nama  
    from krstemp as krs 
			left outer join mhsw m on m.MhswID = krs.MhswID
			left outer join jadwal j on j.JadwalID = krs.JadwalID
			left outer join agama ag on m.Agama = ag.Agama
        where 
		krs.MKKode = '$_SESSION[KodeMK]' 
		and j.NamaKelas = '$_SESSION[Kelas]'
		and krs.NA = 'N'
		$_jj
		order by krs.Mhswid";
	
	$w = _query($stemp);
	$cek = _num_rows($w);
	if (empty($cek)) $w = _query($s);
	$NamaMK = GetaField('mk', 'MKKode', $_SESSION['KodeMK'], "Nama");
	$JenisJadwalnya = GetaField('jenisjadwal', 'JenisJadwalID', $_SESSION['jenjad'], 'Nama');
	$n = 0;
	echo "<p><table class=box><tr><td>Mata Kuliah</td><td>:</td><td><b>$NamaMK</b></td></tr>";
	echo "<tr><td>Kelas/Seksi</td><td>:</td><td><b>$_SESSION[Kelas]</b></td></tr>";
	echo "<tr><td>Jenis</td><td>:</td><td><b>$JenisJadwalnya</b></td></tr></table></p>";
	echo "<p><table class=box cellpadding=4 cellspacing=1>
				<tr><th class=ttl>#</th><th class=ttl>NPM</th><th class=ttl>Nama Mahasiswa</th><th class=ttl>Tempat Lahir</th><th class=ttl>Tanggal Lahir</th><th class=ttl>Agama</th></tr>";
	
	while ($r = _fetch_array($w)) {
		$n++;
		echo "<tr><td class=inp>$n.</td><td class=ul><a href=?mnux=mhsw.inq.det&mhswid=$r[IDM]&mhsw.inq>$r[IDM]</a></td><td class=ul>$r[NamaM]</td><td class=ul>$r[TempatLahir]</td><td class=ul>$r[TanggalLahir]</td><td class=ul>$r[Nama]</td>";
	}
	echo "</table></p>";
}

$tahun = GetSetVar('tahun');
$KodeMK = GetSetVar('KodeMK');
$Kelas = GetSetVar('Kelas');
$jenjad = GetSetVar('jenjad');

TampilkanJudul('Inquiry Peserta Kuliah');
FilterInqMhswPerMK();

if(!empty($tahun) && !empty($KodeMK) && !empty($Kelas)) 
TampilkanDaftarMhsw();

?>