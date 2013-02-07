<?php
	function Daftar() {
  global $_HeaderPrn, $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) $whr[] = " '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  // Query
  $s = "select m.MhswID, m.Nama, sum(krs.SKS) as TSKS, m.IPK, m.BatasStudi, krs.KHSID, m.ProdiID, m.ProgramID
    from krstemp krs
      left outer join mhsw m on krs.MhswID=m.MhswID
    where krs.TahunID='$_SESSION[tahun]'
    $_whr
    group by krs.MhswID ";
  $r = _query($s);
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  $div = str_pad('-', 79, '-').$_lf;
  // parameter2
  //$_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  //$_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $n = 0; $hal = 1;
  $brs = 56;
  $maxbrs = 55;
  $maxcol = 79; $first = 1;
  // Buat header
  $RentangNPM = (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))? "Dari NPM : $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM] " : '';
  // Tampilkan
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  while ($w = _fetch_array($r)) {
    $Cek = GetaField('khs', 'KHSID', $w['KHSID'], 'StatusMhswID');
		if ($Cek == 'A') {}
		else {
			$n++; $brs++;
			$_prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
			$_prid = GetaField('program', 'ProgramID', $w['ProgramID'], 'Nama');
			if ($brs > $maxbrs) {
				if ($first == 0) {
					fwrite($f, $div.chr(12));
				}
				$hd = HeaderKrs($_SESSION['tahun'], $_prid, $_prodi, $div, $maxcol, $hal, $RentangNPM);
				fwrite($f, $hd);
				$brs = 0;
				$first = 0;
				$prodi = $w['ProdiID'];
		} 		
		elseif ($prodi != $w['ProdiID']) {
        $prodi = $w['ProdiID'];
				if ($first == 0){
					fwrite($f, $div);
				}
				fwrite($f, chr(12));
				fwrite($f, HeaderKrs($_SESSION['tahun'], $_prid, $_prodi, $div, $maxcol, $hal, $RentangNPM));
				$brs=0;
				$n=1;
      }
			$isi = str_pad($n.'.', 4, ' ') . ' ' .
				str_pad($w['MhswID'], 12) . ' '.
				str_pad($w['Nama'], 30) . ' '.
				str_pad($w['TSKS'], 3, ' ', STR_PAD_LEFT).' '.
				str_pad($w['IPK'], 6, ' ', STR_PAD_LEFT). ' '.
				str_pad($w['BatasStudi'], 6);
			fwrite($f, $isi.$_lf);
		}
  }
	fwrite($f, $div);
  //fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
  fwrite($f,str_pad('Dicetak oleh : '.$_SESSION['_Login'],55,' ').str_pad('Dibuat : '.date("d-m-Y H:i"),29,' '));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

function HeaderKRS($tahun, $_prid, $_prodi, $div, $maxcol, &$hal, $RentangNPM=''){
	global $_lf;
	$RentangNPM = (!empty($RentangNPM)) ? $RentangNPM . $_lf : '';
	$hdr = str_pad("** Daftar Mahasiswa Terdaftar KRS ".NamaTahun($_SESSION['tahun'])." Tidak Cetak KSS **", $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf;
  $hdr .= "Program  : $_prid". $_lf;
  $hdr .= "Prodi    : $_prodi".$_lf;
  $hdr .= $RentangNPM;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                     Ambil SKS   IPK  Batas Studi".$_lf.$div;
	return $hdr;
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa Terdaftar KRS Tidak Cetak KSS");
//TampilkanPilihanProdiAngkatan('akd.lap.dftrkrsmhswtidakkss', 'Daftar');
TampilkanTahunProdiProgram('akd.lap.dftrkrsmhswtidakkss', 'DftrAkdLapKRSMhsw', '', '', 1);
if (!empty($tahun)) Daftar();
?>