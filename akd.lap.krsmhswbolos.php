<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Selamat Ulang Tahun Ibu

// *** Functions ***
function Daftar() {
  global $_HeaderPrn, $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) $whr[] = " '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  // Query
  $s = "select m.MhswID, m.Nama, m.IPK, m.TotalSKS, m.BatasStudi, m.Telepon, m.Alamat
    from khs 
      left outer join mhsw m on khs.MhswID=m.MhswID
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where khs.TahunID='$_SESSION[tahun]' and khs.JumlahMK=0 $_whr
      and khs.StatusMhswID='P'
      and sm.Keluar='N'
    order by khs.MhswID ";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  // Buat file
  $MaxCol = 114;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  // parameter2
  $_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $n = 0; $hal = 1;
  $brs = 0;
  $maxbrs = 23;
  // Buat header
  $RentangNPM = (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))? "Dari NPM : $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM] " : '';
  $hdr = str_pad("*** Daftar Mahasiswa Bolos KRS $_SESSION[tahun] ***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  $hdr .= "Program  : $_prid" . $_lf;
  $hdr .= "Prodi    : $_prodi" .$_lf;
  $hdr .= $RentangNPM . $_lf;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                         Total   IPK Batas  Sesi   Telp & Alamat".$_lf.
          "                                                 SKS       Studi  Aktif".$_lf.$div;
  fwrite($f, $hdr);
  // Status yg aktif
  //$aktif = GetArrayTable("select concat('\"', StatusMhswID, '\"') as SM
  //  from statusmhsw where Nilai=1", "SM", "SM");
  // Tampilkan
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  while ($w = _fetch_array($r)) {
    $n++;
	$brs++;
	  if($brs > $maxbrs){
	    fwrite($f, $div);
		  fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
		  $hal++; $brs = 1;
		  fwrite($f, chr(12).$_lf);
		  fwrite($f, $hdr);
	  }
    $smtaktif = GetaField('khs', "StatusMhswID in ('A', 'T', 'W') and MhswID", $w['MhswID'], "max(TahunID)");
    //echo $smtaktif;
    $isi = str_pad($n.'.', 4, ' ') . ' ' .
      str_pad($w['MhswID'], 12) . ' '.
      str_pad($w['Nama'], 30) . ' '.
      str_pad($w['TotalSKS'], 3, ' ', STR_PAD_LEFT).' '.
      str_pad($w['IPK'], 5, ' ', STR_PAD_LEFT).' '.
      str_pad($w['BatasStudi'], 6) . ' '.
      str_pad($smtaktif, 6) . ' '.
      str_pad($w['Telepon'], 20).$_lf.
      str_pad(' ', 72, ' '). ' '.
      str_pad($w['Alamat'].', '.$w['Kota'], 30);
    fwrite($f, $isi.$_lf);
  }
  fwrite($f, $div);
  fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f,$_lf.$_lf.str_pad('Dicetak oleh : '.$_SESSION['_Login'],85,' ').str_pad('Dibuat : '.date("d-m-Y H:i"),29,' '));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}


// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa Bolos KRS");
//TampilkanTahunProdiProgram('akd.lap.krsmhswbolos', 'Daftar');
TampilkanTahunProdiProgram('akd.lap.krsmhswbolos', 'Daftar', '', '', 1);
if (!empty($tahun) && !empty($prid) && !empty($prodi) ) Daftar();
?>
