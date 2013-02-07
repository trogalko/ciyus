<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Selamat Ulang Tahun Ibu

// *** Functions ***
function HeaderKRS($tahun, $_prid, $_prodi, $div, $maxcol, &$hal, $RentangNPM=''){
	global $_lf;
	$hal++;
	$RentangNPM = (!empty($RentangNPM)) ? $RentangNPM . $_lf : '';
	$hdr = str_pad("** Daftar Mahasiswa Terdaftar KRS ".NamaTahun($_SESSION['tahun'])." **", $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf;
  $hdr .= "Program  : $_prid". $_lf;
  $hdr .= "Prodi    : $_prodi". str_pad("Halaman : $hal", 45, ' ', STR_PAD_LEFT).$_lf;
  $hdr .= $RentangNPM;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                     Ambil SKS   IPK  Batas Studi  Status".$_lf.$div;
	return $hdr;
}

function TampilkanPilihanUrutan() {
  global $urutan;
  $a = '';
  for ($i=0; $i<sizeof($urutan); $i++) {
    $sel = ($i == $_SESSION['_urutan'])? 'selected' : '';
    $v = explode('~', $urutan[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='akd.lap.dftrkrsmhsw'>
  <input type=hidden name='gos' value='Daftar'>
  <tr><td class=inp>Tampilkan : </td>
  <td class=ul><select name='_urutan' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

function Daftar() {
  global $_HeaderPrn, $_lf, $urutan;
	 $_u = explode('~', $urutan[$_SESSION['_urutan']]);
   $_key = $_u[1];
	$tampil = (!empty($_key)) ? "and k.StatusMhswID = '$_key'" : "and k.StatusMhswID in ('A', 'C', 'T')";
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) $whr[] = " '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  // Query
  $s = "select sm.Nama as NamaStatus, m.MhswID, m.Nama, k.TotalSKS as TSKS, m.IPK, m.BatasStudi, m.ProdiID, m.ProgramID
    from khs k
      left outer join mhsw m on k.MhswID=m.MhswID
			left outer join statusmhsw sm on sm.StatusMhswID = k.StatusMhswID
    where k.TahunID='$_SESSION[tahun]'
		$tampil
    $_whr
		order by k.MhswID";
  $r = _query($s);
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  $div = str_pad('-', 85, '-').$_lf;
  // parameter2
  $n = 0; $hal = 0;
  $brs = 56;
  $maxbrs = 55;
  $maxcol = 85; $first = 1;
  // Buat header
  $RentangNPM = (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))? "Dari NPM : $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM] " : '';
  // Tampilkan
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  while ($w = _fetch_array($r)) {
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
				str_pad($w['BatasStudi'], 12).' '.
				str_pad($w['NamaStatus'], 4);
			fwrite($f, $isi.$_lf);
  }
	fwrite($f, $div);
  //fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
  fwrite($f, str_pad('Dicetak oleh : '.$_SESSION['_Login'],55,' ').str_pad('Dibuat : '.date("d-m-Y H:i"),29,' '));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

// *** Parameters ***
$urutan = array(0=>"Semua~", 1=>"Aktif~A", 2=>"Cuti~C", 3=>"Tunggu Ujian~T");
  
$_urutan = GetSetVar('_urutan', 0);
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa Terdaftar KRS");
//TampilkanPilihanProdiAngkatan('akd.lap.dftrkrsmhsw', 'Daftar');
TampilkanTahunProdiProgram('akd.lap.dftrkrsmhsw', 'DftrAkdLapKRSMhsw', '', '', 1);
TampilkanPilihanUrutan();
if (!empty($tahun)) Daftar();

?>
