<?php
function FilterDosen(){
	$statdos = GetOption2('statusdosen', "concat(StatusDosenID, ' - ', Nama)", 'StatusDosenID', $_SESSION['statusdosen'], '', 'StatusDosenID');
	echo "<p><table class=box cellpadding=4 cellspacing=1>
				<form saction='?' method='post'>
				<input type=hidden name='mnux' value='akd.lap.aktifitasdosen'>
				<input type=hidden name='gos' value='Daftar'>
				<tr><td class=inp>Tahun Akademik</td><td class=ul><input type=text name=tahun value='$_SESSION[tahun]'></td></tr>
				<tr><td class=inp>Jenis Dosen</td><td class=ul><select name=statusdosen>$statdos</select></td></tr>
				<tr><td class=ul colspan=2><input type=submit Value='Kirim'></td></tr>
				</form></table></p>";
}

function Daftar(){
	global $_lf;
	$s = "select j.SKS, j.DosenID, concat(d.Nama, ' ', d.Gelar) as DSN, 
				j.MKKode, j.NamaKelas, mk.Nama as MKNAMA, j.JenisJadwalID
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
			left outer join statusdosen sd on d.StatusDosenID = sd.StatusDosenID
			left outer join mk on mk.MKID = j.MKID
    where j.TahunID='$_SESSION[tahun]'
			and d.StatusDosenID = '$_SESSION[statusdosen]'
			and j.JadwalSer = 0
			and j.JumlahMhsw <> 0
		group by j.MKKODE, j.NamaKelas, j.JenisJadwalID
		order by j.DosenID, j.JenisJadwalID, j.MKKode,  j.NamaKelas";
	
	$r = _query($s);
	$maxcol = 130;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $maxcol, '-').$_lf;
	
	$brs = 0; $hal=1;
  $maxbrs = 50;
	
	$namastatus = GetaField('statusdosen', 'StatusDosenID', $_SESSION['statusdosen'], 'Nama');
	
	$hdr = str_pad("*** AKTIFITAS MENGAJAR DOSEN ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  $hdr .= "Periode      : " . NamaTahun($_SESSION['tahun']) . $_lf;
	$hdr .= "Status Dosen : " . $namastatus . $_lf;
  $hdr .= $div;
  $hdr .= str_pad("NO.", 5) . 
					str_pad("NO DSN", 9) . 
					str_pad("NAMA DOSEN", 40) .
					str_pad("SKS-KUL", 8) .
					str_pad("SKS-REP", 8) .
					str_pad("MATA KULIAH", 48) . 
					str_pad('SKS', 6) . 
					str_pad('KELAS', 5) . 
	$_lf . $div;
	
  fwrite($f, $hdr);
  // Tampilkan
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);

	while($w = _fetch_array($r)) {
		$brs++;
		if($brs > $maxbrs) {
			fwrite($f, $div);
			fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
			$hal++; $brs=1;
			fwrite($f, chr(12));
			fwrite($f, $hdr);
		}	
		
		//$SKSKULL = GetaField('jadwal', "JadwalSer = 0 and JumlahMhsw <> 0 and JenisJadwalID = 'K' and TahunID = '$_SESSION[tahun]' and DosenID", $w['DosenID'], 'sum(SKS)');
		//$SKSRESS = GetaField('jadwal', "JadwalSer = 0 and JumlahMhsw <> 0 and JenisJadwalID = 'R' and TahunID = '$_SESSION[tahun]' and DosenID", $w['DosenID'], 'sum(SKS)');
		
		/*if (($_SKSKUL != $SKSKULL)){
			$_SKSKUL = $SKSKULL;
			$SKSKUL = $_SKSKUL;
		} else $SKSKUL = '';
		
		if ($_SKSRES != $SKSRESS){
			$_SKSRES = $SKSRESS;
			$SKSRES = $_SKSRES;
		} else $SKSRES = '';
		*/
		if ($_DOSENID != $w['DosenID']) {
			$_DOSENID = $w['DosenID'];
			$DOSENID = $_DOSENID;
			$SKSKULL = GetaField('jadwal', "JadwalSer = 0 and JumlahMhsw <> 0 and JenisJadwalID = 'K' and TahunID = '$_SESSION[tahun]' and DosenID", $w['DosenID'], 'sum(SKS)')+0;
		  $SKSRESS = GetaField('jadwal', "JadwalSer = 0 and JumlahMhsw <> 0 and JenisJadwalID = 'R' and TahunID = '$_SESSION[tahun]' and DosenID", $w['DosenID'], 'sum(SKS)')+0;
			$n++;
		} else {
			$DOSENID = '';
			$SKSKULL = '';
			$SKSRESS = '';
		}
		
		if ($_NAMADOSEN != $w['DSN']) {
			$_NAMADOSEN = $w['DSN'];
			$NAMADOSEN = $_NAMADOSEN;
		} else $NAMADOSEN = '';
		
		if ($_n != $n) {
			$_n = $n;
			$no = $_n.'.';
		} else $no = '';
		
		$w['JenisJadwalID'] = ($w['JenisJadwalID'] != 'K') ? "(" . $w['JenisJadwalID'] . ")": '';
		
		$isi = str_pad($no, 5) .
					 str_pad($DOSENID, 6) .
					 str_pad($NAMADOSEN, 43) .
					 str_pad($SKSKULL, 7, ' ', STR_PAD_LEFT) .
					 str_pad($SKSRESS, 8, ' ', STR_PAD_LEFT) . ' '.
					 str_pad($w['MKKode'], 8) .
					 str_pad($w['MKNAMA'] . $w['JenisJadwalID'], 42) .
					 str_pad($w['SKS'], 6) .
					 str_pad($w['NamaKelas'], 5) .
					 $_lf;
		fwrite($f, $isi);
		
	}
	fwrite($f, $div);
  fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],87,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),27,' ').$_lf);
  fwrite($f, str_pad("Akhir laporan", 114, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

$tahun = GetSetVar('tahun');
$statusdosen = GetSetVar('statusdosen');

TampilkanJudul("Aktifitas Mengajar Dosen");
FilterDosen();
if (!empty($tahun) && !empty($statusdosen)) Daftar();


?>