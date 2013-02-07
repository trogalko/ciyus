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
//$_urutanjenis = GetSetVar('_urutanjenis', 0);
$kdosen = GetSetVar('kdosen');
//TampilkanPilihanJenis();

Cetak();
include_once "disconnectdb.php";

function TampilkanPilihanJenis() {
  global $urutanjenis;
  $DariTgl = GetDateOption($_SESSION['DariTgl'], 'DariTgl');
  $SampaiTgl = GetDateOption($_SESSION['SampaiTgl'], 'SampaiTgl');
  $a = '';
  for ($i=0; $i<sizeof($urutanjenis); $i++) {
    $sel = ($i == $_SESSION['_urutanjenis'])? 'selected' : '';
    $v = explode('~', $urutanjenis[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='lap.hadir.rinci.dosen'>
  <input type=hidden name='gos' value='cetak'>
  <input type=hidden name='tahun' value='$_REQUEST[tahun]'>
  <input type=hidden name='prid' value='$_REQUEST[prid]'>
  <input type=hidden name='prodi' value='$_REQUEST[prodi]'>
  <tr><td class=inp>Kode Dosen : </td>
  <td class=ul><input type=text value='$_SESSION[kdosen]' name='kdosen'></td></tr>
  <tr><td class=inp>Dari Tanggal</td>
    <td class=ul>$DariTgl</td>
    <td class=inp>Sampai Tanggal</td>
    <td class=ul>$SampaiTgl</td></tr>
  <tr><td class=inp>Cetak Berdasarkan: </td>
  <td class=ul><select name='_urutanjenis' onChange='this.form.submit()'>$a</select></td></tr>
  <tr><td class=ul><input type=submit name=submit value=Kirim></td></tr>
  </form></table></p>";
}


function Cetak() {
  global $_lf, $_HeaderPrn, $urutanjenis;
  $tahun = $_REQUEST['tahun'];
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
  $prd = GetaField('prodi', "ProdiID", $prodi, "Nama");
  $prg = GetaField('program', "ProgramID", $prid, "Nama");
  $thn = GetFields("tahun", "ProgramID='$prid' and ProdiID='$prodi' and TahunID", $tahun, "*");
  $_u = explode('~', $urutanjenis[$_SESSION['_urutanjenis']]);
        $_key = $_u[1];
  $cek = (empty($_key))? '' : "and j.JenisJadwalID = '$_key'";
  $kd = (empty($_SESSION['kdosen']))? '' : "and p.DosenID = '$_SESSION[kdosen]'";
  if (empty($_SESSION['kdosen'])) {}
  else {
    $NamaDosen = GetaField('dosen','Login',$_SESSION['kdosen'],'Nama');
    $hdrdosen = "Nama          : $NamaDosen" . $_lf;
  } 
  //$tgll = (empty());
  //$tanggalini = ""
  $maxcol = 120;
  // Buat file
  $nmf = "tmp/$prodi.dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(8));
  // Buat Header
  $_DariTgl = FormatTanggal($_SESSION['DariTgl']);
  $_SampaiTgl = FormatTanggal($_SESSION['SampaiTgl']);
  $div = str_pad('-', $maxcol, '-').$_lf;
  $hdr = str_pad("*** Rincian Kehadiran Dosen ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf.
    "Semester      : " . $thn['Nama']. $_lf.
    "Program Studi : " . $prd.$_lf.
    "Periode       : " . "Dari tanggal $_DariTgl s/d $_SampaiTgl".$_lf.
    $hdrdosen;
  $hdr .=  $div .
    "No. No.Dsn    Nama Dosen                    Jenis     Kode      Nama Matakuliah               Kelas  Hadir      SKS".$_lf.
    $div;
  fwrite($f, $hdr);
  // Data
  $s = "select j.JadwalID, j.SKS,
      p.DosenID, d.Nama as DSN, j.Kehadiran as JML, j.RencanaKehadiran,
      jj.Nama as JJ, j.MKKode, LEFT(j.Nama, 30) as MK, LEFT(j.NamaKelas, 5) as KLS
    from presensi p
      left outer join jadwal j on p.JadwalID=j.JadwalID
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on p.DosenID=d.Login
    where j.TahunID='$tahun'
      and ('$_SESSION[DariTgl]' <= p.Tanggal) and (p.Tanggal <= '$_SESSION[SampaiTgl]')
      and INSTR(j.ProgramID, '.$prid.')>0
      and INSTR(j.ProdiID, '.$prodi.')>0
      $kd
      $cek
    group by p.DosenID, j.JadwalID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $n = 0;
  $hal = 0;
  $brs = 0;
  $maxbrs = 35;
  // Isi
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs > $maxbrs) {
      $hal++; $brs =1;
      fwrite($f, str_pad('-', $maxcol, '-').$_lf);
      fwrite($f, str_pad("Hal. " . $hal, $maxcol, ' ', STR_PAD_LEFT));
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $prsn = ($w['RencanaKehadiran'] == 0)? '0' : number_format($w['JML']/$w['RencanaKehadiran']*100, 1);
    $prsn = number_format($prsn, 2);
    $isi_ = BuatRincian($w['JadwalID'], $w['DosenID'],$brs, $counts, $hdr, $maxcol, $hal, $maxbrs);
    $isi = str_pad($n, 4) . 
      str_pad($w['DosenID'], 10) .
      str_pad($w['DSN'], 30) . 
      str_pad($w['JJ'], 10) .
      str_pad($w['MKKode'], 10) .
      str_pad($w['MK'], 30) . ' '.
      str_pad($w['KLS'], 5) .
      str_pad($counts.'/'.$w['RencanaKehadiran'], 6, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['SKS'], 7, ' ', STR_PAD_LEFT).
      $_lf;
    fwrite($f, $isi);
    fwrite($f, $isi_);
  }
  
  fwrite($f, $div . 
    str_pad("Akhir Laporan", $maxcol, ' ', STR_PAD_LEFT) .$_lf);
  fwrite($f, str_pad("Dicetak Tanggal : " . date('d-m-Y H:i'), 50, ' ') .$_lf);  
    fwrite($f, chr(12));
  // Tutup file
  fclose($f);
  if ($_REQUEST['prn'] == 1) {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
  else TampilkanFileDWOPRN($nmf, "");
}
function BuatRincian($jdwlid, $dosenid, &$brs, &$counts, $hdr, $maxcol, &$hal, $maxbrs) {
  global $_lf;
  $s = "select p.Pertemuan, date_format(p.Tanggal, '%d-%m-%Y') as TGL,
    left(p.Catatan, 60) as CTT,
    time_format(p.JamMulai, '%H:%i') as JM
    from presensi p
    where p.JadwalID='$jdwlid'
      and p.DosenID='$dosenid'
      and ('$_SESSION[DariTgl]' <= p.Tanggal) and (p.Tanggal <= '$_SESSION[SampaiTgl]')
    order by p.Pertemuan";
  $r = _query($s);
  $counts = _num_rows($r);
  $isi = ''; $n = 0; $mrg = '              ';
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    $Catatan = str_replace(chr(13), ' ', $w['CTT']);
    $Catatan = str_replace(chr(10), '', $Catatan);
    if ($brs > $maxbrs){
			$hal++; $brs =1;
      $isi .= str_pad('-', $maxcol, '-').$_lf;
      $isi .= str_pad("Hal. " . $hal, $maxcol, ' ', STR_PAD_LEFT);
      $isi .= chr(12);
      $isi .=  $hdr;
		}
		$isi .= $mrg .
      str_pad($n.'.', 4) . ' '.
      str_pad($w['TGL'], 10) . ' '.
      str_pad($w['JM'], 5) . ' '.
      $Catatan. 
      $_lf;
  }
  return $isi.$_lf;
}
?>
