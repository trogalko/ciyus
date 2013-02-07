<?php
// Author: Emanuel Setio Dewo
// email: setio.dewo@gmail.com
//

function HeaderCAMA($w) {
  $bpt = GetaField('bipot', 'BIPOTID', $w['BIPOTID'], 'Nama');
  return "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul>No. PMB :</td><td class=ul><b>$w[PMBID]</b></td></tr>
    <tr><td class=ul>Nama :</td><td class=ul><b>$w[Nama]</b></td></tr>
    <tr><td class=ul>Program :</td><td class=ul>$w[ProgramID] - <b>$w[PRG]</b></td></tr>
    <tr><td class=ul>Program Studi :</td><td class=ul>$w[ProdiID] - <b>$w[PRD]</b></td></tr>
    <tr><td class=ul>Biaya & Potongan :</td><td class=ul><b>$bpt</b></td></tr>
    <tr><td class=ul>Pilihan :</td><td class=ul><a href='?mnux=mhswbaru'>Kembali ke daftar</a></td></tr>
    </table></p>";
}
function TampilkanBIPOTCAMA(&$mhsw) {
  echo "<p><a name='stp1'></a><h3>Step 1. Cek Daftar Biaya/Potongan Mahasiswa</h3></p><hr size=1 color=silver />";
  echo "<blockquote><a href='?mnux=mhswbaru.diskon&pmbid=$mhsw[PMBID]'>Input Diskon/Potongan</a></blockquote>";
  TampilkanBIPOTCAMA1($mhsw);
}
function TampilkanBIPOTCAMA1(&$mhsw) {
  $thn = $mhsw['PMBPeriodID'];
  $s = "select bp.*, bn.Nama, bn.RekeningID,
    format(bp.Besar, 0) as BSR,
    format(bp.Besar * bp.Jumlah, 0) as TTL,
    format(bp.Dibayar, 0) as BYR
    from bipotmhsw bp
      left outer join bipotnama bn on bp.BIPOTNamaID=bn.BIPOTNamaID
    where bp.PMBID='$mhsw[PMBID]' and bp.PMBMhswID=0 and bp.TahunID='$thn'
    order by bp.TrxID, bn.Nama";
  $r = _query($s);
  $n = 0;
  $ttl = 0; $byr = 0;
  
  echo "<blockquote>";
  echo "<table class=box cellspacing=1 cellpadding=4>";
  $hdr = "<tr><th class=ttl title='Prioritas'>Prio</th>
    <th class=ttl>Deskripsi</th>
    <th class=ttl>Rekening</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Besar</th>
    <th class=ttl>Total</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl>Catatan</th>
    </tr>";
  $TrxID = -256;
  while ($w = _fetch_array($r)) {
    if ($TrxID != $w['TrxID']) {
      $TrxID = $w['TrxID'];
      $NamaTrxID = GetaField('trx', "TrxID", $TrxID, "Nama");
      echo "<tr><td class=ul colspan=15><font size=+1>$NamaTrxID</font></td></tr>";
      echo $hdr;
    }
    $n++;
    $Jumlah = $w['TrxID'] * $w['Jumlah'];
    $ttl += $w['TrxID'] * $w['Jumlah'] * $w['Besar'];
    $byr += $w['Dibayar'];
    $lns = ($w['Jumlah'] * $w['Besar'] > $w['Dibayar'])? 'class=wrn' : 'class=nac';
    $c = ($w['Jumlah'] * $w['Besar'] > $w['Dibayar'])? 'class=ul' : 'class=ul';
    // <a href='?mnux=mhswbaru&gos=bipotcamaedt&pmbid=$mhsw[PMBID]&bpid=$w[BIPOTMhswID]'><img src='img/edit.png'>
    echo "<tr><td class=inp1>$n</td>
      <td class=ul title='$w[BIPOTMhswID]'>$w[Nama]</td>
      <td $c>$w[RekeningID]</td>
      <td $c align=right>$Jumlah</td>
      <td $c align=right>$w[BSR]</td>
      <td $c align=right>$w[TTL]</td>
      <td $lns align=right>$w[BYR]</td>
      <td $c align=right>$w[Catatan]&nbsp;</td>
      </tr>";
  }
  // simpan total
  if ($mhsw['TotalBiayaMhsw'] != $ttl) {
    $mhsw['TotalBiayaMhsw'] = $ttl;
    $sbiaya = "update pmb set TotalBiayaMhsw=$ttl where PMBID='$mhsw[PMBID]' ";
    $rbiaya = _query($sbiaya);
  }
  $strttl = number_format($ttl, 0);
  $strbyr = number_format($byr, 0);
  echo "<tr><td class=ul colspan=5 align=right>Total :</td>
    <td class=ul align=right><b>$strttl</b></td>
    <td class=ul align=right><b>$strbyr</b></tr></tr>";
  echo "</table></blockquote>";
}
?>
