<?php
// Author: Emanuel Setio Dewo
// 16 April 2006
// Happy Easter
// Selamat Paskah

// *** Functions ***
function wPeriodeDftr() {
  echo "<p><a href='?mnux=wisuda&token=wPeriodeEdt&md=1'>Tambah Periode Wisuda</a></p>";
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['wsdpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=wisuda&wsdpage==PAGE='>=PAGE=</a>";

  $lst->tables = "wisuda w
    where w.KodeID='$_SESSION[KodeID]'
    order by w.WisudaID desc";
  $lst->fields = "*,
    date_format(TglMulai, '%d/%m/%Y') as TM,
    date_format(TglSelesai, '%d/%m/%Y') as TS,
    date_format(TglWisuda, '%d/%m/%Y') as TW";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>Nama</th>
    <th class=ttl colspan=2>Pendaftaran</th>
    <th class=ttl>Wisuda</th>
    <th class=ttl>Aktif</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=ul><a href='?mnux=wisuda&token=wPeriodeEdt&md=0&WisudaID==WisudaID='><img src='img/edit.png'>
    =WisudaID=</a></td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA=>=TM=</td>
    <td class=cna=NA=>=TS=</td>
    <td class=cna=NA=>=TW=</td>
    <td class=ul align=center><img src='img/book=NA=.gif'></td>
    </tr>";
  echo $lst->TampilkanData();
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "</p>";
}
function wPeriodeEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('wisuda', 'WisudaID', $_REQUEST['WisudaID'], '*');
    $jdl = "Edit Periode Wisuda";
  }
  else {
    $w = array();
    $w['WisudaID'] = 0;
    $w['Nama'] = '';
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $w['TglWisuda'] = date('Y-m-d');
    $w['Jumlah'] = 0;
    $w['NA'] = 'N';
    $jdl = "Tambah Periode Wisuda";
  }
  $TM = GetDateOption($w['TglMulai'], 'TM');
  $TS = GetDateOption($w['TglSelesai'], 'TS');
  $TW = GetDateOption($w['TglWisuda'], 'TW');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan form
  echo <<<END
  <p><table class=box cellspacing=1>
  <form action='?' name='frmWsd' method=POST>
  <input type=hidden name='mnux' value='wisuda'>
  <input type=hidden name='token' value='wPeriodeSav'>
  <input type=hidden name='WisudaID' value='$w[WisudaID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='OldNA' value='$w[NA]'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Nama Periode</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Mulai Pendaftaran</td><td class=ul>$TM</td></tr>
  <tr><td class=inp>Selesai Pendaftaran</td><td class=ul>$TS</td></tr>
  <tr><td class=inp>Tanggal Wisuda</td><td class=ul>$TW</td></tr>
  <tr><td class=inp>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick="location='?mnux=wisuda&token='"></td></tr>
  </form></table>
END;
}
function wPeriodeSav() {
  $md = $_REQUEST['md']+0;
  $WisudaID = $_REQUEST['WisudaID'];
  $Nama = sqling($_REQUEST['Nama']);
  $TM = "$_REQUEST[TM_y]-$_REQUEST[TM_m]-$_REQUEST[TM_d]";
  $TS = "$_REQUEST[TS_y]-$_REQUEST[TS_m]-$_REQUEST[TS_d]";
  $TW = "$_REQUEST[TW_y]-$_REQUEST[TW_m]-$_REQUEST[TW_d]";
  $na = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // Simpan
  if ($md == 0) {
    $s = "update wisuda set Nama='$Nama', TglMulai='$TM', TglSelesai='$TS', 
      TglWisuda='$TW', NA='$na'
      where WisudaID='$WisudaID'";
    $r = _query($s);
  }
  else {
    $s = "insert into wisuda (KodeID, Nama, TglMulai, TglSelesai, TglWisuda, NA)
      values ('$_SESSION[KodeID]', '$Nama', '$TM', '$TS', '$TW', '$na') ";
    $r = _query($s);
    $WisudaID = GetLastID();
  }
  if ($na == 'N') {
    $sn = "update wisuda set NA='Y' where WisudaID<>$WisudaID and NA='N' ";
    $rn = _query($sn);
  }
  wPeriodeDftr();
}


// *** Parameters ***
$wsdpage = GetSetVar('wsdpage');
$actPer = GetFields('wisuda', 'NA', 'N', 'WisudaID');
$token = (empty($_REQUEST['token'])) ? 'wPeriodeDftr' : $_REQUEST['token']; 

// *** Main ***
TampilkanJudul("Setup Wisuda");
if (!empty($token)) $token();
?>
