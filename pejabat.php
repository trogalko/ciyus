<?php
// Author: Emanuel Setio Dewo
// 27 Juli 2006
// www.sisfokampus.net

// *** Functions ***
function DaftarPejabat() {
  global $KodeID;
  $s = "select *
    from pejabat where KodeID='$KodeID'
    order by Ranking";
  $r = _query($s);
  echo "<p><a href='?mnux=pejabat&gos=PejabatEdt&md=1'>Tambahkan Pejabat</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl colspan=2>#</th>
      <th class=ttl>Jabatan</th>
      <th class=ttl>Pejabat</th>
      <th class=ttl>NA</th>
      </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp>$w[Ranking]</td>
      <td class=ul><a href='?mnux=pejabat&gos=PejabatEdt&jid=$w[JabatanID]'><img src='img/edit.png'></a></td>
      <td $c>$w[JabatanID]</td>
      <td $c>$w[Nama]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function PejabatEdt() {
  global $KodeID;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('pejabat', 'JabatanID', $_REQUEST['jid'], '*');
    $jbt = "<input type=hidden name='JabatanID' value='$w[JabatanID]'><b>$w[JabatanID]</b>";
    $jdl = "Edit Pejabat";
  }
  else {
    $w = array();
    $w['JabatanID'] = '';
    $w['Ranking'] = GetaField('pejabat', 'KodeID', $KodeID, "max(Ranking)+1");
    $w['Nama'] = '';
    $w['NA'] = 'N';
    $jbt = "<input type=text name='JabatanID' value='$w[JabatanID]' size=30 maxlength=50>";
    $jdl = "Tambah Pejabat";
  }
  $optfak = GetOption2('fakultas', "Concat(FakultasID, ' - ', Nama)", "FakultasID", $w['FakultasID'], '', 'FakultasID');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("JabatanID,Ranking,Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='pejabat'>
  <input type=hidden name='gos' value='PejabatSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Ranking/Urutan</td>
    <td class=ul><input type=text name='Ranking' value='$w[Ranking]' size=4 maxlength=4></td></tr>
  <tr><td class=inp>Jabatan</td>
    <td class=ul>$jbt</td></tr>
  <tr><td class=inp>Pejabat</td>
    <td class=ul><input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Menjabat di Fakultas</td>
    <td class=ul><select name='FakultasID'>$optfak</select></td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
    <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pejabat'\"></td></tr>
  </form></table></p>";
}
function PejabatSav() {
  global $DefaultGOS, $KodeID;
  $md = $_REQUEST['md']+0;
  $JabatanID = strtoupper(sqling($_REQUEST['JabatanID']));
  $Ranking = $_REQUEST['Ranking']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $FakultasID = $_REQUEST['FakultasID'];
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update pejabat set Ranking=$Ranking, Nama='$Nama', FakultasID='$FakultasID', NA='$NA'
      where JabatanID='$JabatanID' ";
    $r = _query($s);
    $DefaultGOS();
  }
  else {
    $ada = GetFields('pejabat', "KodeID='$KodeID' and JabatanID", $JabatanID, '*');
    if (empty($ada)) {
      $s = "insert into pejabat (JabatanID, KodeID, Ranking, Nama, FakultasID, NA)
        values ('$JabatanID', '$KodeID', $Ranking, '$Nama', '$FakultasID', '$NA')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=pejabat'; </script>";
    }
    else {
      echo ErrorMsg("Gagal Simpan",
      "Data pejabat <b>$JabatanID</b> sudah ada.<br />
      Anda tidak dapat memasukkan jabatan ini lebih dari 1 kali.");
      $DefaultGOS();
    }
  }
}

// *** Parameters ***
$DefaultGOS = "DaftarPejabat";
$gos = (empty($_REQUEST['gos']))? $DefaultGOS : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Pejabat $arrID[Nama]");
$gos();
?>
