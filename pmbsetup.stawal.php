<?php
// Author: Emanuel Setio Dewo
// 10 Feb 2006

function DftrStawal() {
  global $mnux, $pref, $CatatanStatusAwal;
  
  $s = "select *
    from statusawal
    order by StatusAwalID";
  $r = _query($s);
  echo "<p><a href='?mnux=$mnux&$pref=Stawal&sub=StawalEdt&md=1'>Tambah Status Awal Mahasiswa</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>ID</th>
  <th class=ttl>Nama</th>
  <th class=ttl>Beli<br />Formulir</th>
  <th class=ttl>Jalur<br />Khusus</th>
  <th class=ttl>Tanpa<br />Test</th>
  <th class=ttl>NA</th>
  </tr>";
  
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
    <td $c><a href='?mnux=pmbsetup&sub=StawalEdt&md=0&stawalid=$w[StatusAwalID]'><img src='img/edit.png'> 
    $w[StatusAwalID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c align=center><img src='img/$w[BeliFormulir].gif'></td>
    <td $c align=center><img src='img/$w[JalurKhusus].gif'></td>
    <td $c align=center><img src='img/$w[TanpaTest].gif'></td>
    <td $c align=center><img src='img/$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
  echo $CatatanStatusAwal;
}
// Edit/Tambah Status Awal
function StawalEdt() {
  global $mnux, $pref, $CatatanStatusAwal;
  
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('statusawal', "StatusAwalID", $_REQUEST['stawalid'], '*');
    $jdl = "Edit Status Awal Mahasiswa";
    $_strstawalid = "<input type=hidden name='stawalid' value='$w[StatusAwalID]'><b>$w[StatusAwalID]</b>";
  }
  else {
    $w = array();
    $w['StatusAwalID'] = '';
    $w['Nama'] = '';
    $w['BeliFormulir'] = 'Y';
    $w['JalusKhusus'] = 'N';
    $w['TanpaTest'] = 'N';
    $w['Catatan'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Status Awal Mahasiswa";
    $_strstawalid = "<input type=text name='stawalid' size=5 maxlength=5>";
  }
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $BeliFormulir = ($w['BeliFormulir'] == 'Y')? 'checked' : '';
  $JalurKhusus = ($w['JalusKhusus'] == 'Y')? 'checked' : '';
  $TanpaTest = ($w['TanpaTest'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  // Tampilkan formulir
  CheckFormScript("stawalid,Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='Stawal'>
  <input type=hidden name='sub' value='StawalSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>ID</td><td class=ul>$_strstawalid</td></tr>
  <tr><td class=inp1>Nama Status Awal</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Harus Beli Formulir?</td><td class=ul><input type=checkbox name='BeliFormulir' value='Y' $BeliFormulir> Jika harus beli, maka akan dilakukan pemeriksaan terhadap Bukti Setoran pembelian formulir.</td></tr>
  <tr><td class=inp1>Jalur Khusus?</td><td class=ul><input type=checkbox name='JalurKhusus' value='Y' $JalurKhusus></td></tr>
  <tr><td class=inp1>Tanpa Test (USM)?</td><td class=ul><input type=checkbox name='TanpaTest' value='Y' $TanpaTest></td></tr>
  <tr><td class=inp1>Tidak Aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $NA></td></tr>
  <tr><td class=inp1>Catatan</td><td class=ul><input type=text name='Catatan' value='$w[Catatan]' size=50 maxlength=100></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=Stawal&$snm=$sid'\"></td></tr>
  </form></table></p>";
  echo $CatatanStatusAwal;
}
function StawalSav() {
  $md = $_REQUEST['md']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $BeliFormulir = (empty($_REQUEST['BeliFormulir']))? 'N' : $_REQUEST['BeliFormulir'];
  $JalurKhusus = (empty($_REQUEST['JalurKhusus']))? 'N' : $_REQUEST['JalurKhusus'];
  $TanpaTest = (empty($_REQUEST['TanpaTest']))? 'N' : $_REQUEST['TanpaTest'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $Catatan = sqling($_REQUEST['Catatan']);
  // Simpan
  if ($md == 0) {
    $s = "update statusawal set Nama='$Nama', BeliFormulir='$BeliFormulir',
      JalurKhusus='$JalurKhusus', TanpaTest='$TanpaTest',
      NA='$NA', Catatan='$Catatan'
      where StatusAwalID='$_REQUEST[stawalid]' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('statusawal', 'StatusAwalID', $_REQUEST['stawalid'], '*');
    if (empty($ada)) {
      $s = "insert into statusawal (StatusAwalID, Nama, BeliFormulir, JalurKhusus, TanpaTest, NA, Catatan)
        values('$_REQUEST[stawalid]', '$Nama', '$BeliFormulir', '$JalurKhusus', '$TanpaTest', '$NA', '$Catatan')";
      $r = _query($s);
    }
    else echo ErrorMsg("Gagal Simpan",
      "<p>ID Status Awal Mahasiswa: <b>$_REQUEST[stawalid]</b> telah ada. Berikut adalah datanya:</p>
      <p><table class=box cellspacing=1 cellpadding=4>
      <tr><td class=ul>ID</td><td class=ul>: $ada[StatusAwalID]</td></tr>
      <tr><td class=ul>Nama</td><td class=ul>: $ada[Nama]</td></tr>
      <tr><td class=ul>Jalur Khusus?</td><td class=ul>: <img src='img/$ada[JalurKhusus].gif'></td></tr>
      <tr><td class=ul>Tanpa Test?</td><td class=ul>: <img src='img/$ada[TanpaTest].gif'></td></tr>
      <tr><td class=ul>Tidak Aktif (NA)?</td><td class=ul>: <img src='img/$ada[NA].gif'></td></tr>
      <tr><td class=ul>Catatan</td><td class=ul>: $ada[Catatan]</td></tr>
      </table></p>");
  }
  DftrStawal();
}
?>
