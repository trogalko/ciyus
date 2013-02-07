<?php
// Author: Emanuel Setio Dewo
// 27 April 2006
// www.sisfokampus.net

// *** Functions ***
function Daftar() {
  global $_lf, $arrUrutkan, $Urutkan, $arrKeyIP, $KeyIP, $DariIP, $SampaiIP;
  $MaxCol = 114;
  $maxbrs = 52;  
  // filter
  $_arrwhr = array();
  if (!empty($_SESSION['prid'])) $_arrwhr[] = "khs.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $_arrwhr[] = "khs.ProdiID='$_SESSION[prodi]'";
  // rentang MhswID
  $RentangMhswID = '';
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) {
    $_arrwhr[] = "khs.MhswID >= '$_SESSION[DariNPM]' and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
    $RentangMhswID = str_pad(" Rentang NPM: $_SESSION[DariNPM] s/d $_SESSION[SampaiNPM]", $MaxCol, ' ', STR_PAD_BOTH).$_lf;
  }
  // Rentang IP
  $RentangIP = '';
  for ($i = 0; $i < sizeof($arrKeyIP); $i++) {
    $str = explode('~', $arrKeyIP[$i]);
    if ($str[0] == $KeyIP) {
      $_arrwhr[] = "$_SESSION[DariIP]<=$str[2].$str[0] and $str[2].$str[0]<=$_SESSION[SampaiIP]";
      $RentangIP = str_pad("Rentang $str[1] dari $_SESSION[DariIP] s/d $_SESSION[SampaiIP]", $MaxCol, ' ', STR_PAD_BOTH).$_lf;
    }
  }
  $whr = (empty($_arrwhr))? '' : "and " .implode(' and ', $_arrwhr);
  // tentukan urutan
  $ord = ''; $urt = '';
  for ($i = 0; $i < sizeof($arrUrutkan); $i++) {
    $str = explode('~', $arrUrutkan[$i]);
    if ($str[0] == $Urutkan) {
      $ord = ", $str[0] $str[2]";
      $urt = $str[1];
    }
  }
  $s = "select khs.MhswID, m.Nama, khs.IP, khs.IPS, m.TotalSKS, m.ProdiID, khs.StatusMhswID
    from khs khs
      left outer join mhsw m on khs.MhswID=m.MhswID
    where khs.TahunID='$_SESSION[tahun]' 
    and m.Nama is not NULL
    and khs.StatusMhswID not in ('P')
    and m.StatusMhswID = 'A'
    $whr
    order by m.ProdiID $ord";
  $r = _query($s);
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  
  // parameter2
  $n = 0; $hal = 1;
  $brs = 0;
  $_prd = '';
  // Buat header
  $hdr = str_pad("*** Daftar IPK/IPS Mahasiswa Urut $urt ***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf .
    $RentangMhswID. $RentangIP.
    "Semester  : " . NamaTahun($_SESSION['tahun']) . $_lf.$div.
    "No.  NPM             Nama                           Tot SKS   IPK   IPS         STATUS".$_lf.$div;
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
      if ($_prd != $w['ProdiID']) {
      $brs++;
      $_prd = $w['ProdiID'];
      $_nmprd = GetaField('prodi', 'ProdiID', $_prd, 'Nama');
      fwrite($f, "» $_nmprd ($_prd)" . $_lf);
    }
    $n++;
    $brs++;
    if ($brs >= $maxbrs) {
      $brs = 0;
      fwrite($f, $div . chr(12));
      fwrite($f, $hdr);
    }
    $Statusini = GetaField('statusmhsw',"StatusMhswID",$w['StatusMhswID'],'Nama');
    fwrite($f, str_pad($n.'.', 5) .
      str_pad($w['MhswID'], 15) . ' '.
      str_pad($w['Nama'], 35) . 
      str_pad($w['TotalSKS'], 3, ' ', STR_PAD_LEFT). ' '.
      str_pad($w['IP'], 5, ' ', STR_PAD_LEFT) . ' '.
      str_pad($w['IPS'], 5, ' ', STR_PAD_LEFT) .
      str_pad($Statusini,15, ' ', STR_PAD_LEFT) . 
      $_lf);
  }
  
  // Penutupan
  fwrite($f, $div);
  fwrite($f, str_pad("Akhir laporan", 114, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}
function TampilkanOpsiIPKIPS($mnux, $gos) {
  global $Urutkan, $arrUrutkan, $arrKeyIP, $KeyIP, $DariIP, $SampaiIP;
  // pilihan Urutan
  $opt = '';
  for ($i = 0; $i < sizeof($arrUrutkan); $i++) {
    $str = explode('~', $arrUrutkan[$i]);
    $sel = ($str[0] == $Urutkan)? 'selected' : '';
    $opt .= "<option value='$str[0]' $sel>$str[1]</option> \n";
  }
  // Pilihan Rentang IP
  $optip = '';
  for ($i = 0; $i < sizeof($arrKeyIP); $i++) {
    $str = explode('~', $arrKeyIP[$i]);
    $sel = ($str[0] == $KeyIP)? 'selected' : '';
    $optip .= "<option value='$str[0]' $sel>$str[1]</option> \n";
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=inp1>Urutkan</td><td class=ul><select name='Urutkan'>$opt</select> <input type=submit value='Tentukan' name='Tentukan'></td></tr>
  <tr><td class=inp1>Rentang <select name='KeyIP'>$optip</select></td>
    <td class=ul><input type=text name='DariIP' value='$DariIP' size=5 maxlength=5> s/d
    <input type=text name='SampaiIP' value='$SampaiIP' size=5 maxlength=5></td></tr>
  </form></table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$Urutkan = GetSetVar('Urutkan', 'IPS');
$arrUrutkan = array('IPS~IPS~desc', 'IPK~IPK~desc', 'Nama~Nama~asc', 'MhswID~NPM~asc');
$DariIP = GetSetVar('DariIP', 0);
$SampaiIP = GetSetVar('SampaiIP', 4);
$_SESSION['DariIP'] += 0; $_SESSION['SampaiIP'] += 0;
$KeyIP = GetSetVar('KeyIP', 'IPS');
$arrKeyIP = array('IPS~IPS~khs', 'IPK~IPK~m');

// *** Main ***
TampilkanJudul("Daftar IPK/IPS Mahasiswa");
TampilkanTahunProdiProgram('akd.lap.ipkipsmhsw', 'Daftar', '', '', 1);
TampilkanOpsiIPKIPS('akd.lap.ipkipsmhsw', 'Daftar');
if (!empty($tahun)) Daftar();
?>
