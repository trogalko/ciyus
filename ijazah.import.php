<?
session_start();
$arrBulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                    'Agustus', 'September', 'Oktober', 'November', 'Desember');
	include "db.mysql.php";
	include "connectdb.php";
	include "dwo.lib.php";
	include "dbf.function.php";
	CreateDBFIjazah();
	include "disconnectdb.php";
	
  function CreateDBFIjazah(){	
		global $arrBulan;
    $_SESSION["DBF-POS"]++;
		$pos = $_SESSION["DBF-POS"];
		$max = $_SESSION["DBF-MAX"];
		$DBFName = $_SESSION["DBF-FILES"];
		$MhswID = $_SESSION["DBF-MHSWID-$pos"];
		$prd = GetaField('mhsw', 'MhswID', $MhswID, 'ProdiID');
		
		$persen = ($max < 0)? "0" : number_format($pos/$max * 100, 2);
    echo "<h1>$persen %</h1> Processing: $MhswID";
		
		Create($MhswID, $DBFName);
		
		if ($pos < $max) {
			echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 30);</script>";
		}
		else {
			echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
			<input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$DBFName&nm=IJAZAH-$prd'\">
			</p>";
		}
	}
	
	function Create($mhswid, $DBFName){
		global $arrBulan;
		
    $mhsw = GetFields('mhsw', 'MhswID', $mhswid, "Nama, ProdiID, TempatLahir, date_format(TanggalLahir, '%Y-%m-%e') as TanggalLahir, NoIjazah, date_format(TglSKKeluar, '%Y-%m-%e') as TglSKKeluar, SKKeluar, TahunID");
		$ta   = GetFields('ta', "NA='N' and MhswID", $mhswid, "date_format(TglSKYudisium, '%Y-%m-%e') as TglSKYudisium");
		$PRD  = GetFields('prodi', "ProdiID", $mhsw['ProdiID'], "NoSKBAN, date_format(TglSKBAN, '%Y-%m-%e') as TglSKBAN");
		$fak  = substr($mhsw['ProdiID'], 0, 1);
    $Dekan  = GetaField('fakultas', "FakultasID", $fak, "Pejabat");
    $Rektor = GetaField('pejabat', 'JabatanID', 'REKTOR', 'Nama');
		
		$TglLahir = BuatTanggal($mhsw['TanggalLahir']);
		$TglRektor= BuatTanggal($mhsw['TglSKKeluar']);
		$TglLLS   = BuatTanggal($ta['TglSKYudisium']);
		$TglBAN   = BuatTanggal($PRD['TglSKBAN']);
		$TempatLahir = strtoupper($mhsw['TempatLahir']);
		
			$Data = array(
								$mhswid, 
								'',
								$mhsw['Nama'],
								$TempatLahir,
								$TglLahir,
								$mhsw['NoIjazah'],
								$TglLLS,
								$TglRektor,
								$mhsw['SKKeluar'],
								$mhsw['TahunID'],
								$Rektor,
								$Dekan,
								$PRD['NoSKBAN'],
								$TglBAN,
								);
			InsertDataDBF($DBFName, $Data);
	}
	
	function BuatTanggal($tgl, $bhs='id') {
    global $arrBulan;
    
    $tmp = array();
    $tmp = explode('-', $tgl);
    $tmp[1] = (int) $tmp[1];
    $nm_b = $arrBulan[$tmp[1]];
    
    return $tmp[2] . ' ' . $nm_b . ' ' . $tmp[0];
	}
?>
