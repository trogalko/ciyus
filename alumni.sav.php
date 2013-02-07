<?php
// Author: Emanuel Setio Dewo
// 31 May 2006
// www.sisfokampus.net

function AlumniEdtSav() {
  $AlumniID = $_REQUEST['AlumniID'];
  $Alamat = sqling($_REQUEST['Alamat']);
  $RT = sqling($_REQUEST['RT']);
  $RW = sqling($_REQUEST['RW']);
  $Kota = sqling($_REQUEST['Kota']);
  $Negara = sqling($_REQUEST['Negara']);
  $Email = sqling($_REQUEST['Email']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Handphone = sqling($_REQUEST['Handphone']);
  // Simpan
  $s = "update alumni set Alamat='$Alamat',
    RT='$RT', RW='$RW', Kota='$Kota', Negara='$Negara',
    Email='$Email', Telepon='$Telepon', Handphone='$Handphone',
    TanggalEdit=now(), LoginEdit='$_SESSION[_Login]'
    where MhswID='$AlumniID' ";
  $r = _query($s);
}
function AlumniKrjSav() {
  $md = $_REQUEST['md'];
  $AlumniID = $_REQUEST['AlumniID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Facsimile = sqling($_REQUEST['Facsimile']);
  $Website = sqling($_REQUEST['Website']);
  $NA = (empty($_REQUEST['NA']))? "N" : $_REQUEST['NA'];
  $MK = "$_REQUEST[MK_y]-$_REQUEST[MK_m]-$_REQUEST[MK_d]";
  $KK = "$_REQUEST[KK_y]-$_REQUEST[KK_m]-$_REQUEST[KK_d]";
  // simpan
  if ($md == 0) {
    $AlumniKerjaID = $_REQUEST['AlumniKerjaID'];
    $s = "update alumnikerja set Nama='$Nama', Jabatan='$Jabatan',
      Alamat='$Alamat', Kota='$Kota', KodePos='$KodePos',
      Propinsi='$Propinsi', Negara='$Negara',
      Telepon='$Telepon', Facsimile='$Facsimile',
      Website='$Website', NA='$NA',
      MulaiKerja='$MK', KeluarKerja='$KK',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where AlumniKerjaID='$AlumniKerjaID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into alumnikerja (MhswID, Nama, Jabatan,
      Alamat, Kota, KodePos, Propinsi, Negara,
      Telepon, Facsimile, Website, NA,
      MulaiKerja, KeluarKerja)
      values ('$AlumniID', '$Nama', '$Jabatan',
      '$Alamat', '$Kota', '$KodePos', '$Propinsi', '$Negara',
      '$Telepon', '$Facsimile', '$Website', '$NA',
      '$MK', '$KK')";
    $r = _query($s);
  }
}
?>
