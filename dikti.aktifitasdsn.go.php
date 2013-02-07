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
		$dosenid = $_SESSION["DBF-DOSENID-$pos"];
		$tahun = $_SESSION["DBF-TAHUN"];
		$prodi = $_SESSION["DBF-PRODI"];
		$alfabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$konf = array();
	
		$arrAlfabet = str_split($alfabet);
		for($i=0; $i<=count($arrAlfabet); $i++){
			$konf[$arrAlfabet[$i]] = $i+1;
		}
		$persen = ($max < 0)? "0" : number_format($pos/$max * 100, 2);
    echo "<h1>$persen %</h1> Processing: $dosenid";
		
		Create($dosenid, $tahun, $prodi, $konf, $DBFName);
		
		if ($pos < $max) {
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 30);</script>";
		}
		else {
			echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
			<input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$DBFName'\">
			</p>";
		}
	}
	
	function Create($dosenid, $tahun, $prodi, $konf, $DBFName){
		//$_prd = (empty($_SESSION['prodi'])) ? "" : "and INSTR(j.ProdiID, '.$prodi.')>0";
    $s = "select j.MKKode, mk.ProdiID, j.NamaKelas, j.DosenID, j.RencanaKehadiran, j.Kehadiran, j.JadwalID,
					d.NIDN
						from jadwal j
					left outer join dosen d on j.DosenID = d.Login
					left outer join mk on mk.MKID = j.MKID
						where j.DosenID = '$dosenid'
						and TahunID = '$tahun'
						and j.JenisJadwalID = 'K'
					group by d.Login, MKKode, NamaKelas
					order by d.Login, MKKode, NamaKelas";
		//$PD = GetFields("prodi", 'ProdiID', $prodi, "JenjangID, ProdiDiktiID");
		$KDPT = '031010';
		$r = _query($s);
		while($w = _fetch_array($r)) {
		  $DsnTam = GetaField('jadwaldosen', "JadwalID", $w['JadwalID'], "DosenID");
			if (!empty($DsnTam)) {
			  $NID = GetaField('dosen', 'Login', $DsnTam, 'NIDN');
			  $NID = (empty($NID)) ? $DsnTam : $NID;
        $Data = array(
								$tahun,
								$KDPT, 
								$PD['ProdiDiktiID'],
								$PD['JenjangID'],
								$NID,
								$w['MKKode'],
								$NamaKelas,
								$w['RencanaKehadiran'],
								$w['Kehadiran']
								);
			 InsertDataDBF($DBFName, $Data);
			}
      $NamaKelas = $konf[$w['NamaKelas']];
			$NamaKelas = (strlen($NamaKelas) == 1) ? "0".$NamaKelas : $NamaKelas;
			$PD = GetFields("prodi", 'ProdiID', $w['ProdiID'], "JenjangID, ProdiDiktiID");
			$NIDN = (!empty($w['NIDN'])) ? $w['NIDN'] : $w['DosenID'];
        $Data = array(
								$tahun,
								$KDPT, 
								$PD['ProdiDiktiID'],
								$PD['JenjangID'],
								$NIDN,
								$w['MKKode'],
								$NamaKelas,
								$w['RencanaKehadiran'],
								$w['Kehadiran']
								);
			 InsertDataDBF($DBFName, $Data);
		}
	}
?>
