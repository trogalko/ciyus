<?php
// Author: Emanuel Setio Dewo
// 27 June 2006
// www.sisfokampus.net

function DaftarRekapDeposit() {
  global $_lf;
  $mxb = 55;
  $mxc = 80;
  $grs = str_pad('-', $mxc, '-').$_lf;
  // query
  $s = "select dm.MhswID, sum(dm.Jumlah-dm.Dipakai) as Sisa, LEFT(m.Nama, 39) as Nama
    from depositmhsw dm
      left outer join mhsw m on dm.MhswID=m.MhswID
    where (dm.Jumlah-dm.Dipakai) >0
    group by dm.MhswID";
  $r = _query($s); $n = 0; $b = 0; $ttl = 0;
  // Header
  $hdr = str_pad("Rekapitulasi Deposit Mahasiswa", $mxc, ' ', STR_PAD_BOTH).$_lf.$grs.
    str_pad('No.', 5). str_pad('N.P.M', 15). str_pad('Nama', 40). 
    str_pad('Deposit', 20, ' ', STR_PAD_LEFT).
    $_lf.$grs;
  // buat isi
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    $n++; $b++;
    if ($b >= $mxb) {
      $b = 0;
      fwrite($f, $grs.chr(12));
      fwrite($f, $hdr);
    }
    $Sisa = number_format($w['Sisa']);
    $ttl += $w['Sisa'];
    fwrite($f, str_pad($n, 5).
      str_pad($w['MhswID'], 15).
      str_pad($w['Nama'], 40).
      str_pad($Sisa, 20, ' ', STR_PAD_LEFT).
      $_lf);
  }
  $_ttl = number_format($ttl);
  fwrite($f, $grs);
  fwrite($f, str_pad("Total : ", 60, ' ', STR_PAD_LEFT). str_pad($_ttl, 20, ' ', STR_PAD_LEFT).$_lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'klinik.lap');
}

TampilkanJudul("Rekapitulasi Deposit Mahasiswa");
DaftarRekapDeposit();
?>
