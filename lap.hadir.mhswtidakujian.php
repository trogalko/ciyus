<?php
// Author: Emanuel Setio Dewo
// 19 April 2006
session_start();
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
$DariTgl_m = GetSetVar('DariTgl_m', date('m'));
$DariTgl_d = GetSetVar('DariTgl_d', date('d'));
$DariTgl_y = GetSetVar('DariTgl_y', date('Y'));
$DariTgl = "$DariTgl_y-$DariTgl_m-$DariTgl_d";
$_SESSION['DariTgl'] = $DariTgl;

$SampaiTgl_d = GetSetVar('SampaiTgl_d', date('d'));
$SampaiTgl_m = GetSetVar('SampaiTgl_m', date('m'));
$SampaiTgl_y = GetSetVar('SampaiTgl_y', date('Y'));
$SampaiTgl = "$SampaiTgl_y-$SampaiTgl_m-$SampaiTgl_d";
$_SESSION['SampaiTgl'] = $SampaiTgl;

$urutanjenis = array(0=>"Semua~", 1=>"Kuliah~K", 2=>"Responsi~R");

Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_lf, $_HeaderPrn, $urutanjenis;
  $tahun = $_REQUEST['tahun'];
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
  $NamaTahun = NamaTahun($tahun, $prodi);
  $prd = GetaField('prodi', "ProdiID", $prodi, "Nama");
  $prg = GetaField('program', "ProgramID", $prid, "Nama");
  $thn = GetFields("tahun", "ProgramID='$prid' and ProdiID='$prodi' and TahunID", $tahun, "*");
  $maxcol = 120;
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(8));
  $_DariTgl = FormatTanggal($_SESSION['DariTgl']);
  $_SampaiTgl = FormatTanggal($_SESSION['SampaiTgl']);
  $a = '';
  for ($i=0; $i<sizeof($urutanjenis); $i++) {
    $sel = ($i == $_SESSION['_urutanjenis'])? 'selected' : '';
    $v = explode('~', $urutanjenis[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  $_u = explode('~', $urutanjenis[$_SESSION['_urutanjenis']]);
        $_key = $_u[1];
  $cek = (empty($_key))? '' : "and j.JenisJadwalID = '$_key'";
  //fwrite($f, $_HeaderPrn);
  // Buat Header
  $div = str_pad('-', $maxcol, '-').$_lf;
  $div1 = str_pad('-', $maxcol, '-').$_lf;
  $hdr = str_pad("** Laporan Mahasiswa Tidak Boleh Ujian $tahun **", $maxcol, ' ', STR_PAD_BOTH).$_lf.
    "Program Studi : $prd" . $_lf.
    "Tahun Akd.    : $NamaTahun ". $_lf.
      $div.str_pad('No.', 5).
      str_pad('N.P.M', 15).
      str_pad('Nama Mhsw', 30).
      str_pad('Kode', 10).
      str_pad('Matakuliah', 32).
      str_pad('Kelas', 5).
      str_pad('Hadir', 8, ' ', STR_PAD_LEFT).
	  str_pad('% Hadir', 9, ' ', STR_PAD_LEFT).
      $_lf.$div;
  fwrite($f, $hdr);
  // Data
  
  $s="SELECT pm.mhswid AS Nim, LEFT( m.nama, 30 ) AS NamaMahasiswa, khs.prodiid, j.mkkode AS MK, LEFT( j.Nama, 30 ) AS NamaMK, j.NamaKelas, SUM( pm.nilai ) AS NIL, j.kehadiran, FORMAT( (
SUM( pm.nilai ) / j.kehadiran ) *100, 0
) AS HDR
FROM `presensimhsw` pm
LEFT OUTER JOIN jadwal j ON pm.jadwalid = j.jadwalid
LEFT OUTER JOIN mhsw m ON pm.mhswid = m.mhswid
LEFT OUTER JOIN krs k ON pm.krsid = k.krsid
LEFT OUTER JOIN khs khs ON k.khsid = khs.khsid
WHERE j.tahunid = '$tahun'
AND khs.ProdiId = '$_SESSION[prodi]'
GROUP BY pm.mhswid, pm.jadwalid
HAVING HDR < 80";
  $r = _query($s);
  $n = 0; $n1 = 0;
  $hal = 1;
  $brs = 0;
  $maxbrs = 55;
  // Isi
  $mid = 'qwertyuiopasdfghjklzxcvbnm';
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($mid != $w['MhswID']) {
      $n1++; $brs++;
      if ($n != 1) fwrite($f, $div1);
      $nomer = $n1;
      $mid = $w['MhswID'];
      $MhswID = $mid;
      $NamaMhsw = $w['Nama'];
	  
    }
    else {
      $nomer = '';
      $MhswID = '';
      $NamaMhsw = '';
    }
    if ($brs >= $maxbrs) {
      $brs = 0;
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    fwrite($f, str_pad($n, 5). 
      str_pad($w['Nim'], 15). 
      str_pad($w['NamaMahasiswa'], 30).
      str_pad($w['MK'], 10).
      str_pad($w['NamaMK'], 32).
      str_pad($w['NamaKelas'], 5).
      str_pad($w['NIL'].'/'.$w['kehadiran'], 8, ' ', STR_PAD_LEFT).
      str_pad($w['HDR'].'%', 9, ' ', STR_PAD_LEFT).
	 
      $_lf);
  }
  
  fwrite($f, $div . 
    str_pad("Akhir Laporan", $maxcol, ' ', STR_PAD_LEFT) .$_lf);
    fwrite($f, chr(12));
  // Tutup file
  fclose($f);
  if ($_REQUEST['prn'] == 1) {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
  else TampilkanFileDWOPRN($nmf, "");
}
?>
