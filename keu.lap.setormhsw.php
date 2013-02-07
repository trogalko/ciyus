<?php

function TampilkanParam(){
  echo "<p><form action='?' method=post>
  <input type=hidden name=mnux value=keu.lap.setormhsw>
  <input type=hidden name=gos value=daftar>
  <table class=box cellspacing=1 cellpadding=4><tr>
  <td class=inp1>Tahun</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]'></td>
  <td class=ul><input type=submit name=kirim value='Kirim'></td></tr></table></form></p>";
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
    $arr[$key] = $w['Jumlah'] * $w['Besar'] - $w['Dibayar']; 
    //$arr[$key] += $arr[$key]; 
    //var_dump($arr);                             
  }
  return $arr;
}

function Daftar($n,$nm,$hdrid,$totl,$totl_,$gtot){
  $s1 = "select k.* from khs k where k.tahunid = '$_SESSION[tahun]' 
         and JumlahMK > 0 
         and statusmhswid in ('P','A') 
         and ProdiID = '$n'";
  $r1 = _query($s1);
  $pec = array();
  $pec_ = array();
  $pec2 = array();
  $pec2_= array();
  $ang = substr($_SESSION['tahun'],0,4);
  while ($w = _fetch_array($r1)){
    $detail = GetDetailBayar($w['MhswID'], $w['khsid'], $_SESSION['tahun'], $hdrid);
    $angkatan = Getafield('mhsw',"mhswid",$w['MhswID'],'left(TahunID,4)');
    for ($i=0; $i<sizeof($detail); $i++) {
       if($angkatan == $ang){
        $pec[$i] += $detail[$i];
      } else {
        $pec_[$i] += $detail[$i];
      }
    }
  }
  //echo $angkatan;
  //echo $ang;
  $tot_= array_sum($pec_);
  $tot__ = array_sum($pec);
  $tot = $tot_ + $tot__;
  $gtot += $tot;
  for ($j=0; $j<sizeof($pec); $j++) $totl[$j] += $pec[$j];
  for ($j1=0; $j1<sizeof($pec_); $j1++) $totl_[$j1] += $pec_[$j1];
  for ($k=0; $k<sizeof($pec_);$k++) $pec2[$k] = "<td class=ul tittle=$n align=right>".number_format($pec_[$k])."</td>";
  for ($k1=0; $k1<sizeof($pec);$k1++) { 
  $pec2_[$k1] = (!empty($pec[$k1]))? "<td class=ul tittle=$n align=right>".number_format($pec[$k1])."</td>" : "<td class=ul tittle=$n align=right>0</td>";
  }
    
  $isi = implode('',$pec2);
  $isi_ = implode('',$pec2_);
  //$isi_ = implode('',$pec2_);
  //echo $angkatan;
  //$isi = (empty($isi)) ? 
  echo "<tr><td class=ul width=150>$nm Angkatan Lama</td>$isi<td align=right class=ul>".number_format($tot_)."</td></tr>";
  echo "<tr><td class=ul width=150>$nm Angkatan Baru</td>$isi_<td align=right class=ul>".number_format($tot__)."</td></tr>";
}
  
function BuatArrayHeader(&$hdr, &$hdrid) {
  $s = "select BIPOTNamaID, Nama
    from bipotnama
    where TrxID=1
    order by Urutan";
  $r = _query($s);
  $hdr = array();
  $hdrid = array();
  while ($w = _fetch_array($r)) {
    $hdr[] = $w['Nama'];
    $hdrid[] = $w['BIPOTNamaID'];
  }
}
              
$tahun = GetSetVar('tahun');

function BuatTotal(){
  $u = "select ProdiID, Nama from prodi where ProdiID not in ('11','99') order by prodiid desc";
  $ru = _query($u);
   $gab = array();
   $pec4 = array();
  BuatArrayHeader($hdr, $hdrid);
  for ($i=0; $i<sizeof($hdr); $i++) {
    $gab[$i] = "<th class=ttl title='$hdrid[$i]'>$hdr[$i]</th>"; 
  }  
  $_hdr = implode('', $gab); 
  $idnya = Getafield('bipotnama','trxid',1,'Bipotnamaid');
  echo "<p><table class=box cellspacing=1 cellpadding=4><tr><th class=ttl width=150>Fakultas</th>$_hdr<th class=ttl>Total</th></tr>";
  while($wu = _fetch_array($ru)){
    daftar($wu['ProdiID'],$wu['Nama'],$hdrid,&$totl,&$totl_,&$gtot);
  } 
  for ($k=0; $k<sizeof($totl);$k++)
    $pec4[$k] = "<td class=ttl tittle='$n' align=right>".number_format($totl[$k]+$totl_[$k])."</td>";
  $htot = implode('',$pec4);  
  echo "<tr><td class=ttl>Jumlah Total</td>$htot<td class=ttl align=right>".number_format($gtot)."</td></tr>";
  echo "</table></p>";
}
//Main
TampilkanJudul("Laporan Setoran Mahasiswa");
TampilkanParam();

if(!empty($_SESSION['tahun'])) {

//$n = '41';

BuatTotal();
}
?>
