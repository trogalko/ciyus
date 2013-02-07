<?php

//12 Desember 2007

include_once "krs.lib.php";

function TampilkanFilterNilai() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='akd.lap.nilaiedand3'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> s/d
      <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
      </td></tr>
  
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>
END;
}

function GetaField2($_tbl,$_key,$_value,$_order, $_result) {
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
  <input type=hidden name='mnux' value='akd.lap.nilaiedand3'>
  <input type=hidden name='gos' value='daftar'>
  <tr><td class=inp>Urut berdasarkan: </td>
  <td class=ul><select name='_urutannilai' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

function TampilkanFilterBlanko() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='blanko'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> s/d
      <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
      </td></tr>
  
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>
END;
}

function BuatRincian($w, $n, $maxbrs, &$jumhal, $brs, $div, $f, $hdr, $hal){
  global $_lf, $urutannilai;
  $_u = explode('~', $urutannilai[$_SESSION['_urutannilai']]);
        $_key = $_u[1];
  $q = "SELECT mk.MKKODE as mkkode, mk.Nama as mknama, krs.SKS as SKS, krs.GradeNilai as nilai, 
			  krs.TahunID as thnid, krs.BobotNilai as bobot, krs.SKS * krs.BobotNilai as nxk		
			  from krs
			  	left outer join mk on mk.MKID = krs.MKID
                left outer join jadwal j on krs.JadwalID = j.JadwalID
			  where 
		 	 	krs.MhswID = '$w[MhswID]'
					and (krs.GradeNilai LIKE 'D%' OR krs.GradeNilai LIKE 'E%')
                and (j.JenisJadwalID <> 'R' or j.JenisJadwalID is null)
		 	 	order by $_key";
	$r = _query($q);
	$jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	while ($w0 = _fetch_array($r)){
	    $n++; $brs++;
	    if($brs > $maxbrs){
			  $isi .= $div . str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf;
				$hal++; $brs = 1;
				$isi .= chr(12);
				$isi .= $hdr . $_lf;
			}
      $pra = GetaField("mk","MKID",$w0['mkpra'],"MKKODE");
			$get = GetaField2('krs',"mhswid = '$w[MhswID]' and MKKode",$w0['mkkode'],"",'min(Gradenilai)');
			$bt = ($get == $w0['nilai'] and $get <> '-') ? "*" : "";
			if ($_key == 'mk.MKKODE'){
        if ($mkkode != $w0['mkkode']) {
          $mkkode = $w0['mkkode'];
          $_mkkode = $mkkode;
        } else {
          $_mkkode = '';
        }
        if ($mknama != $w0['mknama']) {
          $mknama = $w0['mknama'];
          $_mknama = $mknama;
        } else {
          $_mknama = '';
        }
      } else {
        $_mkkode = $w0['mkkode'];
        $_mknama = $w0['mknama'];
      }
      
      $isi .= str_pad($n.'.', 4, ' ') . ' ' .
      		str_pad($_mkkode, 6) . ' '.
      		str_pad($_mknama, 46) . ' '.
      		str_pad($w0['SKS'], 4, ' ').
      		str_pad($w0['thnid'], 9) .' '.
      		str_pad($w0['nilai'], 4, ' '). ' '.
      		str_pad($bt,2,' ').
			    str_pad($w0['bobot'],8,' ',STR_PAD_LEFT).
			    str_pad($w0['nxk'], 8,' ',STR_PAD_LEFT).
      		$_lf;
  }
  return $isi;
}

