<?
session_start();

function PotSt($kata){
  $pot = 2;
}

function TampilkanParam(){
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><form action='?' method=post>
  <input type=hidden name=mnux value=keu.lap.rincimahasiswa>
  <input type=hidden name=gos value=daftar>
  
  <table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp1>Tahun</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]'></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi'>$optprd</select></td></tr>
  <tr><td class=ul><input type=submit name=kirim value='Kirim'></td></tr></table></form></p>";
}

function GetDetailBayar($mhswid, $khsid, $thn, $arrhdr) {
 $hdr = array();
  for ($i=0; $i < sizeof($arrhdr); $i++) {
    $apa = $arrhdr[$i];
    $hdr[] = $apa[0];
  }
  $arr = array();
  for ($i = 0; $i < sizeof($arrhdr); $i++) $arr[$i] = 0;
  $s = "select bm.BIPOTNamaID, bm.Jumlah, bm.Besar, bm.Dibayar
    from bipotmhsw bm
    where bm.TahunID='$thn' and bm.MhswID='$mhswid' and bm.TrxID=1
  order by bm.BIPOTNamaID";
  $r = _query($s);

  while($w = _fetch_array($r)){
    $val = $w['BIPOTNamaID'];
    $key = array_search($val, $arrhdr);

    $arr[$key] = $w['Jumlah'] * $w['Besar'];                            
  }
  return $arr;
}

function GetDetailBayar2($mhswid, $khsid, $thn, $arrhdr) {
 $hdr = array();
  for ($i=0; $i < sizeof($arrhdr); $i++) {
    $apa = $arrhdr[$i];
    $hdr[] = $apa[0];
  }
  $arr = array();
  for ($i = 0; $i < sizeof($arrhdr); $i++) $arr[$i] = 0;
  $s = "select bm.BIPOTNamaID, bm.Jumlah, bm.Besar, bm.Dibayar
    from bipotmhsw bm
    where bm.TahunID='$thn' and bm.MhswID='$mhswid' and bm.TrxID=1
  order by bm.BIPOTNamaID";
  $r = _query($s);
  while($w = _fetch_array($r)){
    $val = $w['BIPOTNamaID'];
    $key = array_search($val, $arrhdr);
    $arr[$key] = $w['Dibayar'];                         
  }
  return $arr;
}

