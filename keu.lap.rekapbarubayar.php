<?php
// Author: Emanuel Setio Dewo
// 10 May 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanTahun($mnux, $gos, $back) {
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</td></tr>
  <tr><td class=inp>Tahun</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
    <input type=submit name='Tampilkan' value='Tampilkan'>
    <input type=button name='Back' value='Back' onClick=\"location='?mnux=$back'\"></td></tr>
  </form></table></p>"; 
}
function Daftar() {
  $arrTrm = array(0=>'Lulus', 1=>'Konfirmasi NIM');
  // Data gelombang
  $arrGel = GetArrGelombang($_SESSION['tahun']);
  $arrPrd = GetArrPrd();
  $arrStat = GetArrStatus();
  $hdrgel = '';
  for ($i=0; $i < sizeof($arrGel); $i++) {
    $hdrgel .= "<th class=ttl>". $arrGel[$i] ."</th>";
  }
  $s = "select ProdiID, PMBPeriodID, NIM as MhswID, count(PMBID) as JML, LulusUjian, StatusAwalID
    from pmb
    where LEFT(pmb.PMBPeriodID, 4)='$_SESSION[tahun]' and pmb.LulusUjian = 'Y'
    group by left(pmb.NIM, 6), pmb.ProdiID, pmb.PMBPeriodID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  // Ambil data
  $arr = array();
  while ($w = _fetch_array($r)) {
    $key = $w['ProdiID'];
    $keyg = $w['PMBPeriodID'];
    $keys = $w['StatusAwalID'];

    $trm = (empty($w['MhswID']))? 0 : 1;
    
    $arr[$key][$trm][$keyg] = $w['JML'];
    //echo "$w[MhswID]: $w[ProdiID]: $w[PMBPeriodID] : $w[JML]<br>";
  }
  //print_r(array_values($arr));
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl colspan=2>Program Studi</th>
      <th class=ttl>Lulus/Konf. NIM</th>
    $hdrgel
    <th class=ttl>Jumlah</th></tr>";
  //$arrp = array_keys($arr);
  $tot = 0;
  for ($i =0; $i < sizeof($arrPrd); $i++) {
    $prd = $arrPrd[$i];
    $nmprd = GetaField('prodi', 'ProdiID', $arrPrd[$i], 'Nama');
    for ($x = 0; $x <= 1; $x++) {
      $sub = 0;
      $trm = $arrTrm[$x];
      if ($x == 0) {
        $_prd = $prd;
        $_nmprd = $nmprd;
      }
      else {
        $_prd = '&nbsp;';
        $_nmprd = '&nbsp;';
      }
      $isigel = '';
      for ($j=0; $j<sizeof($arrGel); $j++) {
        $jmlgel = $arr[$arrPrd[$i]][$x][$arrGel[$j]]+0;
        $totgel = $arr[$arrPrd[$i]][1][$arrGel[$j]]+$arr[$arrPrd[$i]][0][$arrGel[$j]]+0;
        
        //$tot10 += $arr[$arrPrd[$i]][0][$arrGel[$j]]+0;
        $tot += $jmlgel;
        $sub += $jmlgel;
        
        $sub2 += $sub;
        if ($x == 0) $isigel  .= "<td class=ul align=right>$totgel</td>";
        else $isigel          .= "<td class=ul align=right>$jmlgel</td>";
      }
      //var_dump($tot10);
      echo "<tr>
      <td class=inp1><b>$_prd</b></td>
      <td class=ul>$_nmprd</td>
      <td class=ul>$trm</td>
      $isigel
      <td class=inp>$sub</td>
      </tr>";
    }
  }
  $kol = 3 + sizeof($arrGel);
  //$jmlLulus = GetaField('pmb', "LulusUjian = 'Y' and left(PMBPeriodID, 4)", $_SESSION['tahun'], 'count(PMBID)');
  //$jmlNIM   = GetaField('pmb', "NIM is not null and left(PMBPeriodID, 4)", $_SESSION['tahun'], 'count(PMBID)');
  echo "<tr><td class=ul colspan=$kol align=right>Total :</td><td class=inp><b>$tot</b></td></tr>";
  echo "</table></p>";
}
function GetArrPrd() {
  $s = "select ProdiID from prodi where ProdiID not in (99, 11) order by ProdiID";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) $a[] = $w['ProdiID'];
  return $a;
}
function GetArrGelombang($thn) {
  $s = "select PMBPeriodID
    from pmb
    where PMBPeriodID like '$thn%'
    group by PMBPeriodID";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $a[] = $w['PMBPeriodID'];
    //echo $w['PMBPeriodID'] . "<br>";
  }
  return $a;
}
function GetArrStatus(){
  $s = "select StatusAwalID
        from statusawal
        where StatusAwalID in ('B', 'P', 'S') order by StatusAwalID";
  $r = _query($s);
  $a  = array();
  while ($w = _fetch_array($r)){
    $a[] = $w['StatusAwalID'];
  }
  return $a;
}

// *** Parameters ***
$tahun = substr(GetSetVar('tahun'), 0, 4);
$_SESSION['tahun'] = $tahun;

// *** Main ***
TampilkanJudul("Rekap Pembayaran Mahasiswa");
TampilkanTahun('keu.lap.rekapbarubayar', 'Daftar', 'keu.lap');
if (!empty($tahun)) Daftar();
?>
