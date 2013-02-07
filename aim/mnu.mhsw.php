<?php
// Author: Emanuel Setio Dewo
// 26 May 2006
// http://www.sisfokampus.net

$sub = GetSetVar('sub', 'DM');
$arrmnu = array("Data Mahasiswa~mhsw~DM",
  "KHS~mhsw~KHS",
  "History~mhsw~HIST",
  "Jadwal~mhsw~JDWL",
  "Absensi~mhsw~ABSN",
  "Keuangan~mhsw~KEU",
  "Biaya~mhsw~BIA",
  "Pembayaran~mhsw~BYR",
  "Logout~mhsw~LOGOUT"
  );

// Tampilkan menu
echo "<p align=center>";
for ($i=0; $i<sizeof($arrmnu); $i++) {
  $mnu = explode('~', $arrmnu[$i]);
  $act = ($mnu[2] == $sub)? "ID='mnuactive'" : "";
  echo "<span class=mnuitem $act onClick=\"location='?mnux=$mnu[1]&sub=$mnu[2]'\">$mnu[0]</span>";
}
echo "</p>";
?>
