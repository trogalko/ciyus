<?php
// Author: Emanuel Setio Dewo
// 27 June 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanRentangTanggal($mnux='') {
  $DTGL = GetDateOption($_SESSION['DTGL'], 'DTGL');
  $STGL = GetDateOption($_SESSION['STGL'], 'STGL');
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=ul colspan=5><b>$_SESSION[KodeID]</td></td></tr>
  <tr><td class=inp>Dari Tanggal</td><td class=ul>$DTGL</td>
    <td class=inp>Sampai</td><td class=ul>$STGL</td>
    <td class=ul><input type=submit name='Tampilkan' value='Tampilkan'></td></tr> 
  </form></table></p>";
}
function TampilkanDeposit() {
  global $_lf;
  $mxb = 55;
  $mxc = 112;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $DariTanggal = FormatTanggal($_SESSION['DTGL']);
  $SampaiTanggal = FormatTanggal($_SESSION['STGL']);
  $hdr = str_pad("Daftar Deposit Mahasiswa", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Periode: $DariTanggal sampai $SampaiTanggal", $mxc, ' ', STR_PAD_BOTH).$_lf.$grs.
    str_pad('No.', 5). str_pad('Tanggal', 12). str_pad('N.P.M', 15). str_pad('Nama Mahasiswa', 30).
    str_pad('Jumlah', 15, ' ', STR_PAD_LEFT).
    str_pad('Dipakai', 15, ' ', STR_PAD_LEFT).
    str_pad('Sisa', 15, ' ', STR_PAD_LEFT).$_lf.$grs;
  // Query
  $s = "select dm.DepositMhswID, dm.Tanggal, dm.MhswID,
    dm.Jumlah, dm.Dipakai, (dm.Jumlah - dm.Dipakai) as Sisa, dm.Tutup,
    LEFT(m.Nama, 29) as Nama, m.ProdiID
    from depositmhsw dm
      left outer join mhsw m on dm.MhswID=m.MhswID
    where '$_SESSION[DTGL]' <= dm.Tanggal and dm.Tanggal <= '$_SESSION[STGL]'
    order by dm.Tanggal";
  $r = _query($s); $n = 0; $b = 0;
  
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, $hdr);
  $_Jumlah = 0; $_Dipakai = 0; $_Sisa = 0;
  while ($w = _fetch_array($r)) {
    $n++; $b++;
    if ($b >= $mxb) {
      $mxb = 0;
      fwrite($f, $grs.chr(12));
      fwrite($f, $hdr);
    }
    $TGL = FormatTanggal($w['Tanggal']);
    // Jumlahkan
    $_Jumlah += $w['Jumlah'];
    $_Dipakai += $w['Dipakai'];
    $_Sisa += $w['Sisa'];
    // Format tampilan
    $Jumlah = number_format($w['Jumlah']);
    $Dipakai = number_format($w['Dipakai']);
    $Sisa = number_format($w['Sisa']);
    $Tutup = ($w['Sisa'] == 0)? chr(215) : '';
    fwrite($f, str_pad($n, 5).
      str_pad($TGL, 12).
      str_pad($w['MhswID'], 15).
      str_pad($w['Nama'], 30).
      str_pad($Jumlah, 15, ' ', STR_PAD_LEFT).
      str_pad($Dipakai, 15, ' ', STR_PAD_LEFT).
      str_pad($Sisa, 15, ' ', STR_PAD_LEFT).
      str_pad($Tutup, 4, ' ', STR_PAD_LEFT).
      $_lf);
  }
  $__Jumlah = number_format($_Jumlah);
  $__Dipakai = number_format($_Dipakai);
  $__Sisa = number_format($_Sisa);
  fwrite($f, $grs);
  fwrite($f, str_pad("Jumlah : ", 62, ' ', STR_PAD_LEFT).
    str_pad($__Jumlah, 15, ' ', STR_PAD_LEFT).
    str_pad($__Dipakai, 15, ' ', STR_PAD_LEFT).
    str_pad($__Sisa, 15, ' ', STR_PAD_LEFT).$_lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'klinik.lap');
}


// *** Parameters ***
// Dari tanggal
$DTGL_y = GetSetVar('DTGL_y', date('Y'));
$DTGL_m = GetSetVar('DTGL_m', date('m'));
$DTGL_d = GetSetVar('DTGL_d', 1);
$DTGL = "$DTGL_y-$DTGL_m-$DTGL_d";
$_SESSION['DTGL'] = $DTGL;
// Sampai tanggal
$STGL_y = GetSetVar('STGL_y', date('Y'));
$STGL_m = GetSetVar('STGL_m', date('m'));
$STGL_d = GetSetVar('STGL_d', 30);
$STGL = "$STGL_y-$STGL_m-$STGL_d";
$_SESSION['STGL'] = $STGL;

// *** Main ***
TampilkanJudul("Daftar Deposit Mahasiswa");
TampilkanRentangTanggal('klinik.lap.deposit');
TampilkanDeposit();
?>
