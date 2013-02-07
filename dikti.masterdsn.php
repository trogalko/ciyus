<?php

include "header.dbf.php";
include "dbf.function.php";

function TampilkanHeaderDSN() {
  //CheckFormScript("DariDSN, SampaiDSN");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='CreateDBFDSN'>
  <tr><td class=wrn>$_SESSION[KodeID]</td>
    <td class=inp>Dari Dosen:</td>
    <td class=ul><input type=text name='DariDSN' value='$_SESSION[DariDSN]' size=20 maxlength=50></td>
    <td class=inp>Sampai Dosen:</td>
    <td class=ul><input type=text name='SampaiDSN' value='$_SESSION[SampaiDSN]' size=20 maxlength=50></td>
    <td class=ul><input type=submit name='Cetak' value='Proses'></td>
  </form></table></p>";
}
	
function CreateDBFDSN() {
	global $HeaderMSDOS;
	if (!empty($_SESSION['DariDSN'])) {
    $_SESSION['SampaiDSN'] = (empty($_SESSION['SampaiDSN']))? $_SESSION['DariDSN'] : $_SESSION['SampaiDSN'];
    $_dsn = "'$_SESSION[DariDSN]' <= Login and Login <= '$_SESSION[SampaiDSN]' ";
  } else {
    $drdsn = GetaField('dosen', "NA", "N", "min(Login)");
    $smpdsn = GetaField('dosen', "NA", "N", "max(Login)");
    $_dsn = "'$drdsn' <= Login and Login <= '$smpdsn' ";
  }
	
  $s = "select Login
    from dosen
    where $_dsn
		order by Login";
  $r = _query($s);
  $n = 0;
	
	$DBFName = "dikti/MSDOS-$_SESSION[tahun].DBF";
	DBFCreate($DBFName, $HeaderMSDOS);
	
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["DBF-DOSENID-$n"] = $w['Login'];
  }
	$_SESSION["DBF-FILES"] = $DBFName;
  $_SESSION["DBF-POS"] = 0;
  $_SESSION["DBF-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>$n</font> data.</p>";
  echo "<p><IFRAME src='dikti.masterdsn.go.php' frameborder=0 height=400 width=600>
  </IFRAME></p>";
}

$DariDSN = GetSetVar('DariDSN');
$SampaiDSN = GetSetVar('SampaiDSN');

TampilkanJudul("Proses membuat Master Dosen untuk Dikti");
TampilkanHeaderDSN();
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

?>
