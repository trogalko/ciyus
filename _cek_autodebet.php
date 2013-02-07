<?php
// Author: Emanuel Setio Dewo
// 29 Sept 2006
// www.sisfokampus.net

include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Autodebet");

// *** Functions ***
function TampilkanAD() {
  $s = "select ad.*, bm.Jumlah, bm.MhswID
    from _autodebet ad
      left outer join bayarmhsw bm on ad.nobpmTRINA=bm.BayarMhswID
    where ad.Nominal <> bm.Jumlah
    order by ad.nobpmTRINA";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>NIM</th>
    <th class=ttl>BPM AD</th>
    <th class=ttl>Didebet</th>
    <th class=ttl>-</th>
    <th class=ttl>Sisfo</th>
    <th class=ttl>N.P.M</th>
    </tr>";
  while ($w = _fetch_array($r)) {
	$n++;
	$Nominal = number_format($w['Nominal']);
	$Jumlah = number_format($w['Jumlah']);
	$Tanda = ($w['Nominal'] != $w['Jumlah'])? "&times;" : "&raquo;";
	$c = ($w['Nominal'] != $w['Jumlah'])? "class=wrn" : "class=ul";
	echo "<tr><td class=inp>$n</td>
	  <td class=ul>$w[NIM]</td>
	  <td class=ul>$w[nobpmTRINA]</td>
	  <td class=ul align=right>$Nominal</td>
	  <td $c align=center>$Tanda</td>
	  
	  <td class=ul align=right>$Jumlah</td>
	  <td class=ul>$w[MhswID]</td>
	  </tr>";
  }
  echo "</table></p>";
}

// *** Parameters ***
$gos = empty($_REQUEST['gos'])? "TampilkanAD" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Cek Autodebet");
$gos();
?>