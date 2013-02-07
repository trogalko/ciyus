<?php
//Author Sugeng. S
//Juni 2006

include_once "krs.lib.php";

function GetaField2($_tbl,$_key,$_value,$order,$_result) {
  global $strCantQuery;
	$_sql = "select distinct $_result from $_tbl where $_key='$_value' $order limit 1";
	$_res = _query($_sql);
	//echo $_sql;
	if (_num_rows($_res) == 0) return '';
	else {
	  $w = _fetch_array($_res);
	  return $w[$_result];
	}
}

function BuatNilai($w,$mhsw){
  $kd = "Select distinct mk.MKKode,mkpra.MKID as Kode 
    from mkpra 
    left outer join mk on mkpra.PraID = mk.MKID 
    where mkpra.MKID = '$w[MKID]'";
  $rkd = _query($kd);
  $retkd = '';
   while($wkd = _fetch_array($rkd)){
  	$nl = GetaField2("krs","Mhswid = '$mhsw' and MKKode",$wkd['MKKode'],'order by BobotNilai DESC','GradeNilai');
	$nnl = (!empty($nl)) ? "(".$nl.")" : '';
	$retkd .= $wkd['MKKode'] . $nnl . ", ";
  }
  return TRIM($retkd, ", ");
}

function daftar(){
	global $_lf;
	$Cekdl = GetaField("mhsw","MhswID",$_SESSION['mhswid'],"MhswID");
	if (empty($Cekdl) && empty($tahun)) {
	    echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      "Tidak ada mahasiswa dengan NPM: <b>$_SESSION[mhswid]</b>");
	}
	else {
		$q = "SELECT mk.MKID, mk.MKKODE as mkkode, mk.Nama as mknama, mk.SKS as SKS, krs.GradeNilai as nilai, 
			  krs.TahunID as thnid, krs.BobotNilai
			  from krs
			  	left outer join mk on mk.MKID = krs.MKID
				  left outer join mhsw on mhsw.MhswID = krs.MhswID
			  where 
		 	 	krs.MhswID = '$_SESSION[mhswid]'
		 	 	and krs.tahunID < '$_SESSION[tahun]'
		 	 	and krs.BobotNilai >0
        order by mk.MKKode, krs.TahunID";
		  
		$hsl = _query($q);
		//buat file
		$maxcol = 160; 
		$nmf = "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(77).chr(27).chr(15).$_lf);
  		$div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  		$n = 0; $hal = 1;
  		$brs = 0;
  		$maxbrs = 48;
		  $_Tgl = date("d-m-Y");
	
		$JnsTahun = NamaTahun($_SESSION['tahun']);
		$FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$_SESSION['mhswid'],
					"p.nama as pnama, f.nama as fnama");
		$BatasStudi = GetaField("mhsw", "MhswID", $_SESSION['mhswid'],"BatasStudi");
		$BatasStudi = NamaTahun($BatasStudi);
		$DtMhs	= GetFields('mhsw','MhswID',$_SESSION['mhswid'],"Nama,IPK,TotalSKS");
	
		$hdr = str_pad("*** Daftar Rekapitulasi Nilai dan Prasyarat ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf;
		//$hdr .= str_pad("-----------------------------------------------",$maxcol,' ', STR_PAD_BOTH) . $_lf.$_lf;
	
		$hdr .= str_pad("NIM", 15, ' ') . str_pad(":", 2, ' ') . str_pad($_SESSION['mhswid'] .' '. $DtMhs['Nama'],87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("SEMESTER",15,' ') . str_pad(":", 2, ' ') . str_pad($JnsTahun ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("BATAS STUDI",15,' ') . str_pad(":", 2, ' ') . str_pad("Semester " . $BatasStudi,87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= $div;
  	$hdr .= "No.  MATA KULIAH           			         SKS    NILAI        SEM     PRASYARAT" .$_lf.$div;
  		fwrite($f, $hdr);
		//ISI
		$jumrec = _num_rows($hsl);
		$jumhal = ceil($jumrec/$maxbrs);
		while($w = _fetch_array($hsl)){
		$cekprodi = GetaField('mhsw','MhswID',$_SESSION['mhswid'],'ProdiID');
		  $brs++;
			if($brs > $maxbrs){
				fwrite($f, $div);
				fwrite($f, str_pad("Halaman : ".$hal.'/'.$jumhal,10,' ').$_lf);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr);
			}
			
			if($cekprodi == "10"){
			$tdknilai = GetFields('nilai', "Nama", $w['nilai'],'*');
			if ($tdknilai['HitungIPK'] == 'Y'){
			  $bin = '*';
			  $_nilai = $w['nilai'];
			  $nxk += $w['SKS'] * $w['BobotNilai'];
        $tSKS += $w['SKS'];
        
      }
      else 
      {
        $bin = '';
        $_nilai = '('.$w['nilai'].')';
      }
      
			}
			else
			{
        $_nilai = $w['nilai'];
        $nxk += $w['SKS'] * $w['BobotNilai'];
        //$ipk = $DtMhs['IPK'];
        $tSKS += $w['SKS'];
      }
			
			if ($kdmk != $w['mkkode']) {
      $kdmk = $w['mkkode'];
      $_kdmk = $kdmk;
      $_sks = $w['SKS'];
	    $n++;
      } else { 
      $_kdmk = '';
      $_sks = '';
      }
      
      if ($namamk != $w['mknama']) {
      $namamk = $w['mknama'];
      $_namamk = $namamk;
      } else $_namamk = '';
      
      
      if ($n_ != $n) {
	    $n_ = $n;
	    $_n_ = $n_.".";
	    $titik = "......";
	    } else {
	    $_n_ = '';
	    }
      $pra_ = BuatNilai($w, $_SESSION['mhswid']);
			$isi = str_pad($_n_, 4, ' ') . ' ' .
      		str_pad($_kdmk, 6) . ' '.
      		str_pad($_namamk, 46) . ' '.
      		str_pad($_sks, 7, ' ').
      		str_pad($_nilai, 4,' ',STR_PAD_BOTH) .' '.
      		str_pad($bin, 6, ' ').
      		str_pad($w['thnid'], 8,' '). ' '.
			    str_pad($pra_,4,' '). '  '.
			    str_pad($w['pras'], 20).
      		$_lf;
    	fwrite($f, $isi);
		}
		$ipk = ($cekprodi == '10') ? $ipk = ($nxk / $tSKS) : $DtMhs;
		//fwrite($f, str_pad("Jumlah SKS : ",50, ' ',STR_PAD_LEFT).str_pad($JMLSKS,10,' ',STR_PAD_LEFT ).$_lf);
		fwrite($f, $div);
		fwrite($f,str_pad("IPK  : " . number_format($DtMhs['IPK'],2), 40,' ',STR_PAD_LEFT).str_pad("Total SKS : " . $DtMhs['TotalSKS'],20,' ',STR_PAD_LEFT).$_lf);
		fwrite($f,$div);
		fwrite($f, str_pad("Halaman : ".$hal.'/'.$jumhal,10,' ').$_lf);
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 140,' ', STR_PAD_LEFT).$_lf.$_lf); 
  	fwrite($f, str_pad("Akhir laporan", 160, ' ', STR_PAD_LEFT));
		fwrite($f, chr(12));
		fclose($f);
  	TampilkanFileDWOPRN($nmf, "akd.lap");
	}
}

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
TampilkanJudul("Daftar Rekapitulasi Nilai dan Prasyarat");
TampilkanCariMhsw('akd.lap.rekapnilaiprasyaratmhsw', 'Daftar');
if (!empty($tahun)) Daftar();  
  
?>
