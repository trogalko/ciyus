<?php
// Author: Emanuel Setio Dewo
// 2006-09-16

include_once "sisfokampus.php";
HeaderSisfoKampus("Set KHSID di KRS");

// *** Parameters ***
$tahun = GetSetVar('tahun', '20061');
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Set JadwalID, MKID, SKS di tabel _krs");
$gos();

// *** FUnctions ***
function TampilkanPesan () {
  echo "<p>Script ini akan membuat kelas serial dari kelas pertamanya di jadwal.</p>
  
  <p><form action='?'>
  <input type=hidden name='gos' value='ProsesSerial'>
  Tahun: <input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10>
  <input type=submit name='Proses' value='Proses'>
  </form>";
}
function ProsesSerial() {
  $_prodi = '10';
  $s = "select j.*
    from jadwal j
    where j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProgramID, '.REG.') >0
      and INSTR(j.ProdiID, '.$_prodi.') >0
    order by MKID, NamaKelas, HariID";
  $r = _query($s); $n = 0; $MKID = 0; $Kelas = ''; $JID = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($MKID != "$w[MKID]-$w[NamaKelas]") {
      $MKID = "$w[MKID]-$w[NamaKelas]";
      $str = "Diset &raquo; $MKID";
      $JID = $w['JadwalID'];
    }
    else {
      $ss = "update jadwal set JadwalSer=$JID where JadwalID=$w[JadwalID]";
      $rs = _query($ss);
      $str = $ss; 
    }
    echo "<tr>
    <td class=ul>$w[JadwalID]</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul>$w[HariID]</td>
    <td class=ul>$str</td>
    </tr>";
  }
  echo "</table></p>";
}
?>
