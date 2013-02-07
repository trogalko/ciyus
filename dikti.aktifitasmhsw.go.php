<?php

	session_start();
	include "db.mysql.php";
	include "connectdb.php";
	include "dwo.lib.php";
	include "dbf.function.php";
	CreateDBFTRAKDGo();
	include "disconnectdb.php";
	
	function CreateDBFTRAKDGo(){	
		$_SESSION["DBF-POS"]++;
		$pos = $_SESSION["DBF-POS"];
		$max = $_SESSION["DBF-MAX"];
		$DBFName = $_SESSION["DBF-FILES"];
		$tahun = $_SESSION["DBF-TAHUN"];
		$mhswid = $_SESSION["DBF-MHSWID-$pos"];
		
		$persen = ($max < 0)? "0" : number_format($pos/$max * 100, 2);
    echo "<h1>$persen %</h1> Processing: $mhswid";
		
		Create($mhswid, $tahun, $DBFName);
		
		if ($pos < $max) {
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 30);</script>";
		}
		else {
			echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
			<input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$DBFName'\">
			</p>";
		}
	}
	
	function Create($mhswid, $tahun, $DBFName){
		$w = GetFields("khs k left outer join prodi p on p.ProdiID = k.ProdiID left outer join mhsw m on m.MhswID=k.MhswID", "k.TahunID = '$tahun' and k.MhswID", $mhswid, "k.*, p.ProdiDiktiID, p.JenjangID, m.TotalSKS as TotalSKSAmbil");
		//$jumsks = GetaField("krs k left outer join jadwal j on j.JadwalID = k.JadwalID", "MhswID='$mhswid' and j.JenisJadwalID<>'R' and j.JadwalSer=0 and k.JadwalID", $tahun, 'sum(j.SKS)')+0;
    $jumsks = GetaField("krsprc", "TahunID <", $tahun, "sum(SKS) as TSKS");
    $w['TotalSKSAmbil'] = ($w['TotalSKSAmbil'] > $jumsks) ? $w['TotalSKSAmbil'] : $jumsks;
    $KDPT = '031010';
			$Data = array(
								$w['TahunID'],
								$KDPT,
								$w['ProdiDiktiID'],
								$w['JenjangID'],
								$w['MhswID'],
								$w['IPS'],
								$w['TotalSKS'],
								$w['IP'],
								$w['TotalSKSAmbil']
								);
			InsertDataDBF($DBFName, $Data);
	}
?>
