<?php
// Author: Emanuel Setio Dewo
// Start : 06-08-2007

// *** Main ***
TampilkanJudul("Pembayaran Minimal Mahasiswa Baru");
$gos = (empty($_REQUEST['gos']))? 'DftrProdi' : $_REQUEST['gos'];
$gos();

// *** Functions ***

function DftrProdi() {
  global $mnux, $pref, $tokendef;

  echo "<p align=center>Anda akan mengeset persen pembayaran calon mahasiswa per prodi untuk bisa dibuat NPM-nya.</p>";

  $s = "select * from prodi where KodeID='$_SESSION[KodeID]' order by ProdiID";
  $r = _query($s);
  $_fak = 'qwertyuiop';

  echo "<p><table class=box cellspacing=1 align=center>";
  echo "<tr>
    <th class=ttl width=80>Kode</th>
    <th class=ttl width=300>Prodi</th>
    <th class=ttl colspan=2>Persen</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if ($_fak != $w['FakultasID']) {
      $_fak = $w['FakultasID'];
      $fak = GetaField('fakultas', 'FakultasID', $_fak, 'Nama');
      echo "<tr><td class=ul colspan=4><font size=+1>$fak</font></td></tr>";
    }
    echo "<tr>
    <td class=inp>$w[ProdiID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$w[PersenPMB]%</td>
    <td class=ul><a href='?mnux=$mnux&gos=PCTEDT&prd=$w[ProdiID]'><img src='img/edit.png'></a></td>
    </tr>";
  }
  echo "</table></p>";
}
function PCTEDT() {
  global $mnux, $pref, $tokendef;
  $prd = $_REQUEST['prd'];
  $prodi = GetFields('prodi', 'ProdiID', $prd, '*');
  if (empty($prodi))
    echo ErrorMsg('Data Tidak Ditemukan',
    "Program Studi dengan kode <b>$prd</b> tidak ditemukan.<br />
    Hubungi System Administrator untuk informasi lebih lanjut.
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Back' value='Kembali' onClick=\"location='?mnux=$mnux'\">");
  else EditPersenProdi($prd, $prodi);
}
function EditPersenProdi($prd, $prodi) {
  global $mnux, $pref;
  echo "<p><table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='PCTSAV'>
  <input type=hidden name='prd' value='$prd'>
  <input type=hidden name='BypassMenu' value=1>

  <tr><th class=ttl colspan=2>Edit Persentase Pembayaran</th></tr>
  <tr><td class=inp>Kode Prodi</td>
      <td class=ul><b>$prodi[ProdiID]</b></td>
      </tr>
  <tr><td class=inp>Program Studi</td>
      <td class=ul><b>$prodi[Nama]</b></td>
      </tr>
  <tr><td class=inp>Persen Pembayaran</td>
      <td class=ul><input type=text name='PersenPMB' value='$prodi[PersenPMB]' size=5 maxlength=3> %</td>
      </tr>
  <tr><td class=ul colspan=2>
      <input type=submit name='Simpan' value='Simpan'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux'\">
      </td></tr>

  </form></table></p>";
}
function PCTSAV() {
  global $mnux, $pref;
  $prd = $_REQUEST['prd'];
  $PersenPMB = $_REQUEST['PersenPMB']+0;
  $s = "update prodi set PersenPMB='$PersenPMB' where ProdiID='$prd' ";
  $r = _query($s);
  echo "<script>window.location = '?mnux=$mnux';</script>";
}
?>