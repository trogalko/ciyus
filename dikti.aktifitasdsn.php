<?php

include "header.dbf.php";
include "dbf.function.php";

function TampilkanHeaderAktifDSN() {
	global $arrID;
	CheckFormScript("tahun");
	$optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', "ProdiID");
	echo "<p><table class=box cellspacing=1 cellpadding=4>
	<form action='?' method=POST onSubmit=\"return CheckForm(this)\">
	<input type=hidden name='mnux' value='$_SESSION[mnux]'>
	<input type=hidden name='gos' value='CreateDBFAktifDSN'>
	<tr><th class=ttl colspan=5>$arrID[Nama]</th></tr>
	<tr><td class=inp>Tahun Akademik</td><td class=ul colspan=4><input type=text name=tahun value='$_SESSION[tahun]' size=15></td></tr>
	<tr><td class=inp>Prodi</td><td class=ul colspan=4><select name='prodi'>$optprd</td></tr>
	<td class=inp>Dari Dosen:</td>
	<td class=ul><input type=text name='DariDSN' value='$_SESSION[DariDSN]' size=20 maxlength=50></td>
	<td class=inp>Sampai Dosen:</td>
	<td class=ul><input type=text name='SampaiDSN' value='$_SESSION[SampaiDSN]' size=20 maxlength=50></td>
	<td class=ul><input type=submit name='Cetak' value='Proses'></td>
	</form></table></p>";
}
	
function CreateDBFAktifDSN() {
	global $HeaderAKTFDSN;
	if (!empty($_SESSION['DariDSN'])) {
    $_SESSION['SampaiDSN'] = (empty($_SESSION['SampaiDSN']))? $_SESSION['DariDSN'] : $_SESSION['SampaiDSN'];
    $_dsn = " and '$_SESSION[DariDSN]' <= Login and Login <= '$_SESSION[SampaiDSN]' ";
  } else $_dsn = '';
  
  $_prd = (empty($_SESSION['prodi'])) ? "" : "and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0";
	
  $s = "SELECT d.Login, j.JadwalID
        FROM dosen d
          LEFT OUTER JOIN jadwal j ON j.DosenID = d.Login
          WHERE j.TahunID ='$_SESSION[tahun]'
        AND j.JenisJadwalID = 'K'
      GROUP BY d.Login
      ORDER BY d.Login";
  $r = _query($s);
  $n = 0;
	//echo "<pre>$s</pre>";
	$DBFName = "dbf/TRAKD-$_SESSION[tahun].DBF";
	DBFCreate($DBFName, $HeaderAKTFDSN);
	
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["DBF-DOSENID-$n"] = $w['Login'];
  }
	
	$_SESSION["DBF-PRODI"] = $_SESSION['prodi'];
	$_SESSION["DBF-TAHUN"] = $_SESSION['tahun'];
	$_SESSION["DBF-FILES"] = $DBFName;
  $_SESSION["DBF-POS"] = 0;
  $_SESSION["DBF-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>$n</font> data.</p>";
  echo "<p><IFRAME src='dikti.aktifitasdsn.go.php' frameborder=0 height=400 width=600>
  </IFRAME></p>";
}

$DariDSN = GetSetVar('DariDSN');
$SampaiDSN = GetSetVar('SampaiDSN');
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');

TampilkanJudul("Proses membuat Master Dosen untuk Dikti");
TampilkanHeaderAktifDSN();
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

?>
