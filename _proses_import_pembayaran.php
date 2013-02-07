<?php
// Author: Emanuel Setio Dewo
// 2006-09-19
function DeFormatTanggal($tgl) {
  $_d = substr($tgl, 0, 2);
  $_m = substr($tgl, 3, 2);
  $_y = substr($tgl, 6, 4);
  return "$_y-$_m-$_d";
}
function ProsesSekarang() {
  $s = "select * from _pembayaran where RESTAMSTAR='A' order by NIMHSMSTAR";
  $r = _query($s);
  $jml = _num_rows($r);
  echo "<p>Jumlah yg akan diproses: <font size=+1>$jml</font></p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $byr = number_format($w['NLBPMMSTAR']);
    $jml = $w['NLBPMMSTAR'] - $w['NLLN2MSTAR'] +0;
    $TglBank = DeFormatTanggal($w['TGBANMSTAR']);
    $TglBuat = DeFormatTanggal($w['TGBPMMSTAR']);
    // Cek apakah BPM sudah ada?
    $ada = GetFields('bayarmhsw', 'BayarMhswID', $w['NOBPMMSTAR'], '*');
    if (empty($ada)) {
      // Tampilkan
      echo "<li>$w[NOBPMMSTAR] &raquo; 
        $w[NIMHSMSTAR] &raquo; $w[NOTESMSTAR] = 
        <font size=+1>$byr</font>
        </li>";
      // Insert
      $s1 = "insert into bayarmhsw
        (BayarMhswID, TahunID, RekeningID, 
        PMBMhswID, MhswID, TrxID,
        Bank, BuktiSetoran,
        Tanggal, Jumlah, JumlahLain,
        Proses, Keterangan,
        LoginBuat, TanggalBuat)
        values
        ('$w[NOBPMMSTAR]', '$w[THSMSMSTAR]', 'Import-20061',
        1, '$w[NIMHSMSTAR]', 1,
        '$w[KDBANMSTAR]', '$w[NOTESMSTAR]',
        '$TglBank', $jml, $w[NLLN2MSTAR]+0,
        1, 'IMPORT-20061',
        'IMPORT-20061', '$TglBuat')";
      //echo $s1;
      $r1 = _query($s1);
    }
    else echo "<li>$w[NOBPMMSTAR] &raquo; XXX</li>";
  }
  echo "</ol>";
}

function TanyaDulu() {
  echo "<p>Script ini akan mengimport tabel MSTAR dari program lama ke Sisfo Kampus.<br />
  Harap diperhatikan bahwa tabel temporary <b>_pembayaran</b> harus sudah terisi dari tabel MSTAR.<br />
  Tekan tombol berikut ini untuk memulai proses:
  <input type=button name='Proses' value='Proses Pembayaran' onClick=\"location='?gos=ProsesSekarang'\">";
}

// *** Main ***
$gos = (empty($_REQUEST['gos']))? "TanyaDulu" : $_REQUEST['gos'];
include_once "sisfokampus.php";
HeaderSisfoKampus("Import Pembayaran dari Tabel _pembayaran");
TampilkanJudul("Import Pembayaran dari Tabel _pembayaran");
$gos();

?>