function DaftarMhsw(){
  global $_lf;
  if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= khs.MhswID and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
	//$_npm = "'$_SESSION[DariNPM]' <= khs.MhswID and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
  $s = "select MhswID from khs where TahunID = '$_SESSION[tahun]' $_npm ";
  //$s = "select MhswID from khs where $_npm ";
  $r = _query($s);
  $maxcol = 100;
  $maxbrs = 46;
  $hal = 1;
  $brs = 0;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
	$f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(10));
  $div = str_pad('-', $maxcol, '-').$_lf;
  $n = 0;
  $_Tgl = date("d-m-Y H:i");
  while ($w = _fetch_array($r)){
    $FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$w['MhswID'],
					"p.nama as pnama, f.nama as fnama");
		$BatasStudi = GetaField("mhsw", "MhswID", $w['MhswID'],"BatasStudi");
		$BatasStudi = NamaTahun($BatasStudi);
		$DtMhs	= GetFields('mhsw','MhswID',$w['MhswID'],"Nama,IPK,TotalSKS");
		$DosenPA = GetFields('dosen left outer join mhsw on mhsw.PenasehatAkademik = dosen.Login',"mhsw.MhswID",$w['MhswID'],"dosen.nama as dnama, dosen.Login as ldosen");
		$JmlSKS = GetFields('krsprc','MhswID',$w['MhswID'],"Sum(SKS) as jSKS ,SUM(SKS * BobotNilai) as jx");
    
    $hdr = str_pad("*** Rekapitulasi Nilai D dan E ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
			
		$hdr .= str_pad("NIM", 15, ' ') . str_pad(":", 2, ' ') . str_pad($w['MhswID'] .' '. $DtMhs['Nama'],87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("BATAS STUDI",15,' ') . str_pad(":", 2, ' ') . str_pad("Semester " . $BatasStudi,87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("P.A.",15,' ') . str_pad(":", 2, ' ') . str_pad($DosenPA['ldosen'].' '.$DosenPA['dnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  	$hdr .= "No.  MATA KULIAH           			         SKS    SEM    NILAI       BOBOT     NxK" .$_lf.$div;
    
    fwrite($f, $hdr);
    $isi1 = BuatRincian($w, $n, $maxbrs, $jumhal, $brs, $div, $f, $hdr, &$hal);
    fwrite($f, $isi1);
    fwrite($f, $div);
		fwrite($f,str_pad("IPK  " . $DtMhs['IPK'],39,' ',STR_PAD_LEFT) . str_pad($DtMhs['TotalSKS'],21,' ',STR_PAD_LEFT). str_pad($JmlSKS['jx'],36,' ',STR_PAD_LEFT) . $_lf);
		fwrite($f,$div);
		fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 79,' ', STR_PAD_LEFT).$_lf); 
  	fwrite($f, str_pad("Akhir laporan", 100, ' ', STR_PAD_LEFT).$_lf);
		fwrite($f, chr(12));
		$hal = 1;
		//$hdr = str_pad("*** Rekapitulasi History Nilai Per Semester ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  }
  fclose($f);  
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

function daftar(){
	global $_lf, $urutannilai;
	$Cekdl = GetaField("mhsw","MhswID",$_SESSION['mhswid'],"MhswID");
	if (empty($Cekdl) && empty($tahun)) {
	    echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      "Tidak ada mahasiswa dengan NPM: <b>$_SESSION[mhswid]</b>");
	}
	else {
		$_u = explode('~', $urutannilai[$_SESSION['_urutannilai']]);
        $_key = $_u[1];
		
		$q = "SELECT mk.MKKODE as mkkode, mk.Nama as mknama, krs.SKS as SKS, krs.GradeNilai as nilai, 
			  krs.TahunID as thnid, krs.BobotNilai as bobot, krs.SKS * krs.BobotNilai as nxk		
			  from krs
			  	left outer join mk on mk.MKID = krs.MKID
			  where 
		 	 	krs.MhswID = '$_SESSION[mhswid]'
					and (krs.GradeNilai LIKE 'D%' OR krs.GradeNilai LIKE 'E%')
				Order By $_key"; //and
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
  		$maxbrs = 46;
		$_Tgl = date("d-m-Y");
	
		$FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$_SESSION['mhswid'],
					"p.nama as pnama, f.nama as fnama");
		$BatasStudi = GetaField("mhsw", "MhswID", $_SESSION['mhswid'],"BatasStudi");
		$BatasStudi = NamaTahun($BatasStudi);
		$DtMhs	= GetFields('mhsw','MhswID',$_SESSION['mhswid'],"Nama,IPK");
		$DosenPA = GetFields('dosen left outer join mhsw on mhsw.PenasehatAkademik = dosen.Login',"mhsw.MhswID",$_SESSION['mhswid'],"dosen.nama as dnama, dosen.Login as ldosen");
		$JmlSKS = GetFields('krs',"k.StatusKRSID='A' and (GradeNilai LIKE 'D%' OR GradeNilai LIKE 'E%') and k.Final = 'Y' and MhswID",$_SESSION['mhswid'],"Sum(SKS) as jSKS ,SUM(SKS * BobotNilai) as jx ");
		//$JmlX = GetaField('krs')
		$hdr = str_pad("*** Rekapitulasi History Nilai ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
			
		$hdr .= str_pad("NIM", 15, ' ') . str_pad(":", 2, ' ') . str_pad($_SESSION['mhswid'] .' '. $DtMhs['Nama'],87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("BATAS STUDI",15,' ') . str_pad(":", 2, ' ') . str_pad("Semester " . $BatasStudi,87, ' ', STR_PAD_RIGHT).$_lf; 
		$hdr .= str_pad("P.A.",15,' ') . str_pad(":", 2, ' ') . str_pad($DosenPA['ldosen'].' '.$DosenPA['dnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  		$hdr .= "No.  MATA KULIAH           			         SKS    SEM    NILAI       BOBOT     NxK" .$_lf.$div;
  		fwrite($f, $hdr);
		//ISI
		$jumlahrec = _num_rows($hsl);
    $jumhal = ceil($jumlahrec/$maxbrs);
		while($w = _fetch_array($hsl)){
			$n++; $brs++;
			if($brs > $maxbrs){
			  fwrite($f,$div);
				fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
			}
			$pra = GetaField("mk","MKID",$w['mkpra'],"MKKODE");
			$get = GetaField2('krs',"mhswid = '$_SESSION[mhswid]' and MKKode",$w['mkkode'],"",'min(Gradenilai)');
			$bt = ($get == $w['nilai'] and $get <> '-') ? "*" : "";
      if ($mkkode != $w['mkkode']) {
        $mkkode = $w['mkkode'];
        $_mkkode = $mkkode;
      } else {
        $_mkkode = '';
      }
        
      $isi = str_pad($n.'.', 4, ' ') . ' ' .
      		str_pad($_mkkode, 6) . ' '.
      		str_pad($w['mknama'], 46) . ' '.
      		str_pad($w['SKS'], 4, ' ').
      		str_pad($w['thnid'], 9) .' '.
      		str_pad($w['nilai'], 4, ' '). ' '.
      		str_pad($bt,2,' ').
			    str_pad($w['bobot'],8,' ',STR_PAD_LEFT).
			    str_pad($w['nxk'], 8,' ',STR_PAD_LEFT).
      		$_lf;
    		fwrite($f, $isi);
		}
		//fwrite($f, str_pad("Jumlah SKS : ",50, ' ',STR_PAD_LEFT).str_pad($JMLSKS,10,' ',STR_PAD_LEFT ).$_lf);
		fwrite($f, $div);
		fwrite($f,str_pad("IPK  " . $DtMhs['IPK'],39,' ',STR_PAD_LEFT) . str_pad($JmlSKS['jSKS'],21,' ',STR_PAD_LEFT). str_pad($JmlSKS['jx'],34,' ',STR_PAD_LEFT) . $_lf);
		fwrite($f,$div);
		fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 90,' ', STR_PAD_LEFT).$_lf.$_lf); 
  	fwrite($f, str_pad("Akhir laporan", 114, ' ', STR_PAD_LEFT).$_lf);
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
//$tahun = GetSetVar('tahun');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$urutannilai = array(0=>"Kode Matakuliah~mk.MKKODE", 1=>"Periode~krs.TahunID");
  
$_urutannilai = GetSetVar('_urutannilai', 1);
$tahun = GetSetVar('tahun');

// *** Main ***
TampilkanJudul("Daftar Rekapitulasi History Nilai D dan E Per Semester");
//TampilkanCariMhsw('akd.lap.historynilaisesi', 'Daftar');
TampilkanFilterNilai();
if (!empty($_SESSION['tahun']) && !empty($_SESSION['DariNPM'])) {
TampilkanPilihanUrutanNilai();
DaftarMhsw();
}  
?>
