<?php
// Author: Emanuel Setio Dewo
// 25 April 2006


// *** Functions ***
function HeaderDosen($dsn) {
  global $_LevelDosen;
  if ($_SESSION['_LevelID'] == $_LevelDosen) {
    $opt = "$dsn[Nama], $dsn[Gelar]";
  }
  else {
    $_opt = GetOption2('dosen', "concat(Login, '. ', Nama, ', ', Gelar)", "Nama",
      $_SESSION['DosenID'], '', 'Login');
    $opt = "<select name='DosenID' onChange='this.form.submit()'>$_opt</select>"; 
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='dosenpa' method=POST>
  <input type=hidden name='mnux' value='dosen.PA'>
  <tr><td class=inp>Kode Dosen</td><td class=ul>$dsn[Login] &nbsp;</td>
    <td class=inp>Nama</td><td class=ul>$opt</td></tr>
  <tr><td class=inp>Status</td><td class=ul>$dsn[SD] &nbsp;</td>
    <td class=inp>Homebase</td><td class=ul>$dsn[HB] ($dsn[Homebase])</td></tr>
  </form></table></p>";
}
function DftrMhsw($dsn) {
}


// *** Parameters ***
$_LevelDosen = '10';
$DosenID = GetSetVar('DosenID');
$tahun = GetSetVar('tahun');
$gos = empty($_REQUEST['gos'])? "DftrMhsw" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Bimbingan Akademik");
if ($_SESSION['_LevelID'] == $_LevelDosen) {
  $DosenID = $_SESSION['_Login'];
  $_SESSION['DosenID'] = $DosenID;
}
$dsn = GetFields("dosen d
  left outer join prodi prd on d.Homebase=prd.ProdiID
  left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID", 
  'd.Login', $DosenID, "d.*, sd.Nama as SD, prd.Nama as HB");
HeaderDosen($dsn);
if (!empty($dsn)) {
  $gos($dsn);
}
?>
