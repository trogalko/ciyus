<?php
// Author: Emanuel Setio Dewo
// 31/10/2006

// *** Functions ***
function DaftarMhswTA() {
  global $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]' ";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) 
    $whr[] = "'$_SESSION[DariNPM]' <= k.MhswID and k.MhswID <= '$_SESSION[SampaiNPM]' ";
  $_whr = empty($whr)? '' : 'and ' . implode(' and ', $whr);
  $s = "select k.MhswID, k.MKKode, LEFT(m.Nama, 30) as Nama,
    khs.JumlahMK, khs.TotalSKS,
    khs.Biaya, khs.Bayar, khs.Tarik, khs.Potongan, khs.JumlahLain
    from krs k
      left outer join mk mk on k.MKID=mk.MKID
      left outer join jenispilihan jp on mk.JenisPilihanID=jp.JenisPilihanID
      left outer join mhsw m on k.MhswID=m.MhswID
      left outer join khs khs on k.KHSID=khs.KHSID
    where jp.TA='Y' and k.TahunID='$_SESSION[tahun]' and khs.JumlahMK=1
      $_whr
    order by k.MhswID";
  $r = _query($s);
  
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  // Buat header
  $mxc = 80;
  $mxb = 50;
  $_prd = (empty($_SESSION['prodi']))? "Semua" : GetaField('prodi', "ProdiID", $_SESSION['prodi'], "concat(ProdiID, ' - ', Nama)");
  $grs = str_pad('-', $mxc, '-').$_lf;
  $hdr = str_pad("** Daftar Mhsw Yang Hanya Ambil Skripsi/TA **", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Tahun Akd : " . $_SESSION['tahun'], $mxc/2). $_lf.
    str_pad("Prodi     : " . $_prd, $mxc/2). $_lf;
  $hdr1 = $grs .
    str_pad('No', 5).
    str_pad('N.P.M', 12).
    str_pad('Nama Mhsw', 30).
    str_pad('Kode MK', 8).
    'Jml MK'.
    str_pad('SKS', 4, ' ', STR_PAD_LEFT).
    str_pad('Balance', 15, ' ', STR_PAD_LEFT).
    $_lf.$grs;
    
  $n = 0; $b = 0;
  fwrite($f, $hdr . $hdr1);
  while ($w = _fetch_array($r)) {
    $n++;
    if ($b >= $mxb) {
      fwrite($f, $grs . chr(12));
      fwrite($f, $hdr . $hdr1);
      $b = 0;
    }
    $b++;
    $_bal = $w['Biaya'] - $w['Potongan'] + $w['Tarikan'] - $w['Bayar'];
    $bal = number_format($_bal);
    fwrite($f, str_pad($n, 5).
      str_pad($w['MhswID'], 12).
      str_pad($w['Nama'], 30).
      str_pad($w['MKKode'], 10).
      str_pad($w['JumlahMK'], 4, ' ', STR_PAD_LEFT).
      str_pad($w['TotalSKS'], 4, ' ', STR_PAD_LEFT).
      str_pad($bal, 15, ' ', STR_PAD_LEFT).
      $_lf);
  }
  fwrite($f, $grs);
  $tgl = date('d-m-Y');
  fwrite($f, "Dicetak oleh: $_SESSION[_Login], $tgl".$_lf);
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
TampilkanJudul("Daftar Mhsw Ambil Skripsi/TA");
TampilkanTahunProdiProgram('akd.lap.mhswta', 'DaftarMhswTA', '', '', 1);
if (!empty($tahun)) DaftarMhswTA();
?>
