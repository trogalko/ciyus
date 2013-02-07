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
  $prd = GetaField('prodi', "ProdiID", $prodi, "Nama");
  $prg = GetaField('program', "ProgramID", $prid, "Nama");
  $thn = GetFields("tahun", "ProgramID='$prid' and ProdiID='$prodi' and TahunID", $tahun, "*");
  $maxcol = 120;
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77).chr(27).chr(108).chr(8));
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
  $hdr = str_pad("*** Daftar Persentase Kehadiran Dosen ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf.
    "Semester      : " . $thn['Nama']. $_lf.
    "Program Studi : " . $prd.$_lf.
    "Periode       : " . "Dari tanggal $_DariTgl s/d $_SampaiTgl".$_lf.
    $div .
    "No. No.Dsn    Nama Dosen                    Jenis     Kode      Nama Matakuliah               Kelas  Persen  Hadir".$_lf.
    $div;
  fwrite($f, $hdr);
  // Data
  $s = "select p.DosenID, d.Nama as DSN, j.Kehadiran as JML, j.RencanaKehadiran, j.JadwalID,
      jj.Nama as JJ, j.MKKode, LEFT(j.Nama, 30) as MK, LEFT(j.NamaKelas, 5) as KLS
    from presensi p
      left outer join jadwal j on p.JadwalID=j.JadwalID
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on p.DosenID=d.Login
    where j.TahunID='$tahun'
      and ('$_SESSION[DariTgl]' <= p.Tanggal) and (p.Tanggal <= '$_SESSION[SampaiTgl]')
      and INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
      $cek
    group by j.MKKode, j.JenisJadwalID Order by p.DosenID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $n = 0;
  $hal = 1;
  $brs = 0;
  $maxbrs = 40;
  // Isi
  while ($w = _fetch_array($r)) {
    //$n++;
    //$Count = GetaField("presensi p", " TahunID = '$tahun' and p.JadwalID=$w[JadwalID] and ('$_SESSION[DariTgl]' <= p.Tanggal) and (p.Tanggal <= '$_SESSION[SampaiTgl]') and p.DosenID",$w['DosenID'],'sum(p.DosenID)');
    $prsn = ($w['RencanaKehadiran'] == 0)? '0' : number_format($w['JML']/$w['RencanaKehadiran']*100, 1);
    $prsn .= '% ';
		if ($_DOSENID != $w['DosenID']){
			$_DOSENID = $w['DosenID'];
			$DOSENID = $_DOSENID;
			$n++;
		} else {
			$DOSENID = '';
		}
		
		if ($_DOSEN != $w['DSN']){
			$_DOSEN = $w['DSN'];
			$DOSEN = $_DOSEN;
		} else $DOSEN = '';
		
		if ($_n != $n){
			$_n = $n;
			$no = $_n.".";
		} else $no = '';
		
    $isi = str_pad($no, 4) . 
      str_pad($DOSENID, 10) .
      str_pad($DOSEN, 30) . 
      str_pad($w['JJ'], 10) .
      str_pad($w['MKKode'], 10) .
      str_pad($w['MK'], 30) . ' '.
      str_pad($w['KLS'], 5) .
      str_pad(number_format($prsn, 2), 7, ' ', STR_PAD_LEFT).'%'.
      str_pad($w['JML'], 6, ' ', STR_PAD_LEFT) .
      $_lf;
    fwrite($f, $isi);
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
