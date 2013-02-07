<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 30 Sept 2006

include_once "class/dwolister.class.php";
//$arrPrioritas = array("-", "Biasa", "Penting", "Mendesak");
//$arrStatus = array("-", "Dikaji", "Dikerjakan", "Selesai");

// *** Functions ***
function BuatOpsi($nama='Nama', $arr, $def='') {
  $_ops = "<select name='$nama'>";
  foreach ($arr as $key=>$value) {
    $sel = ($def == $key)? 'selected' : '';
  	$_ops .= "<option value='$key' $sel>$value</option>";
  }
  return $_ops . "</select>";
}
function DaftarBugs() {
  $opt = GetOption2('bugsstatus', "concat(ID, ' - ', Nama)", 'ID', $_SESSION['FilterStatus'], '', 'ID');
  echo "<p><form action='?'>Filter Status: <select name='FilterStatus' onChange='this.form.submit()'>$opt</select>
  <a href='?mnux=bugserror&gos=BugsEdt&md=1'>Tambah Catatan Bugs & Error</a></form></p>";
  $edt = (strpos('.1.2.', ".$_SESSION[_LevelID].") === false)? '&nbsp;' : 
  "<a href='?mnux=bugserror&bid==ID=&gos=BugsEdt&md=0'><img src='img/edit.png'></a>";
  // filter
  $whr = (empty($_SESSION['FilterStatus']))? '' : "where StatusID='$_SESSION[FilterStatus]'"; 
  // tampilkan
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['_bugserror']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=bugserror&_bugserror==PAGE='>=PAGE=</a>";

  $lst->tables = "bugserror be
    left outer join bugsstatus bs on be.StatusID=bs.ID
    left outer join bugsprioritas bp on be.Prioritas=bp.ID
    $whr
    order by be.TanggalBuat desc";
  $lst->fields = "be.*, bs.Nama as STATUS, bp.Nama as PRIO,
    date_format(be.TanggalBuat, '%d-%m-%Y<br />%H:%i') as TB,
    date_format(be.TanggalEdit, '%d-%m-%Y<br />%H:%i') as TE";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>ID</th>
    <th class=ttl title='Edit'>Ed</th>
    <th class=ttl>Status</th>
    <th class=ttl>Prioritas</th>
    <th class=ttl>Judul Bugs/Error</th>
    <th class=ttl>Pelapor</th>
    <th class=ttl>Tgl<br />Buat</th>
    <th class=ttl>Pekerja</th>
    <th class=ttl>Tgl<br />Dikerjakan</th>
    <th class=ttl>Keterangan</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp1>=ID=</td>
    <td class=cna=NA=>$edt</td>
    <td class=cna=NA=>=STATUS=</td>
    <td class=cna=NA=>=PRIO=</td>
    <td class=cna=NA= nowrap>=Judul=</td>
    <td class=cna=NA=>=LoginBuat=</td>
    <td class=cna=NA=>=TB=</td>
    <td class=cna=NA=>=LoginEdit=&nbsp;</td>
    <td class=cna=NA=>=TE=&nbsp;</td>
    <td class=cna=NA= wrap>=Keterangan=&nbsp;</td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}
function BugsEdt() {
  global $arrPrioritas, $arrStatus;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $bid = $_REQUEST['bid'];
    $be = GetFields('bugserror', 'ID', $bid, '*');
    $judul = "Edit Catatan Bugs & Error";
  }
  else {
    $be = array();
    $be['ID'] = 0;
    $be['Judul'] = '';
    $be['Bugs'] = '';
    $be['Prioritas'] = 1;
    $be['StatusID'] = 1;
    $be['Keterangan'] = '';
    $be['NA'] = 'N';
    $judul = "Tambah Catatan Bugs & Error";
  }
  $_na = ($be['NA'] == 'Y')? 'checked' : '';
  //$optprio = BuatOpsi("Prioritas", $arrPrioritas, $be['Prioritas']);
  //$optstat = BuatOpsi("StatusID", $arrStatus, $be['StatusID']);
  
  CheckFormScript('Judul,Bugs');
  if (strpos('.1.2.', ".$_SESSION[_LevelID].") === false) {
    $prio = GetaField('bugsprioritas', 'ID', $be['Prioritas'], 'Nama');
    $stat = GetaField('bugsstatus', 'ID', $be['StatusID'], 'Nama');
    $str = "<tr><td class=inp>Prioritas</td><td class=ul>$prio <input type=hidden name='Prioritas' value='$be[Prioritas]'></td></tr>
      <tr><td class=inp>Status</td><td class=ul>$stat <input type=hidden name='StatusID' value='$be[StatusID]'></td></tr>";
  }
  else {
    $optprio = GetOption2('bugsprioritas', "concat(ID, ' - ', Nama)", "ID", $be['Prioritas'], '', "ID");
    $optstat = GetOption2('bugsstatus', "concat(ID, ' - ', Nama)", 'ID', $be['StatusID'], '', 'ID');
    $str = "<tr><td class=inp>Prioritas</td><td class=ul><select name='Prioritas'>$optprio</select></td></tr>
      <tr><td class=inp>Status</td><td class=ul><select name='StatusID'>$optstat</select></td></tr>
      <tr><td class=ul colspan=2>Keterangan Developer:<br />
      <textarea name='Keterangan' cols=100 rows=10>$be[Keterangan]</textarea></td></tr>";
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='BugsErrorRec' onSubmit='return CheckForm(this)'>
  <input type=hidden name='mnux' value='bugserror'>
  <input type=hidden name='gos' value='BugsSav'>
  <input type=hidden name='bid' value='$be[ID]'>
  <input type=hidden name='md' value='$md'>
  <input type=hidden name='BypassMenu' value='1'>
  <tr><td class=ul colspan=2><font size=+1>$judul</font></td></tr>
  <tr><td class=inp>Judul Catatan</td><td class=ul><input type=text name='Judul' value='$be[Judul]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Bugs/Error</td><td class=ul><textarea name='Bugs' cols=40 rows=6>$be[Bugs]</textarea></td></tr>
  $str
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=bugserror'\"></td></tr>
  </form></table></p>";
}
function BugsSav() {
  $md = $_REQUEST['md']+0;
  $bid = $_REQUEST['bid'];
  $Judul = sqling($_REQUEST['Judul']);
  $Bugs = sqling($_REQUEST['Bugs']);
  $Prioritas = $_REQUEST['Prioritas']+0;
  $StatusID = $_REQUEST['StatusID']+0;
  $StatusNA = GetaField('bugsstatus', 'ID', $StatusID, 'StatusNA');
  if ($md == 0) {
    $Keterangan = sqling($_REQUEST['Keterangan']);
    $ket = (strpos('.1.2.', ".$_SESSION[_LevelID].") === false)? '' : "Keterangan='$Keterangan', ";
    $s = "update bugserror
      set Judul='$Judul', Bugs='$Bugs', Prioritas='$Prioritas',
      StatusID='$StatusID', NA='$StatusNA', $ket
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where ID=$bid";
    $r = _query($s);
  }
  else {
    $s = "insert into bugserror
      (Judul, Bugs, Prioritas, StatusID,
      LoginBuat, TanggalBuat)
      values ('$Judul', '$Bugs', '$Prioritas', '$StatusID',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
  }
  echo "<script>window.location='?mnux=bugserror';</script>";
}

// *** Parameters ***
$_bugserror = GetSetVar('_bugserror', 1);
$FilterStatus = GetSetVar('FilterStatus');
$gos = (empty($_REQUEST['gos']))? "DaftarBugs" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Catatan Bugs & Error");
$gos();
?>
