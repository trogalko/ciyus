<?php
// Author: Emanuel Setio Dewo
// 05 Sept 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanStatus(){
  $optStat = GetOption2("statusmhsw","Concat(StatusMhswID, ' - ', Nama)", "StatusMhswID", $_SESSION['status'],"StatusMhswID in ('A','P','C')",'StatusMhswID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
        <form action='?' method=post>
        <input type=hidden name=mnux value='keu.lap.mhswhutang'>
        <input type=hidden name=gos value='TampilkanLaporan'>
        <tr>
        <tr><td class=inp>Status Mahasiswa</td><td class=ul><select name='status' onChange='this.form.submit()'>$optStat</select></td></tr> 
        </form></table></p>";
}
function GetTotal($mhsw, $tahun) {
  $s = "select sum(Besar * Jumlah) as TOT from bipotmhsw where MhswID = '$mhsw' and TahunID = '$tahun' and TrxID=1";
  $r = _query($s);
  $w = _fetch_array($r);
  return (int)$w['TOT'];
}

function GetBayar($mhsw, $tahun) {
  $s = "select sum(Dibayar) as BYR from bipotmhsw where MhswID = '$mhsw' and TahunID = '$tahun' and TrxID=1";
  $r = _query($s);
  $w = _fetch_array($r);
  return (int)$w['BYR'];
}

