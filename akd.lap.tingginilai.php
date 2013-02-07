<?php
//Author Sugeng. S
//Juni 2006
session_start();
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
daftar();
include_once "disconnectdb.php";
include_once "krs.lib.php";

function daftar(){
	global $_lf;
	echo "<body bgcolor=#EEFFFF>";
   // Parameters
  $pos = $_SESSION['NILAI-POS'];
  $max = $_SESSION['NILAI-MAX'];
  $nmf = $_SESSION['NILAI-FILE'];
  $_khsid = $_SESSION['khsid'];
  $khsid = $_khsid[$pos];
  if ($pos < $max) {
  $mhswid = GetaField('khs',"TahunID = '$_SESSION[tahun]' and KHSID", $khsid,'mhswid');	  
		$q = "SELECT mk.MKKODE as mkkode, mk.Nama as mknama, krsprc.SKS as SKS, krsprc.GradeNilai as nilai, 
			  krsprc.TahunID as thnid, krsprc.BobotNilai as bobot, krsprc.SKS * krsprc.BobotNilai as nxk		
			  from krsprc
			  	left outer join mk on mk.MKID = krsprc.MKID
			  where 
		 	 	krsprc.MhswID = '$mhswid' 
                and krsprc.GradeNilai not in ('-','')
                order by mk.MKKODE"; //and
				//krs.TahunID = '$_SESSION[tahun]'";
		  
		$hsl = _query($q);
		//buat file
		$maxcol = 100; 
		$nmf = "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'a');
  		fwrite($f, chr(27).chr(15));
  		$div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  		$n = 0; $hal = 1;
  		$brs = 0;
  		$maxbrs = 40;
		$_Tgl = date("d-m-Y");
	
		$FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$mhswid,
					"p.nama as pnama, f.nama as fnama");
		$BatasStudi = GetaField("mhsw", "MhswID", $mhswid,"BatasStudi");
		$BatasStudi = NamaTahun($BatasStudi);
		$DtMhs	= GetFields('mhsw','MhswID',$mhswid,"Nama,IPK");
		$DosenPA = GetFields('dosen left outer join mhsw on mhsw.PenasehatAkademik = dosen.Login',"mhsw.MhswID",$mhswid,"dosen.nama as dnama, dosen.Login as ldosen");
		$JmlSKS = GetFields('krsprc',"GradeNilai not in ('', '-') and MhswID",$mhswid,"Sum(SKS) as jSKS ,SUM(SKS * BobotNilai) as jx");
		//$JmlX = GetaField('krs')
		$hdr = str_pad("*** Rekapitulasi History Nilai Tertinggi Per Semester ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
			
		$hdr .= str_pad("NIM", 15, ' ') . str_pad(":", 2, ' ') . str_pad($mhswid .' '. $DtMhs['Nama'],87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("BATAS STUDI",15,' ') . str_pad(":", 2, ' ') . str_pad("Semester " . $BatasStudi,87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("P.A.",15,' ') . str_pad(":", 2, ' ') . str_pad($DosenPA['ldosen'].' '.$DosenPA['dnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  		$hdr .= "No.  MATA KULIAH           			         SKS    PRD    NILAI     BOBOT      NxK" .$_lf.$div;
  		fwrite($f, $hdr);
		//ISI
		$jumrec = _num_rows($hsl);
		$jumhal = ceil($jumrec/$maxbrs);
		while($w = _fetch_array($hsl)){
			$n++; $brs++;
			if($brs > $maxbrs){
				fwrite($f, $div);
				fwrite($f,str_pad("Halaman : ".$hal.'/'.$jumhal,10,' ').$_lf);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
			}
			$pra = GetaField("mk","MKID",$w['mkpra'],"MKKODE");
			$isi = str_pad($n.'.', 4, ' ') . ' ' .
      		str_pad($w['mkkode'], 6) . ' '.
      		str_pad($w['mknama'], 46) . ' '.
      		str_pad($w['SKS'], 4, ' ').
      		str_pad($w['thnid'], 9) .' '.
      		str_pad($w['nilai'], 4, ' '). ' '.
			str_pad($w['bobot'], 8,' ',STR_PAD_LEFT).
			str_pad($w['nxk'], 9, ' ', STR_PAD_LEFT).
      		$_lf;
    		fwrite($f, $isi);
		}
		$ipk = $JmlSKS['jx']/$JmlSKS['jSKS']; 
		//fwrite($f, str_pad("Jumlah SKS : ",50, ' ',STR_PAD_LEFT).str_pad($JMLSKS,10,' ',STR_PAD_LEFT ).$_lf);
		fwrite($f, $div);
		fwrite($f,str_pad("IPK  " . number_format($ipk,2),37,' ',STR_PAD_LEFT) . str_pad($JmlSKS['jSKS'],23,' ',STR_PAD_LEFT). str_pad($JmlSKS['jx'],35,' ',STR_PAD_LEFT) . $_lf);
		fwrite($f,$div);
		fwrite($f,str_pad("Halaman : ".$hal.'/'.$jumhal,10,' ').$_lf);
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 80,' ', STR_PAD_LEFT).$_lf.$_lf); 
  	    fwrite($f, str_pad("Akhir laporan", $maxcol, ' ', STR_PAD_LEFT));
		fwrite($f,chr(12));
		fclose($f);
  		//TampilkanFileDWOPRN($nmf, "akd.lap");
  	echo "<p>Proses Batch Cetak Nilai Tertinggi Per Mahasiswa: <font size=+2>$pos/$max</font><br />
  $khsid &raquo; $mhsw[Nama]</p>";
  echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else {
    echo "<p>Pembuatan file Cetak Laporan Histori Nilai Tertinggi telah selesai.<br />
	Untuk memulai mencetak klik: <a href='$nmf'><img src='img/printer.gif' border=0></a></p>";
  }
  $_SESSION['NILAI-POS']++;
}



/*
// *** Parameters ***
if ($_SESSION['_LevelID'] == 120) {
  $mhswid = $_SESSION['_Login'];
}
else {
  $mhswid = GetSetVar('mhswid');
}
$tahun = GetSetVar('tahun');
//$prodi = GetSetVar('prodi');
//$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Rekapitulasi History Nilai Per Semester");
TampilkanCariMhsw('akd.lap.tingginilai', 'Daftar');
if (!empty($tahun)) Daftar(); 
*/ 
?>
