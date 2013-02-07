<?php

function TampilkanMhswPMB(){
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='KodeID' value='$arrID[Kode]'>
  <input type=hidden name='mnux' value='form.undurdiri'>
  <input type=hidden name='gos' value='Daftar'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Cari Calon Mhsw:</td>
    <td class=ul><input type=text name='pmbid' value='$_SESSION[pmbid]' size=20 maxlength=50>
    <input type=submit name='Cari' value='PMBID'>
    <input type=submit name='Cari' value='Nama'></td></tr>
  </form></table><p>";
}

function Daftar(){
  global $_lf, $arrID;
  if (!empty($_SESSION['pmbid']) && !empty($_SESSION['Cari'])) {
    $s = "select p.* 
      from pmb p
      left outer join program prg on p.ProgramID=prg.ProgramID
      left outer join prodi prd on p.ProdiID=prd.ProdiID
      left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
      where p.$_SESSION[Cari] like '%$_SESSION[pmbid]%'
      order by p.$_SESSION[Cari]";
  $r = _query($s);
  $maxcol = 114; 
	$nmf = "tmp/$_SESSION[_Login].dwoprn";
	$f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(14));
  
    $namatemplate = "template/form.undurdiri.txt";
    $ft = fopen($namatemplate, 'r');
    $tpl = fread($ft, filesize($namatemplate));
    fclose($ft);
    
    $mhsw = _fetch_array($r);
    //if ($mhsw['LulusUjian'] == 'Y') {
    $NamaFakJur = GetFields('prodi p left outer join fakultas f on f.FakultasID = p.FakultasID', "p.ProdiID", $mhsw['ProdiID'],"f.Nama as Fnama, p.Nama as Pnama");
    // Pakai template
    $_t = $tpl . chr(12);
    $_t = str_replace('#TGL#', date('d/m/Y'), $_t);
    $_t = str_replace('#NAMA#', $mhsw['Nama'], $_t);
    $_t = str_replace('#ALAMAT#', $mhsw['Alamat'], $_t);
    $_t = str_replace('#PMBID#', $mhsw['PMBID'], $_t);
    $_t = str_replace('#FAKJUR#', $NamaFakJur['Fnama'] .'/'. $NamaFakJur['Pnama'], $_t);
    $_t = str_replace('#PERIODE#', $mhsw['PMBPeriodID'] ."/". $mhsw['TanggalUjian'], $_t);
    $_t = str_replace('#GRADE#', $mhsw['GradeNilai'], $_t);
    $_t = str_replace('#ORTU#', $mhsw['NamaAyah'], $_t);
    $_t = str_replace('#ALORTU#', $mhsw['AlamatOrtu'], $_t);  
  
  
  fwrite($f, $_t);
  fwrite($f, $_lf . $_lf . "Dicetak tanggal : ".date("d-m-Y") . " ; Jam " . Date("H:i") . " Oleh " . $_SESSION['_Login']);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "form.undurdiri");
    
  }
}
$pmbid = GetSetVar('pmbid');
$Cari = GetSetVar('Cari');
$gos = (empty($_REQUEST['gos']))? 'TampilkanMhswPMB' : $_REQUEST['gos'];

TampilkanJudul("Formulir Rencana Pengunduran Diri");
$gos();

?>
