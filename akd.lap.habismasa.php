<?php
//Author Sugeng. S
//Juni 2006

session_start();
include_once "krs.lib.php";

function TampilkanCariHabisMasa() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='akd.lap.habismasa'>
  <input type=hidden name='gos' value='daftar'>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp>Tahun Akademik</td><td class=ul><input type=text name=tahun value='$_SESSION[tahun]'></td></tr>
  <tr><td class=inp>Periode Habis Masa Studi</td>
  <td class=ul><input type=text name=daritahun value='$_SESSION[daritahun]'> s/d 
  <input type=text name=sampaitahun value='$_SESSION[sampaitahun]'></td>
  <td class=ul><input type=submit name='proses' value='Cari'></td></tr>
  </form></table></p>";
}

function daftar(){
   global $_lf;
	//$Cekdl = GetaField("mhsw","MhswID",$_SESSION['mhswid'],"MhswID");
	//if (empty($Cekdl) && empty($tahun)) {
	    //echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      //"Tidak ada mahasiswa dengan NPM: <b>$_SESSION[mhswid]</b>");
	//}
	//else {
		if (!empty($_SESSION['daritahun'])) {
    $_SESSION['sampaitahun'] = (empty($_SESSION['sampaitahun']))? $_SESSION['daritahun'] : $_SESSION['sampaitahun'];
    $_habismasa = "and '$_SESSION[daritahun]' <= m.BatasStudi and m.BatasStudi <= '$_SESSION[daritahun]' ";
    } else $_habismasa = '';
		$q = "SELECT m.MhswID, left(m.Nama,25) as Nama, m.TotalSKS, m.IPK, m.BatasStudi,
		      concat(m.alamat,', ',m.kota) as Alamat
			      from mhsw m
			    where 
			      m.StatusMhswID in ('A','P')
			      and m.ProgramID='$_SESSION[prid]'
            and m.ProdiID='$_SESSION[prodi]'
		 	 	    $_habismasa "; //and
				//krs.TahunID = '$_SESSION[tahun]'";
		  
		$hsl = _query($q);
		//buat file
		$maxcol = 150; 
		$nmf = "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(77).chr(27).chr(15));
  		$div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  		$n = 0; $hal = 1;
  		$brs = 0;
  		$maxbrs = 40;
		$_Tgl = date("d-m-Y");
	
		$FakNama  = GetFields("prodi p left outer join fakultas f on f.FakultasID = p.FakultasID","p.ProdiID",$_SESSION['prodi'],
					"p.nama as pnama, f.nama as fnama");
		$BatasStudi = GetaField("mhsw", "MhswID", $_SESSION['mhswid'],"BatasStudi");
		$Sem = NamaTahun($_SESSION['tahun']);
		$DtMhs	= GetFields('mhsw','MhswID',$_SESSION['mhswid'],"Nama,IPK");
		$DosenPA = GetFields('dosen left outer join mhsw on mhsw.PenasehatAkademik = dosen.Login',"mhsw.MhswID",$_SESSION['mhswid'],"dosen.nama as dnama, dosen.Login as ldosen");
		$JmlSKS = GetFields('krs','MhswID',$_SESSION['mhswid'],"Sum(SKS) as jSKS ,SUM(SKS * BobotNilai) as jx");
		//$JmlX = GetaField('krs')
		$hdr = str_pad("*** DAFTAR MAHASISWA HABIS MASA STUDI ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
			
		$hdr .= str_pad("SEMESTER", 15, ' ') . str_pad(":", 2, ' ') . str_pad($Sem ,87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("MASA STUDI",15,' ') . str_pad(":", 2, ' ') . str_pad($_SESSION['daritahun'].' s/d '.$_SESSION['sampaitahun'],87, ' ', STR_PAD_RIGHT).$_lf; 
		//$hdr .= str_pad("P.A.",15,' ') . str_pad(":", 2, ' ') . str_pad($DosenPA['ldosen'].' '.$DosenPA['dnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  		$hdr .= "No.  NIM       NAMA MAHASISWA	         SMS.AKTIF  SKS   IPK   BATAS STUDI  ALAMAT" .$_lf.$div;
  		fwrite($f, $hdr);
		//ISI
		$jumlahrec = _num_rows($hsl);
		$jumhal = ceil($jumlahrec/$maxbrs);
		while($w = _fetch_array($hsl)){
			$n++; $brs++;
			if($brs > $maxbrs){
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
			}
			    $tahunaktif = GetaField2('mhsw',"Statusmhswid = 'A' and mhswid",$w['MhswID'],'order by tahunid desc','TahunID');
			    $isi = str_pad($n.'.', 4, ' ') . ' ' .
      		str_pad($w['MhswID'], 6) . ' '.
      		str_pad($w['Nama'], 28) . ' '.
      		str_pad($tahunaktif, 8, ' ').
      		str_pad($w['TotalSKS'], 3,' ',STR_PAD_LEFT) .' '.
      		str_pad($w['IPK'], 5, ' ',STR_PAD_LEFT). ' '.
			    str_pad($w['BatasStudi'],10,' ',STR_PAD_LEFT).'     '.
			    str_pad($w['Alamat'], 20,' ').
          $_lf;
    		  fwrite($f, $isi);
		}
		//fwrite($f, str_pad("Jumlah SKS : ",50, ' ',STR_PAD_LEFT).str_pad($JMLSKS,10,' ',STR_PAD_LEFT ).$_lf);
		fwrite($f, $div);
		//fwrite($f,str_pad("IPK  " . $DtMhs['IPK'],39,' ',STR_PAD_LEFT) . str_pad($JmlSKS['jSKS'],21,' ',STR_PAD_LEFT). str_pad($JmlSKS['jx'],34,' ',STR_PAD_LEFT) . $_lf);
		//fwrite($f,$div);
		fwrite($f, str_pad("Halaman : ".$hal."/".$jumhal,30,' ').$_lf);
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 120,' ', STR_PAD_LEFT).$_lf.$_lf); 
  	fwrite($f, str_pad("Akhir laporan", 150, ' ', STR_PAD_LEFT));
		fwrite($f, chr(12));
		fclose($f);
  	TampilkanFileDWOPRN($nmf, "akd.lap");
	//}
}

function GetaField2($_tbl,$_key,$_value,$order,$_result) {
  global $strCantQuery;
	$_sql = "select $_result from $_tbl where $_key='$_value' $order limit 1";
	$_res = _query($_sql);
	//echo $_sql;
	if (_num_rows($_res) == 0) return '';
	else {
	  $w = _fetch_array($_res);
	  return $w[$_result];
	}
}

// *** Parameters ***
if ($_SESSION['_LevelID'] == 120) {
  $mhswid = $_SESSION['_Login'];
}
else {
  $mhswid = GetSetVar('mhswid');
}
$daritahun = GetSetVar('daritahun');
$sampaitahun = GetSetVar('sampaitahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa Habis Masa Studi");
TampilkanCariHabisMasa();
//Tampilkan('akd.lap.habismasa', 'Daftar');
if (!empty($_SESSION['daritahun'])) {

Daftar();
}  
?>
