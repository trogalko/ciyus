<?php
// Author: Emanuel Setio Dewo
// 05 March 2006
include_once "carimhsw.php";

// *** Functions ***
function CariMhsw1() {
  CariMhsw('mhswmasterbipot', '');
  DaftarMhswBipot();
}
function DaftarMhswBipot($mnux='mhswmasterbipot', $lnk='') {
  include_once "class/dwolister.class.php";
  $arrKey = array('NPM'=>'MhswID', 'Nama'=>'Nama', 'Semua'=>'');
  $whr = '';

  if (!empty($arrKey[$_SESSION['crmhswkey']]) && !empty($_SESSION['crmhswval']))
    $whr = "m." . $arrKey[$_SESSION['crmhswkey']] . " like '%" . $_SESSION['crmhswval'] . "%' ";
  $whr = (empty($whr))? '' : "where " . $whr;

  $lst = new dwolister;
  $lst->tables = "mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID
    $whr
    order by m.MhswID";
  $lst->fields = "m.MhswID, m.Nama, m.ProgramID, m.ProdiID, m.BIPOTID,
    bpt.Nama as BPT,
    prg.Nama as PRG, prd.Nama as PRD, sm.Nama as SM";
  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>No Mhsw</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Master BIPOT</th>
    <th class=ttl>Program</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>Status</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr><td class=inp1>=NOMER=</td>
    <td class=ul>=MhswID=</td>
    <td class=ul>=Nama=</td>
    <td class=ul><a href='?mnux=mhswmasterbipot&gos=BPTEDT&mhswid==MhswID='><img src='img/edit.png'>
    =BPT=</a></td>
    <td class=ul>=ProgramID=-=PRG=</td>
    <td class=ul>=ProdiID=-=PRD=</td>
    <td class=ul>=SM=</td>
    </tr>";
  echo $lst->TampilkanData();
  echo "Hal. : ". $lst->TampilkanHalaman();
}
function BPTEDT() {
  $mhswid = $_REQUEST['mhswid'];
  $w = GetFields('mhsw', 'MhswID', $mhswid, '*');
  $bipotid = GetOption2("bipot", "concat(Tahun, ' - ', Nama, ' - ', Def)", 'Tahun',
    $w['BIPOTID'], "ProgramID='$w[ProgramID]' and ProdiID='$w[ProdiID]'", 'BIPOTID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhswmasterbipot'>
  <input type=hidden name='mhswid' value='$mhswid'>
  <input type=hidden name='gos' value='BPTSAV'>

  <tr><th class=ttl colspan=2>Edit Master Biaya & Potongan Mhsw</th></tr>
  <tr><td class=inp1>NPM</td><td class=ul>$w[MhswID]</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul>$w[Nama]</td></tr>
  <tr><td class=inp1>Program</td><td class=ul>$w[ProgramID]</td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul>$w[ProdiID]</td></tr>
  <tr><td class=inp1>Biaya dan Potongan</td><td class=ul><select name='BIPOTID'>$bipotid</select></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswmasterbipot'\"></td></tr>
  </form></table></p>";
}
function BPTSAV() {
  $s = "update mhsw set BIPOTID='$_REQUEST[BIPOTID]'
    where MhswID='$_REQUEST[mhswid]' ";
  $r = _query($s);
  CariMhsw1();
}

// *** Parameters ***
$crmhswkey = GetSetVar('crmhswkey');
$crmhswval = GetSetVar('crmhswval');
$gos = (empty($_REQUEST['gos']))? 'CariMhsw1' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Master Biaya dan Potongan Mahasiswa");
$gos();

?>