function Daftar(){
  global $_lf;
  $prodix = (empty($_SESSION['prodi'])) ? '' : "and k.prodiid = '$_SESSION[prodi]'";
  if ((!empty($_SESSION['DariNPM'])) and (!empty($_SESSION['SampaiNPM']))) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= k.MhswID and k.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
  $jen = $_REQUEST['jen'];
  if ($jen == 1) { /*$Qjen = "and (Biaya - Bayar - Potongan + Tarik) > 0";*/ $p='>'; $jdls = "Hutang";}
  elseif ($jen == -1) {$Qjen = /*"and (Biaya - Bayar - Potongan + Tarik) < 0";*/ $p='<='; $jdls = "Deposit";}
  else {$Qjen = ''; $jdls = 'Hutang/Deposit';}
  $s1 = "select k.*, m.Nama from khs k 
         left outer join mhsw m on k.mhswid = m.mhswid where k.tahunid = '$_SESSION[tahun]' 
         and k.statusmhswid in ('A') 
         
         $_npm
         $prodix
         order by k.MhswID";
  BuatArrayHeader($hdr, $hdrid);
  for ($i=0; $i<sizeof($hdr); $i++) {
    $gab[$i] = "$hdr[$i]"; 
  }
  $MaxCol = 262;
  $maxbrs = 11;
  $brs = 0;
  //var_dump($gab);
  $nmf = "tmp/rinci.$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(77).chr(27).chr(15).chr(27).chr(108).chr(10)).$_lf;
  $div = str_pad('-', $MaxCol, '-').$_lf;
  $margin = str_pad(' ',33,' ');
  //$tes = str_
  $sprhdr = str_pad("*** Laporan Rincian Kewajiban dan Pembayaran Mahasiswa ***",$MaxCol,' ',STR_PAD_BOTH).$_lf.$_lf.$_lf;
  $hdr  = $margin.                                                          str_pad($gab[1],16,' ').str_pad($gab[4],16,' ').str_pad($gab[3],16,' ').str_pad($gab[15],16,' ').str_pad($gab[19],16,' ').str_pad(substr("Pot.U.K",0,11),16,' ').str_pad('|',3,' ').str_pad($gab[1],16,' ').str_pad($gab[4],16,' ').str_pad($gab[3],16,' ').str_pad($gab[15],16,' ').str_pad($gab[19],16,' ').str_pad("Ujian Skripsi",16,' ').str_pad(' ',16,' ').str_pad(' ',16,' ').$_lf;
  $hdr .= str_pad("No.",5,' ').str_pad("NPM",10,' ').str_pad('Nama',18,' ').str_pad($gab[7],16,' ').str_pad($gab[2],16,' ').str_pad($gab[8],16,' ').str_pad($gab[16],16,' ').str_pad(substr($gab[20],0,17),16,' ').str_pad("Pot Mhsw Baru",16,' ').str_pad("|",3,' ').str_pad($gab[7],16,' ').str_pad($gab[2],16,' ').str_pad($gab[8],16,' ').str_pad($gab[16],16,' ').str_pad(substr($gab[20],0,17),16,' ').str_pad("Lain-Lain",16,' ').str_pad('     TARIK',16,' ').str_pad($jdls,15,' ',STR_PAD_LEFT).$_lf;
  $hdr .= $margin.                                                          str_pad($gab[0],16,' ').str_pad($gab[17],16,' ').str_pad($gab[13],16,' ').str_pad($gab[18],16,' ').str_pad($gab[9],16,' ').str_pad("Total Tagih",16,' ').str_pad('|',3,' ').str_pad($gab[0],16,' ').str_pad($gab[17],16,' ').str_pad($gab[13],16,' ').str_pad($gab[18],16,' ').str_pad($gab[9],16,' ').str_pad("Total Bayar",16,' ').str_pad(' ',16,' ').str_pad(' ',30,' ').$_lf;
  $hdr2  = "                                                                            TAGIHAN                                              |                                                         PEMBAYARAN                                                                  ".$_lf;
  $n = 0;
  $r1 = _query($s1);
  $pec = array();
  $pec2 = array();
  //$_hdr = implode('', $gab); 
  //$idnya = Getafield('bipotnama','trxid',1,'Bipotnamaid');
  //$ff2 = str_pad("No.",4,' ') . str_pad("NPM",11,' ').$_hdr.str_pad("Total",12,' ').str_pad('Bayar',12,' ').str_pad('Saldo',12,' ').$_lf;
  if (empty($_SESSION['prodi'])){}
  else {
    $prodis = GetaField('prodi','ProdiID',$_SESSION['prodi'],'Nama');
    $prodiini = "Fakultas : " . str_pad($_SESSION['prodi']. " - " . $prodis, 30,' '). $_lf;
  }
  fwrite($f, $sprhdr);
  fwrite($f, str_pad("Jenis    : ". $jdls, 30, ' ').$_lf);
  fwrite($f, str_pad("Periode  : ". NamaTahun($_SESSION['tahun']),30,' ').$_lf);
  fwrite($f, $prodiini);
  fwrite($f, str_pad("Tanggal  : ". date("d-m-Y"),30,' ').$_lf);
  fwrite($f, $div);
  fwrite($f, $hdr2);
  fwrite($f, $div);
  fwrite($f, $hdr);
  fwrite($f, $div);
  while ($w = _fetch_array($r1)){
    //$detail = GetDetailBayar($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    //$detailb = GetDetailBayar2($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    $detail = GetDetailBayar($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    $detail2 = GetDetailBayar2($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    $totw = array_sum($detail);
    $totq = array_sum($detail2);
    $diskon = GetaField('bipotmhsw', "TrxID=-1 and TahunID='$_SESSION[tahun]' and MhswID", $w['MhswID'], 'Jumlah')+0;
    $saldo = $totw - $totq - $diskon;
    
    if ($saldo > 0) { 
    
    $n++; $brs++;
    if($brs > $maxbrs){
        $hal++; $brs = 1;
        fwrite($f, chr(12));
        fwrite($f, $sprhdr);
        fwrite($f,$div);
        fwrite($f, $hdr2);
			  fwrite($f,$div);
			  fwrite($f,$hdr);
			  fwrite($f,$div);
				 
		}
		$Pot = GetFields("bipotmhsw","trxid = -1 and tahunid = '$_SESSION[tahun]' and mhswid",$w['MhswID'],"Besar,Dibayar");
		$lain = GetFields("bayarmhsw","tahunid = '$_SESSION[tahun]' and MhswID",$w['MhswID'],"Sum(JumlahLain) as JML");
    
    //$totw = array_sum($detail);
    //$totq = array_sum($detail2);
    $angkatan = Getafield('mhsw',"mhswid",$w['MhswID'],'left(Nama,16)');
    $bm = GetFields("khs","TahunID = '$_SESSION[tahun]' and mhswid",$w['MhswID'],"Biaya, Potongan, Bayar");
    //$saldo = $totw - $totq; //$bm['Bayar']- $bm['Biaya'] + $bm['Potongan'] - $w['Tarik'];
    $isi_ = str_pad($w['MhswID'],11,' ').str_pad($angkatan,34).$_lf;
    $isi_ .= str_pad(' ',11,' ').str_pad(' ',22,' ').$_lf;
    $isi_ .= str_pad(' ',11,' ').str_pad(' ',22,' ').$_lf;
     
    $isi  = str_pad("$n.",5,' ').str_pad($w['MhswID'],10,' ').str_pad($angkatan,16).str_pad(number_format($detail[1]),15,' ',STR_PAD_LEFT).str_pad(number_format($detail[4]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[3]),15,' ',STR_PAD_LEFT).str_pad(number_format($detail[15]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[19]),16,' ',STR_PAD_LEFT).str_pad(number_format($Pot['Dibayar']),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[1]),17,' ',STR_PAD_LEFT).str_pad(number_format($detail2[4]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[3]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[15]),19,' ',STR_PAD_LEFT).str_pad(number_format($detail2[19]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[14]),16,' ',STR_PAD_LEFT).str_pad(number_format(0),16,' ',STR_PAD_LEFT).str_pad(' ',16,' ',STR_PAD_LEFT).$_lf;
    $isi .= str_pad(' ',11,' ').str_pad(' ',20,' ').                                str_pad(number_format($detail[7]),15,' ',STR_PAD_LEFT).str_pad(number_format($detail[2]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[8]),15,' ',STR_PAD_LEFT).str_pad(number_format($detail[16]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[20]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[12]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[7]),17,' ',STR_PAD_LEFT).str_pad(number_format($detail2[2]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[8]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[16]),19,' ',STR_PAD_LEFT).str_pad(number_format($detail2[20]),16,' ',STR_PAD_LEFT).str_pad(number_format($lain['JML']),16,' ',STR_PAD_LEFT).str_pad(number_format($w['Tarik']),16,' ',STR_PAD_LEFT).str_pad(number_format($saldo),16,' ',STR_PAD_LEFT).$_lf;
    $isi .= str_pad(' ',11,' ').str_pad(' ',20,' ').                                str_pad(number_format($detail[0]),15,' ',STR_PAD_LEFT).str_pad(number_format($detail[17]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[13]),15,' ',STR_PAD_LEFT).str_pad(number_format($detail[18]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail[9]),16,' ',STR_PAD_LEFT).str_pad(number_format($bm['Biaya']),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[0]),17,' ',STR_PAD_LEFT).str_pad(number_format($detail2[17]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[13]),16,' ',STR_PAD_LEFT).str_pad(number_format($detail2[18]),19,' ',STR_PAD_LEFT).str_pad(number_format($detail2[9]),16,' ',STR_PAD_LEFT).str_pad(number_format($bm['Bayar']),16,' ',STR_PAD_LEFT).str_pad(number_format(0),16,' ',STR_PAD_LEFT).str_pad(' ',16,' ',STR_PAD_LEFT).$_lf.$div;
    $tot = array_sum($detail);
    $GTot += $tot;
    $Gbayar += $w['Bayar'];
    
    fwrite($f,$isi); //.str_pad(number_format($tot),12,' ',STR_PAD_LEFT).str_pad(number_format($w['Bayar']),12,' ',STR_PAD_LEFT).str_pad(number_format($saldo),12,' ',STR_PAD_LEFT).$_lf;
    $total1 += $detail[1] + $detail[7] + $detail[0];
    $total2 += $detail[4] + $detail[2] + $detail[17];
    $total3 += $detail[3] + $detail[8] + $detail[13];
    $total4 += $detail[15] + $detail[16] + $detail[18];
    $total5 += $detail[19] + $detail[20] + $detail[9];
    $total6 += $detail[14] + $detail[12] + ($bm['Biaya']- $bm['Potongan']);
    $totalb1 += $detail2[1] + $detail2[7] + $detail2[0];
    $totalb2 += $detail2[4] + $detail2[2] + $detail2[17];
    $totalb3 += $detail2[3] + $detail2[8] + $detail2[13];
    $totalb4 += $detail2[15] + $detail2[16] + $detail2[18];
    $totalb5 += $detail2[19] + $detail2[20] + $detail2[9];
    $totalb6 += $detail2[14] + $detail2[12] + $bm['Bayar'];
    $gtotal += $saldo;
    $tar += $w['Tarik'];
  }
  }
  for ($k=0; $k<sizeof($totl);$k++) $tott[$k] = str_pad(number_format($totl[$k]),12,' ',STR_PAD_LEFT);
  $gsal = $gtot - $Gbayar;
  
  $ffs = str_pad(' ',4,' ').str_pad("Jumlah Total",25,' ').str_pad(number_format($total1),17,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($total2),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($total3),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($total4),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($total5),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($total6),16,' ',STR_PAD_LEFT).
                                                           
                                                           
                                                           str_pad(number_format($totalb1),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($totalb2),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($totalb3),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($totalb4),19,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($totalb5),17,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($totalb6),15,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($tar),16,' ',STR_PAD_LEFT).
                                                           str_pad(number_format($gtotal),16,' ',STR_PAD_LEFT).
                                                           $_lf;
  //fwrite($f, $div);
  fwrite($f, $ffs);
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak : ".date("d-m-Y H:i"),10,' ').str_pad("Akhir laporan",236, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "keu.lap.rincimahasiswa");
}
  
function BuatArrayHeader(&$hdr, &$hdrid) {
  $s = "select BIPOTNamaID, Nama
    from bipotnama
    where TrxID=1
    order by BipotNamaID";
  $r = _query($s);
  $hdr = array();
  $hdrid = array();
  while ($w = _fetch_array($r)) {
    $hdr[] = $w['Nama'];
    $hdrid[] = $w['BIPOTNamaID'];
  }
}
              
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');

//Main
TampilkanJudul("Laporan Setoran Rinci Mahasiswa");
//TampilkanParam();

if(!empty($_SESSION['tahun'])) {
daftar();
}
?>

