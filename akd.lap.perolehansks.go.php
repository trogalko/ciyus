<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 28 November 2006
// setio.dewo@gmail.com
// Desc: mencetak daftar perolehan SKS mahasiswa secara massal

session_start();
$_SESSION['bhs'] = 'id';
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
ProsesPerolehanSKS();
include "disconnectdb.php";

// *** Functions ***
function ProsesPerolehanSKS() {
  $_SESSION["PERO-POS"]++;
  $pos = $_SESSION["PERO-POS"];
  $max = $_SESSION["PERO-MAX"];

  $MhswID = $_SESSION["PERO-MhswID-$pos"];
  if (!empty($MhswID)) {
    $mhsw = GetFields("mhsw m
    left outer join dosen d on m.PenasehatAkademik=d.Login
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join fakultas fak on prd.FakultasID=fak.FakultasID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID",
    "m.MhswID", $MhswID,
    "m.*, concat(d.Nama, ', ', d.Gelar) as PA,
    date_format(m.TanggalLahir, '%d %M %Y') as TGLLHR, 
    prg.Nama as PRG, prd.Nama as PRD, prd.Gelar, sm.Nama as SM, sm.Keluar, sm.Nilai,
    fak.FakultasID, fak.Nama as FAK, 
    fak.Pejabat, fak.Jabatan");
    PerolehanSKS($mhsw);
    
    $persen = ($max <= 0)? "0" : number_format($pos/$max * 100, 2);
    echo "<h1>$persen %</h1> Processing: $MhswID";
  }

  if ($pos <= $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 10);</script>";
  }
  else {
    include_once "dwoprn.php";
    DownloadDWOPRN($_SESSION["PERO-FILE"]);
  }
}
function PerolehanSKS($mhsw) {
  $_lf = chr(13).chr(10);
  $nmf = $_SESSION["PERO-FILE"];
  $f = fopen($nmf, 'a');
  fwrite($f, chr(27).chr(18));
  
  // Tampilkan Header
  $div = str_pad('-', 79, '-').$_lf;
  $hdr = str_pad("*** DAFTAR PEROLEHAN SKS ***", 79, ' ', STR_PAD_BOTH) .$_lf.$_lf.
    "NPM / NAMA      : " . $mhsw['MhswID'] . '  ' . $mhsw['Nama'] . $_lf.
    "FAK / JUR       : " . $mhsw['FAK'] . ' / ' . $mhsw['PRD'] . $_lf.
    //"IPK             : " . $mhsw['IPK'] . $_lf.
    "Masa Studi      : " . NamaTahun($mhsw['BatasStudi']) . $_lf.
    "Penasehat Akd.  : " . $mhsw['PA'] . $_lf.
    $div.
    "No.  Kode       Matakuliah                                       SKS   Nilai".$_lf.$div;
  fwrite($f, $hdr); 
  // matakuliah yg diambil
  $s = "select concat(LEFT(krs.MKKode, 3), ' ', SUBSTRING(krs.MKKode, 4, 3)) as MKKode, 
    LEFT(mk.Nama, 45) as NamaMK, LEFT(mk.Nama_en, 40) as NamaMK1,
    krs.BobotNilai, krs.GradeNilai, krs.SKS
    from krsprc krs
      left outer join mk mk on krs.MKID=mk.MKID
    where 
	krs.MhswID='$mhsw[MhswID]' and krs.BobotNilai > 0
	
	and (GradeNilai <> '-' and GradeNilai <> '' and not GradeNilai is NULL)
	
    order by krs.MKKode asc, krs.BobotNilai desc";
  $r = _query($s); 
  $n = 0; 
  $brs = 0;
  $maxbrs = 42;
  $hal = 0;
  $mk = '';
  $_sks = 0;
  $_bbt = 0;
  while ($w = _fetch_array($r)) {
    if ($mk != $w['MKKode']) {
      $mk = $w['MKKode'];
      $n++; $brs++;
      $NamaMK = ($_SESSION['bhs'] == 'id')? $w['NamaMK'] : $w['NamaMK1'];
      $_sks += $w['SKS'];
      $_bbt += $w['SKS'] * $w['BobotNilai'];
      fwrite($f, str_pad($n.'.', 4) . ' '.
        str_pad($w['MKKode'], 10) . ' '.
        str_pad($NamaMK, 45) . '  '.
        str_pad($w['SKS'], 4, ' ', STR_PAD_LEFT) . '    '.
        str_pad($w['GradeNilai'], 3, ' ') . $_lf);
      if ($brs >= $maxbrs) {
        $brs = 0;
        $hal++;
        fwrite($f, $div . str_pad("Hal. ".$hal, 79, ' ', STR_PAD_LEFT).$_lf);
        fwrite($f, chr(12));
        fwrite($f, $hdr);
      } 
    }
  }
  $_ipk = ($_sks > 0)? $_bbt / $_sks : 0;
  fwrite($f, $div);
  fwrite($f, "Jumlah Kredit yang Telah Diambil: $_sks SKS, IPK: " . number_format($_ipk, 2).
    $_lf.$div);
  fwrite($f, str_pad("Dicetak Oleh : " . $_SESSION['_Login'] . ', ' . Date("d-m-Y H:i"), 30, ' ').$_lf);
  fwrite($f, chr(12));
  fclose($f);
}
?>
