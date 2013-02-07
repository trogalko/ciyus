<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-27

// *** Functions ***
function DftrRuang() {
  $opt = GetOption2('kampus', "concat(KampusID, ' - ', Nama)", 'KampusID', $_SESSION['kampusid'], '', 'KampusID');
  $colspan = 9;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><form action='?' method=POST>
    <input type=hidden name='mnux' value='ruang'>
    <td colspan=$colspan class=ul>Kampus : <select name='kampusid' onChange='this.form.submit()'>$opt</select></td>
    </form></tr>";
  echo "<tr><td class=ul colspan=$colspan><a href='?mnux=ruang&gos=RuangEdt&md=1'>Tambah Ruang</a> |
    <a href='?mnux=kampus'>Kampus</a> |
    <a href='ruang.cetak.php?KampusID=$_SESSION[kampusid]'>Cetak</a></td></tr>";
  echo "<tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Prodi</th>
    <th class=ttl>Ruang<br />Kelas?</th>
    <th class=ttl>Kapasitas</th>
    <th class=ttl>Untuk<br />USM?</th>
    <th class=ttl>Keterangan</th>
    <th class=ttl>NA</th>
    </tr>";
  $s = "select * from ruang
    where KampusID='$_SESSION[kampusid]'
    order by RuangID";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    $ket = str_replace(chr(13), ', ', $w['Keterangan']);
    echo "<tr><td $c>$n</td>
      <td $c><a href='?mnux=ruang&gos=RuangEdt&md=0&ruangid=$w[RuangID]'><img src='img/edit.png' border=0>
      $w[RuangID]</a></td>
      <td $c>$w[Nama]</td>
      <td $c>$w[ProdiID]&nbsp;</td>
      <td $c align=center><img src='img/$w[RuangKuliah].gif'></td>
      <td $c>$w[KapasitasUjian] - $w[Kapasitas]</td>
      <td $c align=center><img src='img/$w[UntukUSM].gif'></td>
      <td $c>$ket&nbsp;</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function RuangEdt() {
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('ruang', 'RuangID', $_REQUEST['ruangid'], '*');
    $jdl = "Edit Ruang";
    $strid = "<input type=hidden name='RuangID' value='$w[RuangID]'><b>$w[RuangID]</b>";
  }
  else {
    $w = array();
    $w['RuangID'] = '';
    $w['Nama'] = '';
    $w['ProdiID'] = '';
    $w['KampusID'] = $_SESSION['kampusid'];
    $w['Lantai'] = 1;
    $w['RuangKuliah'] = 'Y';
    $w['Kapasitas'] = 0;
    $w['KapasitasUjian'] = 0;
    $w['KolomUjian'] = 2;
    $w['UntukUSM'] = 'N';
    $w['Keterangan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Ruang";
    $strid = "<input type=text name='RuangID' value='' size=40 maxlength=50>";
  }
  $_ruangkuliah = ($w['RuangKuliah'] == 'Y')? 'checked' : '';
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $_usm = ($w['UntukUSM'] == 'Y')? 'checked' : '';
  $_optkampus = GetOption2('kampus', "concat(KampusID, ' - ', Nama)", 'KampusID', $w['KampusID'], '', 'KampusID');
  $optprodi= GetCheckboxes("prodi", "ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", "NM", $w['ProdiID'], '.');
  CheckFormScript("RuangID,Nama,KampusID,Lantai");
  // Tampilkan
  $c1 = 'class=inp1'; $c2 = 'class=ul';
  $snm = session_name(); $sid = session_id();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='ruang'>
  <input type=hidden name='gos' value='RuangSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td $c1>Kode Ruang</td><td $c2>$strid</td></tr>
  <tr><td $c1>Nama</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Untuk Prodi</td><td $c2>$optprodi</td></tr>
  <tr><td $c1>Kampus</td><td $c2><select name='KampusID'>$_optkampus</select></td></tr>
  <tr><td $c1>Lantai</td><td $c2><input type=text name='Lantai' value='$w[Lantai]' size=5 maxlength=5></td></tr>
  <tr><td $c1>Untuk kuliah?</td><td $c2><input type=checkbox name='RuangKuliah' value='Y' $_ruangkuliah></td></tr>
  <tr><td $c1>Kapasitas</td><td $c2><input type=text name='Kapasitas' value='$w[Kapasitas]' size=5 maxlength=5></td></tr>
  <tr><td $c1>Kapasitas Ujian</td><td $c2><input type=text name='KapasitasUjian' value='$w[KapasitasUjian]' size=5 maxlength=5></td></tr>
  <tr><td $c1>Jumlah Kolom Ujian</td><td $c2><input type=text name='KolomUjian' value='$w[KolomUjian]' size=4 maxlength=3</td></tr>
  <tr><td $c1>Untuk Ujian Saringan Masuk (USM)?</td><td $c2><input type=checkbox name='UntukUSM' value='Y' $_usm></td></tr>
  <tr><td $c1>Keterangan</td><td $c2><textarea name='Keterangan' cols=30 rows=4>$w[Keterangan]</textarea></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=ruang&$snm=$sid'\"></td></tr>
  </form></table></p>";
}
function RuangSav() {
  $md = $_REQUEST['md']+0;
  $RuangID = $_REQUEST['RuangID'];
  $Nama = sqling($_REQUEST['Nama']);
  $KampusID = $_REQUEST['KampusID'];
  $Lantai = $_REQUEST['Lantai']+0;
  $RuangKuliah = (empty($_REQUEST['RuangKuliah']))? 'N' : $_REQUEST['RuangKuliah'];
  $Kapasitas = $_REQUEST['Kapasitas']+0;
  $KapasitasUjian = $_REQUEST['KapasitasUjian']+0;
  $KolomUjian = $_REQUEST['KolomUjian']+0;
  $UntukUSM = (empty($_REQUEST['UntukUSM']))? 'N' : $_REQUEST['UntukUSM'];
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $prodi = $_REQUEST['ProdiID'];
  $ProdiID = (empty($prodi))? '' : '.'.implode('.', $prodi).'.';
  if ($md == 0) {
    $s = "update ruang set Nama='$Nama', ProdiID='$ProdiID',
      KampusID='$KampusID', Lantai='$Lantai',
      RuangKuliah='$RuangKuliah', Kapasitas='$Kapasitas', KapasitasUjian='$KapasitasUjian', KolomUjian='$KolomUjian',
      UntukUSM='$UntukUSM', Keterangan='$Keterangan', NA='$NA' where RuangID='$RuangID' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('ruang', 'RuangID', $_REQUEST['RuangID'], '*');
    if (empty($ada)) {
      $s = "insert into ruang(RuangID, Nama, ProdiID, KampusID, Lantai, RuangKuliah,
        Kapasitas, KapasitasUjian, KolomUjian, UntukUSM, Keterangan, NA)
        values('$RuangID', '$Nama', '$ProdiID', '$KampusID', '$Lantai', '$RuangKuliah',
        '$Kapasitas', '$KapasitasUjian', '$KolomUjian', '$UntukUSM', '$Keterangan', '$NA')";
      $r = _query($s);
    }
    else echo ErrorMsg('Terjadi Kesalahan',
      "Kode ruang telah digunakan: <b>$ada[RuangID] - $ada[Nama]</b> di gedung: $ada[KampusID].<br>
      Gunakan kode ruang lain.");
  }
  DftrRuang();
}

// *** Parameters ***
$kampusid = GetSetVar('kampusid');
$gos = (empty($_REQUEST['gos']))? 'DftrRuang' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Ruang");
$gos();
?>
