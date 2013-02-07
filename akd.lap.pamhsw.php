<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanPilihanDosen($mnux='', $gos='') {
  global $KodeID;
  $homebase = (empty($_SESSION['prodi']))? '' : "and Homebase='$_SESSION[prodi]' ";
  //GetCheckboxes($table, $key, $Fields, $Label, $Nilai='', $Separator=',', $whr = '') {
  $cekstat = GetCheckBoxes("statusmhsw", "StatusMhswID", "Nama", "Nama", $_SESSION['StatusMhswID'], ",", '', ', ');
  $opt = GetOption2("dosen", "concat(Login, ' - ', Nama, ', ', Gelar)",
    "Login", $_SESSION['dsnid'], "KodeID='$KodeID' $homebase", 'Login');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=inp>Dosen :</td><td class=ul><select name='dsnid'>$opt</select>
    <input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  <tr><td class=inp>Status Mhsw :</td><td class=ul>$cekstat</td></tr>
  </form></table></p>";
}
function TampilkanPA() {
  global $_lf;
  $whr = array();
  if (!empty($_SESSION['dsnid'])) $whr[] = "m.PenasehatAkademik='$_SESSION[dsnid]' ";
  if (!empty($_SESSION['DariNPM']) && !empty($_SESSION['SampaiNPM'])) $whr[] = " '$_SESSION[DariNPM]' <= m.MhswID and m.MhswID <= '$_SESSION[SampaiNPM]' ";
  // Status Mhsw
  if (!empty($_SESSION['StatusMhswID'])) {
    $arrsm = explode(',', $_SESSION['StatusMhswID']);
    $strsm = '';
    foreach ($arrsm as $val) $strsm .= ",'$val'";
    $strsm = TRIM($strsm, ',');
    $whr[] = "k.StatusMhswID in ($strsm)";
  }
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
  $s = "select m.MhswID, m.Nama, m.TotalSKS, m.IPK, m.PenasehatAkademik,
    m.BatasStudi, k.StatusMhswID, k.TotalSKS as SKSSemester, k.IPS,
    concat(d.Nama, ', ', d.Gelar) as DSN, sm.Nama as STT
    from khs k 
      left outer join mhsw m on k.MhswID=m.MhswID
      left outer join dosen d on m.PenasehatAkademik=d.Login
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.TahunID='$_SESSION[tahun]' and k.ProdiID='$_SESSION[prodi]'
      and m.StatusMhswID in ('A')
      $_whr
    order by m.PenasehatAkademik, m.MhswID";
  $r = _query($s);

  // Cetak
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(5));
  $mxb = 52;
  $mxc = 114;
  $grs = str_pad('-', $mxc, '-').$_lf;
  $dsn = 'qwertyuiop'; $n = 0; $hal = 0; $brs = 0;
  $def = $dsn;
  $smt = GetaField('tahun', "ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
    $_SESSION['tahun'], 'Nama');
  $fp = GetaField("prodi p left outer join fakultas f on p.FakultasID=f.FakultasID",
    "p.ProdiID", $_SESSION['prodi'], "concat(f.Nama, '/', p.Nama)");
  $tgl = date('d-m-Y H:i');
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/35);
  while ($w = _fetch_array($r)) {
    if ($brs > $mxb) {
      fwrite($f, chr(12));
      $brs = 0;
      $hal++;
    }
    if ($dsn != $w['PenasehatAkademik']) {
      $hal++;
      if ($dsn != $def) {
        fwrite($f, $grs."Dicetak oleh: $_SESSION[_Login]".$_lf);
        fwrite($f, chr(12));
      }
      $dsn = $w['PenasehatAkademik'];
      fwrite($f, str_pad('*** Daftar P.A. dan Mahasiswa ***', $mxc, ' ', STR_PAD_BOTH).$_lf.$_lf.
        str_pad('Semester  : ' . $smt, $mxc/2).
        str_pad("Tanggal : $tgl", $mxc/2, ' ', STR_PAD_LEFT). $_lf.
        str_pad('Fak/Jur   : ' . $fp, $mxc/2).
        str_pad("Form : AKD518", $mxc/2, ' ', STR_PAD_LEFT). $_lf.
        str_pad('P.A.      : ' . $w['PenasehatAkademik'] . ' - '.$w['DSN'], $mxc/2).
        str_pad("Hal. $hal", $mxc/2, ' ', STR_PAD_LEFT).$_lf.
        $grs);
      fwrite($f, "                                                  Ambil                  Total".$_lf);
      fwrite($f, "No.  N.P.M          Nama Mahasiswa                  SKS Status       IPK   SKS Batas Studi          Cuti ".$_lf.$grs);
      $n = 0;
      $brs = 0;
    }

    $n++;
    $brs++;
    $bs = NamaTahun($w['BatasStudi']);
    $ct = AmbilDaftarCutiMhsw($w['MhswID']);
    fwrite($f, str_pad($n.'.', 5).
      str_pad($w['MhswID'], 15).
      str_pad($w['Nama'], 30).
      str_pad($w['SKSSemester'], 5, ' ', STR_PAD_LEFT). ' '.
      str_pad($w['STT'], 10).
      str_pad($w['IPK'], 6, ' ', STR_PAD_LEFT).
      str_pad($w['TotalSKS'], 6, ' ', STR_PAD_LEFT). ' '.
      str_pad($bs, 20).
      $ct.
      $_lf);
  }
  fwrite($f, $grs."Dicetak oleh: $_SESSION[_Login]");
  for ($i=$brs; $i <= $mxb; $i++) fwrite($f, $_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'akd.lap');
}
function AmbilDaftarCutiMhsw($mhswid) {
  $s = "select TahunID from khs where MhswID='$mhswid' and StatusMhswID='C' order by TahunID";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) $arr[] = $w['TahunID'];
  return implode(' ', $arr);
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$dsnid = GetSetVar('dsnid');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
// Cek filter status mhsw
$StatusMhsw = $_REQUEST['StatusMhswID'];
if (empty($StatusMhsw)) $_SESSION['StatusMhswID'] = '';
else {
  $StatusMhswID = implode(',', $StatusMhsw);
  $_SESSION['StatusMHswID'] = $StatusMhswID;
}

// *** Main ***
TampilkanJudul("Daftar Dosen Pembimbing & Mahasiswa");
//TampilkanTahunProdiProgram('akd.lap.pamhsw');
TampilkanTahunProdiProgram('akd.lap.pamhsw', 'Daftar', '', '', 1);
TampilkanPilihanDosen('akd.lap.pamhsw');
if (!empty($tahun) && !empty($prodi)) TampilkanPA();
?>
