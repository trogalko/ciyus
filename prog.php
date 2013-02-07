<?php
// Author: Emanuel Setio Dewo
// 2005-12-19

// *** Functions ***
function DftrProg() {
  $s = "select * 
    from program 
    where KodeID = '$_SESSION[KodeID]'
    order by ProgramID";
  $r = _query($s);
  $n = 0;
  $cs = 5;
  $optkd = GetOption2('identitas', "concat(Kode, ' - ', Nama)", 'Kode', $_SESSION['KodeID'], '', 'Kode');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='prog'>
    <tr><td colspan=$cs class=ul>Institusi: <select name='KodeID' onChange='this.form.submit()'>$optkd</select></td></tr>
    <tr><td colspan=$cs class=ul><a href='?mnux=prog&gos=ProgEdt&md=1'>Tambah Program</a></td></tr>
    <tr><th class=ttl>#</th><th class=ttl colspan=2>Kode</th>
    <th class=ttl>Nama</th><th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c>$n</td>
      <td $c>$w[ProgramID]</td>
      <td $c><a href='?mnux=prog&gos=ProgEdt&md=0&pid=$w[ProgramID]'><img src='img/edit.png' border=0></a></td>
      <td $c>$w[Nama]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function ProgEdt() {
  $md = $_REQUEST['md'] +0;
  if ($md == 0) {
    $w = GetFields('program', 'ProgramID', $_REQUEST['pid'], '*');
    $jdl = 'Edit Program';
    $_pid = "<input type=hidden name='ProgramID' value='$w[ProgramID]'><b>$w[ProgramID]</b>";
  }
  else {
    $w = array();
    $w['ProgramID'] = '';
    $w['Nama'] = '';
    $w['NA'] = 'N';
    $jdl = 'Tambah Program';
    $_pid = "<input type=text name='ProgramID' size=20 maxlength=20>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='prog'>
  <input type=hidden name='gos' value='ProgSav'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ul colspan=2>$jdl</th></tr>
  <tr><td class=inp1>Kode</td><td class=ul>$_pid</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=50></td></tr>
  <tr><td class=inp1>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=prog'\"></td></tr>
  </form></table>";
}
function ProgSav() {
  $md = $_REQUEST['md'] +0;
  $ProgramID = $_REQUEST['ProgramID'];
  $Nama = sqling($_REQUEST['Nama']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update program set Nama='$Nama', NA='$NA' where ProgramID='$ProgramID' ";
    _query($s);
  }
  else {
    $ada = GetFields('program', 'ProgramID', $ProgramID, '*');
    if (empty($ada)) {
      $s = "insert into program(ProgramID, Nama, KodeID, NA)
        values('$ProgramID', '$Nama', '$_SESSION[KodeID]', '$NA')";
      _query($s);
    }
    else echo ErrorMsg('Kesalahan',
      "Kode program: <b>$ProgramID</b> telah dipakai oleh Program: <b>$ada[Nama]</b>.<br>
      Gunakan Kode Program lain.");
  }
  DftrProg();
}

// *** Parameters ***
$KodeID = GetSetVar('KodeID', $_Identitas);
$gos = (empty($_REQUEST['gos']))? 'DftrProg' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Program Pendidikan");
$gos();
?>
