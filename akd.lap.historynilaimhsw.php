<?php
//Author Sugeng. S
//Juni 2006

include_once "krs.lib.php";

function TampilkanPilihanUrutanNilai() {
  global $urutannilai;
  $a = '';
  for ($i=0; $i<sizeof($urutannilai); $i++) {
    $sel = ($i == $_SESSION['_urutannilai'])? 'selected' : '';
    $v = explode('~', $urutannilai[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='akd.lap.historynilaimhsw'>
  <input type=hidden name='gos' value='daftar'>
  <tr><td class=inp>Urut berdasarkan: </td>
  <td class=ul><select name='_urutannilai' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

function Daftar(){
	global $_lf, $urutannilai;	
	
	$Cekdl = GetaField("mhsw","MhswID",$_SESSION['mhswid'],"MhswID");
	if (empty($Cekdl) && empty($tahun)) {
	    echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      "Tidak ada mahasiswa dengan NPM: <b>$_SESSION[mhswid]</b>");
	}
	else {
		$_u = explode('~', $urutannilai[$_SESSION['_urutannilai']]);
        $_key = $_u[1];
		
		$q = "SELECT mk.MKKODE as mkkode, mk.Nama as mknama, krsprc.SKS as SKS, krsprc.GradeNilai as nilai, 
			  krspRc.TahunID as thnid, krsprc.BobotNilai as bobot, krsprc.SKS * krsprc.BobotNilai as nxk		
			  from krsprc
			  	left outer join mk on mk.MKID = krsprc.MKID
			  where 
		 	 	krsprc.MhswID = '$_SESSION[mhswid]' order by $_key"; //and
				//krs.TahunID = '$_SESSION[tahun]'";
		  
		$hsl = _query($q);
		//buat file
		$maxcol = 114; 
		$nmf = "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(5));
  		$div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  		$n = 0; $hal = 1;
  		$brs = 0;
  		$maxbrs = 55;
		$_Tgl = date("d-m-Y");
		
		$FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$_SESSION['mhswid'],
					"p.nama as pnama, f.nama as fnama");
		$BatasStudi = GetaField("mhsw", "MhswID", $_SESSION['mhswid'],"BatasStudi");
		$BatasStudi = NamaTahun($BatasStudi);
		$DtMhs	= GetFields('mhsw','MhswID',$_SESSION['mhswid'],"Nama,IPK");
		$DosenPA = GetFields('dosen left outer join mhsw on mhsw.PenasehatAkademik = dosen.Login',"mhsw.MhswID",$_SESSION['mhswid'],"dosen.nama as dnama, dosen.Login as ldosen");
		$JmlSKS = GetFields('krsprc','MhswID',$_SESSION['mhswid'],"Sum(SKS) as jSKS ,SUM(SKS * BobotNilai) as jx");
		//$JmlX = GetaField('krs')
		$hdr = str_pad("*** Rekapitulasi History Nilai Tertinggi Per Semester ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
			
		$hdr .= str_pad("NIM", 15, ' ') . str_pad(":", 2, ' ') . str_pad($_SESSION['mhswid'] .' '. $DtMhs['Nama'],87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("BATAS STUDI",15,' ') . str_pad(":", 2, ' ') . str_pad("Semester " . $BatasStudi,87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("P.A.",15,' ') . str_pad(":", 2, ' ') . str_pad($DosenPA['ldosen'].' '.$DosenPA['dnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  		$hdr .= "No.  MATA KULIAH           			         SKS    SEM    NILAI     BOBOT      NxK" .$_lf.$div;
  		fwrite($f, $hdr);
		//ISI
		while($w = _fetch_array($hsl)){
			$n++; $brs++;
			if($brs > $maxbrs){
			    fwrite($f, chr(12));
				$hal++; $brs = 1;
				fwrite($f, $hdr.$_lf);
			}
			$pra = GetaField("mk","MKID",$w['mkpra'],"MKKODE");
			$isi = str_pad($n.'.', 4, ' ') . ' ' .
      		str_pad($w['mkkode'], 6) . ' '.
      		str_pad($w['mknama'], 46) . ' '.
      		str_pad($w['SKS'], 5, ' ').
      		str_pad($w['thnid'], 8) .' '.
      		str_pad($w['nilai'], 8, ' '). ' '.
			str_pad($w['bobot'],6,' '). '  '.
			str_pad($w['nxk'], 5,' ',STR_PAD_LEFT).
      		$_lf;
    		fwrite($f, $isi);
		}
		//fwrite($f, str_pad("Jumlah SKS : ",50, ' ',STR_PAD_LEFT).str_pad($JMLSKS,10,' ',STR_PAD_LEFT ).$_lf);
		fwrite($f, $div);
		fwrite($f,str_pad("IPK  " . $DtMhs['IPK'],37,' ',STR_PAD_LEFT) . str_pad($JmlSKS['jSKS'],23,' ',STR_PAD_LEFT). str_pad($JmlSKS['jx'],35,' ',STR_PAD_LEFT) . $_lf);
		fwrite($f,$div);
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 90,' ', STR_PAD_LEFT).$_lf.$_lf); 
  		fwrite($f, str_pad("Akhir laporan", 114, ' ', STR_PAD_LEFT));
		fwrite($f,chr(12).$_lf);
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

$urutannilai = array(0=>"Kode Matakuliah~mk.MKKODE", 1=>"Periode~krsprc.TahunID");
  
$_urutannilai = GetSetVar('_urutannilai', 1);
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar Rekapitulasi History Nilai Per Semester");
TampilkanCariMhsw('akd.lap.historynilaisesi', 'Daftar');
if (!empty($mhswid)) {
TampilkanPilihanUrutanNilai();
Daftar();
}  
?>