<?php
// Author: Emanuel Setio Dewo
// 18 April 2006
session_start();
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

$urutanjenis = array(0=>"Semua~", 1=>"Kuliah~K", 2=>"Responsi~R");
$_urutanjenis = GetSetVar('_urutanjenis', 0);
$maxbrs = 55;

TampilkanPilihanJenis();
Cetak();

include_once "disconnectdb.php";

function TampilkanPilihanJenis() {
  global $urutanjenis;
  $a = '';
  for ($i=0; $i<sizeof($urutanjenis); $i++) {
    $sel = ($i == $_SESSION['_urutanjenis'])? 'selected' : '';
    $v = explode('~', $urutanjenis[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='lap.hadir.rinci'>
  <input type=hidden name='gos' value='cetak'>
  <input type=hidden name='tahun' value='$_REQUEST[tahun]'>
  <input type=hidden name='prid' value='$_REQUEST[prid]'>
  <input type=hidden name='prodi' value='$_REQUEST[prodi]'>
  <tr><td class=inp>Cetak Berdasarkan: </td>
  <td class=ul><select name='_urutanjenis' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

function Cetak() {
  global $_lf, $urutanjenis, $maxbrs;
  $tahun = $_REQUEST['tahun'];
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
  $prd = GetaField('prodi', "ProdiID", $prodi, "Nama");
  $prg = GetaField('program', "ProgramID", $prid, "Nama");
  $_u = explode('~', $urutanjenis[$_SESSION['_urutanjenis']]);
        $_key = $_u[1];
  $cek = (empty($_key))? '' : "and j.JenisJadwalID = '$_key'";
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f,chr(27).chr(15). chr(27).chr(108).chr(8));
  // Data
  $s = "select j.*, LEFT(j.Nama, 30) as NM
    from jadwal j
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
      $cek 
      and j.NamaKelas <> 'KLINIK'
    order by j.MKKode, j.NamaKelas";
  $r = _query($s);
  $n = 0;
  $hal = 0;
  $brs = 0;
  
  // Buat header
  $div = str_pad('-', 120, '-').$_lf;
  $hdr = str_pad("*** Daftar Persentase Kehadiran Kuliah ***", 120, ' ', STR_PAD_BOTH) .$_lf.
    "Semester    : " . NamaTahun($tahun) . $_lf .
    "Fak/Jur     : " . $prd . ",  ".
    "Program : " . $prg . $_lf. 
    $div.
    "No. Kode      Matakuliah                    Kls Jen Dosen                                         SKS            Persen" .$_lf.
    $div;
  fwrite($f, $hdr);
  // Tampilkan data
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs >= $maxbrs) {
      $hal++; $brs =1;
      fwrite($f, str_pad('-', 120, '-').$_lf);
      fwrite($f, str_pad("Hal. " . $hal, 120, ' ', STR_PAD_LEFT));
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", ',');
    $prsn = ($w['RencanaKehadiran'] == 0) ? 0 : $w['Kehadiran']/$w['RencanaKehadiran']*100;
    fwrite($f,  
      str_pad($n, 4) .
      str_pad($w['MKKode'], 10) .
      str_pad($w['NM'], 30, ' ') . ' '.
      str_pad($w['NamaKelas'], 4).
      str_pad($w['JenisJadwalID'], 3).
      str_pad($dosen, 48) .
      str_pad($w['SKS'],13,' ') .
      str_pad(number_format($prsn, 2), 5, ' ', STR_PAD_LEFT). '%'.
      $_lf);
    $rinci = AmbilRincian($f, $w['JadwalID'], $brs, $hdr);
    //fwrite($f, $rinci);
  }
  $hal++;
  fwrite($f, $div . str_pad("Akhir laporan,  Hal. ". $hal, 120, ' ', STR_PAD_LEFT) . $_lf);
  fwrite($f, "Catatan: Persentase kehadiran dihitung berdasarkan rencana kehadiran.". $_lf);
  fwrite($f, "         Hari libur tidak mengurangi rencana kehadiran.");
  // Tutup file
  fclose($f);
  if ($_REQUEST['prn'] == 1) {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
  else TampilkanFileDWOPRN($nmf, "");
}
function AmbilRincian($f, $jdwlid, &$brs, $hdr) {
  global $_lf, $maxbrs;
  $s = "select date_format(p.Tanggal, '%d/%m/%Y') as TGL, d.Nama, p.Catatan
    from presensi p
      left outer join dosen d on p.DosenID=d.Login
    where p.JadwalID='$jdwlid' order by p.Pertemuan";
  $r = _query($s);
  $arin = array(); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs >= $maxbrs) {
      $hal++; $brs =1;
      fwrite($f, str_pad('-', 120, '-').$_lf);
      fwrite($f, str_pad("Hal. " . $hal, 120, ' ', STR_PAD_LEFT));
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    //$cat = str_replace(chr(13).chr(10,);
    //$arin[] = '              '. $n . '. ' . $w['TGL'] . ' : ' . $w['Nama'];
    fwrite($f,  "               ".
      str_pad("$n.", 5) .
      "$w[TGL] : $w[Nama]". $_lf); 
  }
  return (empty($arin))? '' : implode($_lf, $arin) . $_lf; 
}


?>
