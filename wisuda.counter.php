<?php
// Author: Emanuel Setio Dewo
// 13 Sept 2006

// *** Functions ***
function DaftarCounter() {
  $s = "select p.*,
      i.Nama as NamaIdentitas,
      f.Nama as NamaFakultas
    from prodi p
      left outer join fakultas f on p.FakultasID=f.FakultasID
      left outer join identitas i on p.KodeID=i.Kode
    order by p.KodeID, p.FakultasID, p.ProdiID";
  $r = _query($s);
  
  $id = 'abcdqwer'; $fak = $id;
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($id != $w['KodeID']) {
      $id = $w['KodeID'];
      $_id = GetFields('identitas', "Kode", $id, "*");
      echo "<form action='?' method=POST>
        <input type=hidden name='mnux' value='wisuda.counter'>
        <input type=hidden name='gos' value='SimpanCounter'>
        <input type=hidden name='prd' value='$id'>
        <input type=hidden name='tabel' value='identitas'>
        <tr><td class=ul colspan=3><font size=+1>$w[NamaIdentitas]</font></td>
        <td class=ul><input type=text name='Start' value='$_id[StartNoIdentitas]' style='text-align:right' size=8 maxlength=8></td>
        <td class=ul><input type=text name='Nomer' value='$_id[NoIdentitas]' style='text-align:right' size=8 maxlength=8></td>
        <td class=ul><input type=submit name='Simpan' value='Simpan'><input type=reset name='Reset' value='Reset'></td>
        </tr></form>
      
        <tr><th class=ttl>&nbsp;</th>
        <th class=ttl>ID</th>
        <th class=ttl>Nama</th>
        <th class=ttl>Counter<br />Start</th>
        <th class=ttl>Kelulusan<br />Saat ini</th>
        <th class=ttl>Simpan</th>
        </tr>";
    }
    if ($fak != $w['FakultasID']) {
      $fak = $w['FakultasID'];
      echo "<tr><td class=ul align=center><img src='img/kanan.gif'></td>
        <td class=ul>$w[FakultasID]</td>
        <td class=ul colspan=4><font size=+1>$w[NamaFakultas]</font></td></tr>";
    }
    echo "<form action='?' method=POST>
      <input type=hidden name='mnux' value='wisuda.counter'>
      <input type=hidden name='gos' value='SimpanCounter'>
      <input type=hidden name='prd' value='$w[ProdiID]'>
      <input type=hidden name='tabel' value='prodi'>
      <tr><td class=ul>&nbsp;</td>
      <td class=ul width=5><img src='img/brch.gif'></td>
      <td class=ul>$w[Nama]</td>
      <td class=ul><input type=text name='Start' value='$w[StartNoProdi]' style='text-align:right' size=8 maxlength=10></td>
      <td class=ul><input type=text name='Nomer' value='$w[NoProdi]' style='text-align:right' size=8 maxlength=10></td>
      <td class=ul><input type=submit name='Simpan' value='Simpan'><input type=reset name='Reset' value='Reset'></td>
      </tr>
      </form>";
  }
  echo "</table></p>"; 
}
function SimpanCounter() {
  $prd = $_REQUEST['prd'];
  $tabel = $_REQUEST['tabel'];
  $Start = $_REQUEST['Start']+0;
  $Nomer = $_REQUEST['Nomer']+0;
  $s = ($tabel == 'prodi') ? "update prodi set StartNoProdi=$Start, NoProdi=$Nomer where ProdiID='$prd' " :
    "update identitas set StartNoIdentitas=$Start, NoIdentitas=$Nomer where Kode='$prd' ";
  $r = _query($s);
  DaftarCounter();
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "DaftarCounter" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Counter Wisuda");
$gos();
?>
