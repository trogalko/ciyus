<?php
// Author: Emanuel Setio Dewo
// 25 June 2006
// www.sisfokampus.net

// *** Functions ***
function DaftarHonorer() {
  global $_lf;
  $mxb = 55; $hal = 1;
  $mxc = 80;
  $grs = str_pad("-", $mxc, '-').$_lf;
  $hdr = str_pad("Daftar Semua Dosen Honorer (Urut Nama)", $mxc, ' ', STR_PAD_BOTH). $_lf.$grs.
    str_pad('No.', 5).
    str_pad('Kode', 10).
    str_pad('Nama Dosen', 50).
    str_pad('Homebase', 10).$_lf.$grs;
  
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).$_lf);
  fwrite($f, $hdr);
  $Urut = ($_REQUEST['Urut']+0 == 0)? "order by d.Nama" : "order by d.Login";
  $s = "select d.Login, d.Nama, d.Gelar, d.Homebase
    from dosen d
    where d.StatusDosenID='H'
    $Urut";
  $r = _query($s); $n = 0; $brs = 0;
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$mxb);
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs > $mxb) {
      $brs = 0; $hal++;
      fwrite($f, $grs);
      fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    fwrite($f, str_pad($n, 5).
      str_pad($w['Login'], 10).
      str_pad($w['Nama'].', '.$w['Gelar'], 50).
      str_pad($w['Homebase'], 10).
      $_lf);
  }
  fwrite($f, $grs);
  fwrite($f, "Jumlah Dosen: $n orang $_lf"); 
  fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf); 
  fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 45, ' ') . str_pad("Dicetak Tgl : " . date("d-m-Y H:i"), 35,' ', STR_PAD_LEFT).$_lf.$_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Semua Dosen Honorer");
//TampilkanTahunProdiProgram('akd.lap.dosenhonorer', 'DaftarHonorer');
if (!empty($tahun)) DaftarHonorer();
?>
