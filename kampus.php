<?php
// Author: Emanuel Setio Dewo, setio_dewo@telkom.net
// 2005-12-26

// *** Functions ***
function DftrKampus() {
  global $_Identitas;
  $s = "select * 
    from kampus where KodeID='$_SESSION[KodeID]'
    order by KampusID";
  $r = _query($s);
  $cs = 4;
  $optkd = GetOption2('identitas', "concat(Kode, ' - ', Nama)", 'Kode', $_SESSION['KodeID'], '', 'Kode');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='kampus'>
    <tr><td colspan=$cs class=ul>Institusi : <select name='KodeID' onChange=\"this.form.submit()\">$optkd</select></td></tr>
    </form>
    <tr><td colspan=$cs class=ul><a href='?mnux=kampus&gos=KampEdt&md=1'>Tambah Kampus</a></td></tr>
    <tr><th class=ttl>ID</th>
    <th class=ttl>Nama</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c><a href='?mnux=kampus&gos=KampEdt&md=0&KampID=$w[KampusID]'><img src='img/edit.png' border=0>
    $w[KampusID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function KampEdt() {
  global $_Identitas;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('kampus', 'KampusID', $_REQUEST['KampID'], '*');
    $jdl = "Edit Kampus";
    $strid = "<input type=hidden name='KampusID' value='$w[KampusID]'><b>$w[KampusID]</b>";
  }
  else {
    $w = array();
    $w['KampusID'] = '';
    $w['Nama'] = '';
    $w['Alamat'] = '';
    $w['Kota'] = '';
    $w['KodeID'] = $_Identitas;
    $w['Telepon'] = '';
    $w['Fax'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Kampus";
    $strid = "<input type=text name='KampusID' size=20 maxlength=20>";
  }
  $snm = session_name(); $sid = session_id();
  $optkodid = GetOption2('identitas', "concat(Kode, ' - ', Nama)", 'Kode', $w['KodeID'], '', 'Kode');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $c1 = 'class=inp1'; $c2 = 'class=ul';
  CheckFormScript('KampusID,Nama,KodeID');
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='kampus'>
  <input type=hidden name='gos' value='KampSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th colspan=2 class=ttl>$jdl</th></tr>
  <tr><td $c1>Kode Kampus</td><td $c2>$strid</td></tr>
  <tr><td $c1>Nama</td><td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Institusi</td><td $c2><select name='KodeID'>$optkodid</select></td></tr>
  <tr><td $c1>Alamat</td><td $c2><textarea name='Alamat' cols=30 rows=2>$w[Alamat]</textarea></td></tr>
  <tr><td $c1>Kota</td><td $c2><input type=text name='Kota' value='$w[Kota]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Telepon</td><td $c2><input type=text name='Telepon' value='$w[Telepon]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Fax</td><td $c2><input type=text name='Fax' value='$w[Fax]' size=40 maxlength=50></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=Reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=kampus&gos=&$snm=$sid'\"></td></tr>
  
  </form></table></p>";
}
function KampSav() {
  $md = $_REQUEST['md']+0;
  $KampusID = $_REQUEST['KampusID'];
  $Nama = sqling($_REQUEST['Nama']);
  $KodeID = $_REQUEST['KodeID'];
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Fax = sqling($_REQUEST['Fax']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  // simpan
  if ($md == 0) {
    $s = "update kampus set Nama='$Nama', KodeID='$KodeID',
      Alamat='$Alamat', Kota='$Kota', Telepon='$Telepon', Fax='$Fax', NA='$NA'
      where KampusID='$KampusID' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('kampus', 'KampusID', $KampusID, '*');
    if (!empty($ada)) echo ErrorMsg("Gagal Simpan",
      "Kampus dengan kode: <b>$KampusID</b> telah ada dengan nama <b>$ada[Nama]</b>.<br>
      Gunakan kode kampus lain.");
    else {
      $s = "insert into kampus (KampusID, Nama, KodeID,
        Alamat, Kota, Telepon, Fax, NA)
        values ('$KampusID', '$Nama', '$KodeID',
        '$Alamat', '$Kota', '$Telepon', '$Fax', '$NA')";
      $r = _query($s);
    }
  }
  DftrKampus();
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? 'DftrKampus' : $_REQUEST['gos'];
$KodeID = GetSetVar('KodeID', $_Identitas);

// *** Main ***
TampilkanJudul("Kampus");
$gos();
?>
