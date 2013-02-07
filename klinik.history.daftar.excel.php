<?php
// Author: Emanuel Setio Dewo
// Email : markus.hardiyanto@gmail.com
// Creation Date: 23 November 2006
// Description: Code to create excel file for Nilai Mahasiswa

session_start();
// *** Buat File ***
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
BuatExcel();
include_once "disconnectdb.php";

function BuatExcel() {
  $DariNPM = $_REQUEST['DariNPM'];
  $SampaiNPM = $_REQUEST['SampaiNPM'];
	
	// Buat file Excel
	include_once "Spreadsheet/Excel/Writer.php";
	$xls =& new Spreadsheet_Excel_Writer();
	$xls->send("historyklinik.xls");
	$sh =& $xls->addWorksheet('History Klinik');
	
	// Format untuk title cell
	$formattitle =&$xls->addFormat();
	$formattitle->setAlign('center');
	$formattitle->setBold();
	$formattitle->setSize(16);
	
	// Format untuk header cell
	$formatheader =& $xls->addFormat();
	$formatheader->setBorder(1);
	$formatheader->setAlign('center');
	$formatheader->setBold();
	
	// Format untuk data cell
	$format =& $xls->addFormat();
	$format->setAlign('left');
	$format->setBorder(0);
	
	// Format spesifik untuk nama mahasiswa
	$formatmhs =& $xls->addFormat();
	$formatmhs->setAlign('left');
	$formatmhs->setBorder(1);
	
	// Format numerik
	$fmtnum =& $xls->addFormat();
	$fmtnum->setAlign("right");
	
	// Cetak header file excel
	$sh->setMerge(0, 0, 0, 5);
	$sh->write(0, 0, "History Mahasiswa", $formattitle);
	$sh->setMerge(1, 0, 1, 5);
	$sh->write(1, 0, "Dari NPM: $DariNPM s/d $SampaiNPM", $formattitle);
	
	$hd = 2;
	$sh->write($hd, 0, "No", $formatheader);
	$sh->write($hd, 1, "N.P.M", $formatheader);
	$sh->write($hd, 2, "Nama", $formatheader);
	$sh->write($hd, 3, "IPK", $formatheader);
	$sh->write($hd, 4, "Lls SKed", $formatheader);
	$sh->write($hd, 5, "IPK SKed", $formatheader);
	// Daftar Matakuliah
	$prodi = '11';
	$kurid = GetaField('kurikulum', "NA='N' and ProdiID", $prodi, "KurikulumID");
	$smk = "select MKID, MKKode, Singkatan, Nama
	  from mk
	  where KurikulumID='$kurid' and NA='N'
	  order by Sesi, MKKode";
	$rmk = _query($smk);
	$nmk = 5;
	$arrmk = array();
	while ($wmk = _fetch_array($rmk)) {
	  $nmk++;
	  $arrmk[$nmk] = $wmk['MKKode'];
	  $NamaMK = (empty($wmk['Singkatan']))? $wmk['Nama'] : $wmk['Singkatan'];
    $sh->write($hd, $nmk, $wmk['MKKode'], $formatheader);
    $sh->write($hd+1, $nmk, $NamaMK, $format);	
	}
 	
 	// Ambil data
	$s = "SELECT k.KRSID, k.KHSID, k.TahunID, k.MKKode, k.GradeNilai, k.BobotNilai, k.NilaiAkhir, 
       m.MhswID, m.Nama AS NamaMhsw, m.IPK, m.TahunLulus, m.NilaiSekolah
		 FROM krs k
		   LEFT OUTER JOIN mhsw m ON k.MhswID=m.MhswID
		 WHERE k.MhswID=m.MhswID
		   and ('$DariNPM' <= k.MhswID) and (k.MhswID <= '$SampaiNPM')
		 ORDER BY k.MhswID, k.MKKode";
	$r = _query($s);
	//$sh->write(0, 0, $s);
 	// Cetak data
 	$sh->setColumn(0, 0, 5); // Kolom nomer
 	$sh->setColumn(0, 1, 12); // Kolom MhswID
 	$sh->setColumn(0, 2, 30); // Kolom Nama Mahasiswa
 	$n = 0;
 	$_MhswID = 'qwertyuiop';
 	while ($w = _fetch_array($r))	{
 	  if ($_MhswID != $w['MhswID']) {
		  $n++;
		  $_MhswID = $w['MhswID'];
		  $rw = $n + 3;
		  $sh->write($rw, 0, $n, $format);
		  $sh->write($rw, 1, $w['MhswID'], $format);
		  $sh->write($rw, 2, $w['NamaMhsw'], $format);
      $sh->write($rw, 3, $w['IPK'], $fmtnum);
      $sh->write($rw, 4, $w['TahunLulus'], $format);
      $sh->write($rw, 5, $w['NilaiSekolah'], $format); 
		  //$sh->write($rw, 3, $w['MKKode'], $format);
		}
		$col = array_search($w['MKKode'], $arrmk);
		if ($col > 0) {
      $sh->write($rw, $col, $w['GradeNilai'], $format);
    }
	}
	$xls->close();
}
?>
