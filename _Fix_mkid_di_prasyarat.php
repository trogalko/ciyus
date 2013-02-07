<?php
// Fix MKID di Prasyarat
// Author: E. Setio Dewo

include_once "sisfokampus.php";
HeaderSisfoKampus("Fix MKID di Prasyarat");

$s = "select MKID, MKKode
  from mk
  order by MKID";
$r = _query($s);
echo "<ol>";
while ($w = _fetch_array($r)) {
  $s1a = "update mkpra set MKKode='$w[MKKode]'
    where MKID=$w[MKID]
      and MKKode='' ";
  $s1 = "update mkpra set MKPra='$w[MKKode]'
    where PraID=$w[MKID]
      and MKPra='' ";
  $r1 = _query($s1a);
  $jml = _affected_rows($r1);
  echo "<li>$w[MKID]: $w[MKKode]: $jml</li>";
}
echo "</ol>";
?>
