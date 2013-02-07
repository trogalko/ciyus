<?php
// Author: Emanuel Setio Dewo
// 20 July 2006
// www.sisfokampus.net

// *** functions ***
function TampilkanFilterDosen($mnux='dosen', $add=1) {
  global $arrID;
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $ck_nama = ($_SESSION['dsnurt'] == 'Nama') ? 'checked' : '';
  $ck_login = ($_SESSION['dsnurt'] == 'Login') ? 'checked' : '';
  $stradd = ($add == 0)? '' : "<tr><td class=ul>Pilihan:</td>
    <td class=ul><a href='?mnux=dosen&gos=DsnAdd&md=1'>Tambah Dosen</td></tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='dsnpage' value='1'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=ul>Cari Dosen:</td>
  <td class=ul><input type=text name='dsncr' value='$_SESSION[dsncr]' size=10 maxlengh=10>
    <input type=submit name='dsnkeycr' value='Login'>   
    <input type=submit name='dsnkeycr' value='Nama'>
    <input type=submit name='dsnkeycr' value='Reset'></td></tr>
  <tr><td class=ul>Urut berdasarkan:</td><td class=ul>
    <input type=radio name='dsnurt' value='Nama' $ck_nama> Nama,
    <input type=radio name='dsnurt' value='Login' $ck_login>Login/NIP
    <input type=submit name='Urutkan' value='Urutkan'></td></tr>
    <tr><td class=ul>Homebase :</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  $stradd
  </form></table></p>";
}
function DaftarDosen($mnux='', $lnk='', $fields='') {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
  //$lnk = "gos=DsnEdt&md=0&dsnid==Login="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=&nbsp;</td>";
  }
  $whr = array();
  if (!empty($_SESSION['dsnkeycr']) && !empty($_SESSION['dsncr'])) {
    if ($_SESSION['dsnkeycr'] == 'Login') {
			$whr[] = "$_SESSION[dsnkeycr] like '$_SESSION[dsncr]%'";
		} else $whr[] = "$_SESSION[dsnkeycr] like '%$_SESSION[dsncr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['prodi'])) ? '' : "and Homebase = '$_SESSION[prodi]'";
  
  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['dsnpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=&dsnpage==PAGE='>=PAGE=</a>";
  $lst->tables = "dosen
    where KodeID='$_SESSION[KodeID]' $where $hom
    order by $_SESSION[dsnurt]";
  $lst->fields = "* ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Login/NIP</th>
    $hdr
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0>
      =Login=</a></td>
    $brs
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}
?>
