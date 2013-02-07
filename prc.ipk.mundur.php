<?php
// Author: Emanuel Setio Dewo
// 10 Januari 2007
// http://www.sisfokampus.net
// Email: setio.dewo@gmail.com

include_once "dwo.lib.php";

// *** Parameters ***
$tahun1 = GetSetVar('tahun1');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$gos = $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Proses Hitung IPK/IPS Mundur");
TampilkanHeaderHitungIPKMundur();
if (!empty($gos) && !empty($tahun1) && !empty($DariNPM)) $gos();

// *** Functions ***
function TampilkanHeaderHitungIPKMundur() {
  CheckFormScript('tahun1,DariNPM');
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST onSubmit=\"return CheckForm(this);\">
  <input type=hidden name='mnux' value='prc.ipk.mundur'>
  <input type=hidden name='gos' value='KonfirmasiHitungMundur'>
  <tr><td class=inp1>Tahun Akd</td>
      <td class=ul><input type=text name='tahun1' value='$_SESSION[tahun1]' size=10 maxlength=10></td></tr>
  <tr><td class=inp1>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> harus diisi</td></tr>
  <tr><td class=inp1>Sampai NPM</td>
      <td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50> kosongkan jika hanya memproses 1 mhsw saja</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Proses' value='Proses'></td></tr>
  </form></table></p>";
}
function KonfirmasiHitungMundur(){
  $tahun1 = $_REQUEST['tahun1'];
  $DariNPM = $_REQUEST['DariNPM'];
  $SampaiNPM = $_REQUEST['SampaiNPM'];
  if (empty($SampaiNPM)) {
    $ada = GetFields('mhsw', "MhswID", $DariNPM, "Nama, ProgramID, ProdiID");
    if (empty($ada))
      die(ErrorMsg("Tidak dapat diproses",
      "Mahasiswa dengan NPM: <font size=+1>$DariNPM</font> tidak ditemukan."));
    $pesan = "Akan diproses 1 mahasiswa, yaitu: <font size=+1>$DariNPM</font>.";
  }
  else {
    $jml = GetaField('mhsw', "'$DariNPM' <= MhswID and MhswID <= '$SampaiNPM' and NA", 'N', "count(MhswID)")+0;
    if ($jml == 0)
      die(ErrorMsg("Tidak dapat diproses", 
      "Tidak ada mahasiswa dalam rentang NPM: <font size=+1>$DariNPM</font> sampai <font size=+1>$SampaiNPM</font>.
      Proses tidak dapat dilakukan."));
    $pesan = "Akan diproses mahasiswa dari NPM: <font size=+1>$DariNPM</font>
      sampai <font size=+1>$SampaiNPM</font> yang berjumlah: <font size=+1>$jml</font> mahasiswa.";
  }
  echo Konfirmasi("Konfirmasi", $pesan . "<br />".
    "Proses akan dilakukan untuk Tahun Akademik: <font size=+1>$tahun1</font> dan seterusnya.
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=prc.ipk.mundur&gos=ProsesHitungMundur&tahun1=$tahun1&DariNPM=$DariNPM&SampaiNPM=$SampaiNPM'>Proses Hitung Mundur</a>");
}
function ProsesHitungMundur() {
  $tahun1 = $_REQUEST['tahun1'];
  $DariNPM = $_REQUEST['DariNPM'];
  $SampaiNPM = $_REQUEST['SampaiNPM'];
  $_SESSION['HM-tahun1'] = $tahun1;
  // Bila hanya 1 mhsw
  if (empty($SampaiNPM)) {
    $_SESSION['HM-JML'] = 1;
    $_SESSION['HM-MhswID-1'] = $DariNPM;
    $_SESSION['HM-POS'] = 0;
  }
  // Jika banyak
  else {
    $s = "select MhswID
      from mhsw
      where '$DariNPM' <= MhswID and MhswID <= '$SampaiNPM'
        and NA='N'";
    $r = _query($s);
    $jml = _num_rows($r);
    $n = 0;
    while ($w = _fetch_array($r)) {
      $n++;
      $_SESSION['HM-MhswID-'.$n] = $w['MhswID'];
    }
    $_SESSION['HM-JML'] = $n;
    $_SESSION['HM-POS'] = 0;
  }
  echo "<p>Akan diproses: <font size=+2>".$_SESSION['HM-JML']."</font> mahasiswa.</p>
  <p><IFRAME SRC='prc.ipk.go.php?gos=PRCMUNDUR&tahun=$tahun&prodi=$prodi&prid=$prid' width=90% frameborder=0>
  </IFRAME></p>";
}
function ProsesIPK1() {// Tidak dipakai. Ini punya prc.ipk.php
  global $tahun, $prodi, $prid, $DariNPM, $SampaiNPM;
  if (!empty($DariNPM)) {
    $SampaiNPM = (empty($SampaiNPM))? $DariNPM : $SampaiNPM;
    $_npm = "  and '$DariNPM' <= k.MhswID and k.MhswID <= '$SampaiNPM' ";
  } else $_npm = '';
  $s = "select k.MhswID, k.KHSID
    from khs k
    where k.TahunID='$tahun'
      and k.ProdiID='$prodi'
      and k.ProgramID='$prid'
      $_npm
    order by k.MhswID";
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
?>
