<?php
// Author: Emanuel Setio Dewo
// 30 Sept 2006
// http://www.sisfokampus.net

include_once "mhswkeu.sav.php";
// *** Functions ***
function BPMEdt() {
  global $_bck;
  $w = GetFields('bayarmhsw', 'BayarMhswID', $_SESSION['bpmid'], '*');
  if (empty($w))
    echo ErrorMsg('BPM Tidak Ditemukan',
      "BPM dengan nomer: <font size=+1>$_SESSION[bpmid]</font> tidak ditemukan.
      <hr size=1 color=silver>
      $_bck");
  else {
    $mhsw = GetFields('mhsw', 'MhswID', $w['MhswID'], 'Nama, StatusMhswID');
    $khsid = GetaField('khs', "MhswID='$w[MhswID]' and TahunID", $w['TahunID'], 'KHSID');
    $_ad = ($w['Autodebet'] == 0)? '' : 'checked';
    $_adv = ($w['Autodebet'] == 0)? 1 : $w['Autodebet'];
    $Tanggal = GetDateOption($w['Tanggal'], 'Tanggal');
    echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST name='BPMEdt'>
    <input type=hidden name='mnux' value='bpm.edt'>
    <input type=hidden name='gos' value='BPMSav'>
    <input type=hidden name='bpmid' value='$_SESSION[bpmid]'>
    <input type=hidden name='MhswID' value='$w[MhswID]'>
    <input type=hidden name='khsid' value='$khsid'>
    <input type=hidden name='BypassMenu' value=1>
    <tr><td class=inp>No. BPM</td><td class=ul><font size=+1>$_SESSION[bpmid]</font></td></tr>
    <tr><td class=inp>Tahun</td>
      <td class=ul>$w[TahunID]</td></tr>
    <tr><td class=inp>N.P.M</td>
      <td class=ul>$w[MhswID] - <font size=+1>$mhsw[Nama]</font></td></tr>
    <tr><td class=inp>Rekening Disetor</td>
      <td class=ul><input type=text name='RekeningID' value='$w[RekeningID]' size=30 maxlength=50></td></tr>
    <tr><td class=inp>Autodebet?</td>
      <td class=ul><input type=checkbox name='Autodebet' value='$_adv' $_ad> [Autodebet ke-$_adv] Beri centang jika autodebet</td></tr>
    <tr><td class=ul colspan=2><b>Jika pembayaran manual:</td></tr>
    <tr><td class=inp>Bank</td>
      <td class=ul><input type=text name='Bank' value='$w[Bank]' size=30 maxlength=50></td></tr>
    <tr><td class=inp>Bukti Setoran</td>
      <td class=ul><input type=text name='BuktiSetoran' value='$w[BuktiSetoran]' size=30 maxlength=50></td></tr>
    <tr><td class=inp>Tanggal Setor</td>
      <td class=ul>$Tanggal</td></tr>
    <tr><td class=inp>Jumlah</td>
      <td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20 maxlength=30></td></tr>
    <tr><td class=inp>Jumlah Lain2</td>
      <td class=ul><input type=text name='JumlahLain' value='$w[JumlahLain]' size=20 maxlength=30> Tidak dihitung dalam balance</td></tr>
    <tr><td class=inp>Keterangan</td>
      <td class=ul><textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
    <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      $_bck</td></tr>
    </form></table></p>";
  }
}
function BPMSav() {
  $bpmid = $_REQUEST['bpmid'];
  $RekeningID = $_REQUEST['RekeningID'];
  $Autodebet = (empty($_REQUEST['Autodebet']))? '0' : $_REQUEST['Autodebet'];
  $Bank = $_REQUEST['Bank'];
  $BuktiSetoran = $_REQUEST['BuktiSetoran'];
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Jumlah = $_REQUEST['Jumlah']+0;
  $JumlahLain = $_REQUEST['JumlahLain']+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $MhswID = $_REQUEST['MhswID'];
  $khsid = $_REQUEST['khsid'];
  
  // Simpan
  $s = "update bayarmhsw
    set RekeningID='$RekeningID', Autodebet='$Autodebet',
    Bank='$Bank', BuktiSetoran='$BuktiSetoran', Keterangan='$Keterangan',
    Jumlah=$Jumlah, JumlahLain=$JumlahLain,
    Tanggal='$Tanggal', LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where BayarMhswID='$bpmid'";
  $r = _query($s);
  // update pembayaran
  /*$_REQUEST['khsid'] = $khsid;
  $_REQUEST['mhswid'] = $MhswID;
  $_REQUEST['pmbmhswid'] = 1;
  PrcBIPOTSesi(); */
  include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($MhswID, $khsid);
  echo "<script>window.location='?mnux=$_SESSION[bck]&gos=$_SESSION[bckgos]&MhswID=$MhswID&khsid=$khsid';</script>";
}

// *** Parameters ***
$bck = GetSetVar('bck', "bpm.inq");
$bckgos = GetSetVar('bckgos');
$bpmid = GetSetVar('bpmid');
$khsid = GetSetVar('khsid');
$MhswID = GetSetVar('MhswID');
$gos = (empty($_REQUEST['gos']))? "BPMEdt" : $_REQUEST['gos'];
$_bck = "<input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[bck]&gos=$bckgos&MhswID=$MhswID&khsid=$khsid'\">";

// *** Main ***
TampilkanJudul("Edit BPM");
if (strpos(".1.60.70.", ".$_SESSION[_LevelID].") === false)
  echo ErrorMsg("Tidak Punya Hak Akses",
    "Anda tidak memiliki hak untuk mengakses modul ini.
    <hr size=1 color=silver>
    $_bck
    ");
else {
  if (!empty($bpmid)) $gos();
}
?>
