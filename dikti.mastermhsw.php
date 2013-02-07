<?php

include "header.dbf.php";
include "dbf.function.php";

function TampilkanHeaderMhsw() {
  //CheckFormScript("DariNPM, SampaiNPM");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='CreateDBFMHS'>
  <tr><td class=wrn>$_SESSION[KodeID]</td>
    <td class=inp>Dari NPM :</td>
    <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50></td>
    <td class=inp>Sampai NPM :</td>
    <td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50></td>
    <td class=ul><input type=submit name='Cetak' value='Proses'></td>
  </form></table></p>";
}
	
function CreateDBFMHS() {
	global $HeaderMSHS;
	if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "'$_SESSION[DariNPM]' <= MhswID and MhswID <= '$_SESSION[SampaiNPM]' ";
  } else {
    $drmhsw = GetaField('mhsw', "NA", "N", "min(MhswID)");
    $smpmhsw = GetaField('mhsw', "NA", "N", "max(MhswID)");
    $_npm = "'$drmhsw' <= MhswID and MhswID <= '$smpmhsw' ";
  }
	
  $s = "select MhswID
    from mhsw
    where $_npm
		order by MhswID";
  $r = _query($s);
  $n = 0;
	
	$DBFName = "dikti/MHMHS-20062.DBF";
	DBFCreate($DBFName, $HeaderMSHS);
	
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["DBF-MHSWID-$n"] = $w['MhswID'];
  }
	$_SESSION["DBF-FILES"] = $DBFName;
  $_SESSION["DBF-POS"] = 0;
  $_SESSION["DBF-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>$n</font> data.</p>";
  echo "<p><IFRAME src='dikti.mastermhsw.go.php' frameborder=0 height=400 width=600>
  </IFRAME></p>";
}

$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

TampilkanJudul("Proses membuat Master Mahasiswa untuk Dikti");
TampilkanHeaderMhsw();
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

?>
