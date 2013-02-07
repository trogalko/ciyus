<?php
// Author: Emanuel Setio Dewo
// 12 May 2006
// www.sisfokampus.net

// *** Functions ***
function DefMKSet() {
  global $mnux, $pref, $token;
  TampilkanPilihanKurikulum();
  //TampilkanPilihanProdi($mnux, '', $pref, "MK");
  if (!empty($_SESSION['prodi'])) {
    TampilkanMenuMKSetara();
    TampilkanMK();
  }
}
function TampilkanMenuMKSetara() {
  global $mnux, $pref, $token;
  echo "<p><a href='?mnux=$mnux&$pref=$token&sub=CetakMKSetara'>Cetak</a></p>";
}
function TampilkanMK() {
  if (!empty($_SESSION['kurid_'.$_SESSION['prodi']])) TampilkanMK1();
}
function TampilkanMK1() {
  global $mnux, $pref, $arrID;
  $arrKurid = GetFields('kurikulum', "KurikulumID", $_SESSION['kurid_'.$_SESSION['prodi']], '*');
  $s = "select mk.*
    from mk mk
    where mk.KurikulumID='$arrKurid[KurikulumID]'
    order by mk.MKKode";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Nama</th>
    <th class=ttl>SKS</th>
    <th class=ttl colspan=2>Setara</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $MKSetara = TRIM($w['MKSetara'], '.');
    $MKSetara = str_replace('.', ', ', $MKSetara);
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[SKS]</td>
    <td class=ul><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&mkkode_$_SESSION[prodi]=$w[MKKode]&sub=EdtSet'><img src='img/edit.png'></a></td>
    <td class=ul>$MKSetara&nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
}
function EdtSet() {
  global $mnux, $pref, $arrID;
  $prodi = $_SESSION['prodi'];
  $mkkode = $_SESSION['mkkode_'.$prodi];
  $kurid = $_SESSION['kurid_'.$prodi];
  $mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $mkkode, '*');
  $optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama)", 'MKKode', '', 
    "MKKode<>'$mkkode' and KurikulumID=".$_SESSION['kurid_'.$_SESSION['prodi']], 'MKKode'); 
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Kode</td><td class=ul><b>$mk[MKKode]</td></tr>
  <tr><td class=inp>Nama</td><td class=ul><b>$mk[Nama]</td></tr>
  <tr><td class=inp>SKS</td><td class=ul><b>$mk[SKS]</td></tr>
  <tr><td class=inp>Pilihan</td>
    <td class=ul><input type=submit name='Kembali' value='Kembali' onClick=\"location='?mnux=$mnux&$pref=$_SESSION[$pref]'\"></td></tr>
  </table></p>";
  $setara = GetMKSetara($mk['MKSetara'], $mkkode);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=5><b>Matakuliah Setara</td></tr>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='mkkode_$prodi' value='$mkkode'>
  <input type=hidden name='$pref' value='$_SESSION[$pref]'>
  <input type=hidden name='sub' value='EdtSetSav'>
  <tr><td class=inp1 colspan=2>Tambahkan:</td>
    <td class=ul colspan=3><select name='mkkode_add'>$optmk</select>
  <input type=submit name='Tambahkan' value='Tambahkan'></td></tr>
  </form>
  $setara
  </table></p>"; 
}
function EdtSetSav() {
  $prodi = $_SESSION['prodi'];
  $mkkode = $_REQUEST['mkkode_'.$prodi];
  $kurid = $_SESSION['kurid_'.$prodi];
  $add = $_REQUEST['mkkode_add'];
  $mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $mkkode, '*');
  $_setara = TRIM($mk['MKSetara'], '.');
  // Simpan
  $arrSet = array();
  if (!empty($_setara)) {
    $arrSet = explode('.', $_setara);
  }
  $key = array_search($add, $arrSet);
  if ($key === false) {
    $arrSet[] = $add;
    $arrSet = array_unique($arrSet);
    sort($arrSet);
    $_setara = '.'.implode('.', $arrSet).'.';
    $s = "update mk set MKSetara='$_setara' where MKID='$mk[MKID]' ";
    $r = _query($s);
  }
  // Tambahkan pula di matakuliah setaranya
  $mk1 = GetFields('mk', "KurikulumID='$kurid' and MKKode", $add, '*');
  $_setara1 = TRIM($mk1['MKSetara'], '.');
  $arrSet1 = array();
  if (!empty($_setara1)) {
    $arrSet1 = explode('.', $_setara1);
  }
  $key1 = array_search($mkkode, $arrSet);
  if ($key === false) {
    $arrSet1[] = $mkkode;
    $arrSet1 = array_unique($arrSet1);
    sort($arrSet1);
    $_setara1 = '.'.implode('.', $arrSet1).'.';
    $s1 = "update mk set MKSetara='$_setara1' where MKID='$mk1[MKID]' ";
    $r1 = _query($s1);
  }
  EdtSet(); 
}
function GetMKSetara($stara, $mkkode) {
  global $mnux, $pref, $token;
  $stara = TRIM($stara, '.');
  $arrset = explode('.', $stara);
  $a = '';
  for ($i = 0; $i < sizeof($arrset); $i++) {
    $kd = $arrset[$i];
    if (!empty($kd)) {
      $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
      $mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $kd, "Nama, SKS");
      $n = $i+1;
      $a .= "<tr><td class=inp>$n</td>
      <td class=ul>$kd</td>
      <td class=ul>$mk[Nama]</td>
      <td class=ul>$mk[SKS]</td>
      <td class=ul align=center><a href='?mnux=$mnux&$pref=$_SESSION[$pref]&sub=EdtSetDel&mkkode_$_SESSION[prodi]=$mkkode&del=$kd'><img src='img/del.gif'></a></td>
      </tr>";
    }
  }
  return $a;
}
function EdtSetDel() {
  $mkkode = $_SESSION['mkkode_'.$_SESSION['prodi']];
  $del = $_REQUEST['del'];
  $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
  $mk = GetFields('mk', "KurikulumID='$kurid' and MKKode", $mkkode, '*');
  $setara = $mk['MKSetara'];
  $setara = str_replace($del.'.', '', $setara);
  $s = "update mk set MKSetara='$setara' where MKID='$mk[MKID]' ";
  $r = _query($s);
  echo $s;
  EdtSet();  
}
function CetakMKSetara() {
  $kurid = $_SESSION['kurid_'.$_SESSION['prodi']];
  $kur = GetFields('kurikulum', 'KurikulumID', $kurid, '*');
  if (!empty($kur)) {
    CetakMKSetara1($kurid, $kur); 
  }
  else echo ErrorMsg("Kurikulum Belum Diset",
  "Tidak dapat mencetak karena kurikulum belum ditentukan atau tidak ditemukan.");
}
function CetakMKSetara1($kurid, $kur) {
  global $pref, $_lf;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  $mxc = 80;
  $mxb = 50;
  $grs = str_pad('-', $mxc, '-').$_lf;
  
  $prd = GetaField('prodi', 'ProdiID', $kur['ProdiID'], 'Nama');
  $hdr = str_pad("Daftar Matakuliah Setara", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Program Studi: $prd ($kur[ProdiID])", $mxc, ' ', STR_PAD_BOTH).$_lf.
    str_pad("Kurikulum: $kur[Nama]", $mxc, ' ', STR_PAD_BOTH).$_lf.
    $grs.
    "No.  " . str_pad('Kode', 10). str_pad('Nama Matakuliah', 40).
    str_pad('SKS', 4) . "Matakuliah yg Setara" . $_lf.$grs;
  // tuliskan
  fwrite($f, $hdr);
  $s = "select MKKode, LEFT(Nama, 39) as Nama, SKS, Sesi, MKSetara
    from mk
    where KurikulumID=$kurid and NA='N'
    order by Sesi, MKKode";
  $r = _query($s); $n = 0; $brs = 0;
  while ($w = _fetch_array($r)) {
    if ($brs >= $mxb) {
      $brs = 0;
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $brs++;
    $n++;
    $setara = TRIM($w['MKSetara'], '.');
    $setara = str_replace('.', ',', $setara);
    fwrite($f, str_pad($n, 5).
      str_pad($w['MKKode'], 10).
      str_pad($w['Nama'], 40).
      str_pad($w['SKS'], 4).
      str_pad($setara, 30).
      $_lf);
  }
  fwrite($f, $grs);
  fclose($f);
  TampilkanFileDWOPRN($nmf, "matakuliah");
}
?>
