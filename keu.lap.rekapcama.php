<?php
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
function Daftar(){
  $arrGel = GetArrGelombang($_SESSION['tahun']);
  $arrPrd = GetArrPrd();
  $arrSts = GetArrStatus();
  $col = sizeof($arrGel) * sizeof($arrSts);
  $n=0;
  $JumTes = array();
  $s = "SELECT StatusAwalID, PMBPeriodID, count( PMBID ) as JML , ProdiID
        FROM pmb
        WHERE left( PMBPeriodID, 4 ) = '$_SESSION[tahun]'
        GROUP BY PMBPeriodID, ProdiID, StatusAwalID
        ORDER BY PMBPeriodID, ProdiID";
  $r = _query($s);
  while ($w = _fetch_array($r)){
    $Thn = $w['PMBPeriodID'];
    $Prd = $w['ProdiID'];
    $StA = $w['StatusAwalID'];
    $JumTes[$Prd][$Thn][$StA] = $w['JML']+0;
  }
  $s1 = "SELECT StatusAwalID, PMBPeriodID, count( PMBID ) as JML , ProdiID
        FROM pmb
        WHERE left( PMBPeriodID, 4 ) = '$_SESSION[tahun]'
              AND LulusUjian = 'Y'
        GROUP BY PMBPeriodID, ProdiID, StatusAwalID
        ORDER BY PMBPeriodID, ProdiID";
  $r1 = _query($s1);
  while ($w1 = _fetch_array($r1)){
    $Thn = $w1['PMBPeriodID'];
    $Prd = $w1['ProdiID'];
    $StA = $w1['StatusAwalID'];
    $JumLLS[$Prd][$Thn][$StA] = $w1['JML']+0;
  }
  $s2 = "SELECT StatusAwalID, PMBPeriodID, count( PMBID ) as JML , ProdiID
        FROM pmb
        WHERE left( PMBPeriodID, 4 ) = '$_SESSION[tahun]'
              AND NIM is Not NULL
        GROUP BY PMBPeriodID, ProdiID, StatusAwalID
        ORDER BY PMBPeriodID, ProdiID";
  $r2 = _query($s2);
 
  while ($w2 = _fetch_array($r2)){
    $Thn = $w2['PMBPeriodID']; 
    $Prd = $w2['ProdiID'];
    $StA = $w2['StatusAwalID'];
    $JumKFS[$Prd][$Thn][$StA] = $w2['JML']+0;
  }

  for ($l=0; $l<sizeof($arrGel); $l++){
    $hdr   .= "<th class=ttl colspan=3>$arrGel[$l]</th>";
    $hdrst .= "<th class=ttl>B</th><th class=ttl>P</th><th class=ttl>S</th>";
  }
  
  echo "<p><table class=box cellpadding=4 cellspacing=1>";
  echo "<tr><th class=ttl rowspan=2>PRODI</th><th class=ttl rowspan=2>KETERANGAN</th>$hdr<th rowspan=2 class=ttl>TOTAL</th></tr>";
  echo "<tr>$hdrst</tr>";
  
  for ($i=0; $i<sizeof($arrPrd); $i++){
    for ($j=0; $j<sizeof($arrGel); $j++){
      for ($k=0; $k<sizeof($arrSts); $k++){
        $n++;
        if ($n == 1) {
          $t = array();
          $GTES = 0;
          $GLLS = 0;
          $GKFS = 0;
        }
        $TOTTES = $JumTes[$arrPrd[$i]][$arrGel[$j]][$arrSts[$k]]+0;
        $TOTLLS = $JumLLS[$arrPrd[$i]][$arrGel[$j]][$arrSts[$k]]+0;
        $TOTKFS = $JumKFS[$arrPrd[$i]][$arrGel[$j]][$arrSts[$k]]+0;
        
        $GTES += $TOTTES;
        $GLLS += $TOTLLS;
        $GKFS += $TOTKFS;
        $t[1] .= "<td class=ul>$TOTTES&nbsp;</td>";
        $t[2] .= "<td class=inp3>$TOTLLS&nbsp;</td>";
        $t[3] .= "<td class=inp4>$TOTKFS&nbsp;</td>";
        
        if ($n >= $col) {
          TuliskanData($t, $arrPrd[$i], $GTES, $GLLS, $GKFS);
          $n = 0;
        }
      }
    }
  }
 // var_dump($jt);
 echo "</table>";
}
function TuliskanData($t, $arrPrd, $tt, $tl, $tk){
  $PRD = GetaField('prodi', 'ProdiID', $arrPrd, 'Nama');
  echo "<tr><td class=inp1>$PRD</td><td class=ul>Mengikuti Tes</td>" . $t[1] . "<td class=ttl align=right>$tt</td></tr>";
  echo "<tr><td class=ul>&nbsp;</td><td class=inp3>Lulus</td>" . $t[2] . "<td class=ttl align=right>$tl</td></tr>";
  echo "<tr><td class=ul>&nbsp;</td><td class=inp4>Konfirmasi NIM</td>" . $t[3] . "<td class=ttl align=right>$tk</td></tr>";
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
TampilkanJudul("Rekap Jumlah Calon Mahasiswa");
TampilkanTahun('keu.lap.rekapcama', 'Daftar', 'keu.lap');
if (!empty($tahun)) Daftar();
?>
