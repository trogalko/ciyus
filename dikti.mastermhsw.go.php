<?php

	session_start();
	include "db.mysql.php";
	include "connectdb.php";
	include "dwo.lib.php";
	include "dbf.function.php";
	CreateDBFMSMHSGo();
	include "disconnectdb.php";
	
	function CreateDBFMSMHSGo(){	
		$_SESSION["DBF-POS"]++;
		$pos = $_SESSION["DBF-POS"];
		$max = $_SESSION["DBF-MAX"];
		$DBFName = $_SESSION["DBF-FILES"];
		$mhswid = $_SESSION["DBF-MHSWID-$pos"];
		
		$persen = ($max < 0)? "0" : number_format($pos/$max * 100, 2);
    echo "<h1>$persen %</h1> Processing: $mhswid";
		
		Create($mhswid, $DBFName);
		
		if ($pos < $max) {
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 30);</script>";
		}
		else {
			echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
			<input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$DBFName'\">
			</p>";
		}
	}
	
	function Create($mhswid, $DBFName){
		//$s = "select m.*, p.ProdiDiktiID, p.JenjangID from mhsw m
		//				left outer join prodi p on p.ProdiID = m.ProdiID
		//				where MhswID = '$mhswid'";
		//$r = _query($s);
		$w = GetFields("mhsw m left outer join prodi p on p.ProdiID = m.ProdiID", "MhswID", $mhswid, "m.*, date_format(m.TanggalLahir, '%Y%m%d') as TanggalLahir, p.ProdiDiktiID, p.JenjangID");
		$KDPT = '04329';
		$KDJenPT = 'C';
		
		//$w = _fetch_array($r);
			$StatusAwal = (!empty($w['AsalPT'])) ? "P" : "B";
			$Kelamin = ($w['KelaminID'] == 'W') ? "P" : "L";
			$Data = array(
								$KDPT, 
								$w['ProdiDiktiID'],
								$w['JenjangID'],
								$w['MhswID'],
								$w['Nama'],
								$w['TempatLahir'],
								$w['TanggalLahir'],
								$Kelamin,
								$w['TahunID'],
								$w['TahunID'].'1',
								$w['BatasStudi'],
								$w['AsalPT'],
								$w['TglMasuk'],
								$w['TglLulus'],
								$w['StatusMhswID'],
								$StatusAwal,
								$w['TotalSKSPindah'],
								$w['MhswIDAsalPT'],
								$w['AsalPT'],
								$KDJenPT,
								$w['ProdiAsalPT'],
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								''
								);
			InsertDataDBF($DBFName, $Data);
	}
?>
