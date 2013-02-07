<?php
// Author: Emanuel Setio Dewo
// 05 Sept 2006
// www.sisfokampus.net
session_start();
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";

Cetak();

include_once "disconnectdb.php";

function Cetak() {
  $_lf = chr(13).chr(10);
  $id = $_REQUEST['id'];
  $jdwl = GetFields("jadwal j
    left outer join dosen d on j.DosenID=d.Login",
    "j.JadwalID", $id, "j.*, concat(d.Nama, ', ', d.Gelar) as DSN");
  $_prodi = TRIM($jdwl['ProdiID'], '.');
  $_prodi = explode('.', $_prodi);
  $_prodi = $_prodi[0];
  $prodi = GetFields('prodi', 'ProdiID', $_prodi, "ProdiID, Nama, FakultasID");
  $fak = GetaField('fakultas', 'FakultasID', $prodi['FakultasID'], 'Nama'); 
  
  $nmf = "tmp/$_SESSION[_Login].jdwl.php";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  fwrite($f, chr(27).chr(108).chr(5));
  
  $mxc = 135;
  $mxb = 21;
  $grs = str_pad("-", $mxc, "-").$_lf;
  
  $s = "select k.MhswID, left(m.Nama, 31) as Nama
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
      left outer join khs khs on k.KHSID=khs.KHSID
    where k.JadwalID=$id
    order by k.MhswID";
  $r = _query($s);
	//echo "<pre>$r</pre>";
	//exit;
  $n = 0;
  $brs = 0;
  $hal = 1;
  $satu = Garis(12, '-');
  $dua = Garis(12);
  $header0 = str_pad('** Daftar Penilaian Portofolio & Performance ***', $mxc, ' ', STR_PAD_BOTH). $_lf; 
  $header = $grs . str_pad(' ', 50, ' ') . GarisHeader(12).$_lf.
    str_pad(' ', 50, ' ') . $satu . $_lf .
    str_pad("No.  N.P.M        Nama Mahasiswa ", 50, ' '). Kosong(12).
    str_pad('=', 50, '=') . Garis(12, '=') . $_lf;
  fwrite($f, $header0);
  $header1 = BuatHeader($mxc, $jdwl, $prodi, $fak, $hal);
  fwrite($f, $header1);
  fwrite($f, $header);
  
  while ($w = _fetch_array($r)) {
    $n++;
    $brs++;
    if ($brs >= $mxb) {
      $hal++;
      $brs = 1;
      fwrite($f, "Dicetak oleh: $_SESSION[_Login]");
      fwrite($f, chr(12));
      fwrite($f, $header0);
      fwrite($f, BuatHeader($mxc, $jdwl, $prodi, $fak, $hal));
      fwrite($f, $header1);
      fwrite($f, $header);
    }
    fwrite($f, str_pad($n, 5).
      str_pad($w['MhswID'], 13).
      str_pad($w['Nama'], 32).
      $dua.
      $_lf);
    fwrite($f, str_pad('-', 50, '-'). 
      $satu.$_lf);
  }
  fwrite($f, str_pad("Akhir cetakan", $mxc, ' ', STR_PAD_LEFT).$_lf);
  for ($i = $brs; $i <= $mxb; $i++) {
    fwrite($f, $_lf.$_lf);
  }
  fwrite($f, "Dicetak oleh: $_SESSION[_Login]");
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}
function BuatHeader($mxc, $jdwl, $prodi, $fak, $hal) {
  $_lf = chr(13).chr(10);
  $_prg = TRIM($jdwl['ProgramID'], '.');
  $_prg = explode('.', $_prg);
  $_prg = $_prg[0];
  
  $NamaTahun = GetaField('tahun', "TahunID='$jdwl[TahunID]' and ProgramID='$_prg' and ProdiID",
    $prodi['ProdiID'], 'Nama');
  $tgl = str_pad(date('d-m-Y'), 10);
  $jam = str_pad(date('H:i'), 10);
  $s = 
    str_pad("Semester   : $NamaTahun", $mxc/2). 
      str_pad("TGL   : $tgl", $mxc/2, ' ', STR_PAD_LEFT). $_lf.
    str_pad("Fak/Jur    : $fak/$prodi[Nama]", $mxc/2).
      str_pad("Jam   : $jam", $mxc/2, ' ', STR_PAD_LEFT).$_lf.
    str_pad("Kode M.K.  : $jdwl[MKKode] - $jdwl[Nama]", $mxc/2).
      str_pad("Form  : AKD610    ", $mxc/2, ' ', STR_PAD_LEFT).$_lf.
    str_pad("Dosen      : $jdwl[DSN]", $mxc/2).
      str_pad("Hal.  : ". str_pad($hal, 10), $mxc/2, ' ', STR_PAD_LEFT).$_lf;
  return $s;
}
function GarisHeader($j) {
  $s = '';
  for ($i = 0; $i < $j; $i++) {
    $n = $i+1;
    $_n = str_pad($n, 2, '0', STR_PAD_LEFT);
    $s .= "|" . str_pad($_n, 6, ' ', STR_PAD_BOTH);
  }
  return $s . '|';
}

function Garis($j, $c = ' ') {
  $s = '';
  for ($i = 0; $i < $j; $i++) {
    $s .= str_pad('|', 7, $c);
  }
  return $s . '|';
}
function Kosong($j) {
  $s = Garis($j) . chr(13). chr(10);
  return $s;
}
?>
