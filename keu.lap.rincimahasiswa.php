<?
//include_once "mhswkeu.lib.php";

function TampilkanParam(){
  global $arrID;
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><form action='?' method=post>
  <input type=hidden name=mnux value=keu.lap.rincimahasiswa>
  <input type=hidden name=gos value=daftar>
  <table class=box cellspacing=1 cellpadding=4>
  <tr><td class=hdr colspan=2>$arrID[Nama]</td></tr>
  <tr><td class=inp>Tahun</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]'></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi'>$optprd</select></td></tr>
  <tr><td class=inp>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> s/d
      <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
  <tr><td class=ul colspan=2><input type=submit name=kirim value='Kirim'></td></tr></table></form></p>";
  $linkhut = "<a href=?mnux=keu.lap.rincimahasiswa.cetak&jen=1&bck=keu.lap>Cetak Hutang</a>";
  $linkdep = "<a href=?mnux=keu.lap.rincimahasiswa.cetak&jen=-1&bck=keu.lap>Cetak Deposit</a>";
  echo "<table><tr><td><a href='?mnux=keu.lap'>Kembali</a> &nbsp;|&nbsp;<a href='?mnux=keu.lap.rincimahasiswa.cetak&bck=keu.lap'>  Cetak  </a> | &nbsp;$linkhut | &nbsp;$linkdep</td></tr></table>";
}

function GetDetailBayar($mhswid, $khsid, $thn, $arrhdr) {
 $hdr = array();
  for ($i=0; $i < sizeof($arrhdr); $i++) {
    $apa = $arrhdr[$i];
    //$apa[1] = str_replace(')', '', $apa[1]);
    $hdr[] = $apa[0];
  }
  //var_dump($arrhdr);
  $arr = array();
  for ($i = 0; $i < sizeof($arrhdr); $i++) $arr[$i] = 0;
  $s = "select bm.BIPOTNamaID, bm.Jumlah, bm.Besar, bm.Dibayar
    from bipotmhsw bm
    where bm.TahunID='$thn' and bm.MhswID='$mhswid' and bm.TrxID=1
  order by bm.BIPOTNamaID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  while($w = _fetch_array($r)){
    $val = $w['BIPOTNamaID'];
    $key = array_search($val, $arrhdr);
    //var_dump($hdr);
    $arr[$key] = $w['Jumlah'] * $w['Besar']; 
    //$arr[$key] += $arr[$key]; 
    //var_dump($arr);                             
  }
  return $arr;
}

function GetDetailBayar2($mhswid, $khsid, $thn, $arrhdr) {
 $hdr = array();
  for ($i=0; $i < sizeof($arrhdr); $i++) {
    $apa = $arrhdr[$i];
    //$apa[1] = str_replace(')', '', $apa[1]);
    $hdr[] = $apa[0];
  }
  //var_dump($arrhdr);
  $arr = array();
  for ($i = 0; $i < sizeof($arrhdr); $i++) $arr[$i] = 0;
  $s = "select bm.BIPOTNamaID, bm.Jumlah, bm.Besar, bm.Dibayar
    from bipotmhsw bm
    where bm.TahunID='$thn' and bm.MhswID='$mhswid' and bm.TrxID=1
  order by bm.BIPOTNamaID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  while($w = _fetch_array($r)){
    $val = $w['BIPOTNamaID'];
    $key = array_search($val, $arrhdr);
    //var_dump($hdr);
    $arr[$key] = $w['Dibayar']; 
    //$arr[$key] += $arr[$key]; 
    //var_dump($arr);                             
  }
  return $arr;
}

