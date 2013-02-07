<?php
// Author: Emanuel Setio Dewo
// 2006-01-02

function DftrJenSek() {
  global $mnux, $pref;
  $s = "select * from jenissekolah order by JenisSekolahID";
  $r = _query($s);
  echo "<table class=box cellspacing=1 cellpadding=4>
    <tr><td colspan=4 class=ul><a href='?mnux=$mnux&$pref=JenSek&sub=JenSekEdt&md=1'>Tambah Jenis Sekolah</a></td></tr>
    <tr><th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Group</th>
    <th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c nowrap>
    <a href='?mnux=$mnux&$pref=JenSek&sub=JenSekEdt&md=0&jensekid=$w[JenisSekolahID]'><img src='img/edit.png' border=0>
    $w[JenisSekolahID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c align=center><img src='img/$w[SatuGroup].gif'></td>
    <td $c align=center><img src='img/$w[NA].gif'></td>
    </tr>";
  }
  echo "</table>";
}
function JenSekEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('jenissekolah', "JenisSekolahID", $_REQUEST['jensekid'], '*');
    $jdl = "Edit Jenis Sekolah";
    $strid = "<input type=hidden name='JenisSekolahID' value='$w[JenisSekolahID]'><b>$w[JenisSekolahID]</b>";
  }
  else {
    $w = array();
    $w['JenisSekolahID'] = '';
    $w['Nama'] = '';
    $w['TemplateSuratPMB'] = '';
    $w['SatuGroup'] = 'N';
    $w['NA'] = 'N';
    $jdl = "Tambah Jenis Sekolah";
    $strid = "<input type=text name='JenisSekolahID' value='' size=30 maxlength=20>";
  }
  $_na = ($w['NA'] == 'Y')? 'cheched' : '';
  $_gr = ($w['SatuGroup'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $c1 = 'class=inp1'; $c2 = 'class=ul';
  // Tampilkan
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='JenSek'>
  <input type=hidden name='sub' value='JenSekSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td $c1>Kode Jenis Sekolah</td><td $c2>$strid</td></tr>
  <tr><td $c1>Nama</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Group Penabur</td><td $c2><input type=checkbox name='SatuGroup' value='Y' $_gr></td></tr>
  <tr><td $c1>Template Surat Pemberitahuan</td><td $c2><input type=text name='TemplateSuratPMB' value='$w[TemplateSuratPMB]' size=50 maxlength=100></td></tr>
  <tr><td $c1>NA (Tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=JenSek'\"></td></tr>
  </form></table>";
}
function JenSekSav() {
  $md = $_REQUEST['md'] +0;
  $JenisSekolahID = $_REQUEST['JenisSekolahID'];
  $Nama = sqling($_REQUEST['Nama']);
  $SatuGroup = (empty($_REQUEST['SatuGroup']))? 'N' : $_REQUEST['SatuGroup'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $TemplateSuratPMB = $_REQUEST['TemplateSuratPMB'];
  // Simpan
  if ($md == 0) {
    $s = "update jenissekolah set Nama='$Nama', SatuGroup='$SatuGroup',
      NA='$NA', TemplateSuratPMB='$TemplateSuratPMB'
      where JenisSekolahID='$JenisSekolahID'";
    $r = _query($s);
  }
  else {
    $ada = GetFields("jenissekolah", 'JenisSekolahID', $JenisSekolahID, '*');
    if (empty($ada)) {
      $s = "Insert into jenissekolah (JenisSekolahID, Nama,
        SatuGroup, NA, TemplateSuratPMB)
        values('$JenisSekolahID', '$Nama', '$SatuGroup', '$NA', '$TemplateSuratPMB')";
      $r = _query($s);
    }
    else echo ErrorMsg("Gagal Disimpan", "Kode Jenis Sekolah: <b>$JenisSekolahID</b>
      telah digunakan oleh: <b>$ada[Nama]</b>.<br>
      Gunakan Kode Jenis Sekolah yang lain.");
  }
  DftrJenSek();
}
?>