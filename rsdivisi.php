<?php
// Author: Emanuel Setio Dewo, setio.dewo@gmail.com
// 14/03/2007

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanRSDIV" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Panduan RS - Divisi");
$gos();

function TampilkanRSDIV() {
  // Menu
  echo "<p>
  <a href='?mnux=$_SESSION[mnux]&gos=RSDIVAD&md=1'>Tambah</a>
  </p>";
  // buat array RS
  $arrRSID = array(); $arrRS = array();
  GetArrayRS($arrRSID, $arrRS);
  if (!empty($arrRSID)) {
    echo "<p><table class=box cellspacing=0 cellpadding=4>";
    BuatHeaderRSID($arrRSID, $arrRS);
    BuatDivisiRS($arrRSID);
    echo "</table></p>";
  }
}
function GetArrayRS(&$arrRSID, &$arrRS) {
  $s = "select RSID, Nama
    from rumahsakit
    order by RSID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arrRSID[] = $w['RSID'];
    $arrRS[] = $w['Nama'];
  }
}
function BuatHeaderRSID($arrRSID, $arrRS) {
  echo "<tr><th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>";
  for ($i=0; $i < sizeof($arrRSID); $i++) {
    echo "<th colspan=3 class=ttl title='" . $arrRS[$i]. "'>" . $arrRSID[$i] . "</th>";
  }
}
function BuatDivisiRS($arrRSID) {
  // Ambil data kuliah
  $s = "select rsd.RSDID, rsd.RSID, rsd.MKKode, rsd.Kapasitas, rsd.Durasi,
    mk.Nama, mk.SKS
    from rumahsakitdivisi rsd
      left outer join mk mk on rsd.MKKode=mk.MKKode
    order by rsd.MKKode, rsd.RSID";
  $r = _query($s); $n = 0;
  $arr = array();
  $_mkkode = "a23svlkjadf;lkjnj989p"; // karakter acak
  while ($w = _fetch_array($r)) {
    if ($_mkkode != $w['MKKode']) {
      $n++;
      $_mkkode = $w['MKKode'];
      $arr[$n][-1] = $_mkkode;
      $arr[$n][-2] = $w['Nama'];
      $arr[$n][-3] = $w['SKD'];
    }
    $key = array_search($w['RSID'], $arrRSID);
    if ($key) {
      $arr[$n][$key] = $w['Kapasitas'] . '~' . $w['Durasi'] . '~' . $w['RSDID'];
    }
  }
  // Tampilkan dalam array
  for ($i = 1; $i <= sizeof($arr); $i++) {
    echo "<tr>";
    echo "<td class=ul1>" . $arr[$i][-1] . "</td>";
    echo "<td class=ul1>" . $arr[$i][-2] . "</td>";
    for ($j = 0; $j < sizeof($arrRSID); $j++) {
      if (isset($arr[$i][$j])) {
        $_isi = explode('~', $arr[$i][$j]);
        echo "<td class=ul align=center><a href='?mnux=$_SESSION[mnux]&gos=RSDIVAD&md=0&RSDID=". $_isi[2].
          "'><img src='img/edit.png'></a></td>
          <td class=ul1 align=right>" . $_isi[0] . "</td>".
          "<td class=ul1 align=right>" . $_isi[1] . "</td>";
      }
      else {
        echo "<td class=ul>&nbsp;</td>
          <td class=ul1>&nbsp;</td>
          <td class=ul1>&nbsp;</td>";
      }
    }
    echo "</tr>";
  }
}
function RSDIVAD() {
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $RSDID = $_REQUEST['RSDID']+0;
    $w = GetFields('rumahsakitdivisi', "RSDID", $RSDID, "*");
    $jdl = "Edit";
    $hps = "<input type=button name='Hapus' value='Hapus' onClick=\"location='?mnux=$_SESSION[mnux]&gos=RSDEL&RSDID=$RSDID'\">";
    $ro = "disabled=true";
  }
  else {
    $w = array();
    $jdl = "Tambah";
    $hps = '';
    $ro = '';
  }
  $w['Kapasitas'] += 0;
  $w['Durasi'] += 0;
  //GetaField($_tbl,$_key,$_value,$_result, $_order='', $_group='', $_limit= 'limit 1') {
  $kurid = GetaField('kurikulum', "ProdiID='11' and NA", 'N', 'KurikulumID', 'order by KurikulumKode DESC');
  $optMKKode = GetOption2("mk", "concat(MKKode, ' - ', Nama)", "MKKode", $w['MKKode'], "KurikulumID=$kurid", "MKKode");
  $optRS = GetOption2('rumahsakit', "concat(RSID, ' - ', Nama)", 'RSID', $w['RSID'], '', 'RSID'); 
  echo "<p><table class=box cellspacing=1>
    <form action='?' name='frmRSDID' method=POST>
    <input type=hidden name='RSDID' value='$w[RSDID]'>
    <input type=hidden name='md' value='$md'>
    <input type=hidden name='gos' value='RSDIVSV'>
    <input type=hidden name='BypassMenu' value=1>
    <tr><th class=ttl colspan=2>$jdl</th></tr>
    <tr><td class=inp>Matakuliah</td>
      <td class=ul><select name='MKKode' $ro>$optMKKode</select></td></tr>
    <tr><td class=inp>Rumah Sakit</td>
      <td class=ul><select name='RSID' $ro>$optRS</select></td></tr>
    <tr><td class=inp>Kapasitas</td>
      <td class=ul><input type=text name='Kapasitas' value='$w[Kapasitas]' size=5 maxlength=5></td></tr>
    <tr><td class=inp>Durasi</td>
      <td class=ul><input type=text name='Durasi' value='$w[Durasi]' size=5 maxlength=5> minggu</td></tr>
    <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]'\">
      $hps</td></tr>
    </form></table></p>";
}
function RSDIVSV() {
  $md = $_REQUEST['md']+0;
  $RSDID = $_REQUEST['RSDID']+0;
  $MKKode = $_REQUEST['MKKode'];
  $RSID = $_REQUEST['RSID'];
  $Kapasitas = $_REQUEST['Kapasitas']+0;
  $Durasi = $_REQUEST['Durasi']+0;
  if ($md == 0) {
    $s = "update rumahsakitdivisi
      set Kapasitas=$Kapasitas, Durasi=$Durasi
      where RSDID=$RSDID";
    $r = _query($s);
    echo "<script>window.location = '?mnux=rsdivisi';</script>";
  }
  else {
    $ada = GetaField('rumahsakitdivisi', "RSID='$RSID' and MKKode", $MKKode, "RSDID");
    if ($ada > 0)
      echo ErrorMsg("Sudah Terdaftar",
      "Kuliah <b>$MKKode</b> di rumahsakit <b>$RSID</b> sudah terdaftar.<br />
      Anda tidak bisa mendaftarkan lebih dari 1 kali.
      <hr size=1 color=silver>
      Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
    else {
      $s = "insert into rumahsakitdivisi
        (MKKode, RSID, Kapasitas, Durasi)
        values
        ('$MKKode', '$RSID', '$Kapasitas', '$Durasi')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=rsdivisi';</script>";
    }
  }
}
function RSDEL() {
  $RSDID = $_REQUEST['RSDID']+0;
  $w = GetFields('rumahsakitdivisi', "RSDID", $RSDID, "*");
  echo Konfirmasi("Konfirmasi Penghapusan",
  "Benar Anda akan menghapus data ini?
  <table class=box>
  <tr><td class=inp>Matakuliah</td><td class=ul>$w[MKKode]</td></tr>
  <tr><td class=inp>Rumah Sakit</td><td class=ul>$w[RSID]</td></tr>
  <tr><td class=inp>Kapasitas</td><td class=ul>$w[Kapasitas]</td></tr>
  <tr><td class=inp>Durasi</td><td class=ul>$w[Durasi] minggu</td></tr>
  </table>
  <hr size=1 color=silver>
  Opsi:
  <input type=button name='Batal' value='Batal' onClick=\"location='?$mnux=$_SESSION[mnux]'\"> 
  <input type=button name='Hapus' value='Hapus' onClick=\"location='?mnux=$_SESSION[mnux]&gos=RSDEL1&RSDID=$RSDID&BypassMenu=1'\">");
}
function RSDEL1() {
  $RSDID = $_REQUEST['RSDID']+0;
  $s = "delete from rumahsakitdivisi where RSDID=$RSDID";
  $r = _query($s);
  echo "<script>window.location = '?mnux=$_SESSION[mnux]';</script>";
}
?>
