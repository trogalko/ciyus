<?php
// Author: Emanuel Setio Dewo
// 06 Sept 2006
// www.sisfokampus.net

// *** Functions ***
function KonfirmasiKeluar($m) {
  $_status = GetFields("statusmhsw", "StatusMhswID", $m['StatusMhswID'], "*");
  if ($_status['Keluar'] == "Y")
    echo ErrorMsg("Mahasiswa Telah $_status[Nama]",
    "Mahasiswa <b>$m[Nama]</b> ($m[MhswID]) telah berstatus: <b>$_status[Nama]</b> 
    sehingga status tidak dapat diubah lagi.
    <hr size=1>
    Pilihan: <a href='?mnux=mhsw.keluar'>Kembali ke Daftar Mhsw</a>");
  else {
    $prd = GetFields('prodi', 'ProdiID', $m['ProdiID'], "ProdiID, FakultasID, Nama");
    $fak = GetaField('fakultas', 'FakultasID', $prd['FakultasID'], "Nama");
    $PA = GetaField("dosen", "Login", $m['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
    $khsidterakhir = GetaField("khs", "MhswID", $m['MhswID'], "max(KHSID)");
    $terakhir = GetFields('khs', "KHSID", $khsidterakhir, "*");
    $statusterakhir = GetaField('statusmhsw', 'StatusMhswID', $terakhir['StatusMhswID'], 'Nama');
    $optsta = GetOption2("statusmhsw", "concat(StatusMhswID, ' - ', Nama)", 'StatusMhswID', '',
      "Keluar='Y' and Lulus='N'", 'StatusMhswID');
    $TglKeluar = GetDateOption(date('Y-m-d'), 'TglSKKeluar');
    CheckFormScript("SKKeluar,StatusMhswID,Tahunnya");
    $optTahun = GetOption2("khs", "TahunID", "TahunID", '', "MhswID='$m[MhswID]'", "TahunID");
    echo Konfirmasi("Perubahan Status Mhsw",
    "Benar Anda akan mengubah status mahasiswa berikut ini?<br />
    <p><table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><td class=inp>NPM</td><td class=ul>$m[MhswID]</td></tr>
    <tr><td class=inp>Nama</td><td class=ul>$m[Nama]</td></tr>
    <tr><td class=inp>Fak/Jur</td><td class=ul>$fak/$prd[Nama]</td></tr>
    <tr><td class=inp>PA</td><td class=ul>$PA ($m[PenasehatAkademik])</td></tr>
    <tr><td class=inp>Semester Terakhir</td><td class=ul>$terakhir[Sesi]-$terakhir[TahunID], IPS: $terakhir[IPS], IPK: $m[IPK]</td></tr>
    <tr><td class=inp>Status Semester Terakhir</td><td class=ul>$statusterakhir ($terakhir[StatusMhswID])</td></tr>
    
    <tr><td class=ul colspan=2><font size=+1>Perubahan Status Menjadi:</td></tr>
    <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
    <input type=hidden name='mnux' value='mhsw.keluar.go'>
    <input type=hidden name='gos' value='Keluarkan'>
    <input type=hidden name='mhswid' value='$m[MhswID]'>
    <tr><td class=inp>Tahun Akd</td><td class=ul><select name='Tahunnya'>$optTahun</select></td></tr>
    <tr><td class=inp>Status</td><td class=ul><select name='StatusMhswID'>$optsta</select></td></tr>
    <tr><td class=inp>Nomer SK</td><td class=ul><input type=text name='SKKeluar' size=30 maxlength=50></td></tr>
    <tr><td class=inp>Tanggal SK</td><td class=ul>$TglKeluar</td></tr>
    <tr><td class=inp>Catatan Keluar</td><td class=ul><textarea name='CatatanKeluar' cols=30 rows=4></textarea></td></tr>
    <tr><td class=inp>Pilihan</td><td class=ul><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batalkan Perubahan Status' onClick=\"location='?mnux=mhsw.keluar'\"></td></tr>
    </table></p>
    ");
  } 
}
function Keluarkan($m) {
  $mhswid = $_REQUEST['mhswid'];
  $Tahunnya = $_REQUEST['Tahunnya'];
  $StatusMhswID = $_REQUEST['StatusMhswID'];
  $SKKeluar = sqling($_REQUEST['SKKeluar']);
  $TglSKKeluar = "$_REQUEST[TglSKKeluar_y]-$_REQUEST[TglSKKeluar_m]-$_REQUEST[TglSKKeluar_d]";
  $CatatanKeluar = sqling($_REQUEST['CatatanKeluar']);
  // update mhsw
  $s = "update mhsw set StatusMhswID='$StatusMhswID', SKKeluar='$SKKeluar', TglSKKeluar='$TglSKKeluar',
    CatatanKeluar='$CatatanKeluar'
    where MhswID='$mhswid'";
  $r = _query($s);
  $s0 = "update pmb set StatusMundur='Y' where NIM = '$mhswid'";
  $r0 = _query($s0);
  // update khs-nya
  $s1 = "update khs set StatusMhswID='$StatusMhswID' where TahunID='$Tahunnya' and MhswID='$mhswid' ";
  $r1 = _query($s1);
  $sta = GetaField("statusmhsw", "StatusMhswID", $StatusMhswID, "Nama");
  echo Konfirmasi("Mahasiswa Sudah $sta",
    "Mahasiswa <b>$m[Nama]</b> ($mhswid) sudah diset statusnya menjadi: <b>$sta</b>.<br />
    Status ini sudah tidak dapat diubah lagi dan mhsw sudah tidak dapat mengikuti kegiatan akademik lagi.
    <hr size=1>
    Pilihan: <a href='?mnux=mhsw.keluar'>Kembali ke Daftar</a>"); 
}

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$gos = (empty($_REQUEST['gos']))? "KonfirmasiKeluar" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Mhsw Keluar/DO");
$mhsw = GetFields("mhsw", "MhswID", $mhswid, "*");
if (!empty($mhsw)) {
  $gos($mhsw);
}
else echo ErrorMsg("Mahasiswa Tidak Ditemukan",
  "Mahasiswa dengan NPM <b>$mhswid</b> tidak ditemukan.
  <hr />
  Pilihan: <a href='?mnux=mhsw.keluar'>Kembali ke Daftar Mhsw</a>");
?>
