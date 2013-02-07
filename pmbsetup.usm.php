<?php
// Author: Emanuel Setio Dewo
// 2005-12-19

function DefUSM() {
  global $pref, $mnux;
  $s = "select * from pmbusm order by PMBUSMID";
  $r = _query($s);
  $n = 0;
  echo "<table class=box cellspacing=1 cellpadding=4
    <tr><td colspan=3 class=ul><a href='?mnux=$mnux&$pref=Usm&sub=UsmEdt&md=1'>Tambah Komponen USM</a></td></tr>
    <tr><th class=ttl>#</th>
    <th class=ttl>Nama</th>
    <th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c>$w[PMBUSMID]</td>
      <td $c><a href='?mnux=$mnux&$pref=Usm&sub=UsmEdt&md=0&pmbusmid=$w[PMBUSMID]'><img src='img/edit.png' border=0>
      $w[Nama]</a></td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table>";
}
function UsmEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('pmbusm', 'PMBUSMID', $_REQUEST['pmbusmid'], '*');
    $jdl = "Edit Komponen USM";
    $strpmbusmid = "<input type=hidden name='PMBUSMID' value='$w[PMBUSMID]'><b>$w[PMBUSMID]";
  }
  else {
    $w = array();
    $w['PMBUSMID'] = '';
    $w['Nama'] = '';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Komponen USM";
    $strpmbusmid = "<input type=text name='PMBUSMID' size=20 maxlength=10>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  $c1 = 'class=inp1'; $c2 = 'class=ul';
  CheckFormScript("PMBUSMID,Nama");
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='Usm'>
  <input type=hidden name='sub' value='UsmSav'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='PMBUSMID' value='$w[PMBUSMID]'>
  <tr><th colspan=2 class=ttl>$jdl</th></tr>
  <tr><td $c1>Kode Test</td><td $c2>$strpmbusmid</td></tr>
  <tr><td $c1>Nama</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Keterangan</td><td $c2><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=Usm'\"></td></tr>
  
  </form></table>";
}
function UsmSav() {
  $md = $_REQUEST['md']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update pmbusm set Nama='$Nama', Keterangan='$Keterangan', NA='$NA'
      where PMBUSMID='$_REQUEST[PMBUSMID]' ";
    $r = _query($s);
  }
  else {
    $PMBUSMID = $_REQUEST['PMBUSMID'];
    $ada = GetFields('pmbusm', 'PMBUSMID', $PMBUSMID, '*');
    if (!empty($ada)) echo ErrorMsg("Gagal Simpan", "Kode test <b>$PMBUSMID</b> telah digunakan oleh test:<br>
      <b>$ada[Nama]</b>. Gunakan kode test lain.");
    else {
      $s = "insert into pmbusm(PMBUSMID, Nama, Keterangan, NA)
      values('$_REQUEST[PMBUSMID]', '$Nama', '$Keterangan', '$NA')";
      $r = _query($s);
    }
  }
  DefUSM();
}
?>
