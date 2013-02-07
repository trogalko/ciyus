<?php
// Author: Emanuel Setio Dewo
// 18 June 2006
// www.sisfokampus.net

include_once "klinik.lib.php";
include_once "mhswkeu.lib.php";

// *** Functions ***
function TampilkanPembayaranKlinik() {
  $mhsw = GetFields('mhsw', 'MhswID', $_SESSION['MhswID'], '*');
  $krs = GetFields('krs', 'KRSID', $_SESSION['KRS'], '*');
  TampilkanHeaderMhswKlinik($mhsw);
  TampilkanHeaderMatakuliahKlinik($mhsw, $krs, 'klinik.biyar&gos=Biyar');
  TampilkanPembayaranKlinik1($mhsw, $krs);
}
function TampilkanHeaderMatakuliahKlinik($mhsw, $krs, $mnux='') {
  $jdwl = GetFields('jadwal', 'JadwalID', $krs['JadwalID'], '*');
  $mk = GetFields('mk', 'MKID', $krs['MKID'], '*');
  $RS = GetaField('rumahsakit', 'RSID', $jdwl['RuangID'], 'Nama');
  $TM = FormatTanggal($jdwl['TglMulai']);
  $TS = FormatTanggal($jdwl['TglSelesai']);
  $bia = number_format($jdwl['Harga']);
  $byr = number_format($krs['Bayar']);
  $sisa = $jdwl['Harga'] - $krs['Bayar'];
  $ss = number_format($sisa);
  echo "<p><table class=box>
  <tr><td class=inp>Matakuliah</td>
    <td class=ul colspan=3>$krs[MKKode] - <b>$mk[Nama]</b> ($mk[Singkatan])</td>
    <td class=inp>SKS</td>
    <td class=ul>$mk[SKS]</td>
  </tr>
  <tr><td class=inp>Rumah Sakit</td>
    <td class=ul>$jdwl[RuangID] - <b>$RS</b></td>
    <td class=inp>Dari</td>
    <td class=ul>$TM</td>
    <td class=inp>Sampai</td>
    <td class=ul>$TS</td></tr>
  <tr><td class=inp>Biaya</td>
    <td class=ul>$bia</td>
    <td class=inp>Dibayar</td>
    <td class=ul>$byr</td>
    <td class=inp>Kekurangan</td>
    <td class=ul><b>$ss</b></td>
    </tr>
  <tr><td colspan=3><input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$mnux'\"></td></tr>
  </table></p>";
}
function TampilkanPembayaranKlinik1($mhsw, $krs) {
  $byr = $krs['Harga'] - $krs['Bayar'];
  if ($byr == 0)
    echo Konfirmasi("Sudah Lunas",
      "Matakuliah ini telah lunas. Tidak perlu dibayar lagi.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
  else {
    TampilkanPembayaranKlinik2($mhsw, $krs, $byr);
    TampilkanPembayaranDariDeposit($mhsw, $krs, $byr);
  }
}
function TampilkanPembayaranKlinik2($mhsw, $krs, $byr) {
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)", 'RekeningID', '', '', 'RekeningID'); 
  CheckFormScript("RekeningID,Jumlah");
  $TGL = GetDateOption(date('Y-m-d'), 'Tanggal');
  echo "<p><font size=+2>&raquo; Pembayaran Dengan BPM</font></p>";
  echo "<blockquote><table class=box>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='klinik.biyar.trx'>
  <input type=hidden name='gos' value='BPMSav'>
  <input type=hidden name='MhswID' value='$mhsw[MhswID]'>
  <input type=hidden name='KRSID' value='$krs[KRSID]'>
  
  <tr><th class=ttl colspan=2>B P M</th></tr>
  <tr><td class=inp>Via Bank</td>
    <td class=ul><input type=text name='Bank' value='INA' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Bukti Setoran Bank</td>
    <td class=ul><input type=text name='BuktiSetoran' size=30 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal Bank</td>
    <td class=ul>$TGL</td></tr>
  <tr><td class=inp>Dibayar ke Rekening</td>
    <td class=ul><select name='RekeningID'>$optrek</select></td></tr>
  <tr><td class=inp>Jumlah</td>
    <td class=ul><input type=text name='Jumlah' value='$byr'></td></tr>
  <tr><td class=inp>Keterangan</td>
    <td class=ul><textarea name='Keterangan' cols=30 rows=4></textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=klinik.biyar&gos=Biyar'\"></td></tr>
  </form></table></blockquote>";
}
function TampilkanPembayaranDariDeposit($mhsw, $krs, $byr) {
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)", 'RekeningID', '', '', 'RekeningID'); 
  CheckFormScript("RekeningID");
  echo "<p><font size=+2>&raquo; Pembayaran Dari Deposit</font></p>";
  $s = "select dep.*, date_format(dep.Tanggal, '%d-%m-%Y') as TGL
    from depositmhsw dep
    where dep.MhswID='$mhsw[MhswID]'
      order by Tanggal";
  $r = _query($s); $n = 0;
  echo "<blockquote><table class=box>
    <tr><th class=ttl>#</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Dipakai</th>
    <th class=ttl>Sisa</th>
    <th class=ttl>Opsi</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($w['Tutup'] == 'Y') {
      $c = "class=nac";
      $byrkn = "<td class=ul>&nbsp;</td>";
    }
    else {
      $c = "class=ul";
      $byrkn = "<form action='?' method=POST onSubmit=\"return CheckForm(this)\">
        <input type=hidden name='mnux' value='klinik.biyar.trx'>
        <input type=hidden name='gos' value='DariDeposit'>
        <input type=hidden name='DMID' value='$w[DepositMhswID]'>
        <input type=hidden name='MhswID' value='$_SESSION[MhswID]'>
        <input type=hidden name='KRS' value='$krs[KRSID]'>
        <td class=ul><select name='RekeningID'>$optrek</select>
        <input type=submit name='Bayarkan' value='Bayarkan'></td>
        </form>"; 
    }
    $jml = number_format($w['Jumlah']);
    $pki = number_format($w['Dipakai']);
    $ssa = number_format($w['Jumlah'] - $w['Dipakai']);
    echo "<tr><td class=inp>$n</td>
    <td $c>$w[TGL]</td>
    <td $c align=right>$jml</td>
    <td $c align=right>$pki</td>
    <td $c align=right>$ssa</td>
    $byrkn
    </tr>";
  }
  echo "</table></blockquote>";
}
function DariDeposit() {
  $DMID = $_REQUEST['DMID'];
  $RekeningID = $_REQUEST['RekeningID'];
  $MhswID = $_REQUEST['MhswID'];
  $KRSID = $_REQUEST['KRS'];
  $krs = GetFields('krs', 'KRSID', $KRSID, '*');
  $dep = GetFields('depositmhsw', 'DepositMhswID', $DMID, '*');
  $sisa = $dep['Jumlah'] - $dep['Dipakai'];
  $biaya = $krs['Harga'] - $krs['Bayar'];
  
  $dibayar = ($sisa >= $biaya)? $biaya : $sisa;
  $_dibayar = number_format($dibayar);
  
  echo Konfirmasi("Konfirmasi Pembayaran Dari Deposit",
  "Anda akan membayarkan matakuliah <b>$krs[MKKode]</b> ini.<br />
  Jumlah yang akan dibayarkan: <b>$_dibayar</b>. <br />
  Ke rekening: <b>$RekeningID</b>.<br />
  <hr size=1 color=silver>
  Pilihan: <input type='button' name='Bayarkan' value='Bayarkan' 
  onClick=\"location='?mnux=klinik.biyar.trx&gos=DariDeposit1&DMID=$DMID&MhswID=$MhswID&KRSID=$KRSID&RekeningID=$RekeningID'\">
  <input type='button' name='Batal' value='Batal' onClick=\"location='?mnux=klinik.biyar.trx'\">");
}
function DariDeposit1() {
  $DMID = $_REQUEST['DMID'];
  $RekeningID = $_REQUEST['RekeningID'];
  $MhswID = $_REQUEST['MhswID'];
  $KRSID = $_REQUEST['KRSID'];
  $krs = GetFields('krs', 'KRSID', $KRSID, '*');
  $dep = GetFields('depositmhsw', 'DepositMhswID', $DMID, '*');
  $sisa = $dep['Jumlah'] - $dep['Dipakai'];
  $biaya = $krs['Harga'] - $krs['Bayar'];
  
  $dibayar = ($sisa >= $biaya)? $biaya : $sisa;
  $_dibayar = number_format($dibayar);
  // 1. Buat BPM
  $BPMID = GetNextBPM();
  $s = "insert into bayarmhsw (BayarMhswID, BayarMhswRef, TahunID,
    RekeningID, MhswID, TrxID, PMBMhswID,
    BuktiSetoran, Tanggal, Jumlah, Proses,
    Keterangan, LoginBuat, TanggalBuat)
    values ('$BPMID', $KRSID, '$krs[TahunID]', 
    '$RekeningID', '$MhswID', 1, 1,
    '$DMID', now(), $dibayar, 1,
    '$krs[MKKode]', '$_SESSION[_Login]', now())";
  $r = _query($s);
  // 2. Set KRS -> sudah dibayar
  $s1 = "update krs set Bayar=Bayar+$dibayar where KRSID=$KRSID";
  $r1 = _query($s1);
  // 3. Set bipotmhsw
  $s2 = "update bipotmhsw set Dibayar=Dibayar+$dibayar where BIPOTMhswRef=$KRSID";
  $r2 = _query($s2);
  // 4. Set Deposit
  $tutup = ($sisa == $dibayar)? ", Tutup='Y'" : '';
  $s3 = "update depositmhsw set Dipakai=Dipakai+$dibayar $tutup where DepositMhswID=$DMID";
  $r3 = _query($s3);
  TampilkanPembayaranKlinik();
}
function BPMSav() {
  $TGL = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $KRSID = $_REQUEST['KRSID'];
  $krs = GetFields('krs', 'KRSID', $KRSID, '*');
  $Bank = sqling($_REQUEST['Bank']);
  $RekeningID = $_REQUEST['RekeningID'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);
  //$jdwl = GetFields('jadwal', 'JadwalID', $krs['JadwalID'], '*');
  //$Harga = $jdwl['Harga'];
  $Kurang = $krs['Harga'] - $krs['Bayar'];
  $Bayar = ($Jumlah > $Kurang)? $Kurang : $Jumlah;
  $Sisa = $Jumlah - $Kurang;
  // Tambahkah BPM
  $BPM = GetNextBPM();
  $s = "insert into bayarmhsw
    (BayarMhswID, BayarMhswRef, TahunID, RekeningID, 
    MhswID, TrxID, PMBMhswID,
    Bank, BuktiSetoran, Tanggal, Jumlah,
    Keterangan, LoginBuat, TanggalBuat, Proses)
    values ('$BPM', '$krs[KRSID]', '$krs[TahunID]', '$RekeningID',
    '$krs[MhswID]', 1, 1,
    '$Bank', '$BuktiSetoran', '$TGL', $Bayar,
    '$Keterangan', '$_SESSION[_Login]', now(), 1)";
  $r = _query($s);
  // update KRS
  $s0 = "update krs set Bayar=Bayar+$Bayar where KRSID=$KRSID";
  $r0 = _query($s0);
  // update bipotmhsw
  $s1 = "update bipotmhsw set Dibayar=Dibayar+$Bayar where BIPOTMHswRef='$krs[KRSID]' ";
  $r1 = _query($s1);
  //echo "<pre>$s1</pre>";
  // Jika ada sisa, maka buat deposit mhsw
  if ($Sisa > 0) {
    $s2 = "insert into depositmhsw
    (Tanggal, MhswID, Jumlah,
    Catatan, LoginBuat, TglBuat)
    values (now(), '$krs[MhswID]', $Sisa,
    'Kelebihan pembayaran BPM: $krs[MKKode] > KRSID: $krs[KRSID]', '$_SESSION[_Login]', now())";
    $r2 = _query($s2);
  }
  //echo "<pre>$s2</pre>";
  // Hitung Ulang
  HitungBiayaBayarMhsw($MhswID, $krs['KHSID']);
  // Reload
  echo "<script>window.location = '?mnux=klinik.biyar&gos=Biyar';</script>";
}


// *** Parameters ***
$MhswID = GetSetVar('MhswID');
$KRS = GetSetVar('KRS');
$gos = (empty($_REQUEST['gos']))? "TampilkanPembayaranKlinik" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Pembayaran Mahasiswa Klinik");
if (!empty($MhswID) && !empty($KRS)) 
  $gos();
?>
