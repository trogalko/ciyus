<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2006-01-02

if (!empty($_REQUEST['rgid'])) {
  $rg = GetFields("ruang", "RuangID", $_REQUEST['rgid'], '*');
  $pr = GetFields("pmbperiod", "PMBPeriodID", $_REQUEST['pmbperiod'], "*, date_format(UjianMulai, '%d %M %Y') as TGLUJIAN");
  $c = 'class=ul';
  // Tampilkan header
  $a = "<font face=roman size=6>Ujian Saringan Masuk $_Institution</font><hr size=1>
    <p><table class=bsc cellspacing=1 cellpadding=4>
    <tr><td class=bsc>Periode</td><td $c>: <b>$pr[PMBPeriodID]</td></tr>
    <tr><td class=bsc>Tanggal Ujian</td><td $c>: <b>$pr[TGLUJIAN]</td></tr>
    <tr><td class=bsc>Ruang</td><td $c>: <b>$rg[RuangID] - $rg[Nama]</td></tr>
    </table></p>";
  
  $a .= "<table class=box cellspacing=1 cellpadding=4>";
  // Tampilkan kolom ruang
  $a .= '<tr>';
  for ($i=1; $i<=$rg['KolomUjian']; $i++) {
    $a .= "<th class=box>Kolom$i</th>";
  }
  $a .= "</tr>";
  
  // Data ruang
  $baris = ceil($rg['KapasitasUjian'] / $rg['KolomUjian']);
  // Setup array
  $data = array();
  for ($i=0; $i<=($baris*$rg['KolomUjian']); $i++) $data[$i] = '.<br>.';
  // Ambil data dari tabel
  $s = "select p.* 
    from pmb p
    where p.RuangID='$_REQUEST[rgid]'
    order by p.NomerUjian";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $NomerUjian = $w['NomerUjian'];
    $data[$NomerUjian] = "$w[PMBID]<br>$w[Nama]";
  }
  // Tampilkan isi ruang
  $nmr = 0;
  $a .= "<tr>";
  // Kolom ke-n
  for ($col=1; $col<=$rg['KolomUjian']; $col++) {
    $a .= "<td valign=top>
      <table class=bsc cellspacing=1 cellpadding=4>";
    for ($i=1; $i<=$baris; $i++) {
      $nmr++;
      $a .= "<tr><td class=box nowrap width=25>$nmr</td>
      <td class=box nowrap width=150>$data[$nmr]</td></tr>";
    }
    $a .= "</table></td>";
  }
  // End kolom ke-n
  $a .= "</tr>";
  
  echo $a."</table>";
}
else echo ErrorMsg("Tidak Dapat Mencetak",
  "Tentukan dahulu ruang yang akan dicetak.");
?>