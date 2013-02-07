<?php

function daftar(){
  global $_lf;
	$whr = (!empty($_SESSION['prodi'])) ? "and m.ProdiID='$_SESSION[prodi]'" : '';
	$s = "select k.MhswID, k.ProdiID, m.Nama, m.ProgramID as program, k.Biaya, k.Bayar, k.Potongan, k.Tarik from khs k left outer join mhsw m 
	      on m.MhswID = k.MhswID where
       k.TahunID = '$_SESSION[tahun]' 
			 and JumlahMK > 0 
			 and (m.TahunID >= '2002')
			 and k.Autodebet=0
			 $whr
			 order by k.MhswID, k.ProdiID";
			 
	$r = _query($s);
	
	$maxcol = 80;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(10));
  $div = str_pad('-', $maxcol, '-').$_lf;

  $n = 0; $hal = 0; $nprd = 0; 
  $brs = 56;
  $maxbrs = 49;
	
	$jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	$prodi = "";
	$first = 1;
	$ctt = 1;
	while($w = _fetch_array($r)) {
		$bal = $w['Biaya'] - $w['Bayar'] - $w['Potongan'] + $w['Tarik'];
		$bipot = GetFields('bipotmhsw', "Dibayar = 0 and TahunID = '$_SESSION[tahun]' and TrxID = 1 and MhswID", $w['MhswID'], 'sum(Besar * Jumlah) as TOT');
		$pot = GetFields('bipotmhsw', "TahunID = '$_SESSION[tahun]' and TrxID = -1 and MhswID", $w['MhswID'], 'Besar as POT');
		$_prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
		$n++; $brs++;
		
		$_sum = ($bipot['TOT']-$pot['POT']);
		if ($brs > $maxbrs) {
			if ($first == 0) {
				fwrite($f, $div.chr(12));
			}
			$hd = Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal);
			fwrite($f, $hd);
			$brs = 0;
			$first = 0;
			$prodi = $w['ProdiID'];
		} 		
		elseif ($prodi != $w['ProdiID']) {
        $prodi = $w['ProdiID'];
				if ($first == 0){
					fwrite($f, $div);
					fwrite($f, str_pad("## TOTAL PER PRODI : ", 53) . str_pad(number_format($sum),18, ' ', STR_PAD_LEFT).$_lf);
					fwrite($f, $div);
					//fwrite($f, str_pad("## TOTAL PER PRODI : ", 53) . str_pad(number_format($tot_),18, ' ', STR_PAD_LEFT).$_lf);
				}
				fwrite($f, chr(12));
				fwrite($f, Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal));
				$sum = 0;
				$brs=0;
				$n=1;
      } 
		
		$isi = str_pad($n, 6).
					 str_pad($w['MhswID'], 12).
					 str_pad($w['Nama'], 35).
					 str_pad($w['program'], 6).
					 str_pad(number_format($bal), 12, ' ', STR_PAD_LEFT).'    '.
					 //str_pad(number_format($pot['POT']), 25, ' ', STR_PAD_LEFT).
					 //str_pad(number_format($sum).'    '.number_format($sumtotal), 23, ' ', STR_PAD_LEFT).
					 $_lf;
		
		fwrite($f, $isi);
		$sum += $bal;
		$sumtotal += $bal;
		$tot_ += $pot['POT'];
    $summ += $bipot['TOT'];
		$summm = $summ - $tot_;
	}
	fwrite($f, $div);
	fwrite($f, str_pad("## TOTAL PER PRODI : ", 53) . str_pad(number_format($sum),18, ' ', STR_PAD_LEFT).$_lf);
	fwrite($f, $div);
  fwrite($f, str_pad("## TOTAL KESELURUHAN : ", 53) . str_pad(number_format($sumtotal),18, ' ', STR_PAD_LEFT).$_lf);
	fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],50,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),30,' ').$_lf);
  fwrite($f, str_pad("Akhir laporan", 79, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

function Headerxx($tahun, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** REKAPITULASI TAGIHAN KRS - AUTODEBET **', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahun($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 42, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 6) . str_pad("NIM", 12) . str_pad("NAMA", 35) . str_pad('PRG   ', 3) . str_pad("       TAGIH", 12) . $_lf;
		$hdr .= $div;
		
		return $hdr;
}

$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');

TampilkanJudul("Laporan Validitas Autodebet");
TampilkanTahunProdiProgram('validitas.autodebet', 'Daftar');
if (!empty($tahun)) Daftar();

?>
