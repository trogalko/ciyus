<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

// *** Functions ***
function Daftar() {
  global $_lf;
  $whr = array();
  $status = (empty($_REQUEST['status']))? 'A' : $_REQUEST['status'];
  $NamaStatus = GetaField('statusmhsw', 'StatusMhswID', $status, 'Nama');
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) $whr[] = " '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  // Query
  $s = "select m.MhswID, LEFT(m.Nama, 25) as Nama, m.IPK, m.TotalSKS, m.BatasStudi, m.Telepon, 
      LEFT(m.Alamat, 50) as Alamat, m.RT, m.RW, m.KodePos, m.Kota,
      m.AsalSekolah, LEFT(asek.Nama, 30) as ASEK
    from khs 
      left outer join mhsw m on khs.MhswID=m.MhswID
      left outer join asalsekolah asek on m.AsalSekolah=asek.SekolahID
    where khs.TahunID='$_SESSION[tahun]'
      and khs.StatusMhswID='$status' 
      $_whr
    order by khs.MhswID ";
  //echo "<pre>$s</pre>";
  $r = _query($s);
    // Buat file
  $MaxCol = 200;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  // parameter2
  $_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $n = 0; $hal = 1;
  $brs = 0;
  $maxbrs = 50;
  $maxkol = 200;
  // Buat header
  $RentangNPM = (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))? "Dari NPM : $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM] " : '';
  $hdr = str_pad("** Daftar Mahasiswa $NamaStatus $tahun **", $MaxCol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  $hdr .= "Semester : ".NamaTahun($_SESSION['tahun']).$_lf;
  $hdr .= "Program  : $_prid".$_lf;
  $hdr .= "Prodi    : $_prodi" .$_lf;
  $hdr .= $RentangNPM .$_lf;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                      Asal Sekolah                    Telepon         Alamat                                                      RT/RW         Kodepos     Kota".$_lf.$div;
  fwrite($f, $hdr);
  // Tampilkan
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs > $maxbrs) {
      fwrite($f, $div);
      fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
      $hal++; $brs =1;
	    fwrite($f, chr(12).$_lf);
      fwrite($f, $hdr);
    }
    $w['Alamat'] = str_replace(chr(13), ' ', $w['Alamat']);
    $w['Alamat'] = str_replace(chr(10), '', $w['Alamat']);
    $isi = str_pad($n.'.', 4, ' ') . ' ' .
      str_pad($w['MhswID'], 12) . ' '.
      str_pad($w['Nama'], 25) . ' '.
      str_pad($w['ASEK'], 30, ' '). '  '.
      str_pad($w['Telepon'], 15) .' '.
      str_pad($w['Alamat'], 60, ' '). 
      str_pad($w['RT'].'/'.$w['RW'], 14, ' ').
      str_pad($w['KodePos'], 12, ' ') .
      str_pad($w['Kota'],8,' ') .
      $_lf;
    fwrite($f, $isi);
  }
  fwrite($f, $div);
  fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
  fwrite($f, str_pad("Akhir laporan", 114, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],87,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),27,' ').$_lf);
  fwrite($f,chr(12).$_lf);
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
TampilkanJudul("Daftar Mahasiswa");
//TampilkanTahunProdiProgram('akd.lap.mhswakd', 'Daftar');
TampilkanTahunProdiProgram('akd.lap.mhswakd', "Daftar", 'status', $_REQUEST['status'], 1);
if (!empty($tahun)) Daftar();
?>
