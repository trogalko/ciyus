<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

// *** Functions ***
function Daftar() {
  global $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  // Query
  $s = "select m.MhswID, LEFT(m.Nama, 25) as Nama, m.IPK, 
      m.TotalSKS, m.BatasStudi, m.Telepon, 
      LEFT(m.Alamat, 30) as Alamat
    from khs 
      left outer join mhsw m on khs.MhswID=m.MhswID
    where khs.TahunID='$_SESSION[tahun]' 
      and khs.StatusMhswID='C'
    $_whr
    order by khs.MhswID ";
  //echo "<pre>$s</pre>";
  $r = _query($s);
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
  $maxbrs = 60;
  // Buat header
  $hdr = str_pad("*** Daftar Mahasiswa Cuti Kuliah***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  $hdr .= str_pad("Program: $_prid, Program Studi: $_prodi", $MaxCol, ' ', STR_PAD_BOTH).$_lf;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                    SKS    IPK  Batas Cuti".$_lf.$div;
  fwrite($f, $hdr);
  // Tampilkan
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs > $maxbrs) {
      fwrite($f, $div);
      fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
      $hal++; $brs =1;
	    fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $CUTI = GetArrayTable("select TahunID from khs where MhswID='$w[MhswID]' and StatusMhswID='C' order by TahunID",
      'TahunID', 'TahunID');
    $isi = str_pad($n.'.', 4, ' ') . ' ' .
      str_pad($w['MhswID'], 12) . ' '.
      str_pad($w['Nama'], 25) . ' '.
      str_pad($w['TotalSKS'], 4, ' ').
      str_pad($w['IPK'], 5) .' '.
      str_pad($w['BatasStudi'], 6, ' ').
      $CUTI.
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

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa Cuti Kuliah");
TampilkanTahunProdiProgram('akd.lap.mhswcuti', 'Daftar');
if (!empty($tahun)) Daftar();
?>
