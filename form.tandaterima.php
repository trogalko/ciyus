<?php

function GetOptionPrinter() {
  $arrPrinter = array('Dot matrix', 'Laser');
  $str = '<option>-</option>';
  for ($i = 0; $i < sizeof($arrPrinter); $i++) {
    $sel = ($i == $_SESSION['_Printer'])? 'selected' : '';
    $str .= "<option value='$i' $sel>$arrPrinter[$i]</option>";
  }
  return $str;
}

function FilterTahunProdiTanggal(){
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $TglSK = GetDateOption($_SESSION['TglSK'], 'TglSK');
  $optPrinter = GetOptionPrinter();
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='form.tandaterima'>
  <input type=hidden name='gos' value='CetakTandaTerima'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Tanggal SK Rektor</td>
      <td class=ul>$TglSK</td></tr>
      <td class=inp>Jenis Printer</td><td class=ul><select name='_Printer' onChange='this.form.submit()'>$optPrinter</select></td></tr>
    <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>
END;
}

function CetakTandaTerima(){
  global $_lf;
  $_whr = array();
  if (!empty($_SESSION['prodi'])) $_whr[] = "m.ProdiID='$_SESSION[prodi]'";
  $whr = (empty($_whr))? '' : " and " . implode(' and ', $_whr);
  $s = "select ta.MhswID, m.*
    from ta ta
      left outer join mhsw m on m.MhswID = ta.MhswID
      left outer join prodi p on m.ProdiID=p.ProdiID 
      left outer join wisudawan w on w.MhswID = ta.MhswID
    where m.KodeID='$_SESSION[KodeID]'
	and m.noijazah != ' '
    and m.TglSKKeluar = '$_SESSION[TglSK]'
    and ta.Lulus = 'Y'
    $whr
    order by ta.MhswID";
  $r = _query($s);
  $maxcol = 120; 
	$nmf = "tmp/$_SESSION[_Login].dwoprn";
	$f = fopen($nmf, 'w');     
  $prn = ($_SESSION['_Printer'] == 0) ? chr(27).chr(15).chr(27).chr(108).chr(5) : chr(27) . chr(38) . chr(107) . chr(50) . chr(83). // condensed
      chr(27) . chr(38) . chr(108) . chr(54) . chr(68). // 6 lines per inches
      chr(27) . chr(40) . chr(115) . chr(51) . chr(66);
  fwrite($f, $prn);
  $div = str_pad('-', $maxcol, '-').$_lf;
  		// parameter2
  $n = 0; $hal = 1;
  $brs = 0;
  $maxbrs = 23;
  $Njur = GetFields("prodi p left outer join fakultas f on f.FakultasID = p.FakultasID", "p.ProdiID", $_SESSION['prodi'], "p.Nama as pnama, f.Nama as fnama");
  $NamaFakJur = (!empty($Njur)) ? $Njur['fnama'] .'/'. $Njur['pnama'] : "Semua Prodi"; 
  $hdr = str_pad("** TANDA TERIMA PENGAMBILAN IJAZAH **", $maxcol, ' ', STR_PAD_BOTH) . $_lf . $_lf . $_lf .
         "SEMESTER       : " . NamaTahun($_SESSION['tahun']) . $_lf .
         "Fak/Jur        : " . $NamaFakJur . $_lf .
         "TGL SK REKTOR  : " . $_SESSION['TglSK'] . $_lf .
         $div . 
         "No     NIM                NAMA                           NO.IJAZAH            TGL.TERIMA     TTD IJAZAH    TTD TRANSKRIP" . $_lf .
         $div . $_lf;
  $jump = 0; $jumw = 0;
  $Titik = "...........";
  fwrite($f, $hdr);
  while($w = _fetch_array($r)) {
    $n++; $brs++;
			if($brs > $maxbrs){
			  fwrite($f,$div);
				fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
			}
     
		$isi = str_pad($n.'.', 4, ' ') . 
           str_pad($w['MhswID'], 11, ' ') . 
           str_pad($w['Nama'], 33, ' ') .
           str_pad($w['NoIjazah'], 30, ' ') .
           str_pad($Titik, 15, ' ') . 
           str_pad($Titik, 15, ' ') . 
           str_pad($Titik, 13, ' ') . $_lf.$_lf;
    fwrite($f, $isi);	
  }      
  //$jumtotP = GetaField('wisudawan w left outer join mhsw m on w.MhswID = m.MhswID',"m.Kelamin = 'P' and WisudaID", $wsd['WisudaID'], "count(m.MhswID)"); 
  //$jumtotW = GetaField('wisudawan w left outer join mhsw m on w.MhswID = m.MhswID',"m.Kelamin = 'W' and WisudaID", $wsd['WisudaID'], "count(m.MhswID)"); 
  fwrite($f, $div);
 // fwrite($f, "Jumlah Seluruh Peserta/Jurusan : - Pria = $jump  - Wanita = $jumw" . $_lf);
  //fwrite($f, $div);
  //fwrite($f, "Jumlah Seluruh Peserta Seluruhnya : - Pria = $jumtotP  - Wanita = $jumtotW" . $_lf);
  //fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh : $_SESSION[_Login], ". date("d-m-Y H:i"),50,' ') . str_pad("Akhir Laporan",60,' ',STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f); 
  TampilkanFileDWOPRN($nmf, "form.tandaterima");
}

$TglSK_m = GetSetVar('TglSK_m', date('m'));
$TglSK_d = GetSetVar('TglSK_d', date('d'));
$TglSK_y = GetSetVar('TglSK_y', date('Y'));
$TglSK = "$TglSK_y-$TglSK_m-$TglSK_d";
$_Printer = GetSetVar('_Printer');
$_SESSION['TglSK'] = $TglSK;

$prodi = GetSetVar('prodi');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'FilterTahunProdiTanggal' : $_REQUEST['gos'];

TampilkanJudul("Tanda Terima Pengambilan Ijazah");
$gos();

?>
