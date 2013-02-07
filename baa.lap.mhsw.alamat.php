<?php
// Author: Emanuel Setio Dewo
// 20 November 2006

// *** parameters ***
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

// *** Main ***
TampilkanJudul("Data Alamat Mahasiswa");
TampilkanRentangNPM($_SESSION['mnux'], "AlamatMhsw");
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

// *** Functions ***
function TampilkanRentangNPM($mnux='', $gos='') {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul colspan=5><font size=+1>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50></td>
      <td class=inp>Sampai NPM</td>
      <td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50></td>
      <td class=ul><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table></p>";
}
function AlamatMhsw() {
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) AlamatMhsw1();
  else echo ErrorMsg("Tidak Dapat Ditampilkan",
    "Data tidak dapat ditampilkan. Isikan Rentang NPM Dari dan Sampai NPM.");
}
function AlamatMhsw1() {
  global $_lf;
  $s = "select m.MhswID, LEFT(m.Nama, 30) as Nama, m.StatusMhswID,
    LEFT(m.Alamat, 50) as Alamat, m.Kota, m.Propinsi, m.Telepon, m.Telephone, m.Email,
    m.RT, m.RW, m.KodePos
    from mhsw m
    where '$_SESSION[DariNPM]' <= m.MhswID
      and m.MhswID <= '$_SESSION[SampaiNPM]'
    order by m.MhswID";
  $r = _query($s);
  
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77));
  
  // parameter
  $mxc = 160;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-').$_lf;
  // header
  $hdr = str_pad("** Daftar Mahasiswa **", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Rentang NPM: $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM]", $mxc, ' ', STR_PAD_BOTH).$_lf.
    $grs.
    str_pad("No", 6).
    str_pad("N.P.M", 15).
    str_pad("Nama Mahasiswa", 30).
    str_pad("Telepon", 20).
    str_pad("Alamat", 50).
    str_pad("RT/RW", 10) .
    str_pad("Kodepos",10) .
    str_pad("Kota", 20).
    $_lf.$grs;
  fwrite($f, $hdr);
  
  // Data mhsw
  $n = 0; $hal = 1; $brs = 0;
  while ($w = _fetch_array($r)) {
    if ($brs >= $mxb) {
      $brs = 0;
      fwrite($f, str_pad("Bersambung...", $mxc, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $n++; $brs++;
    fwrite($f, str_pad($n.'.', 6).
      str_pad($w['MhswID'], 15).
      str_pad($w['Nama'], 30).
      str_pad($w['Telephone'], 20).
      str_pad($w['Alamat'], 50).
      str_pad($w['RT'].'/'.$w['RW'], 10) .
      str_pad($w['KodePos'],10) .
      str_pad($w['Kota'], 20).
      $_lf);
  }
  fwrite($f, $grs);
  fwrite($f, "Dicetak oleh: $_SESSION[_Login]".$_lf);
  fwrite($f, chr(12).chr(27));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'baa.lap', 0); 
}
?>
