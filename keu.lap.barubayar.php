<?php
// Author: Emanuel Setio Dewo
// 05 May 2006
// http://www.sisfokampus.net

// *** Functions ***
function Daftar() {
  global $_HeaderPrn, $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "p.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "p.ProgramID='$_SESSION[prid]'";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  // Query
  $s = "select byr.*, LEFT(p.Nama, 25) as Nama,
    p.ProdiID
    from bayarmhsw byr
      left outer join pmb p on byr.PMBID=p.PMBID
      left outer join prodi prd on p.ProdiID=prd.ProdiID
    where p.PMBPeriodID='$_SESSION[tahun]'
      and byr.PMBMhswID=0
    $_whr
    order by p.ProdiID, byr.PMBID ";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $MaxCol = 114;
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  // parameter2
  $n = 0; $hal = 0;
  $brs = 0;
  $maxbrs = 50;
  // Buat header
  $hdr = str_pad("*** Daftar Pembayaran Mahasiswa Baru ***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf;
  $hdr .= $div;
  $hdr .= "No.  PMB ID       Nama                      NPM             Tanggal            Nilai BPM     Total Bayar".$_lf.$div;
  fwrite($f, $hdr);
  // Tampilkan
  $pmbid = '';
  $prd = '';
  $TotalBiaya = 0;
  $TotalBayar = 0;
  while ($w = _fetch_array($r)) {
    $brs++;
    if ($prd != $w['ProdiID']) {
      $brs++;
      $prd = $w['ProdiID'];
      $_prd = GetaField('prodi', "ProdiID", $prd, "Nama");
      fwrite($f, $_lf . chr(187) . " $prd - $_prd" . $_lf);
    }
    if ($brs >= $maxbrs) {
      $brs = 0;
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    if ($pmbid != $w['PMBID']) {
      $n++;
      $_n = $n;
      $pmbid = $w['PMBID'];
      $_pmbid = $pmbid;
      $_nama = $w['Nama'];
      $BiayaMhsw = GetaField('pmb', 'PMBID', $pmbid, "TotalBiayaMhsw")+0;
      $TBIA = number_format($BiayaMhsw);
      $TotalBiaya += $BiayaMhsw;
    }
    else {
      $_n = '';
      $_pmbid = '';
      $_nama = '';
      $TBIA = '';
    }
    $TotalBayar += $w['Jumlah'];
    $TBYR = number_format($w['Jumlah']);
    $Tgl = FormatTanggal($w['Tanggal']);
    $isi = str_pad($_n, 4, ' ') . ' ' .
      str_pad($_pmbid, 12, ' ') . ' '.
      str_pad($_nama, 25) . ' '.
      str_pad($w['MhswID'], 15, ' ').' '.
      str_pad($Tgl, 12). ' '.
      str_pad($TBIA, 15, ' ', STR_PAD_LEFT).' '.
      str_pad($TBYR, 15, ' ', STR_PAD_LEFT);
    fwrite($f, $isi.$_lf);
  }
  fwrite($f, $div);
  $_TotalBiaya = number_format($TotalBiaya);
  $_TotalBayar = number_format($TotalBayar);
  fwrite($f, str_pad('Total :', 72, ' ', STR_PAD_LEFT). ' '.
    str_pad($_TotalBiaya, 15, ' ', STR_PAD_LEFT) . ' '.
    str_pad($_TotalBayar, 15, ' ', STR_PAD_LEFT) . $_lf);
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " . date("d-m-Y H:i"), 100) . str_pad("Akhir Laporan", 100).$_lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf, "keu.lap");
}


// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Pembayaran Mahasiswa");
TampilkanTahunProdiProgram('keu.lap.barubayar', 'Daftar');
if (!empty($tahun)) Daftar();

?>
