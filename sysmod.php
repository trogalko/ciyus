<?php
// Author: Emanuel Setio Dewo
// 2005-12-17

// *** Functions ***
function TampilkanMdlGrp() {
//function GetOption2($_table, $_field, $_order='', $_default='', $_where='', $_value='', $not=0) {
  $opt = GetOption2('mdlgrp', "concat(Urutan, '. ', Nama)", 'Urutan', $_SESSION['mdlgrp'], "Accounting = 'N'", 'MdlGrpID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='sysmod'>
  <input type=hidden name='token' value='DftrMdl'>
  <tr><td class=inp1>Group Modul</td><td class=ul><select name='mdlgrp' onChange=\"this.form.submit()\">$opt</select></td></tr>
  </form></table></p>";
}
function TampilkanMenuModul() {
  echo "<p><a href=\"?mnux=sysmod&token=DftrMdl\">Daftar Modul</a> |
    <a href=\"?mnux=sysmod&token=ModEdt&md=1\">Tambah Modul</a> |
    <a href=\"?mnux=sysmod&token=DftrGrp\">Daftar Group</a> |
    <a href=\"?mnux=sysmod&token=GrpEdt&md=1\">Tambah Group</a>
    </p>";
}
function DftrMdl() {
  TampilkanMdlGrp();
  $whr = '';
  $whr .= (empty($_SESSION['mdlgrp']))? '' : "and m.MdlGrpID='$_SESSION[mdlgrp]' ";
  $s = "select m.*, mg.Urutan
    from mdl m
    left outer join mdlgrp mg on m.MdlGrpID=mg.MdlGrpID
    where m.MdlID>0 and mg.Accounting = 'N' $whr
    order by mg.Urutan, m.Nama";
  $r = mysql_query($s) or die("Gagal: $s<br>".mysql_error());
  $n = 0;
  TampilkanMenuModul();
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th><th class=ttl>Module</td>
    <th class=ttl>Level</th>
    <th class=ttl>Script</th>
    <th class=ttl>Web</th>
    <th class=ttl>Exe</th>
    <th class=ttl>Group</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = mysql_fetch_array($r)) {
    $n++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td $c><a href=\"?mnux=sysmod&token=ModEdt&md=0&mid=$w[MdlID]\"><img src='img/edit.png' border=0></a>
      $n</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[LevelID]</td>
      <td $c>$w[Script]</td>
      <td $c align=center><img src='img/$w[Web].gif'></td>
      <td $c align=center><img src='img/$w[CS].gif'></td>
      <td $c>$w[MdlGrpID]</td>
      <td $c align=center><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function ModEdt() {
  global $_Author, $_AuthorEmail;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mdl', 'MdlID', $_REQUEST['mid'], '*');
    $jdl = 'Edit Modul';
  }
  else {
    $w = array();
    $w['MdlID'] = '';
    $w['MdlGrpID'] = $_SESSION['mdlgrp'];
    $w['Nama'] = '';
    $w['Script'] = '';
    $w['LevelID'] = '.';
    $w['Web'] = 'Y';
    $w['CS'] = 'N';
    $w['Author'] = $_Author;
    $w['EmailAuthor'] = $_AuthorEmail;
    $w['Simbol'] = '';
    $w['Help'] = '';
    $w['NA'] = 'N';
    $w['Keterangan'] = '';
    $jdl = "Tambah Modul";
  }
  $optgrp = GetOption2('mdlgrp', "concat(MdlGrpID, ' - ', Nama)", 'Nama', $w['MdlGrpID'], "Accounting = 'N'", 'MdlGrpID');
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $web = ($w['Web'] == 'Y')? 'checked' : '';
  $cs = ($w['CS'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $DftrLevel = GetDftrLevel($w['LevelID']);
  // Tampilkan form
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='data' method=POST>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='MdlID' value='$w[MdlID]'>
  <input type=hidden name='mnux' value='sysmod'>
  <input type=hidden name='token' value='ModSav'>

  <tr><th colspan=3 class=ttl>$jdl</th></tr>
  <tr><td class=inp1>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td>
    <td class=ul rowspan=12 valign=bottom>$DftrLevel</td></tr>
  <tr><td class=inp1>Group</td><td class=ul><select name='MdlGrpID'>$optgrp</select></td></tr>
  <tr><td class=inp1>Script</td><td class=ul><input type=text name='Script' value='$w[Script]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Level</td><td class=ul><input type=text name='LevelID' value='$w[LevelID]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Versi Web</td><td class=ul><input type=checkbox name='Web' value='Y' $web></td></tr>
  <tr><td class=inp1>Versi CS</td><td class=ul><input type=checkbox name='CS' value='Y' $cs></td></tr>
  <tr><td class=inp1>Author</td><td class=ul><input type=text name='Author' value='$w[Author]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Email</td><td class=ul><input type=text name='EmailAuthor' value='$w[EmailAuthor]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Simbol</td><td class=ul><input type=text name='Simbol' value='$w[Simbol]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Help</td><td class=ul><input type=text name='Help' value='$w[Help]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>NA (tdk aktif)</td><td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=sysmod'\"></td></tr>
  </form></table></p>";
}
function ModSav() {
  $md = $_REQUEST['md'];
  $MdlID = $_REQUEST['MdlID'];
  $MdlGrpID = $_REQUEST['MdlGrpID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Script = $_REQUEST['Script'];
  $_levelid = TRIM($_REQUEST['LevelID'], '.');
  if (empty($_levelid)) $LevelID = '';
  else {
    $arrLevelID = explode('.', $_levelid);
    sort($arrLevelID);
    $LevelID = '.'.implode('.', $arrLevelID).'.';
  }
  $Web = (!empty($_REQUEST['Web']))? $_REQUEST['Web'] : 'N';
  $CS = (!empty($_REQUEST['CS']))? $_REQUEST['CS'] : 'N';
  $Author = sqling($_REQUEST['Author']);
  $EmailAuthor = sqling($_REQUEST['EmailAuthor']);
  $Simbol = $_REQUEST['Simbol'];
  $Help = $_REQUEST['Help'];
  $NA = (!empty($_REQUEST['NA']))? $_REQUEST['NA'] : 'N';
  $Keterangan = sqling($_REQUEST['Keterangan']);
  // Simpan
  if ($md == 0) {
    $s = "update mdl set Nama='$Nama', MdlGrpID='$MdlGrpID', Script='$Script',
      LevelID='$LevelID', Web='$Web', CS='$CS',
      Author='$Author', EmailAuthor='$EmailAuthor', Simbol='$Simbol',
      Help='$Help', NA='$NA', Keterangan='$Keterangan'
      where MdlID='$MdlID'";
  }
  else {
    $s = "insert into mdl (MdlGrpID, Nama, Script, LevelID, Web, CS,
      Author, EmailAuthor, Simbol, Help, NA, Keterangan)
      values ('$MdlGrpID', '$Nama', '$Script', '$LevelID', '$Web', '$CS',
      '$Author', '$EmailAuthor', '$Simbol', '$Help', '$NA', '$Keterangan')";
  }
  _query($s);
  DftrMdl();
}
function GetDftrLevel($lvl='') {
  TulisScriptUbahLevel();
  $s = "select *
    from level 
    where Accounting = 'N'
    order by LevelID";
  $r = _query($s);
  $a = '';
  while ($w = _fetch_array($r)) {
    $ck = (strpos($lvl, ".$w[LevelID].") === false)? '' : 'checked';
    $a .= "<input type=checkbox name='Level$w[LevelID]' value='$w[LevelID]' $ck onChange='javascript:UbahLevel(data.Level$w[LevelID])'> $w[LevelID] - $w[Nama]<br />";
  }
  return $a;
}
function TulisScriptUbahLevel() {
  echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  function UbahLevel(nm){
    ck = "";
    if (nm.checked == true) {
      var nilai = data.LevelID.value;
      if (nilai.match(nm.value+".") != nm.value+".") data.LevelID.value += nm.value + ".";
    }
    else {
      var nilai = data.LevelID.value;
      data.LevelID.value = nilai.replace(nm.value+".", "");
    }
  }
  //-->
  </script>
END;
}
function DftrGrp() {
  TampilkanMenuModul();
  $s = "select mg.*
    from mdlgrp mg
    where Accounting = 'N'
    order by mg.Urutan";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>#</th>
  <th class=ttl>ID</th>
  <th class=ttl>Group</th>
  <th class=ttl>Nama</th>
  <th class=ttl>NA</th></tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp1>$w[Urutan]</td>
    <td $c><a href='?mnux=sysmod&token=GrpEdt&md=0&grid=$w[MdlGrpID]'><img src='img/edit.png' border=0>
    $w[MdlGrpID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function GrpEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('mdlgrp', 'MdlGrpID', $_REQUEST['grid'], '*');
    $_grid = "<input type=hidden name='MdlGrpID' value='$w[MdlGrpID]'><b>$w[MdlGrpID]</b>";
    $jdl = "Edit Group";
  }
  else {
    $w = array();
    $w['MdlGrpID'] = '';
    $w['Nama'] = '';
    $w['Urutan'] = 0;
    $w['NA'] = 'N';
    $_grid = "<input type=text name='MdlGrpID' size=10 maxlength=10>";
    $jdl = "Tambah Group";
  }
  $_NA = ($w['NA'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='sysmod'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='token' value='GrpSav'>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>Group ID</td><td class=ul>$_grid</td></tr>
  <tr><td class=inp1>Nama Group</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Urutan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=5 maxlength=5></td></tr>
  <tr><td class=inp1>Tidak Aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_NA></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
  <input type=reset name='Reset' value='Reset'>
  <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=sysmod&token=DftrGrp'\"></td></tr>
  </form></table></p>";
}
function GrpSav() {
  $md = $_REQUEST['md'];
  $MdlGrpID = $_REQUEST['MdlGrpID'];
  if (!empty($MdlGrpID)) {
    $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
    $Nama = sqling($_REQUEST['Nama']);
    $Urutan = $_REQUEST['Urutan']+0;
    if ($md == 0) {
      $s = "update mdlgrp set Nama='$Nama', Urutan='$Urutan', NA='$NA' where MdlGrpID='$MdlGrpID' ";
      $r = _query($s);
    }
    else {
      $ada = GetFields('mdlgrp', 'MdlGrpID', $MdlGrpID, '*');
      if (empty($ada)) {
        $s = "insert into mdlgrp (MdlGrpID, Nama, Urutan, NA)
          values ('$MdlGrpID', '$Nama', '$Urutan', '$NA')";
        $r = _query($s);
      }
      else echo ErrorMsg("Data Tidak Dapat Disimpan",
        "Group dengan ID <b>$MdlGrpID</b> telah ada. Gunakan ID lain.");
    }
  }
  DftrGrp();
}

// *** Parameters ***
$mdlgrp = GetSetVar('mdlgrp');
$token = (empty($_REQUEST['token']))? 'DftrMdl' : $_REQUEST['token'];


// *** Main ***
TampilkanJudul("Modul $_ProductName");
$token();
?>
