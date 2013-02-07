<?php
// Author: Emanuel Setio Dewo
// 31 Agustus 2006
// www.sisfokampus.net

// *** Functions ***
function mhswPrestasi($m) {
  echo "<p><a href='?mnux=mhsw.prestasi.det&gos=PresEdt&md=1&pres=1'>Tambah Catatan Prestasi Mhsw</a></p>";
  DaftarPrestasi($m, 1);
}
function mhswWan($m) {
  echo "<p><a href='?mnux=mhsw.prestasi.det&gos=PresEdt&md=1&pres=-1'>Tambah Catatan Wanprestasi/Sangsi Mhsw</a></p>";
  DaftarPrestasi($m, -1);
}
function DaftarPrestasi($m, $pres=1) {
  $s = "select *
    from prestasi
    where MhswID='$m[MhswID]' and JenisPrestasi=$pres
    order by Tanggal desc";
  $r = _query($s); $n = 0;
  $hdrSkorsing = ($pres == -1)? "<th class=ttl>Skors?</th>" : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl colspan=2>#</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Judul</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>Hapus</th>
    $hdrSkorsing
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $tgl = FormatTanggal($w['Tanggal']);
    if ($pres == -1) {
      $strSkorsing = "<td class=ul><a href='?mnux=skorsing.det&mhswid=$m[MhswID]'>&raquo; Skorsing</a></td>";
    }
    else {
      $strSkorsing = '';
    }
    echo "<tr><td class=inp>$n</td>
      <td class=ul align=center><a href='?mnux=mhsw.prestasi.det&gos=PresEdt&md=0&presid=$w[PrestasiID]'><img src='img/edit.png'></a></td>
      <td $c>$tgl</td>
      <td $c>$w[Judul]</td>
      <td $c>$w[Keterangan]</td>
      <td class=ul align=right><a href='?mnux=$_SESSION[mnux]&gos=PresDel&presid=$w[PrestasiID]'><img src='img/del.gif'></a></td>
      $strSkorsing
      <tr>";
  }
  echo "</table></p>";
}
function PresEdt($m) {
  $arrJudul = array(-1=>"Wanprestasi", 1=>"Prestasi");
  $pres = $_REQUEST['pres']+0;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('prestasi', "PrestasiID", $_REQUEST['presid'], '*');
    $jdl = "Edit " . $arrJudul[$pres];
  }
  else {
    $w = array();
    $w['PrestasiID'] = 0;
    $w['Tanggal'] = date('Y-m-d');
    $w['JenisPrestasi'] = $pres;
    $w['Judul'] = '';
    $w['Keterangan'] = '';
    $jdl = "Tambah " . $arrJudul[$pres];
  }
  $opttgl = GetDateOption($w['Tanggal'], 'Tanggal');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhsw.prestasi.det'>
  <input type=hidden name='gos' value='PresSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='mhswid' value='$m[MhswID]'>
  <input type=hidden name='presid' value='$w[PrestasiID]'>
  <input type=hidden name='JenisPrestasi' value='$w[JenisPrestasi]'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Tanggal</td>
    <td class=ul>$opttgl</td></tr>
  <tr><td class=inp>Judul :</td>
    <td class=ul><input type=text name='Judul' value='$w[Judul]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Keterangan :</td>
    <td class=ul><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhsw.prestasi.det'\"></td></tr>
  </form></table></p>";
}
function PresSav($m) {
  $md = $_REQUEST['md']+0;
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $JenisPrestasi = $_REQUEST['JenisPrestasi']+0;
  $PrestasiID = $_REQUEST['presid'];
  $Judul = sqling($_REQUEST['Judul']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  if ($md == 0) {
    $s = "update prestasi set Tanggal='$Tanggal', Judul='$Judul', Keterangan='$Keterangan',
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where PrestasiID='$PrestasiID' ";
    $r = _query($s);
  }
  else {
    $s = "insert into prestasi (Tanggal, JenisPrestasi, MhswID, Judul, Keterangan,
      LoginBuat, TanggalBuat)
      values ('$Tanggal', '$JenisPrestasi', '$m[MhswID]', '$Judul', '$Keterangan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  $_SESSION['mhswPrestasi']($m);
}
function PresDel($m) {
  $arrJenisPrestasi = array(-1=>"Wanprestasi", 0=>"-", 1=>"Prestasi");
  $presid = $_REQUEST['presid'];
  $pres = GetFields("prestasi", "PrestasiID", $presid, "*");
  $jenis = $arrJenisPrestasi[$pres['JenisPrestasi']];
  echo Konfirmasi("Konfirmasi Penghapusan",
    "Benar Anda akan menghapus <font size=+1>$jenis</font> berikut ini?
    <p><table class=box cellspacing=1>
      <tr><td class=inp>Judul</td><td class=ul>$pres[Judul]</td></tr>
      <tr><td class=inp>Keterangan</td><td class=ul>$pres[Keterangan]</td></tr>
      <tr><td class=ul colspan=2>
        <input type=button name='Hapus' value='Hapus' onClick=\"location='?mnux=$_SESSION[mnux]&gos=PresDel1&BypassMenu=1&presid=$presid'\">
        <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]'\">
        </td> 
    </table></p>");
}
function PresDel1($m) {
  $presid = $_REQUEST['presid'];
  $s = "delete from prestasi where PrestasiID='$presid' ";
  $r = _query($s);
  // kembalikan
  echo "<script>window.location='?mnux=$_SESSION[mnux]';</script>";
}

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$mhswPrestasi = GetSetVar('mhswPrestasi', 'mhswPrestasi');
//if (empty($mhswPrestasi)) $mhswPrestasi = 'mhswPrestasi';
$gos = (empty($_REQUEST['gos']))? "" : $_REQUEST['gos'];

$arrPrestasi = array("Prestasi Mhsw->mhswPrestasi",
  "Sangsi/Wanprestasi Mhsw->mhswWan");

// *** Main ***
TampilkanJudul("Catatan Prestasi dan Wanprestasi Mhsw");
if (!empty($mhswid)) {
  $m = GetFields("mhsw m 
    left outer join program prg on m.ProgramID=m.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID", 
    'MhswID', $mhswid, "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT");
  if (!empty($m)) {
    include_once "mhsw.hdr.php";
    TampilkanHeaderBesar($m, 'mhsw.prestasi', '', 1);
    TampilkanSubMenu('mhsw.prestasi.det', $arrPrestasi, 'mhswPrestasi', $mhswPrestasi);
    if (empty($gos)) {
      if (!empty($mhswPrestasi)) $mhswPrestasi($m);
    }
    else $gos($m);
  }
}
?>
