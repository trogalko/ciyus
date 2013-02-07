<?php
// Author: Emanuel Setio Dewo
// 18 April 2006
session_start();
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_lf;
  $tahun = $_REQUEST['tahun'];
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
  $prd = GetaField('prodi', "ProdiID", $prodi, "Nama");
  $prg = GetaField('program', "ProgramID", $prid, "Nama");
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  // Data
  $s = "select j.*, LEFT(j.Nama, 30) as NM
    from jadwal j
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
      and j.NamaKelas <> 'KLINIK'
    order by j.MKKode, j.NamaKelas";
  $r = _query($s);
  $n = 0;
  $hal = 1;
  $brs = 0;
  $maxbrs = 40;
  // Buat header
  $div = str_pad('-', 120, '-').$_lf;
  $hdr = str_pad("*** Daftar Persentase Kehadiran Kuliah ***", 120, ' ', STR_PAD_BOTH) .$_lf.
    "Semester    : " . NamaTahun($tahun) . $_lf .
    "Fak/Jur     : " . $prd . ",  ".
    "Program : " . $prg . $_lf. 
    $div.
    "No. Kode      Matakuliah                    Kls Jen Dosen                                                        Persen" .$_lf.
    $div;
  fwrite($f, $hdr);
  // Tampilkan data
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs > $maxbrs) {
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
    fwrite($f, str_pad($n, 4) .
      str_pad($w['MKKode'], 10) .
      str_pad($w['NM'], 30, ' ') . ' '.
      str_pad($w['NamaKelas'], 4).
      str_pad($w['JenisJadwalID'], 3).
      str_pad($dosen, 62) .
      str_pad(number_format($prsn, 2), 5, ' ', STR_PAD_LEFT). '%'.
      $_lf);
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
?>
