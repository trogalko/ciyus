<?php
// Copy MKSetara di suatu prodi
include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Denda & Pembayaran");

$s = "select mp.*
  from mkpra mp
    left outer join mk mk on mp.MKID=mk.MKID
  group by mp.MKID";
$r = _query($s);
echo "<ol>";
while ($w = _fetch_array($r)) {
  echo "<li>$w[MKKode]
  </li>";
}
echo "</ol>";
?>
