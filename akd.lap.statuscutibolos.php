<?php
//Created By Sugeng
//Juli 2006

function Daftar() {
  global $_lf,$pilstatus;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['angkat'])) $whr[] = "m.TahunID='$_SESSION[angkat]'";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  $_u = explode('~', $pilstatus[$_SESSION['_pilstatus']]);
        $_key = $_u[1];
  // Query
  $pilta = GetArrayTable("select TahunID from khs where TahunID <= '$_SESSION[tahun]' and right(TahunID, 1) <> 3 group by TahunID order by TahunID DESC limit 0,$_SESSION[banyak]",'TahunID','TahunID');
	$s = "select m.MhswID, LEFT(m.Nama, 25) as Nama, count(khs.MhswID) as tot, KHSID
    from khs 
      left outer join mhsw m on khs.MhswID=m.MhswID
    where khs.TahunID in ($pilta) $_whr
	  and khs.StatusMhswID = '$_key' 
	group by khs.MhswID
    order by khs.MhswID ";
  //echo "<pre>$s</pre>";
  $r = _query($s);
    // Buat file
  $MaxCol = 114;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  // parameter2
  $_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $n = 0; $hal = 1;
  $brs = 0;
  $maxbrs = 50;
  $_Tgl = Date("d-m-Y H:i");
  $Head = ($_key == 'C') ? "*** Daftar Mahasiswa Cuti $_SESSION[banyak] Semester Berturut-turut ***" : "*** Daftar Mahasiswa Bolos $_SESSION[banyak] Semester Berturut-turut ***";
  // Buat header
  $hdr = str_pad($Head, $MaxCol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  $hdr .= "Priode  : " . NamaTahun($_SESSION['tahun']) . $_lf;
	$hdr .= "Prodi 	: $_prodi" . $_lf;
	$hdr .=	"Program : $_prid" . $_lf;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                          SEMESTER".$_lf.$div;
  fwrite($f, $hdr);
  // Tampilkan
  while ($w = _fetch_array($r)) {
    
	//if($w['tot'] >= $_SESSION['banyak']){	
	  //$n++; $brs++;
      //if ($brs > $maxbrs) {
      //  $hal++; $brs =1;
      //  fwrite($f, $div);
		  //  fwrite($f, chr(12));
      //  fwrite($f, $hdr);
      //}
			$MH = CariBerurutan($_key, $pilta, $w, $hdr, $brs, $maxbrs, $div, $n);
			//$DET = GetFields("mhsw", "MhswID", $MH, "Nama, MhswID");
      //$isi = str_pad($n.'.', 4, ' ') . ' ' .
      //str_pad($DET['MhswID'], 12) . ' '.
      //str_pad($DET['Nama'], 29) . ' '.
      //$pilta.
      //$_lf;
      fwrite($f, $MH);
	//}
  }
  fwrite($f, $div);
  fwrite($f, str_pad("Akhir laporan", 0, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 90,' ', STR_PAD_LEFT).$_lf.$_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
} 

function TampilkanPilihanStatus() {
  global $pilstatus;
  $a = '';
  for ($i=0; $i<sizeof($pilstatus); $i++) {
    $sel = ($i == $_SESSION['_pilstatus'])? 'selected' : '';
    $v = explode('~', $pilstatus[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='akd.lap.statuscutibolos'>
  <input type=hidden name='gos' value='daftar'>
  <tr><td class=inp>Cari berdasarkan Status: </td>
  <td class=ul><select name='_pilstatus' onChange='this.form.submit()'>$a</select></td>
  <td class=inp>Sebanyak: </td>
  <td class=ul><input type='text' name='banyak' value='$_SESSION[banyak]' size=5></td>
  <td class=inp>X Semester</td>
  <td class=inp>Angkatan: </td>
  <td class=ul><input type='text' name='angkat' value='$_SESSION[angkat]' size=5></td>
  <td class=ul><input type=submit name='cari' Value='Kirim'></td></tr>
  </form></table></p>";
}

function CariBerurutan($key, $Tahuns, $mhsw, $hdr, &$brs, $maxbrs, $div, &$n){
	global $_lf;
	$arrTahun = array();
	$arrAda = array();
	//$n=0;
	//var_dump($Tahuns);
	$arrTahun = explode(", ", $Tahuns);
	foreach($arrTahun as $value) {
		$ada = GetaField('khs', "StatusMhswID = '$key' and TahunID = $value and MhswID", $mhsw['MhswID'], 'TahunID');
		if (!empty($ada)) $arrAda[] = $ada;
		//echo $ada;
	}
	//var_dump($arrAda);
	if (count($arrAda) == $_SESSION['banyak']) {
		$n++; $brs++;
      if ($brs > $maxbrs) {
        $hal++; $brs =1;
        $isi .= $div;
		    $isi .= chr(12);
        $isi .= $hdr;
      }
      $isi .= str_pad($n.'.', 4, ' ') . ' ' .
      str_pad($mhsw['MhswID'], 12) . ' '.
      str_pad($mhsw['Nama'], 29) . ' '.
      $Tahuns.
      $_lf;
	}
	return $isi;
}

//Parameter
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$angkat = GetSetVar('angkat');
$pilstatus = array(0=>"Bolos~P", 1=>"Cuti~C");
$_pilstatus = GetSetVar('_pilstatus', 0);
$banyak = GetSetVar('banyak', 2);

//Main
TampilkanJudul("Daftar Mahasiswa Cuti atau Bolos Kuliah");
TampilkanTahunProdiProgram('akd.lap.statuscutibolos', 'Daftar');
TampilkanPilihanStatus();
if (!empty($tahun)) Daftar();
?>
