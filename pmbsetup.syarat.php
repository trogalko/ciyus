<?php
// Author: Emanuel Setio Dewo
// 28 Feb 2006

function DftrSyarat () {
  global $arrID, $mnux, $pref;
  TampilkanPilihanInstitusi($mnux);
  // Tampilkan Daftar
  $s = "select *
    from pmbsyarat
    where KodeID='$_SESSION[KodeID]'
    order by PMBSyaratID";
  $r = _query($s);
  echo "<p><a href='?mnux=$mnux&$pref=Syarat&sub=SyaratEdt&md=1'>Tambah Syarat</a></p>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>ID</th>
  <th class=ttl>Nama</th>
  <th class=ttl>Status Mhsw</th>
  <th class=ttl>Program Studi</th>
  <th class=ttl>NA</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr>
    <td $c><a href='?mnux=$mnux&$pref=Syarat&sub=SyaratEdt&syid=$w[PMBSyaratID]&md=0'><img src='img/edit.png'>
    $w[PMBSyaratID]</a></td>
    <td $c>$w[Nama]</td>
    <td $c>$w[StatusAwalID]</td>
    <td $c>$w[ProdiID]</td>
    <td $c align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function SyaratEdt() {
  global $arrID, $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('pmbsyarat', "KodeID='$_SESSION[KodeID]' and PMBSyaratID",
      $_REQUEST['syid'], '*');
    $jdl = "Edit Syarat";
    $syid = "<input type=hidden name='syid' value='$w[PMBSyaratID]'><b>$w[PMBSyaratID]</b>";
  }
  else {
    $w = array();
    $w['PMBSyaratID'] = '';
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['Nama'] = '';
    $w['StatusAwalID'] = '';
    $w['ProdiID'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Syarat";
    $syid = "<input type=text name='syid' size=10 maxlength=10>";
  }
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  $SAID = GetCheckBoxes('statusawal', "StatusAwalID",
    "concat(StatusAwalID, ' - ', Nama) as NM", 'NM', $w['StatusAwalID'], '.');
  $prodi = GetCheckBoxes('prodi', "ProdiID",
    "concat(ProdiID, ' - ', Nama) as NM", 'NM', $w['ProdiID'], '.');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='$pref' value='Syarat'>
  <input type=hidden name='sub' value='SyaratSav'>
  <input type=hidden name='md' value='$md'>

  <tr>
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp1>ID</td><td class=ul>$syid</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Status Awal</td><td class=ul>$SAID</td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>$prodi</td></tr>
  <tr><td class=inp1>Tidak Aktif (NA)?</td><td class=ul><input type=checkbox name='NA' value='Y' $NA></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=Syarat'\"></td></tr>
  </form></table>";
}
function SyaratSav() {
  $md = $_REQUEST['md']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $_StatusAwalID = array();
  $_StatusAwalID = $_REQUEST['StatusAwalID'];
  $StatusAwalID = (empty($_StatusAwalID))? '' : "." . implode('.', $_StatusAwalID) .".";
  $_ProdiID = array();
  $_ProdiID = $_REQUEST['ProdiID'];
  $ProdiID = (empty($_ProdiID))? '' : "." . implode('.', $_ProdiID) . ".";
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update pmbsyarat set ProdiID='$ProdiID', StatusAwalID='$StatusAwalID', NA='$NA'
      where PMBSyaratID='$_REQUEST[syid]' and KodeID='$_SESSION[KodeID]' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('pmbsyarat', "KodeID='$_SESSION[KodeID]' and PMBSyaratID", $_REQUEST['syid'], '*');
    if (empty($ada)) {
      $s = "insert into pmbsyarat (PMBSyaratID, KodeID, Nama, StatusAwalID, ProdiID, NA)
        values ('$_REQUEST[syid]', '$_SESSION[KodeID]', '$Nama', '$StatusAwalID', '$ProdiID', '$NA')";
      $r = _query($s);
    }
    else echo ErrorMsg("Gagal Simpan",
      "Anda tidak bisa menyimpan syarat dengan ID <b>$_REQUEST[syid]</b> karena telah digunakan:
      <p><table class=bsc cellspacing=1 cellpadding=4>
      <tr><td class=ul>ID</td><td class=ul><b>$ada[PMBSyaratID]</b></td></tr>
      <tr><td class=ul>Nama</td><td class=ul><b>$ada[Nama]</b></td></tr>
      </table></p>");
  }
  DftrSyarat();
}

?>
