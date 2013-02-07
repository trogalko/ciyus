<?php

	session_start();
	include "db.mysql.php";
	include "connectdb.php";
	include "dwo.lib.php";
	include "dbf.function.php";
	CreateDBFTRNLMGo();
	include "disconnectdb.php";
	
	function CreateDBFTRNLMGo(){	
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
		$mhsw = GetaField('mhsw', 'MhswID', $mhswid, 'ProdiID');
		$s = "select k.TahunID, k.MhswID, k.GradeNilai, k.BobotNilai, k.MKKode from krs k
					left outer join jadwal j on j.JadwalID = k.JadwalID
						where MhswID = '$mhswid'
						and k.TahunID = '$tahun'
						and StatusKRSID = 'A'
						and j.JenisJadwalID = 'K'";
		$r = _query($s);
		$PD = GetFields("prodi", 'ProdiID', $mhsw, "ProdiDiktiID, JenjangID");
		$KDPT = '031010';
		while($w = _fetch_array($r)){
			if ($w['GradeNilai'] == '-') $w['GradeNilai'] = 'T';
      $Data = array(
								$w['TahunID'],
								$KDPT,
								$PD['ProdiDiktiID'],
								$PD['JenjangID'],
								$w['MhswID'],
								$w['MKKode'],
								$w['GradeNilai'],
								$w['BobotNilai']
								);
			InsertDataDBF($DBFName, $Data);
		}
	}
?>
