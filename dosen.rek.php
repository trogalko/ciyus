<?php
// Author: Emanuel Setio Dewo
// 20 July 2006
// www.sisfokampus.net

include_once "dosen.hdr.php";

// *** Functions ***
function CariDosen() {
  TampilkanFilterDosen('dosen.rek', 1);
  DaftarDosen('dosen.rek', "gos=DsnRekEdt&md=0&dsnid==Login=", "NIDN,Nama,Gelar,Telephone,GolonganID,KategoriID,NamaBank,NamaAkun,NomerAkun");
}
function DsnRekEdt() {
  $dsnid = $_REQUEST['dsnid'];
  $w = GetFields('dosen', 'Login', $dsnid, '*');
  
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dosen.rek'>
  <input type=hidden name='gos' value='DsnRekSav'>
  <input type=hidden name='dsnid' value='$dsnid'>
  
  <tr><td class=ul colspan=2><font size=+1>Dosen</font></td></tr>
  <tr><td class=inp>Login/NIP</td>
    <td class=ul>$w[Login]</td></tr>
  <tr><td class=inp>NIDN</td>
    <td class=ul>$w[NIDN] &nbsp;</td></tr>
  <tr><td class=inp>Nama Dosen</td>
    <td class=ul>$w[Nama], $w[Gelar]</td></tr>
  <tr><td class=inp>Golongan</td>
    <td class=ul>$w[GolonganID] &nbsp;</td></tr>
  <tr><td class=inp>Kategori</td>
    <td class=ul>$w[KategoriID] &nbsp;</td></tr>
  
  <tr><td class=ul colspan=2><font size=+1>Rekening Dosen</font></td></tr>
  <tr><td class=inp>Nama Bank</td>
    <td class=ul><input type=text name='NamaBank' value='$w[NamaBank]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Nama Rekening</td>
    <td class=ul><input type=text name='NamaAkun' value='$w[NamaAkun]' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Nomer Rekening</td>
    <td class=ul><input type=text name='NomerAkun' value='$w[NomerAkun]' size=30 maxlength=50></td></tr>
  
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=dosen.rek&gos='\"></td></tr>
  </form></table></p>";
}
function DsnRekSav() {
  $dsnid = $_REQUEST['dsnid'];
  $NamaBank = sqling($_REQUEST['NamaBank']);
  $NamaAkun = sqling($_REQUEST['NamaAkun']);
  $NomerAkun = sqling($_REQUEST['NomerAkun']);
  // simpan
  $s = "update dosen set NamaBank='$NamaBank', NamaAkun='$NamaAkun', NomerAkun='$NomerAkun'
    where Login='$dsnid' ";
  $r = _query($s);
  echo Konfirmasi1("Data sudah disimpan");
  DsnRekEdt();
}


// *** Parameters ***
$dsnsub = GetSetVar('dsnsub');
$dsnurt = GetSetVar('dsnurt', 'Login');
$dsnid = GetSetVar('dsnid');
$dsncr = GetSetVar('dsncr');
$dsnkeycr = GetSetVar('dsnkeycr');
$dsnpage = GetSetVar('dsnpage');
if ($dsnkeycr == 'Reset') {
  $dsncr = '';
  $_SESSION['dsncr'] = '';
  $dsnkeycr = '';
  $_SESSION['dsnkeycr'] = '';
}
$prodi = GetSetVar('prodi');

$gos = (empty($_REQUEST['gos']))? 'CariDosen' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Rekening Dosen");
$gos();
?>