function GetPot($mhsw, $tahun){
  $s = "select sum(Besar * Jumlah) as POT from bipotmhsw where MhswID = '$mhsw' and TahunID = '$tahun' and TrxID=-1";
  $r = _query($s);
  $w = _fetch_array($r);
  return (int)$w['POT'];
}
function TampilkanLaporan() {
  global $_lf;
  $stat = (!empty($_SESSION['status'])) ? "and k.StatusMhswID = '$_SESSION[status]'" : "and k.StatusMhswID in ('P','A','C')";
  $whr = array();
  if (!empty($_SESSION['prid'])) $whr[] = "k.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $whr[] = "k.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))
    $whr[] = "'$_SESSION[DariNPM]' <= k.MhswID and k.MhswID <= '$_SESSION[SampaiNPM]'";
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : " and $_whr ";
  $hut = $_SESSION['hut'];
  $kode = ($hut == -1)? ">" : "<";
  $strtotal = ($hut == -1)? "Hutang" : "Kelebihan";
  $s = "select k.TahunID, k.MhswID, k.ProgramID, k.ProdiID,
    k.SaldoAwal, k.Biaya, k.Potongan, k.Bayar, k.Tarik,
    k.TotalSKS, k.JumlahMK, k.IPS, k.IP,
    (k.Biaya -k.Bayar +k.Tarik -k.Potongan) as BAL,
    (-k.Biaya +k.Bayar -k.Tarik +k.Potongan) as LBH,
    LEFT(m.Nama, 30) as Nama, k.StatusMhswID
    from khs k
      left outer join mhsw m on m.MhswID=k.MhswID
    where k.TahunID='$_SESSION[tahun]'
      and k.KodeID='$_SESSION[KodeID]' 
      
      $stat
      $_whr
    order by k.MhswID";
  $r = _query($s);
  
  $nmf = "tmp/$_SESSION[_Login].keu.dwoprn";
  $f = fopen($nmf, 'w');
  $mxc = 90;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-') . $_lf;
  fwrite($f, chr(27).chr(15));
  // buat header
  $_prodi = (empty($_SESSION['prodi']))? "Semua Prodi" : GetaField('prodi', 'ProdiID', $_SESSION['prodi'], "Nama");
  $_prid = (empty($_SESSION['prid']))? "Semua Program" : GetaField('program', 'ProgramID', $_SESSION['prid'], "Nama");
  $_rentang = (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM']))?
    "Rentang  : $_SESSION[DariNPM] s.d $_SESSION[SampaiNPM]".$_lf : '';
  $judul = ($hut == -1)? "Berhutang" : "Kelebihan Bayar";
  $hdr = str_pad("** Daftar Mahasiswa yg $judul **", $mxc, ' ', STR_PAD_BOTH).$_lf. $_lf .
    "Tahun    : " . NamaTahun($_SESSION['tahun']) . $_lf .
    "Program  : $_prid " . $_lf.
    "Prodi    : $_prodi" . $_lf.
    $_rentang.
    $grs.
    str_pad('No.', 5).
    str_pad('N.P.M', 12).
    str_pad('Nama Mahasiswa', 32).
    "Ambil  SKS   STATUS". 
    str_pad('Jumlah', 15, ' ', STR_PAD_LEFT).$_lf.
    $grs;
  fwrite($f, $hdr);
  // tuliskan
  $n = 0; $b = 0; $tot = 0; $hal = 1;
  while ($w = _fetch_array($r)) {
    $TOTSS = GetTotal($w['MhswID'], $_SESSION['tahun']) - GetBayar($w['MhswID'], $_SESSION['tahun']) - GetPot($w['MhswID'], $_SESSION['tahun']);
    if ($TOTSS > 0 and $hut == -1) {
    $TOTSS = number_format($TOTSS);
    if ($b >= $mxb) {
      $b = 0;
      $tgl = date('d-m-Y H:i');
      fwrite($f, "Dicetak oleh $_SESSION[_Login], $tgl".$_lf); 
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $n++; $b++;
    
    $tot += ($hut == -1)? $w['BAL'] : $w['LBH'];
    $_bal = ($hut == -1)? $w['BAL'] : $w['LBH'];
    $bal = number_format($_bal);
    fwrite($f, str_pad($n, 5).
      str_pad($w['MhswID'], 12).
      str_pad($w['Nama'], 32).
      str_pad($w['JumlahMK'], 5, ' ', STR_PAD_LEFT).
      str_pad($w['TotalSKS'], 5, ' ', STR_PAD_LEFT).
      str_pad($w['StatusMhswID'], 7, ' ', STR_PAD_LEFT).
      str_pad($TOTSS, 17, ' ', STR_PAD_LEFT).
      $_lf);
    } else if ($TOTSS < 0 and $hut == 1){
      $TOTSS = number_format($TOTSS * -1);
    if ($b >= $mxb) {
      $b = 0;
      $tgl = date('d-m-Y H:i');
      fwrite($f, "Dicetak oleh $_SESSION[_Login], $tgl".$_lf); 
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $n++; $b++;
    
    $tot += ($hut == -1)? $w['BAL'] : $w['LBH'];
    $_bal = ($hut == -1)? $w['BAL'] : $w['LBH'];
    $bal = number_format($_bal);
    fwrite($f, str_pad($n, 5).
      str_pad($w['MhswID'], 12).
      str_pad($w['Nama'], 32).
      str_pad($w['JumlahMK'], 5, ' ', STR_PAD_LEFT).
      str_pad($w['TotalSKS'], 5, ' ', STR_PAD_LEFT).
      str_pad($w['StatusMhswID'], 7, ' ', STR_PAD_LEFT).
      str_pad($TOTSS, 17, ' ', STR_PAD_LEFT).
      $_lf);
    }
  }
  $_tot = number_format($tot);
  fwrite($f, $grs);
  fwrite($f, str_pad("Total $strtotal : ", 68, ' ', STR_PAD_LEFT).
    str_pad($_tot, 15, ' ', STR_PAD_LEFT). $_lf);
    fwrite($f, $grs);
  fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " . date("d-m-Y H:i"), 60) . str_pad("Akhir Laporan", 100).$_lf);
  fwrite($f, chr(27).chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'keu.lap');
}

// *** Parameters ***
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$hut = GetSetVar('hut', -1);
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$tahun = GetSetVar('tahun');
$status = GetSetVar('status', 'A');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa yang Berhutang/Lebih Bayar");
TampilkanTahunProdiProgram('keu.lap.mhswhutang', '', '', '', 1);
TampilkanStatus();
if (!empty($tahun))
  TampilkanLaporan();
?>
