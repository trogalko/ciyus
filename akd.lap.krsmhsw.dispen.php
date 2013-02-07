<?php

// *** Functions ***
function HeaderKrs($tahun, $_prid, $_prodi, $div, $maxcol, &$hal, $RentangNPM=''){
	Global $_lf;
	$hdr  = str_pad("*** Daftar KRS Mahasiswa Dispensasi ***", $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $hdr .= str_pad("Program       : $_prid", $maxcol, ' '). $_lf;
  $hdr .= str_pad("Program Studi : $_prodi", $maxcol, ' ') . $_lf;
  $hdr .= str_pad("Semester      : " . NamaTahun($_SESSION['tahun']), 50, ' ') .
     $_lf . $RentangNPM. $_lf . $div;
  $hdr .= "No. NPM          NAMA            Kode    Nama Matakuliah               SKS   TGL DISPEN   CATATAN DISPEN                 ".$_lf;
  $hdr .= $div;
	
	return $hdr;
}

function DftrAkdLapKRSMhswDispen() {
  global $_HeaderPrn, $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) $whr[] = " '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ".$_whr;
  // query
  $s = "select krs.KRSID, krs.MKID, krs.StatusKRSID, mk.MKKode, j.JenisJadwalID, 
    LEFT(mk.Nama, 25) as NamaMK, mk.SKS, krs.CatatanDispensasi, krs.TanggalDispensasi, 
    krs.MhswID, LEFT(m.Nama, 15) as NamaMhsw,
    m.ProdiID, m.ProgramID, prd.Nama as PRD, m.TotalSKS
    from krs krs
      left outer join mhsw m on krs.MhswID=m.MhswID
      left outer join prodi prd on m.ProdiID=prd.ProdiID
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join jadwal j on krs.JadwalID=j.JadwalID
    where krs.StatusKRSID='A' and krs.TahunID='$_SESSION[tahun]' $_whr 
    and krs.CatatanDispensasi <> ''
    order by m.ProdiID, m.MhswID, mk.MKKode";
  $r = _query($s);
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  
  // parameter2
  //$_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  //$_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $hal = 0; $brs = 45; 
  $maxbrs = 45;
  $maxcol = 150;
  $div = str_pad('-', $maxcol, '-').$_lf; 
  $tgl = date('d-m-Y h:i');
  fwrite($f, chr(27).chr(15).chr(27).chr(77)); // --> set 66 baris (kuarto)
  // Buat header
  $RentangNPM = (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))? "Dari NPM: $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM] " : '';
  $_npm = ''; 
  $prd = '';
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	$first = 1;
	$n_ = 0;
  while ($w = _fetch_array($r)) {
    // Tampilkan Prodi
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
				$_n=0;
      }
    if ($_npm != $w['MhswID']) {
      $_npm = $w['MhswID'];
      $_mhswid = $w['MhswID'];
      $_mhswnm = $w['NamaMhsw'];
      $_TOTSKS = $w['TotalSKS'];
      $_n++;
      $_strn = str_pad($_n, 3, '0', STR_PAD_LEFT);
      $_SKS = GetaField('krs left outer join jadwal j on j.JadwalID = krs.JadwalID', "krs.TahunID='$_SESSION[tahun]' and j.JenisJadwalID = 'K' and krs.MhswID", $w['MhswID'], "sum(krs.SKS)")+0;
      $_IPS = '0,00';
    } 
    else {
      $_mhswid = '';
      $_mhswnm = '';
      $_str = '';
      $_strn = '   ';
      $_SKS = '  ';
      $_IPS = '    ';
      $_TOTSKS = ' ';
    }
    $Catatan = str_replace(chr(13), ' ', $w['CatatanDispensasi']);
    $Catatan = str_replace(chr(10), '', $Catatan);
    // Tuliskan
    $jj = ($w['JenisJadwalID'] <> 'K')? " ($w[JenisJadwalID])" : '';
    $isi = $_strn . ' ';
    $isi .= str_pad($_mhswid, 12, ' ') . ' ';
    $isi .= str_pad($_mhswnm, 15, ' ') . ' ';
    $isi .= str_pad($w['MKKode'], 7) . ' ';
    $isi .= str_pad($w['NamaMK'].$jj, 30) . ' ';
    $isi .= str_pad($w['SKS'], 2, ' ', STR_PAD_LEFT) . ' ';
    $isi .= str_pad($w['TanggalDispensasi'], 12, ' ', STR_PAD_LEFT) . '   ';
    $isi .= str_pad($Catatan, 30, ' ') . ' ';
    $isi .= $_lf;
    fwrite($f, $isi);
  }
  $hal++;
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh: ". $_SESSION['_Login'] . ', '. date("d-m-Y H:i") , 50, ' '));
    //str_pad("(Akhir Laporan) Hal. : ".$hal.'/'.$jumhal, 100, ' ', STR_PAD_LEFT));
  // Tutup & tampilkan
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'akd.lap');
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

// *** Main ***
TampilkanJudul("Daftar KRS Mahasiswa Yang Dispensasi");
//TampilkanTahunProdiProgram('akd.lap.krsmhsw', 'DftrAkdLapKRSMhsw');
TampilkanTahunProdiProgram('akd.lap.krsmhsw.dispen', 'DftrAkdLapKRSMhswDispen', '', '', 1);
if (!empty($tahun)) DftrAkdLapKRSMhswDispen();
?>

