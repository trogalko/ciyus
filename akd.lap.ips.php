<?php
include_once "krs.lib.php";
/*
function daftar(){
	global $_lf;
		$DariTahun = $_SESSION['DariTahun'];
    $SampaiTahun = $_SESSION['SampaiTahun'];
		//while($DariTahun <= $SampaiTahun){
		$q = "SELECT IPS as IPS from khs
			  where
		 	 	TahunID like '$DariTahun%'
			  order by TahunID";
		  
		$hsl = _query($q);
		//buat file
		$maxcol = 150; 
		$nmf = "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(15).chr(77));
  		$div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  		$n = 0; $hal = 1;
  		$brs = 0;
  		$maxbrs = 40;
		$_Tgl = date("d-m-Y");
		
		
	
		$JnsTahun = NamaTahun($_SESSION['tahun']);
		$FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$_SESSION['mhswid'],
					"p.nama as pnama, f.nama as fnama");
			
		$hdr = str_pad("*** DAFTAR DISTRIBUSI IPS MAHASISWA ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf;
			
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("SEMESTER",15,' ') . str_pad(":", 2, ' ') . str_pad($JnsTahun ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  		$hdr .= "NO.  ANGKATAN     < 1.0  < 1.2  < 1.4   < 1.6  < 1.8  < 2.0  < 2.2  < 2.4  < 2.6  < 2.8  < 3.0  < 3.2  < 3.4  < 3.6  < 3.8  < 4.0  = 4.0  RATA-2  S.T.D" .$_lf.$div;
  		fwrite($f, $hdr);
		//ISI
		$jum = 0;
		while($w = _fetch_array($hsl)){
			$n++; $brs++;
			if($brs > $maxbrs){
				$hal++; $brs = 1;
				fwrite($f, $hdr);
			}
			//$jml = array();
			$jml = CekNilai($w['IPS']);
			$isi = str_pad($n.'.', 4, ' ') . ' ' .
      		   str_pad($DariTahun, 6) . ' '.
			       str_pad($jml,6,' ',STR_PAD_LEFT).
			//$jum++;
      		str_pad($w['mknama'], 46) . ' '.
      		str_pad($w['SKS'], 7, ' ').
      		$_lf;
    		fwrite($f, $isi);
		}
		//fwrite($f, str_pad("Jumlah SKS : ",50, ' ',STR_PAD_LEFT).str_pad($JMLSKS,10,' ',STR_PAD_LEFT ).$_lf);
		
		fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 90,' ', STR_PAD_LEFT).$_lf.$_lf); 
  		fwrite($f, str_pad("Akhir laporan", 150, ' ', STR_PAD_LEFT));
		fclose($f);
  		TampilkanFileDWOPRN($nmf, "akd.lap");
		//$DariTahun = $DariTahun+1;
	
}
*/

function CekNilai($IP){
	//$let = array();
	$let=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	$let[0] = ($IP < 1.0) ? $let[0]+1 : 0;
	$let[1] = (($IP > 1.0) and ($IP < 1.2 )) ? $let[1]+1 : 0;
	$let[2] = (($IP > 1.2) and ($IP < 1.4 )) ? $let[2]+1 : 0;
	$let[3] = (($IP > 1.4) and ($IP < 1.6 )) ? $let[3]+1 : 0;
	$let[4] = (($IP > 1.6) and ($IP < 1.8 )) ? $let[4]+1 : 0;
	$let[5] = (($IP > 1.8) and ($IP < 2.0 )) ? $let[5]+1 : 0;
	$let[6] = (($IP > 2.0) and ($IP < 2.2 )) ? $let[6]+1 : 0;
	$let[7] = (($IP > 2.2) and ($IP < 2.4 )) ? $let[7]+1 : 0;
	$let[8] = (($IP > 2.4) and ($IP < 2.6 )) ? $let[8]+1 : 0;
	$let[9] = (($IP > 2.6) and ($IP < 2.8 )) ? $let[9]+1 : 0;
	$let[10] = (($IP > 2.8) and ($IP < 3.0 )) ? $let[10]+1 : 0;
	$let[11] = (($IP > 3.0) and ($IP < 3.2 )) ? $let[11]+1 : 0;
	$let[12] = (($IP > 3.2) and ($IP < 3.4 )) ? $let[12]+1 : 0;
	$let[13] = (($IP > 3.4) and ($IP < 3.6 )) ? $let[13]+1 : 0;
	$let[14] = (($IP > 3.6) and ($IP < 3.8 )) ? $let[14]+1 : 0;
	$let[15] = (($IP > 3.8) and ($IP < 4.0 )) ? $let[15]+1 : 0;
	$let[16] = ($IP == 4.0) ? $let[16]+1 : 0;
	$whr = (empty($let))? '' : implode('      ', $let);
	return $whr;
}