function Daftar(){
 // HitungBiayaBayarMhsw($MhswID='', $KHSID='');
  $prodix = (empty($_SESSION['prodi'])) ? '' : "and k.prodiid = '$_SESSION[prodi]'";
  if ((!empty($_SESSION['DariNPM'])) and (!empty($_SESSION['SampaiNPM']))) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= k.MhswID and k.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
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
  $r1 = _query($s1);
  $pec = array();
  $pec2 = array();
  $_hdr = implode('', $gab); 
  $n = 0;
  //var_dump($gab);
  $header2 = "   <th class=ttl>$gab[1]</th><th class=ttl>$gab[4]</th><th class=ttl>$gab[3]</th><th class=ttl>$gab[15]</th><th class=ttl>$gab[19]</th><th class=ttl>$gab[14]</th><th class=ttl>$gab[1]</th><th class=ttl>$gab[4]</th><th class=ttl>$gab[3]</th><th class=ttl>$gab[15]</th><th class=ttl>$gab[19]</th><th class=ttl>$gab[14]</th></tr>
                 <tr><th class=ttl>$gab[7]</th><th class=ttl>$gab[2]</th><th class=ttl>$gab[8]</th><th class=ttl>$gab[16]</th><th class=ttl>$gab[20]</th><th class=ttl>$gab[12]</th><th class=ttl>$gab[7]</th><th class=ttl>$gab[2]</th><th class=ttl>$gab[8]</th><th class=ttl>$gab[16]</th><th class=ttl>$gab[20]</th><th class=ttl>$gab[12]</th></tr>
                 <tr><th class=ttl>$gab[0]</th><th class=ttl>$gab[17]</th><th class=ttl>$gab[13]</th><th class=ttl>$gab[18]</th><th class=ttl>$gab[9]</th><th class=ttl>Total Tagih</th><th class=ttl>$gab[0]</th><th class=ttl>$gab[17]</th><th class=ttl>$gab[13]</th><th class=ttl>$gab[18]</th><th class=ttl>$gab[9]</th><th class=ttl>Total Tagih</th>
              
             ";
  $header = "<th class=ttl><table class=bsc width=20><tr><th class=ttl width=20>$gab[1]</th></tr><tr><th class=ttl width=20>$gab[7]</th></tr><tr><th class=ttl width=20>$gab[0]</th></tr></table></th>
             <th class=ttl><table class=bsc width=20><tr><th class=ttl width=20>$gab[4]</th></tr><tr><th class=ttl width=20 width=20>$gab[2]</th></tr><tr><th class=ttl>$gab[17]</th></tr></table></th>
             <th class=ttl><table class=bsc><tr><th class=ttl>$gab[3]</th></tr><tr><th class=ttl>$gab[8]</th></tr><tr><th class=ttl>$gab[13]</th></tr></table></th>
             <th class=ttl><table class=bsc><tr><th class=ttl>$gab[15]</th></tr><tr><th class=ttl>$gab[16]</th></tr><tr><th class=ttl>$gab[18]</th></tr></table></th>
             <th class=ttl><table class=bsc><tr><th class=ttl>$gab[19]</th></tr><tr><th class=ttl>$gab[20]</th></tr><tr><th class=ttl>$gab[9]</th></tr></table></th>
             <th class=ttl><table class=bsc><tr><th class=ttl>$gab[14]</th></tr><tr><th class=ttl>$gab[12]</th></tr><tr><th class=ttl>Total Tagih</th></tr></table></th>";
  $idnya = Getafield('bipotnama','trxid',1,'Bipotnamaid');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th rowspan=4 class=ttl>No.</th><th rowspan=4 class=ttl>NIM</th><th rowspan=4 class=ttl>Nama Mahasiswa</th><th colspan=6 class=ttl>Tagihan</th><th class=ttl rowspan=4>&nbsp;&nbsp;&nbsp;&nbsp;</th><th class=ttl colspan=6>Pembayaran</th><th class=ttl rowspan=4>Lebih Bayar</th><th class=ttl rowspan=4>Lebih Bayar Diambil</th><th class=ttl rowspan=4>Balance</th>
  <tr>$header2</tr>";
  while ($w = _fetch_array($r1)){
    //HitungBiayaBayarMhsw($w['MhswID'], $w['khsid']);
    $n++;
    $detail = GetDetailBayar($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    $detailb = GetDetailBayar2($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    $angkatan = Getafield('mhsw',"mhswid",$w['MhswID'],'left(TahunID,4)');
    //$Bayar = GetaField('khs')
    for ($i=0; $i<sizeof($detail); $i++) {
        $pec[$i] = $detail[$i];
        for ($k=0; $k<sizeof($pec);$k++) $pec2[$k] = "<td class=ul align=right>".number_format($pec[$k])."</td>";
    } 
    $tot = array_sum($pec);
    $tot_ = array_sum($detailb);
    $GTot += $tot;
    $Gbayar += $w['Bayar'];
    for ($j=0; $j<sizeof($pec); $j++) $totl[$j] += $pec[$j];
    $isi = implode('',$pec2);
    $saldo = $tot - $tot_;
    $cls = ($saldo > 0) ? "wrn" : "ul";
    for ($k=0; $k<sizeof($totl);$k++) $tott[$k] = "<td class=ttl align=right>".number_format($totl[$k])."</td>";
    $htot = implode('',$tott);
    $gtot = array_sum($totl);
    $gsal = $gtot - $Gbayar;
    $tot1 = $detail;
    $sals = ($saldo > 0) ? 0 : str_replace('-', '', $saldo);
    echo "<tr align=right><td class=inp1>$n.</td><td class=inp1>$w[MhswID]</td><td class=ul>$w[Nama]</td>
    <td class=ul>".number_format($detail[1])."<br>".number_format($detail[7])."<br>".number_format($detail[0])."</td>
    <td class=ul>".number_format($detail[4])."<br>".number_format($detail[2])."<br>".number_format($detail[17])."</td>
    <td class=ul>".number_format($detail[3])."<br>".number_format($detail[8])."<br>".number_format($detail[13])."</td>
    <td class=ul>".number_format($detail[15])."<br>".number_format($detail[16])."<br>".number_format($detail[18])."</td>
    <td class=ul>".number_format($detail[19])."<br>".number_format($detail[20])."<br>".number_format($detail[9])."</td>
    <td class=ul>".number_format($detail[14])."<br>".number_format($detail[12])."<br>".number_format($tot)."</td>
    <td class=ul align=right>&nbsp;</td>
    <td class=ul>".number_format($detailb[1])."<br>".number_format($detailb[7])."<br>".number_format($detailb[0])."</td>
    <td class=ul>".number_format($detailb[4])."<br>".number_format($detailb[2])."<br>".number_format($detailb[17])."</td>
    <td class=ul>".number_format($detailb[3])."<br>".number_format($detailb[8])."<br>".number_format($detailb[13])."</td>
    <td class=ul>".number_format($detailb[15])."<br>".number_format($detailb[16])."<br>".number_format($detailb[18])."</td>
    <td class=ul>".number_format($detailb[19])."<br>".number_format($detailb[20])."<br>".number_format($detailb[9])."</td>
    <td class=ul>".number_format($detailb[14])."<br>".number_format($detailb[12])."<br>".number_format($tot_)."</td>
    <td class=ul align=right>".number_format($sals)."</td>
    <td class=ul align=right>".number_format($w['Tarik'])."</td>
    <td class='$cls' align=right>".number_format($saldo)."</td></tr>";
    $total1 += $detail[1] + $detail[7] + $detail[0];
    $total2 += $detail[4] + $detail[2] + $detail[17];
    $total3 += $detail[3] + $detail[8] + $detail[13];
    $total4 += $detail[15] + $detail[16] + $detail[18];
    $total5 += $detail[19] + $detail[20] + $detail[9];
    $total6 += $detail[14] + $detail[12] + $tot;
    $totalb1 += $detailb[1] + $detailb[7] + $detailb[0];
    $totalb2 += $detailb[4] + $detailb[2] + $detailb[17];
    $totalb3 += $detailb[3] + $detailb[8] + $detailb[13];
    $totalb4 += $detailb[15] + $detailb[16] + $detailb[18];
    $totalb5 += $detailb[19] + $detailb[20] + $detailb[9];
    $totalb6 += $detailb[14] + $detailb[12] + $tot_;
    $gtotal += $saldo;
    $salst += $sals;
    $tar += $w['Tarik'];
  }
  //for ($k=0; $k<sizeof($totl);$k++) $tott[$k] = "<td class=ttl align=right>".number_format($totl[$k])."</td>";
  //$htot = implode('',$tott);
  //$gtot = array_sum($totl);
  //$gsal = $gtot - $Gbayar;
  echo "<tr align=right><td class=ttl colspan=3>Jumlah Total</td>
        <td class=ttl>".number_format($total1)."</td>
        <td class=ttl>".number_format($total2)."</td>
        <td class=ttl>".number_format($total3)."</td>
        <td class=ttl>".number_format($total4)."</td>
        <td class=ttl>".number_format($total5)."</td>
        <td class=ttl>".number_format($total6)."</td>
        <td class=ttl>&nbsp</td>";
  echo "<td class=ttl>".number_format($totalb1)."</td>
        <td class=ttl>".number_format($totalb2)."</td>
        <td class=ttl>".number_format($totalb3)."</td>
        <td class=ttl>".number_format($totalb4)."</td>
        <td class=ttl>".number_format($totalb5)."</td>
        <td class=ttl>".number_format($totalb6)."</td>
        <td class=ttl>".number_format($salst)."</td>
        <td class=ttl>".number_format($tar)."</td>
        <td class=ttl>".number_format($gtotal)."</td>";
  echo "</table></p>";
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
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

//Main
TampilkanJudul("Laporan Setoran Rinci Mahasiswa");
TampilkanParam();

if(!empty($_SESSION['tahun'])) {
daftar();
}
?>
