<?php
// Author: Emanuel Setio Dewo
// 17/01/2007, setio.dewo@gmail.com
// Desc: Untuk memblokir pencetakan LRS bagi mahasiswa dengan catatan tertentu

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$BlokNPM = GetSetVar('BlokNPM');
$gos = empty($_REQUEST['gos'])? 'TampilkanDaftarBlok' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Blok Pencetakan LRS/Blanko");
TampilkanTahunProdiProgram($_SESSION['mnux'], '');
TampilkanBlokNPM();
if (!empty($tahun)) {
  if (!empty($gos)) $gos();
  //TampilkanDaftarBlok();
}

// *** Functions ***
Function TampilkanBlokNPM() {
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='BlokNPM'>
  <tr><td class=inp>Blok NPM :</td>
      <td class=ul><input type=text name='BlokNPM' value='$_SESSION[BlokNPM]' size=20 maxlength=50></td>
      <td class=inp>Keterangan Blok :</td>
      <td class=ul><input type=text name='KeteranganBlok' size=40 maxlength=100></td>
      <td class=ul><input type=submit name='Blok' value='Blok'></td></tr>
  </form></table></p>";
}
function BlokNPM() {
  $BlokNPM = $_REQUEST['BlokNPM'];
  $KeteranganBlok = $_REQUEST['KeteranganBlok'];
  $s = "update khs set Blok='Y', KeteranganBlok='$KeteranganBlok'
    where TahunID='$_SESSION[tahun]'
      and MhswID='$BlokNPM' ";
  $r = _query($s);
  $jml = _affected_rows($r)+0;
  if ($jml <= 0) echo ErrorMsg("Gagal Blok",
    "Mahasiswa dgn NPM: <font size=+1>$BlokNPM</font> tidak dapat diblok.<br />
    Mungkin tidak terdaftar di semester <font size=+1>$_SESSION[tahun]</font>");
}
function TampilkanDaftarBlok() {
  echo "<p>Berikut adalah mahasiswa yang diblok sehingga tidak bisa mencetak LRS/Blanko:</p>";
  // Ambil daftarnya
  $whr = '';
  if (!empty($_SESSION['prodi'])) $whr .= " and k.ProdiID='$_SESSION[prodi]' ";
  if (!empty($_SESSION['prid'])) $whr .= " and k.ProgramID='$_SESSION[prid]' ";
  $s = "select k.KHSID, k.MhswID, k.StatusMhswID, m.Nama,
    k.Blok, k.KeteranganBlok
    from khs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.Blok='Y'
      and k.TahunID='$_SESSION[tahun]'
      $whr
    order by k.MhswID";
  $r = _query($s);
  // Tampilkan
  $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>No</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Blok?</th>
    <th class=ttl>Keterangan Blok</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul><a href='?mnux=$_SESSION[mnux]&gos=BlokNPMRel&khsid=$w[KHSID]' title='Lepaskan Blokir'><img src='img/$w[Blok].gif'></a></td>
      <td class=ul>$w[KeteranganBlok]</td>
    </tr>";
  }
  echo "</table></p>";
}
function BlokNPMRel() {
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields("khs k
    left outer join mhsw m on k.MhswID=m.MhswID",
    "KHSID", $khsid, "k.*, m.Nama");
  if (empty($khs)) {
    echo ErrorMsg("Data Tidak Ditemukan",
      "Data KHS dengan ID: <b>$khsid</b> tidak ditemukan.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos='>Kembali</a>");
  }
  else {
    echo Konfirmasi("Konfirmasi Pelepasan Block Blanko",
      "Benar Anda akan melepaskan pengeblokan blanko?
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]&gos=BlokNPMRel1&khsid=$khsid&BypassMenu=1' title='Lepaskan Blok'>Lepaskan</a> |
      <a href='?mnux=$_SESSION[mnux]&gos='>Kembali</a>");
  }
}
function BlokNPMRel1() {
  $khsid = $_REQUEST['khsid'];
  $s = "update khs set Blok='N' where KHSID='$khsid' ";
  $r = _query($s);
  echo "<script>window.location = '?mnux=$_SESSION[mnux]&gos=';</script>";
}
?>
