<?php
// Author: Emanuel Setio Dewo
// 29 Sept 2006
// www.sisfokampus.net

include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Responsi");

// *** Functions ***
function TampilkanAD() {
  $s = "select  k.MhswID, count(*) as JUM
      from krs k
      where tahunID = '20061'
    Group by mhswid, mkid";
    
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>NIM</th>
    <th class=ttl>Jumlah MK</th>
    </tr>";
    
  while ($w = _fetch_array($r)) {
	$n++;
	if ($w['JUM'] >= 2){
  echo "<tr><td class=inp>$n</td>
	  <td class=ul>$w[MhswID]</td>
	  <td class=ul>$w[JUM]</td>
	  </tr>";
  }
  else {}
}
echo "</table></p>";
// *** Parameters ***
$gos = empty($_REQUEST['gos'])? "TampilkanAD" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Cek Responsi");
TampilkanAD();
?>