function Daftar2(){
  for($i=1.0;$i<=3.8;$i=$i+0.2){
   for($j=1.2;$j<=4.0;$j=$j+0.2){
    for($k=1;$k<=15;$k++){
      //$s.$k = "select sum(IPS) as jum from khs where TahunID like '2000%' and IPS <= $i and IPS >= $j";
      $tmp.$k = GetaField("khs","TahunID like '2000%' and IPS <= $i and IPS >",$j,"count(IPS)");
      //$r.$k = _query($s.$k);
      //$w.$k = _fetch_array($r.$k);
      echo $tmp.$k;
    }
   }
  }
}


function GetAntaraTahun($mnux,$gos){
  echo "<p><form method=post action=?>
  		<table class= box cellspacing=1 celpadding=4>
		<tr><td class=ul colspan=2>Tahun Akademik</td></tr>
		<tr><td class=inp1>Mulai Angkatan</td><td class=li><input type=text name=DariTahun value='$_SESSION[DariTahun]' size=15>s/d<input type=text name=SampaiTahun value='$_SESSION[SampaiTahun]' maxlength=6 size=15></td></tr>
		<tr><td colspan><input type=submit name=submit value=Kirim></td></tr>
		</table>
		<input type=hidden name=mnux value=$mnux>
		<input type=hidden name=gos value=$gos>
		</form></p>";
}

function TlsDaftar(){
  $DariTahun = $_SESSION['DariTahun'];
  $SampaiTahun = $_SESSION['SampaiTahun'];
  $maxcol = 150; 
		$nmf = "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(15).chr(77));
  		$div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  		$n = 0; $hal = 1;
  		$brs = 0;
  		$maxbrs = 40;
		$_Tgl = date("d-m-Y");
	
		$JnsTahun = NamaTahun($_SESSION['tahun']);
		$FakNama  = GetFields("mhsw left outer join prodi p on mhsw.ProdiId = p.ProdiID 
					left outer join fakultas f on f.FakultasID = p.FakultasID","mhsw.MhswID",$_SESSION['mhswid'],
					"p.nama as pnama, f.nama as fnama");
			
		$hdr = str_pad("*** DAFTAR DISTRIBUSI IPS MAHASISWA ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf;
			
		$hdr .= str_pad("FAK/JUR",15,' ') . str_pad(":", 2, ' ') . str_pad($FakNama['pnama'] . '/' . $FakNama['fnama'] ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= str_pad("SEMESTER",15,' ') . str_pad(":", 2, ' ') . str_pad($JnsTahun ,87, ' ', STR_PAD_RIGHT).$_lf;
		$hdr .= $div;
  	$hdr .= "NO.  ANGKATAN     < 1.0  < 1.2  < 1.4   < 1.6  < 1.8  < 2.0  < 2.2  < 2.4  < 2.6  < 2.8  < 3.0  < 3.2  < 3.4  < 3.6  < 3.8  < 4.0  = 4.0  RATA-2  S.T.D" .$_lf.$div;
  		fwrite($f, $hdr);
  
  //while($DariTahun <= $SampaiTahun){
    $s = "select IPS from khs where TahunID like '$DariTahun%'";
	  $r = _query($s);
	  $w = _fetch_array($r);
	  for($i=1;$i<=sizeof($w['IPS']);$i++){
      $jml = CekNilai($w['IPS']);
    }  
	  $isi = str_pad($n.'.', 4, ' ') . ' ' .
    str_pad($jml, 6) . ' ';
	
  //}
  //$DariTahun=$DariTahun+1;
}

// *** Parameters ***

$DariTahun = GetSetVar('DariTahun');
$SampaiTahun = GetSetVar('SampaiTahun');

// *** Main ***
TampilkanJudul("Daftar Distribusi IPS Mahasiswa");
GetAntaraTahun("akd.lap.ips","Daftar2");
if (!empty($DariTahun)) Daftar2();  

?>
