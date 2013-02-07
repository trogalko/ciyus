<?php
  // Author: Emanuel Setio Dewo
  // Start: 2005-12-04
  
function TampilkanLogin() {
  $col = 4;
  
  $s = "select * from level where Accounting = 'N' order by LevelID";
  $r = mysql_query($s) or die("Gagal: "+mysql_error());
  echo "<table class=bsc cellspacing=1 cellpadding=4 align=center>";
  echo "<tr>";
  $cnt = 0;
  while ($w = mysql_fetch_array($r)) {
    if ($cnt >= $col) {
      echo "</tr><tr>";
      $cnt = 0;
    }
    $cnt++;
    $simbol = (empty($w['Simbol']))? 'img/login.png' : $w['Simbol'];
    echo "<td class=bsc align=center valign=top width=100>
    <a href='?mnux=login&lgn=frm&lid=$w[LevelID]&nme=$w[Nama]' title='Login sebagai $w[Nama]'>
    <img src='$simbol' border=0><br>
    $w[Nama]</a></td>";
  }
  echo "</tr></table>";
}
function frm(){
  ResetLogin();
  global $arrID;
  $LabelLogin = ($_REQUEST['lid'] == 120)? "N.P.M" : "Kode Login";
  $institusi = GetOption2('identitas', 'Nama', 'Kode', $arrID['Kode'], '', 'Kode');
  $isifrm = "<table class=bsc cellspacing=0 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='lid' value='$_REQUEST[lid]'>
  <input type=hidden name='slnt' value='loginprc'>
  <input type=hidden name='slntx' value='cek'>
  <input type=hidden name='mnux' value='donothing'>
  <input type=hidden name='nme' value='$_REQUEST[nme]'>
  
  <tr><td class=ul>Institusi</td><td class=ul><select name=institusi>$institusi</select></td></tr>
  <tr><td class=ul>$LabelLogin</td><td class=ul><input type=text name='Login' value='' size=10 maxlength=20></td></tr>
  <tr><td class=ul>Password</td><td class=ul><input type=password name='Password' value='' size=10 maxlength=20></td></tr>
  <tr><td colspan=3>
  <input type=submit name='Submit' value='Login'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux='\">
  </td></tr>
  </form></table>";
  echo Konfirmasi("Login: $_REQUEST[nme]", $isifrm);
}


// *** Parameter ***
$lgn = (!empty($_REQUEST['lgn']))? $_REQUEST['lgn'] : 'TampilkanLogin';
$_SESSION['lgn'] = $lgn;
$lgn();

?>
