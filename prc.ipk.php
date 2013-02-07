<?php
// Author: Emanuel Setio Dewo
// 19 May 2006 (Pengganti prc.ipk.x.php)
// http://www.sisfokampus.net

// *** Functions ***
function ProsesIPK() {
  $tahun = GetaField('tahun', "NA='N' and ProgramID='$_SESSION[prid]' and ProdiID", $_SESSION['prodi'], "TahunID");
  $_SESSION['tahun'] = $tahun;
  if (empty($tahun))
    die(ErrorMsg('Tidak Dapat Diproses', 
    "IPK/IPS tidak dapat diproses karena tidak ditemukan tahun akademik yang aktif untuk Program=$_SESSION[prid], Program Studi=$_SESSION[prodi]"));
  echo Konfirmasi("Konfirmasi Proses",
    "Benar Anda akan memproses IPK untuk tahun akademik:
    <h1>$tahun</h1>
    Proses mungkin memakan waktu yang lama.<hr>
    Pilihan: <input type=button name='Proses' value='ProsesIPK' onClick=\"location='?mnux=prc.ipk&gos=ProsesIPK1&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]'\">");
}
function ProsesIPK1() {
  global $tahun, $prodi, $prid, $DariNPM, $SampaiNPM;
  /*
  if (!empty($DariNPM)) {
    $SampaiNPM = (empty($SampaiNPM))? $DariNPM : $SampaiNPM;
    $_npm = "  and '$DariNPM' <= k.MhswID and k.MhswID <= '$SampaiNPM' ";
  } else */ 
  $_npm = '';
  $s = "select k.MhswID, k.KHSID
    from khs k
    where k.TahunID='$tahun'
      and k.ProdiID='$prodi'
      and k.ProgramID='$prid'
      $_npm
    order by k.MhswID";
  //echo "<pre>$s</pre>";
  $r = _query($s);
  $_SESSION['IPK'.$prodi] = 0;
  while ($w = _fetch_array($r)) {
    $_pos = $_SESSION['IPK'.$prodi];
    $_SESSION['IPK-MhswID'. $prodi . $_pos] = $w['MhswID'];
    $_SESSION['IPK-KHSID' . $prodi . $_pos] = $w['KHSID'];
    $_SESSION['IPK'.$prodi]++;
  }
  $max = $_SESSION['IPK'.$prodi];
  $_SESSION['IPK'.$prodi.'POS'] = 0;
  echo "<p>Akan diproses: <font size=+2>$max</font> mahasiswa.</p>
  <p><IFRAME SRC='prc.ipk.go.php?gos=PRC2&tahun=$tahun&prodi=$prodi&prid=$prid' frameborder=0>
  </IFRAME></p>";
}
function TampilkanHeaderProsesIPK($mnux, $gos) {
  global $arrID;
  // Ambil hak akses prodi
  if (empty($_SESSION['_ProdiID'])) $_prodi = '-1';
  else {
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    //echo $_ProdiID;
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = '';
    for ($i = 0; $i<sizeof($arrProdi); $i++) $_prodi .= ",'".$arrProdi[$i]."'";
    $_prodi = trim($_prodi, ',');
    $_prodi = (empty($arrProdi))? '-1' : $_prodi; //implode(', ', $arrProdi);
  }
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], "KodeID='$arrID[Kode]' and ProdiID in ($_prodi)", 'ProdiID');
  $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$arrID[Kode]'", 'ProgramID');
  // Tampilkan header
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=inp1>Program</td>
    <td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp1>program Studi</td>
    <td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  
  </form></table></p>";
}

// *** Parameter ***
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? "ProsesIPK" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Hitung IPK");
//TampilkanTahunProdiProgram("prc.ipk", "ProsesIPK", '', '', 1);
TampilkanHeaderProsesIPK('prc.ipk', 'ProsesIPK');
if (!empty($prodi) && !empty($prid)) $gos();
?>
