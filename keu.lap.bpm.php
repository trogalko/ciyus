<?php
// Author: Emanuel Setio Dewo
// 23 Agustus 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanRentangTanggal($mnux='', $gos='', $kembali='') {
  $DariTgl = GetDateOption($_SESSION['DariTgl'], 'DariTgl');
  $SampaiTgl = GetDateOption($_SESSION['SampaiTgl'], 'SampaiTgl');
  $k = (empty($kembali))? '' : "<input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$kembali'\">";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=inp>Dari Tanggal</td>
    <td class=ul>$DariTgl</td>
    <td class=inp>Sampai Tanggal</td>
    <td class=ul>$SampaiTgl</td>
    <td class=ul><input type=submit name='Tampilkan' value='Tampilkan'> $k</td></tr>
  </form></table></p>";
}

function TampilkanFilterProdi(){
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='keu.lap.bpm'>
  <input type=hidden name='gos' value='Tampilkan'>
  <tr><td class=inp>Prodi : </td>
    <td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td>
  </form></table></p>";
}
function Headernya($mxc, $hal=0) {
  global $_lf;
  $tgl = date('d-m-Y H:i');
  $grs = str_pad('-', $mxc, '-').$_lf;

  return $hdr;
}
function Tampilkan() {
  global $_lf;
  //$QProdi = (!empty($_SESSION['prodi'])) ? "and m.ProdiID = '$_SESSION[prodi]'" : "";
  if (!empty($_SESSION['prodi'])){
    $QProdi = "and m.ProdiID = '$_SESSION[prodi]'";
    $nPRD = GetaField('prodi','ProdiID',$_SESSION['prodi'],"Nama");
    $Jdls = str_pad("Prodi : " . $nPRD, 30, ' ').$_lf;
  } else {
    $QProdi = "";
    $Jdls = '';
  }
  $s = "select bm.*, LEFT(m.Nama, 35) as Nama, Format(bm.Jumlah, 0) as JML, Format(bm.JumlahLain,0) as JMLLAIN, bm.PMBID 
    from bayarmhsw bm
      left outer join mhsw m on bm.MhswID=m.MhswID
    where ('$_SESSION[DariTgl]' <= bm.Tanggal) and (bm.Tanggal <= '$_SESSION[SampaiTgl]')
      and (bm.Jumlah > 0 or bm.JumlahLain > 0)
      $QProdi
      and TrxID = 1
    order by bm.BayarMhswID";
  $r = _query($s); $n = 0; $brs = 0; $hal = 1; $tot = 0;
  $mxb = 55;
  $mxc = 160;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $_tgl = date('d-m-Y H:i');
  $_DariTgl = FormatTanggal($_SESSION['DariTgl']);
  $_SampaiTgl = FormatTanggal($_SESSION['SampaiTgl']);
  $hdr = str_pad("*** Daftar Pembayaran Mahasiswa (BPM) ***", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Dari tanggal $_DariTgl s/d $_SampaiTgl", $mxc, ' ', STR_PAD_BOTH).$_lf.
    $Jdls . 
    $grs.
    str_pad("NO", 8). str_pad("No. BPM", 15).
    str_pad("Tanggal", 12).
    str_pad("NPM/PMBID", 13).
    str_pad("Nama Mahasiswa", 35).
    str_pad("Jumlah", 15, ' ', STR_PAD_LEFT).
    str_pad("Jumlah Lain", 15, ' ',STR_PAD_LEFT).
    " Keterangan". $_lf.
    $grs;
  
  $nmf = "tmp/$_SESSION[_Login].lap.bpm.dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f,
    chr(27).chr(15). // set jenis karakter
    chr(27).chr(108).'5'); // set margin +5
  fwrite($f, $hdr);
  
  while ($w = _fetch_array($r)) {
    $n++;
    $brs++;
    // Ganti halaman
    if ($brs > $mxb) {
      fwrite($f, $grs.str_pad("Dicetak oleh: $_SESSION[_Login], $_tgl", $mxc/2).
        str_pad("Hal: $hal", $mxc/2, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, chr(12));
      $brs = 1;
      $hal++;
      fwrite($f, $hdr);
    }
    if ($w['MhswID'] == '') {
      $NAMA = Getafield('pmb','PMBID',$w['PMBID'],"Nama");
      $ID = $w['PMBID'];
    }
    else {
       $NAMA = $w['Nama'];
       $ID = $w['MhswID'];
    }
    $tgl = FormatTanggal($w['Tanggal']);
    $tot += $w['Jumlah'];
    $totlain += $w['JumlahLain'];
    fwrite($f,
      str_pad($n.'.', 8). 
      str_pad($w['BayarMhswID'], 15) .
      str_pad($tgl, 12).
      str_pad($ID, 13).
      str_pad($NAMA, 35).
      str_pad($w['JML'], 15, ' ', STR_PAD_LEFT).
      str_pad($w['JMLLAIN'],15, ' ', STR_PAD_LEFT).
      " " . $w['Keterangan'].
      $_lf); 
  }
  $_tot = number_format($tot);
  $_totlain = number_format($totlain);
  fwrite($f, $grs);
  
  fwrite($f,
    str_pad("Total : ", 60, ' ', STR_PAD_LEFT).
    str_pad($_tot, 38, ' ', STR_PAD_LEFT). str_pad($_totlain, 15, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, $grs.str_pad("Dicetak oleh: $_SESSION[_Login], $_tgl", $mxc/2).
        str_pad("Hal: $hal", $mxc/2, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'keu.lap');
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
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
$gos = $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Pembayaran Mahasiswa (BPM)");
TampilkanRentangTanggal('keu.lap.bpm', 'Tampilkan', 'keu.lap');
TampilkanFilterProdi();
if (!empty($gos)) $gos();
?>
