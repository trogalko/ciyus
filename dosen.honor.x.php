<?php
// Author: Emanuel Setio Dewo
// 02 April 2006

// *** Functions ***
function DftrDsn() {
  if (!empty($_SESSION['tahun'])) {
    $_whr = array();
	if (!empty($_SESSION['prodi'])) $_whr[] = "INSTR(j.ProdiID, '.$_SESSION[prodi].')>0";
	if (!empty($_SESSION['prid'])) $_whr[] = "INSTR(j.ProgramID, '.$_SESSION[prid].')>0";
	$whr = (empty($_whr))? '' : 'and '. implode(' and ', $_whr);
    $s = "select j.*, h.Nama as HR,
	  time_format(j.JamMulai, '%H:%i') as JM,
	  time_format(j.JamSelesai, '%H:%i') as JS
	  from jadwal j
	    left outer join hari h on j.HariID=h.HariID
	  where j.TahunID='$_SESSION[tahun]' $whr
	  order by j.ProgramID, j.ProdiID, j.MKKode";
	$r = _query($s); $nmr = 0; $_prodi = '';
	$hdr = "<tr><th class=ttl>#</th>
	  <th class=ttl>Kode</th>
	  <th class=ttl>Matakuliah</th>
	  <th class=ttl>Kelas</th>
	  <th class=ttl>SKS</th>
	  <th class=ttl>PROG</th>
	  <th class=ttl>RG</th>
	  <th class=ttl>Hari</th>
	  <th class=ttl>Jam</th>
	  <th class=ttl>Dosen</th>
	  <th class=ttl>per SKS</th>
	  <th class=ttl>per Kuliah</th>
	  <th class=ttl>Transport</th>
	  <th class=ttl>Lain2</th>
	  </tr>";
	echo "<p><table class=box cellspacing=1 cellpadding=4>";
	while ($w = _fetch_array($r)) {
	  $nmr++;
	  if ($_prodi != $w['ProdiID']) {
		// Ambil nama prodi
		$_prodi = $w['ProdiID'];
		if (!empty($w['ProdiID'])) {
		  $arrprodi = explode('.', TRIM($w['ProdiID'], '.'));
		  $strprodi = implode(',', $arrprodi);
		  $prodi = (empty($strprodi))? '' : GetArrayTable("select Nama from prodi where ProdiID in ($strprodi) order by ProdiID",
		    "ProdiID", "Nama", ', ');
		} else $prodi = '';
	    $nmprodi = GetaField('prodi', 'ProdiID', $_prodi, 'Nama');
	    echo "<tr><td class=ul colspan=15><b>$prodi</b></td></tr>";
		echo $hdr;
	  }
	  // Ambil data dosen
	  if (!empty($w['DosenID'])) {
	    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
        $strdosen = implode(',', $arrdosen);
        $dosen = (empty($strdosen))? '' : GetArrayTable("select concat('» ', Nama) as NM from dosen where Login in ($strdosen) order by Nama",
          "Login", "NM", '<br /> ');
	  } else $dosen = '';

	  $c = "class=ul";
	  echo "<tr><td class=inp>$nmr</td>
	    <td $c nowrap>$w[MKKode]</td>
		<td $c>$w[Nama]</td>
		<td $c>$w[NamaKelas]&nbsp;</td>
		<td $c>$w[SKS] ($w[SKSAsli])</td>		
		<td $c>$w[ProgramID]</td>
		<td $c>$w[RuangID]</td>
		<td $c>$w[HR]</td>
		<td $c>$w[JM]~$w[JS]</td>
		<td $c>$dosen&nbsp;</td>
		<td $c>&nbsp;</td>
		<td $c>&nbsp;</td>
		<td $c>&nbsp;</td>
		<td $c>&nbsp;</td>
		</tr>";
	}
	echo "</table></p>";
  }
}


// *** Parameters ***
$tahun = GetSetVar('tahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$gos = (empty($_REQUEST['gos']))? 'DftrDsn' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Honor Dosen");
TampilkanTahunProdiProgram("dosen.honor", "DftrDsn", '', '');
$gos();
?>
