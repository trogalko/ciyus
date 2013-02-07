<?php

include "header.dbf.php";
include "dbf.function.php";

function Daftar(){
  global $HeaderAlamat;
  $s = "select m.* from mhsw m
          left outer join khs k on k.MhswID = m.MhswID
        where k.TahunID = '$_SESSION[tahun]'
          and k.StatusMhswID = 'A'";
  $r = _query($s);
  
  $DBFName = "tmp/".date('m')."Alamat-$_SESSION[tahun].DBF";
	DBFCreate($DBFName, $HeaderAlamat);
  
  while ($w = _fetch_array($r)) {
    $n++;
    $Alamat = (empty($w['Alamat'])) ? $w['AlamatAsal'] : $w['Alamat'];
    $Kota = (empty($w['Kota'])) ? $w['KotaAsal'] : $w['Kota'];
    $RT = (empty($w['RT'])) ? $w['RTAsal'] : $w['RT'];
    $RW = (empty($w['RW'])) ? $w['RWAsal'] : $w['RW'];
    $KODEPOS = (empty($w['KodePos'])) ? $w['KodePosAsal'] : $w['KodePos'];
    $Telepon = (empty($w['Telepon'])) ? $w['TeleponAsal'] : $w['Telepon'];
    
    $RW = (empty($RW)) ? '' : "/$RW";
    $Data = array($n,
            $w['MhswID'],
            $w['Nama'],
            $Alamat,
            $RT . $RW,
            $Kota,
            $KODEPOS,
            $Telepon);
    InsertDataDBF($DBFName, $Data);
  }
  echo "<hr><p>Proses pembuatan file <b>Berhasil</b>. Silakan download file di:
			<input type=button name='Download' value='Download File' onClick=\"location='downloaddbf.php?fn=$DBFName'\">
			</p>";
}

$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Alamat Mahasiswa");
TampilkanTahunProdiProgram('baa.khs.alamat', 'Daftar');
if (!empty($tahun)) Daftar();
?>
