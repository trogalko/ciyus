<?php
// Author: Emanuel Setio Dewo
// 10 May 2006
// www.sisfokampus.net

// *** Functions ***
function CariMhswTahun(){
  echo "<p><form method=post action='?'><input type=hidden name=mnux value='keu.lap.rinciwajibbayar'>
  <input type=hidden name=gos value='Daftar'>
  <table class=box cellspacing=1 cellpadding=4><tr><td class=inp>Tahun Akademik</td>
  <td class=ul><input type=text size=10 maxlength=10 name=tahun value='$_SESSION[tahun]'></td>
  <td class=inp>NIM</td><td class=ul><input type=text name=mhswid value='$_SESSION[mhswid]'></td>
  <td class=ul><input type=submit name=Kirim Value=Kirim></td></tr></table></form></p>" ;
}

function Daftar() {
  global $_HeaderPrn, $_lf;
  $s = "select bm.*, bn.Nama, 
      (bm.Jumlah * bm.Besar) as TOT,
      format(bm.Jumlah * bm.Besar, 0) as TOTS,
      format(bm.Dibayar, 0) as BYR,
      bm.TrxID, b2.Prioritas,
      format(bm.Besar, 0) as BSR
    from bipotmhsw bm
      left outer join bipotnama bn on bn.BIPOTNamaID=bm.BIPOTNamaID
      left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
      left outer join rekening rek on bn.RekeningID=rek.RekeningID
    where bm.MhswID='$_SESSION[mhswid]' and bm.TahunID='$_SESSION[tahun]' and bm.trxid = 1
    order by bm.TrxID, b2.Prioritas";
  $r = _query($s);
  $MaxCol = 114;
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(10));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  // parameter2
  $n = 0; $hal = 0;
  $brs = 0;
  $maxbrs = 45;
  // Buat header
  
  $GK = GetFields('khs',"MhswID = '$_SESSION[mhswid]' and TahunID",$_SESSION['tahun'],'*');
  $NamaTahun = NamaTahun($_SESSION['tahun']);
  $namamhsw = GetaField('mhsw','mhswid',$_SESSION['mhswid'],'Nama');
  $hdr = str_pad("*** RINCIAN PEMBAYARAN MAHASISWA ***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf. $_lf;
  $hdr .= str_pad("SEMESTER : ".$NamaTahun,30,' ').str_pad('Jumlah SKS : '. $GK['TotalSKS'], 84, ' ', STR_PAD_LEFT).$_lf;
  $hdr .= str_pad("NIM      : ".$_SESSION['mhswid'].' '.$namamhsw,50,' ').$_lf;
  $hdr .= $div;
  $hdr .= "NAMA                        KEWAJIBAN             PEMBAYARAN".$_lf.$div;
  fwrite($f, $hdr);
  // Tampilkan
  $pmbid = '';
  $TotalBiaya = 0;
  $Totbayar = number_format($w['TOT']);
  while ($w = _fetch_array($r)) {
  //$CekAuto = Getafield('bayarmhsw',"MhswID = '$_SESSION[mhswid]'' and Jumlah <> 0 and tahunid",$_SESSION['tahun'],'BuktiSetoran');  
  if ($w['BYR'] == 0){
    $khscek = Getafield('khs',"mhswid = '$_SESSION[mhswid]' and tahunid",$_SESSION['tahun'],'bayar');
    $auto = ($khscek <> '0') ? "      AUTODEBET" : 0;
  } else {
    $auto = str_pad("Rp.",9,' ',STR_PAD_LEFT).str_pad($w['BYR'],13,' ',STR_PAD_LEFT);
  }
  $khsbyr = Getafield('khs',"mhswid = '$_SESSION[mhswid]' and tahunid",$_SESSION['tahun'],'bayar');
  $keterangan = ($auto == "      AUTODEBET") ? "         Proses Autodebet" : '';
  $TOTAL += $w['TOT'];
    $isi = 
      str_pad($w['Nama'], 20, ' ') . ' '.
      str_pad(':', 1) . ' '.
      str_pad("Rp.", 2, ' ').' '.
      str_pad(number_format($w['TOT']), 10,' ',STR_PAD_LEFT). ' '.
      $auto .
      //str_pad($auto, 21, ' ', STR_PAD_LEFT).'     '.
      str_pad($keterangan, 15, ' ', STR_PAD_LEFT);
    fwrite($f, $isi.$_lf);
  }
  fwrite($f, $div);
  $_TotalBiaya = number_format($TOTAL);
  $_TotalBayar = number_format($khsbyr);
  fwrite($f, str_pad('Total       : Rp.', 26, ' ', STR_PAD_LEFT). ' '.
    str_pad($_TotalBiaya, 11, ' ', STR_PAD_LEFT) . ' '.
    str_pad('Rp.', 8, ' ', STR_PAD_LEFT) . ' '.
    str_pad($_TotalBayar, 12, ' ', STR_PAD_LEFT) . $_lf);
  fwrite($f, str_pad("Potongan    : Rp. ",27, ' ',STR_PAD_LEFT).' '.str_pad(number_format($GK['Potongan']),10,' ',STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad("Tarik       : Rp. ",27, ' ',STR_PAD_LEFT).' '.str_pad(number_format($GK['Tarik']),10,' ',STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad("Jumlah Lain :     ",27, ' ',STR_PAD_LEFT).str_pad('Rp. ',21,' ',STR_PAD_LEFT).str_pad(number_format($GK['JumlahLain']),12,' ',STR_PAD_LEFT).$_lf.$_lf.$_lf.$_lf);
  //CEK BPM
  $s1 = "select bm.*, date_format(Tanggal, '%d/%m/%Y') as TGL,
    date_format(TanggalBuat, '%d/%m/%Y') as TGLTRX, date_format(TanggalEdit, '%d/%m/%Y') as TGLINPT,
    format(Jumlah, 0) as JML
    from bayarmhsw bm
    where bm.MhswID='$_SESSION[mhswid]' and bm.TahunID='$_SESSION[tahun]' and bm.Proses = 1
    order by bm.BayarMhswID";
  //echo $s;
  $r1 = _query($s1);
  //$hdr = str_pad("*** RINCIAN PEMBAYARAN MAHASISWA ***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf;
  //$hdr1 .= str_pad("SEMESTER : ".$NamaTahun,30,' ').$_lf;
  //$hdr1 .= str_pad("NIM      : ".$_SESSION['mhswid'].' '.$namamhsw,50,' ').$_lf;
  $hdr1 .= $div;
  $hdr1 .= "NO  BPM            TGL CETAK     TGL INPUT      TGL BANK        NILAI      JML LAIN   RINCIAN      KETERANGAN".$_lf.$div;
  fwrite($f, $hdr1);
  while ($w1 = _fetch_array($r1)) {
    $tot += $w1['Jumlah'];
    $pross = ($w1['Proses'] == 0) ? 0 : $w1['Jumlah'];
		$pross2 = ($w1['Proses'] == 0) ? 0 : $w1['JumlahLain'];
    $n++;
    $isi1 = str_pad("$n.",3,' ').' '.
      str_pad($w1['BayarMhswID'],13,' ').' '.
      str_pad($w1['TGLTRX'],13,' ').' '.
      str_pad($w1['TGLINPT'],13,' ').' '.
      str_pad($w1['TGL'], 13, ' ').' '.
      str_pad(number_format($pross),10,' ',STR_PAD_LEFT).
			str_pad(number_format($pross2),13, ' ', STR_PAD_LEFT).' '.$_lf;
    fwrite($f, $isi1).$_lf;
  }
  fwrite($f,$div);
  $balance = $GK['Bayar'] - $GK['Biaya'] + $GK['Potongan'] - $GK['Tarik'];
  $_balance = number_format($balance);
  $BYR = number_format($GK['Bayar']);
  $BIA = number_format($GK['Biaya']);
  $POT = number_format($GK['Potongan']);
  $TRK = number_format($GK['Tarik']);
  $JMLL = number_format($GK['JumlahLain']);
  $hdr2 = $div;
  $hdr2 .= "      TOTAL BIAYA     TOTAL BAYAR    TOTAL POTONGAN     TOTAL TARIK         BALANCE     JUMLAH LAIN      ".$_lf;
  $hdr2 .= $div;
  fwrite($f, $hdr2);
  $isi2 = str_pad(' ',5,' ').
          str_pad($BIA, 12, ' ',STR_PAD_LEFT) . 
          str_pad($BYR, 16, ' ', STR_PAD_LEFT) .
          str_pad($POT, 18, ' ', STR_PAD_LEFT) .
          str_pad($TRK, 16, ' ', STR_PAD_LEFT) .
          str_pad($_balance,16, ' ',STR_PAD_LEFT) .
          str_pad($JMLL, 16, ' ', STR_PAD_LEFT). $_lf;
  fwrite($f, $isi2);
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " . date("d-m-Y H:i"), 100) . str_pad("Akhir Laporan", 100).$_lf);
  fwrite($f,chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "keu.lap");
}


// *** Parameters ***
$tahun = GetSetVar('tahun');
$mhswid = GetSetVar('mhswid');

// *** Main ***
TampilkanJudul("Daftar Pembayaran Mahasiswa");
CariMhswTahun();
if (!empty($tahun) and !empty($mhswid)) Daftar();
?>
