<?php

function TampilkanTahunProdiStatus($mnux='',$gos=''){
   $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
   $optStat = GetOption2("statusmhsw","Concat(StatusMhswID, ' - ', Nama)", "StatusMhswID", $_SESSION['status'],"StatusMhswID in ('K','D')",'StatusMhswID');
   echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Status Mahasiswa</td><td class=ul><select name='status'>$optStat</select></td></tr> 
  <tr><td class=inp>Dari NPM</td>
  <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> s/d
  <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
  </td></tr>
  
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>
END;
}

function Daftar(){
  global $_lf;
  if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
  $prd = (empty($_SESSION['prodi'])) ? '' : "and m.ProdiID = '$_SESSION[prodi]'";
  $s = "select m.* from mhsw m 
        where m.StatusMhswID = '$_SESSION[status]' $prd $_npm ";
  $r = _query($s);
  
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77));
  
  // parameter
  $mxc = 160;
  $mxb = 50;
  $brs = 1;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $n = 0;
  $NamJur = GetaField('prodi', "ProdiID", $_SESSION['prodi'], "Nama");
  $NamaJudul = ($_SESSION['status'] == 'D') ? "Drop Out (DO)" : "Keluar";
  $hdr = str_pad("** Daftar Mahasiswa $NamaJudul", $mxc, ' ', STR_PAD_BOTH) . $_lf . $_lf .
         "SEMESTER         : " . NamaTahun($_SESSION['tahun']) . $_lf .
         "JURUSAN          : " . $NamJur . $_lf . 
         $grs .
         "No.   NIM         NAMA                                        NO SK                 TANGGAL SK     CATATAN" . $_lf . $grs;
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs >= $mxb) {
      $brs = 0;
      fwrite($f, str_pad("Bersambung...", $mxc, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $isi = str_pad($n.'. ', 4) . 
           str_pad($w['MhswID'], 10) .
           str_pad($w['Nama'], 40) . 
           str_pad($w['SKKeluar'], 30) .
           str_pad($w['TglSKKeluar'], 15) .
           str_pad($w['CatatanKeluar'], 30) .
           $_lf;
    fwrite($f, $isi);
    
  }
  fwrite($f, $grs);
  fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " . date("d-m-Y H:i"), 60) . str_pad("Akhir Laporan", 100));
  fwrite($f, chr(12));
  fclose($f);  
  TampilkanFileDWOPRN($nmf, 'akd.lap', 0);      
}

$prodi = GetSetVar('prodi');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$tahun = GetSetVar('tahun');
$status = GetSetVar('status');

//$gos = (empty($_REQUEST['gos'])) ? TampilkanTahunProdiStatus('akd.lap.mhswdokeluar','daftar') : $_REQUEST['gos'];

TampilkanJudul("Daftar Mahasiswa Keluar Atau DO");
TampilkanTahunProdiStatus('akd.lap.mhswdokeluar','daftar');
if (!empty($status)) Daftar();
//$gos();
?>
