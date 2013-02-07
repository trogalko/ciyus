<?
session_start();
	include "db.mysql.php";
	include "connectdb.php";
	include "dwo.lib.php";
	include "dbf.function.php";
	CreateDBFMSDOSGo();
	include "disconnectdb.php";
	
	function CreateDBFMSDOSGo(){	
		$_SESSION["DBF-POS"]++;
		$pos = $_SESSION["DBF-POS"];
		$max = $_SESSION["DBF-MAX"];
		$DBFName = $_SESSION["DBF-FILES"];
		$dosenid = $_SESSION["DBF-DOSENID-$pos"];
		
		$persen = ($max < 0)? "0" : number_format($pos/$max * 100, 2);
    echo "<h1>$persen %</h1> Processing: $dosenid";
		
		Create($dosenid, $DBFName);
		
		if ($pos < $max) {
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 30);</script>";
		}
		else {
			echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
			<input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$DBFName'\">
			</p>";
		}
	}
	
	function Create($dosenid, $DBFName){
		$w = GetFields("dosen d left outer join prodi p on p.ProdiID = d.Homebase", "Login", $dosenid, "d.*, p.ProdiDiktiID, p.JenjangID");
		$KDPT = '031010';
		$KDJenPT = 'C';
		
		//$w = _fetch_array($r);
			$Kelamin = ($w['KelaminID'] == 'W') ? "P" : "L";
			$Data = array(
								$KDPT, 
								$w['ProdiDiktiID'],
								$w['JenjangID'],
								$w['KTP'],
								$w['NIDN'],
								$w['Nama'],
								$w['Gelar'],
								$w['TempatLahir'],
								$w['TglLahir'],
								$Kelamin,
								$w['JabatanID'],
								$w['JenjangID'],
								$w['StatusKerjaID'],
								$w['TglLulus'],
								$w['TglBekerja'],
								$w['NIPPNS'],
								$w['Login']
								);
			InsertDataDBF($DBFName, $Data);
	}
?>
