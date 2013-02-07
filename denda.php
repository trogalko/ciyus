<?php
// Administrasi Denda
// Author: Emanuel Setio Dewo
// 17 July 2006
// www.sisfokampus.net

// *** Functions ***
function DftrDendaPrd() {
  $s = "select prd.ProdiID, prd.Nama, prd.Denda1, prd.Denda2, prd.NA,
    prd.FakultasID, f.Nama as NamaFak
    from prodi prd
      left outer join fakultas f on prd.FakultasID=f.FakultasID
    order by prd.FakultasID, prd.ProdiID";
  $r = _query($s); $fak = 'abcdefghijklmnopqrstuvwxyz';
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>Prodi</th>
    <th class=ttl>Nama Program Studi</th>
    <th class=ttl>Edit</th>
    <th class=ttl>Terlambat<br />Bayar</th>
    <th class=ttl>Sampai Akhir<br />Semester</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    if ($fak != $w['NamaFak']) {
      $fak = $w['NamaFak'];
      echo "<tr><td class=ul colspan=5>$w[FakultasID] <font size=+1>$fak</font></td></tr>";
    }
    echo "<tr><td class=inp>$w[ProdiID]</td>
    <td $c>$w[Nama]</td>
    <td $c align=center><a href='?mnux=denda&gos=DendaEdt&ProdiID=$w[ProdiID]'><img src='img/edit.png'></a></td>
    <td $c align=right>$w[Denda1]%</td>
    <td $c align=right>$w[Denda2]%</td>
    <td class=ul align=center><img src='img/book$w[NA].gif'></td>
    </tr>";
  }
  echo "</table></p>";
}
function DendaEdt() {
  $ProdiID = $_REQUEST['ProdiID'];
  $prd = GetFields('prodi', 'ProdiID', $ProdiID, '*');
  CheckFormScript('Denda1,Denda2');
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='denda'>
  <input type=hidden name='gos' value='DendaSav'>
  <input type=hidden name='ProdiID' value='$ProdiID'>
  
  <tr><th class=ttl colspan=2>Edit Denda Program Studi</th></tr>
  <tr><td class=inp>Kode Prodi</td><td class=ul><b>$prd[ProdiID]</b></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><b>$prd[Nama]</b></td></tr>
  <tr><td class=inp>Denda Terlambat</td>
    <td class=ul><input type=text name='Denda1' value='$prd[Denda1]' size=3 maxlength=3> %</td></tr>
  <tr><td class=inp>Sampai Akhir Semester</td>
    <td class=ul><input type=text name='Denda2' value='$prd[Denda2]' size=3 maxlength=3> %</td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=denda'\"></td></tr>
  </form></table></p>";
}
function DendaSav() {
  $ProdiID = $_REQUEST['ProdiID'];
  $Denda1 = $_REQUEST['Denda1']+0;
  $Denda2 = $_REQUEST['Denda2']+0;
  $s = "update prodi set Denda1=$Denda1, Denda2=$Denda2
    where ProdiID='$ProdiID' ";
  $r = _query($s);
  echo "<script>window.location = '?mnux=denda'; </script>";
} 

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "DftrDendaPrd" : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Setup Denda");
$gos();
?>
