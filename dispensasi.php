<?php
// Author: Emanuel Setio Dewo
// 01 March 2006

// *** Functions ***
function TampilkanCariDispensasi() {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dispensasi'>
  <tr><td class=ul>Cari Dispensasi:</td>
    <td class=ul><input type=text name='crdispid' value='$_SESSION[crdispid]' size=10 maxlength=20>
    <input type=submit name='Cari' value='Cari'>
    <input type=button name='Reset' value='Reset' onClick=\"location='?mnux=dispensasi&crdispid='\">
    </td></tr>
  </form></table></p>";
}
function DispDispensasi() {
  include_once "class/dwolister.class.php";

  echo "<p><a href='?mnux=dispensasi&gos=DispensasiEdt&md=1'>Buat Surat Dispensasi</a></p>";

  $whr = '';
  $whr = (empty($_SESSION['crdispid']))? '' : "DispensasiID like '%$_SESSION[crdispid]%' ";
  $whr = (empty($whr))? '' : "where " . $whr;

  $lst = new dwolister;
  $lst->tables = "dispensasi $whr
    order by Tanggal Desc";
  $lst->fields = "*, date_format(Tanggal, '%d/%m/%Y') as TGL,
    date_format(SampaiTanggal, '%d/%m/%Y') as STGL";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>Nomer Surat</th>
    <th class=ttl>Tertanggal</th>
    <th class=ttl>Mahasiswa</th>
    <th class=ttl>Perihal</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr><td class=inp1>=NOMER=</td>
    <td class=ul><a href='?mnux=dispensasi&gos=DispensasiEdt&md=0&dispid==DispensasiID='><img src='img/edit.png'>
    =DispensasiID=</a></td>
    <td class=ul>=TGL= ~ =STGL=</td>
    <td class=ul>=MhswID=</td>
    <td class=ul>=Judul=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo "<p>Halaman: ". $lst->TampilkanHalaman() . "</p>";;
}
function DispensasiEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('dispensasi', "DispensasiID", $_REQUEST['dispid'], '*');
    $jdl = "Edit Surat Dispensasi";
    $strid = "<input type=hidden name='dispid' value='$w[DispensasiID]'><b>$w[DispensasiID]</b>";
  }
  else {
    $w = array();
    $w['DispensasiID'] = '';
    $w['Tanggal'] = date("Y-m-d");
    $w['SampaiTanggal'] = date('Y-m-d');
    $w['Judul'] = "Dispensasi";
    $w['MhswID'] = '';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Buat Surat Dispensasi";
    $strid = "<input type=text name='dispid' size=30 maxlength=50>";
  }
  $tgl = GetDateOption($w['Tanggal'], "Tanggal");
  $stgl = GetDateOption($w['SampaiTanggal'], "SampaiTanggal");
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dispensasi'>
  <input type=hidden name='gos' value='DispensasiSav'>
  <input type=hidden name='md' value='$md'>

  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>Nomer Surat</td><td class=ul>$strid</td></tr>
  <tr><td class=inp1>Tertanggal</td><td class=ul>$tgl</td></tr>
  <tr><td class=inp1>Berlaku sampai tanggal</td><td class=ul>$stgl</td></tr>
  <tr><td class=inp1>Perihal/Judul</td><td class=ul><input type=text name='Judul' value='$w[Judul]' size=50 maxlength=50></td></tr>
  <tr><td class=inp1>Kepada Mahasiswa/NPM</td><td class=ul><input type=text name='MhswID' value='$w[MhswID]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><textarea name='Keterangan' cols=40 rows=8>$w[Keterangan]</textarea></td></tr>
  <tr><td class=inp1>Tidak aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=dispensasi'\"></td></tr>
  </table></p>";
}
function DispensasiSav() {
  $md = $_REQUEST['md']+0;
  $DispensasiID = sqling($_REQUEST['dispid']);
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $SampaiTanggal = "$_REQUEST[SampaiTanggal_y]-$_REQUEST[SampaiTanggal_m]-$_REQUEST[SampaiTanggal_d]";
  $Judul = sqling($_REQUEST['Judul']);
  $MhswID = $_REQUEST['MhswID'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update dispensasi set Tanggal='$Tanggal', SampaiTanggal='$SampaiTanggal', Judul='$Judul',
      MhswID='$MhswID', Keterangan='$Keterangan', NA='$NA',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where DispensasiID='$DispensasiID' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('dispensasi', "DispensasiID", $DispensasiID, '*');
    if (empty($ada)) {
      $s = "insert into dispensasi
        (DispensasiID, Tanggal, SampaiTanggal, Judul,
        MhswID, Keterangan, NA, LoginBuat, TanggalBuat)
        values ('$DispensasiID', '$Tanggal', '$SampaiTanggal', '$Judul',
        '$MhswID', '$Keterangan', '$NA', '$_SESSION[_Login]', now())";
      $r = _query($s);
    }
    else echo ErrorMsg("Nomer Dispensasi <b>$DispensasiID</b> telah dipakai.<br />
      Gunakan nomer lain.");
  }
  DispDispensasi();
}

// *** Parameters ***
$crdispid = GetSetVar('crdispid');
$gos = (empty($_REQUEST['gos']))? "DispDispensasi" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Dispensasi");
TampilkanPilihanInstitusi('dispensasi');
if (!empty($_SESSION['KodeID'])) {
  TampilkanCariDispensasi();
  $gos();
}

?>
