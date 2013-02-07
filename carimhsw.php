<?php
// Author: Emanuel Setio Dewo
// 05 March 2006

function CariMhsw($mnux, $lnk='') {
  $crmhswkey = GetSetVar('crmhswkey');
  $crmhswval = GetSetVar('crmhswval');
  if ($_SESSION['crmhswkey'] == 'Semua')
    $_SESSION['crmhswval'] = '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>

  <tr><td colspan=2 class=ul><b>$_SESSION[KodeID]</b></td></tr>
  <tr><td class=inp1>Cari Mahasiswa</td>
    <td class=ul><input type=text name='crmhswval' value='$_SESSION[crmhswval]' size=20 maxlength=20>
    <input type=submit name='crmhswkey' value='NPM'>
    <input type=submit name='crmhswkey' value='Nama'>
    <input type=submit name='crmhswkey' value='Semua'>
    </td></tr>
  </form></table></p>";
  //DaftarMhsw($mnux, $lnk='');
}
function DaftarMhsw($mnux, $lnk='', $keluar=0) {
  include_once "class/dwolister.class.php";
  $inqMhswPage = GetSetVar('inqMhswPage');
  $arrKey = array('NPM'=>'MhswID', 'Nama'=>'Nama', 'Semua'=>'');
  // filter
  $whr = array();
  if (!empty($arrKey[$_SESSION['crmhswkey']]) && !empty($_SESSION['crmhswval']))
    $whr[] = "m." . $arrKey[$_SESSION['crmhswkey']] . " like '%" . $_SESSION['crmhswval'] . "%' ";
  if ($keluar > 0) $whr[] = "sm.Keluar <> 1";
  if ($_SESSION['_LevelID'] > 1) {
    if (empty($_SESSION['_LevelID']))
      die(ErrorMsg("Tidak Dapat Akses", "Anda tidak memiliki hak akses ke data Mahasiswa."));
    $_ProdiID = trim($_SESSION['_ProdiID'], ',');
    $arrProdi = explode(',', $_ProdiID);
    $_prodi = '';
    for ($i = 0; $i<sizeof($arrProdi); $i++) $_prodi .= ",'".$arrProdi[$i]."'";
    $_prodi = trim($_prodi, ',');
    $_prodi = (empty($arrProdi))? '-1' : $_prodi; //implode(', ', $arrProdi);
    if ($_SESSION['LevelID'] == 100) {}
    else $whr[] = "m.ProdiID in ($_prodi)";
  }
  // Satukan filter
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : "where $_whr";
  // Tampilkan daftar
  $lst = new dwolister;
  $lst->page = $_SESSION['inqMhswPage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$lnk&inqMhswPage==PAGE='>=PAGE=</a>";

  $lst->tables = "mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    $_whr
    order by m.MhswID";
  $lst->fields = "m.MhswID, m.Nama, m.ProgramID, m.ProdiID, m.SKKeluar, m.Foto,
    prg.Nama as PRG, prd.Nama as PRD, sm.Nama as SM, sm.Keluar";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>No Mhsw</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Program</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status</th>
    <th class=ttl>SK Lulus/Keluar</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr><td class=inp1>=NOMER=</td>
    <td class=cna=Keluar=><a href='?mnux=$mnux&mhswid==MhswID=&$lnk'><img src='img/edit.png'>
    =MhswID=</a></td>
    <td class=cna=Keluar=><a id='=MhswID=' class='jTip' name='    Foto Mahasiswa' href='foto.pop.php?width=156&mhswid==MhswID=&foto==Foto='>=Nama=</a></td>
    <td class=cna=Keluar=>=ProgramID=-=PRG=</td>
    <td class=cna=Keluar=>=ProdiID=-=PRD=</td>
    <td class=cna=Keluar=>=SM=</td>
    <td class=cna=Keluar=>=SKKeluar=&nbsp;</td>
    </tr>";
  echo $lst->TampilkanData();
  echo "Halaman : ". $lst->TampilkanHalaman();
}

?>
