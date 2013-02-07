<?php
// Author: Emanuel Setio Dewo
// 03 Agustus 2006
// www.sisfokampus.net

// *** Functions ***
function TampilkanCariBPM($mnux='', $gos='') {
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr><td class=inp>Cari No BPM :</td>
    <td class=ul><input type=text name='bpmid' value='$_SESSION[bpmid]' size=20 maxlength=50>
    <input type=submit name='Cari' value='Cari'></td></tr>
  </form></table></p>";
}
function TampilkanBPM() {
  $ada = GetFields('bayarmhsw', 'BayarMhswID', $_SESSION['bpmid'], '*');
  if (!empty($ada)) {
    // Tampilkan header
    $EditBPM = '';
    $mhsw = GetFields('mhsw', 'MhswID', $ada['MhswID'], '*');
    $khsid = GetaField('khs', "MhswID='$ada[MhswID]' and TahunID", $ada['TahunID'], 'KHSID');
    if (strpos('.1.60.70.', ".$_SESSION[_LevelID].") === false) {
      $strOps = '';
    }
    else {
      $EditBPM = "<input type=button name='Edit' value='Edit BPM' onClick=\"location='?mnux=bpm.edt&bpmid=$_SESSION[bpmid]&bck=bpm.inq&bckgos='\">";
      $KeuMhsw = "<input type=button name='Keu' value='Keuangan Mhsw' onClick=\"location='?mnux=mhswkeu.det&gos=MhswKeuSesi&mhswid=$ada[MhswID]&khsid=$khsid'\">";
      $strOps = "<tr><td class=inp>Pilihan</td><td class=ul colspan=5>$EditBPM $KeuMhsw</td></tr>";
      //http://localhost/?mnux=mhswkeu.det&gos=MhswKeuSesi&mhswid=222004001&khsid=85520
    }
    $auto = ($ada['Autodebet'] > 0)? 'Y' : 'N';
    $pros = ($ada['Proses'] > 0)? 'Y' : 'N';
    $jml = ($ada['Proses'] > 0)? number_format($ada['Jumlah']) : 0;
    $jmllain = ($ada['Proses'] >0)? number_format($ada['JumlahLain']) : 0;
    $TanggalBuat = FormatTanggal($ada['TanggalBuat']);
    $TanggalBayar = FormatTanggal($ada['Tanggal']);
    $TanggalEntry = FormatTanggal($ada['TanggalEdit']);
    echo "<p><table class=box cellspacing=1 cellpadding=1>
    <tr><td class=inp>No BPM :</td>
      <td class=ul>$ada[BayarMhswID]</td>
      <td class=inp>Bank - Bukti Setoran :</td>
      <td class=ul>$ada[Bank] - $ada[BuktiSetoran]</td>
      <td class=inp>Tanggal Cetak BPM :</td>
      <td class=ul>$TanggalBuat</td></tr>
    <tr><td class=inp>Rekening :</td>
      <td class=ul>$ada[RekeningID]</td>
      <td class=inp>Tahun Akd :</td>
      <td class=ul>$ada[TahunID]</td>
      <td class=inp>Tanggal Bayar ke Bank :</td>
      <td class=ul>$TanggalBayar</td></tr>
    <tr><td class=inp>N.P.M :</td>
      <td class=ul>$ada[MhswID]</td>
      <td class=inp>Nama Mahasiswa :</td>
      <td class=ul>$mhsw[Nama]</td>
      <td class=inp>Tanggal Entry :</td>
      <td class=ul>$TanggalEntry</td></tr>
    <tr><td class=inp>Autodebet?</td>
      <td class=ul><img src='img/$auto.gif'></td>
      <td class=inp>Sudah diproses?</td>
      <td class=ul colspan=3><img src='img/$pros.gif'></td></tr>
    <tr><td class=inp>Jumlah</td>
      <td class=ul><font size=+1>$jml</font></td>
      <td class=inp>Jumlah Lain</td>
      <td class=ul colspan=3><font size=+1>$jmllain</font></td></tr>
    $strOps
    </table></p>";
    // Tampilkan detail pembayaran
    echo "<p>Berikut adalah detail pembayaran BPM ini:</p>
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
      <th class=ttl>Keterangan</th>
      <th class=ttl>Dibayarkan</th></tr>";
    $s = "select bm2.*, bn.Nama
      from bayarmhsw2 bm2
        left outer join bipotmhsw bm on bm.BIPOTMhswID=bm2.BIPOTMhswID
        left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
      where bm2.BayarMhswID='$_SESSION[bpmid]'
      order by bn.Urutan";
    $r = _query($s); $n = 0;
    while ($w = _fetch_array($r)) {
      $n++;
      $jml = number_format($w['Jumlah']);
      echo "<tr><td class=inp>$n</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$jml</td>
      </tr>";
    }
    echo "</table></p>";
  }
  else echo ErrorMsg('BPM Tidak Ditemukan',
    "BPM dengan no: <font size=+1>$_SESSION[bpmid]</font> tidak ditemukan.");
}

// *** Parameters ***
$bpmid = GetSetVar('bpmid');
$gos = (empty($_REQUEST['gos']))? 'TampilkanBPM' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Inquiry BPM");
TampilkanCariBPM('bpm.inq', 'TampilkanBPM');
if (!empty($bpmid)) $gos();
?>
