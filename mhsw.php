<?php
// Author: Emanuel Setio Dewo
// 16 Feb 2006

// *** Functions ***
function TampilkanFilterMhsw() {
  global $arrID;
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['prodi'], "KodeID='$_SESSION[KodeID]'", 'ProdiID');
  $optprid = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$_SESSION[KodeID]'", 'ProgramID');

  // Tampilkan formulir
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhsw'>

  <tr><td colspan=2 class=ul><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprid</select></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprodi</select></td></tr>
  <tr><td class=inp1>Cari mahasiswa</td><td class=ul><input type=text name='srcmhswval' value='$_SESSION[srcmhswval]' size=10 maxlength=15>
    <input type=submit name='srcmhswkey' value='NPM'>
    <input type=submit name='srcmhswkey' value='Nama'>
    <input type=submit name='srcmhswkey' value='Reset'></td></tr>
  </table></p>";
}
function DaftarMhsw() {
  // setup where-statement
  $whr = array();
  $ord = '';
  if (($_SESSION['srcmhswkey'] != 'Reset') &&
  !empty($_SESSION['srcmhswkey']) && !empty($_SESSION['srcmhswval'])) {
    $whr[] = "m.$_SESSION[srcmhswkey] like '%$_SESSION[srcmhswval]%' ";
    $ord = "order by m.$_SESSION[srcmhswkey]";
  }
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($whr)) $strwhr = "where " .implode(' and ', $whr);
  $strwhr = str_replace('NPM', "MhswID", $strwhr);
  $ord = str_replace('NPM', "MhswID", $ord);
  // Tampilkan
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['mhswpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=mhsw&mhswpage==PAGE='>=PAGE=</a>";

  $lst->tables = "mhsw m
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    $strwhr $ord";
  $lst->fields = "m.MhswID, m.Nama, m.StatusAwalID, m.StatusMhswID,
    m.Telepon, m.Handphone, m.Email, m.Foto,
    m.ProgramID, m.ProdiID, m.Alamat, m.Kota,
    prd.Nama as PRD, sm.Nama as SM, sm.Keluar";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No.</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status</th>
    <th class=ttl>Telp/HP</th>
    <th class=ttl>Alamat</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
    <td class=inp1>=NOMER=</td>
    <td class=cna=Keluar=><a href='?mnux=mhsw.edt&gos=MhswEdt&mhswid==MhswID='><img src='img/edit.png'>
    =MhswID=</a></td>
    <td class=cna=Keluar= nowrap><a id='=MhswID=' class='jTip' name='Foto Mahasiswa' href='foto.pop.php?width=156&mhswid==MhswID=&foto==Foto='>=Nama=</a></td>
    <td class=cna=Keluar=>=ProgramID= - =PRD=</td>
    <td class=cna=Keluar=>=SM=</td>
    <td class=cna=Keluar=>=Telepon=/=Handphone=</td>
    <td class=cna=Keluar=>=Alamat=, =Kota=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}


// *** Parameters ***
$mhswpage = GetSetVar('mhswpage', 1);
$_srcmhswkey = GetSetVar('srcmhswkey');
$_srcmhswval = GetSetVar('srcmhswval');
if ($_REQUEST['srcmhswkey'] == 'Reset') {
  $_SESSION['srcmhswkey'] = '';
  $_SESSION['srcmhswval'] = '';
}
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? 'DaftarMhsw' : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Master Mahasiswa");
TampilkanFilterMhsw();
$gos();
?>
