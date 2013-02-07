<?php
// Author: Emanuel Setio Dewo
// 24 November 2005
// www.sisfokampus.net
// email: setio.dewo@gmail.com

include_once "klinik.lib.php";

// *** Parameters ***
$crkey = GetSetVar('crkey');
$crval = GetSetVar('crval');
$MhswID = GetSetVar('MhswID', $crval);
$gos = (empty($_REQUEST['gos']))? "TampilkanHutangKlinik" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Catatan Hutang Mahasiswa");
TampilkanCariMhsw1('klinik.hutang', 0);
$gos();

// *** Functions ***
function TampilkanHutangKlinik() {
  $MhswID = $_SESSION['crval'];
  $_SESSION['MhswID'] = $MhswID;
  $mhsw = GetFields('mhsw', 'MhswID', $MhswID, '*');
  if (!empty($mhsw)) TampilkanHutangMhsw1($mhsw);
  
}
function TampilkanHutangMhsw1($mhsw) {
  TampilkanHeaderMhswKlinik($mhsw);
  echo "<p><a href='?mnux=klinik.hutang&gos=HTGEDT&md=1&MhswID=$mhsw[MhswID]'>Tambah Hutang Mahasiswa</a></p>";
  
  // Ambil data
  $s = "select *
    from hutangmhsw hm
    where hm.MhswID='$mhsw[MhswID]'
    order by hm.Tanggal";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl colspan=2>#</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl>Catatan</th>
    </tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $tgl = FormatTanggal($w['Tanggal']);
    $jml = number_format($w['Jumlah']);
    $byr = number_format($w['Dibayar']);
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul><a href='?mnux=$_SESSION[mnux]&gos=HTGEDT&md=0&HTGID=$w[HutangMhswID]'><img src='img/edit.png'></a></td>
      <td $c>$tgl</td>
      <td $c align=right>$jml</td>
      <td $c align=right>$byr</td>
      <td $c>$w[Catatan]</td>
      </tr>";
  }
  echo "</table></p>";
}
/// untuk mengedit hutang
function HTGEDT() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $HTGID = $_REQUEST['HTGID'];
    $w = GetFields('hutangmhsw', 'HutangMhswID', $HTGID, '*');
    $jdl = "Edit Hutang Mahasiswa";
  }
  else {
    $w = array();
    $w['HutangMhswID'] = 0;
    $w['Tanggal'] = date('Y-m-d');
    $w['MhswID'] = $_SESSION['MhswID'];
    $w['Jumlah'] = 0;
    $w['Dibayar'] = 0;
    $w['Tutup'] = 'N';
    $w['Catatan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Hutang Mahasiswa";
  }
  $OptTgl = GetDateOption($w['Tanggal'], 'Tanggal');
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='HTGSAV'>
  <input type=hidden name='HutangMhswID' value='$w[HutangMhswID]'>
  <input type=hidden name='MhswID' value='$w[MhswID]'>
  <input type=hidden name='BypassMenu' value=1>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Tanggal</td>
    <td class=ul>$OptTgl</td></tr>
  <tr><td class=inp>Jumlah Hutang</td>
    <td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Sudah Dibayar</td>
    <td class=ul><input type=text name='Dibayar' value='$w[Dibayar]' size=30 maxlength=30></td></tr>
  <tr><td class=inp>Catatan (Matakuliah)</td>
    <td class=ul><textarea name='Catatan' cols=30 rows=4>$w[Catatan]</textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]'\"></td></tr>
  </form></table></p>";
}
/// Menyimpan catatan hutang mhsw
function HTGSAV() {
  $md = $_REQUEST['md']+0;
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $MhswID = $_REQUEST['MhswID'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Dibayar = $_REQUEST['Dibayar']+0;
  $Catatan = sqling($_REQUEST['Catatan']);
  $NA = ($Jumlah == $Dibayar)? 'Y' : 'N';
  
  // Simpan
  if ($md == 0) {
    $HutangMhswID = $_REQUEST['HutangMhswID']+0;
    $s = "update hutangmhsw
      set Tanggal='$Tanggal', Jumlah='$Jumlah', Dibayar='$Dibayar',
      Catatan='$Catatan', NA='$NA'
      where HutangMhswID=$HutangMhswID";
    $r = _query($s);
  }
  else {
    $s = "insert into hutangmhsw
      (Tanggal, MhswID,
      Jumlah, Dibayar, NA,
      Catatan, LoginBuat, TglBuat)
      values ('$Tanggal', '$MhswID',
      $Jumlah, $Dibayar, '$NA',
      '$Catatan', '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  echo "<script>window.location = '';</script>";
}
?>
