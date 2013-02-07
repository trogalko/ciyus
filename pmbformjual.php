<?php
// Author: Emanuel Setio Dewo
// 2006-01-04

// *** Functions ***
function DispFormulir() {
  global $arrID, $pmbaktif;
  $opt = GetOption2("pmbformulir", "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $_SESSION['pmbfid'], "KodeID='$_SESSION[KodeID]'", 'PMBFormulirID');
  $summ = '';
  if (!empty($_SESSION['pmbfid'])) {
    $jml = GetaField("pmbformjual", "KodeID='$_SESSION[KodeID]' and PMBFormulirID='$_SESSION[pmbfid]' and PMBPeriodID", $pmbaktif, "count(*)");
    $summ = "<tr><td class=ul>Jumlah terjual</td><td class=ul>: <b>$jml</b></td></tr>";
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbformjual'>
  <tr><td class=ul colspan=2><strong>$arrID[Nama]</strong></td></tr>
  <tr><td class=ul>Periode/Gelombang</td><td class=ul>: $_SESSION[pmbaktif]</td></tr>
  <tr><td class=ul>Jenis Formulir</td><td class=ul>: <select name='pmbfid' onChange='this.form.submit()'>$opt</select></tr></td>
  </form>
  $summ
  <tr><td colspan=2><a href='?mnux=pmbformjual&gos=summ'>Jumlah Formulir Terjual</a> |
  <a href='?mnux=pmbformjual'>Jual Formulir</a> |
  <a href='?mnux=pmbformjual&gos=KwiEdt'>Edit Kwitansi</a> |
  <a href='?mnux=pmbformjual.daftar&gos=DftrFrm'>Daftar Form</a>
  </td></tr>
  </table></p>";
}
function TambahPenjualanFormulir() {
if (!empty($_SESSION['pmbfid'])) {
  $arrfrm = GetFields('pmbformulir', "PMBFormulirID", $_SESSION['pmbfid'], "*, format(Harga, 0) as HRG");
  $tgl = Date('d-m-Y');
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbformjual'>
  <input type=hidden name='gos' value='JLFRMSAV'>
  <input type=hidden name='pmbfid' value='$_SESSION[pmbfid]'>
  <input type=hidden name='pmbaktif' value='$_SESSION[pmbaktif]'>
  <tr><th class=ttl colspan=2>Penjualan Formulir</th></tr>
  <tr><td class=ul>Tanggal</td><td class=ul>: <strong>$tgl</strong></td></tr>
  <tr><td class=ul>Jenis Formulir</td><td class=ul>: <strong>$arrfrm[Nama]</strong></td></tr>
  <tr><td class=ul>Jumlah Pilihan</td><td class=ul>: <strong>$arrfrm[JumlahPilihan]</strong></td></tr>
  <tr><td class=ul>Harga</td><td class=ul>: Rp. <strong>$arrfrm[HRG]</strong></td></tr>
  <tr><td class=ul>No Bukti Setoran</td><td class=ul>: <input type=text name='BuktiSetoran' value='' size=40 maxlength=50></td></tr>
  <tr><td class=ul>Pembeli</td><td class=ul>: <input type=text name='Pembeli' value='' size=40 maxlength=50></td></tr>
  <tr><td class=ul>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=3></textarea></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]&pmbfid='\"></td></tr>
  </form></table>";
}
}
function JLFRMSAV() {
  global $_kwitansipmb, $_lf;
  $arrfrm = GetFields('pmbformulir', "PMBFormulirID", $_REQUEST['pmbfid'], '*, format(Harga, 0) as HRG');
  $arrfrm['Harga'] += 0;
  $next = GetNextPMBFormulirID($_REQUEST['pmbfid']);
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $Nama = sqling($_REQUEST['Pembeli']);
  $s = "insert into pmbformjual (PMBFormJualID, PMBFormulirID, PMBPeriodID, KodeID,
    Tanggal, BuktiSetoran, Nama,
    LoginBuat, TanggalBuat, Jumlah, Keterangan)
    values ('$next', '$_REQUEST[pmbfid]', '$_SESSION[pmbaktif]', '$_SESSION[KodeID]',
    now(), '$BuktiSetoran', '$Nama',
    '$_SESSION[_Login]', now(), $arrfrm[Harga], '$Keterangan')";
  $r = _query($s);
  CetakKwitansi($next);
}
function CetakKwitansi($ID) {
  global $_kwitansipmb, $_lf, $_HeaderPrn, $_EjectPrn, $_TestColumnPrn;
  // Tulis ke file
  $w = GetFields('pmbformjual', "PMBFormJualID", $ID, "*, format(Jumlah, 0) as JML, date_format(Tanggal, '%d-%m-%Y') as TGL");
  $f = fopen($_kwitansipmb, 'w');
  $thn = substr($_SESSION['pmbaktif'], 0, 4);
  $gel = substr($_SESSION['pmbaktif'], 4, 1);
  //str_pad($pmbcnt, $_PMBDigit, '0', STR_PAD_LEFT);
  fwrite($f, $_HeaderPrn);
  //fwrite($f, '         1         2         3         4         5         6         7'.$lf);
  //fwrite($f, $_TestColumnPrn);
  fwrite($f, $_lf);
  fwrite($f, str_pad(" ", 60, ' ', STR_PAD_LEFT).$w['PMBFormJualID'].$_lf.$_lf);
  fwrite($f, str_pad("No. Ref: ", 60, ' ', STR_PAD_LEFT).$w['BuktiSetoran'].$_lf);
  fwrite($f, "$_lf$_lf$_lf$_lf");
  fwrite($f, str_pad(" ", 34, ' ', STR_PAD_LEFT).$w['JML'].$_lf);
  fwrite($f, str_pad(" ", 57, ' ', STR_PAD_LEFT).$thn.'             '.$gel);
  fwrite($f, $_lf.$_lf.$_lf.$_lf.$_lf);
  fwrite($f, str_pad(' ', 64, ' ').$w['TGL'].$_lf.$_lf.$_lf);
  fwrite($f, str_pad(' ', 50).$_SESSION['_Nama'].$_lf);
  fwrite($f, $_lf.$_lf.$_lf.$_lf);
  fclose($f);
  //DownloadDWOPRN($_kwitansipmb);

  Echo Konfirmasi("Penjualan Formulir", "Data penjualan telah disimpan.<br>
    Nomer Formulir: <strong>$ID</strong><br>
    Bukti Setoran: <strong>$w[BuktiSetoran]</strong><hr size=1 color=silver>
    Pilihan: <a href='dwoprn.php?f=$_kwitansipmb'>Cetak Kwitansi</a> |
    <a href='?mnux=pmbformjual'>Jual Formulir</a>");
}
function summ() {
  $s = "select count(pfj.PMBFormJualID) as JML, pf.Nama as JNS
    from pmbformjual pfj 
    left outer join pmbformulir pf on pfj.PMBFormulirID=pf.PMBFormulirID
    where pfj.PMBPeriodID='$_SESSION[pmbaktif]'
    group by pfj.PMBFormulirID";
  $r = _query($s);
  $c = 'class=ul'; $n = 0; $tot = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th><th class=ttl>Jenis Formulir</th>
    <th class=ttl>Jumlah</th></tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $tot += $w['JML'];
    echo "<tr><td $c>$n</td>
    <td $c>$w[JNS]</td>
    <td $c align=right>$w[JML]</td>
    </tr>";
  }
  echo "<tr><td colspan=2 align=right>Total:</td><td align=right><b>$tot</td></tr>";
  echo "</table></p>";
}
function KwiEdt() {
  echo "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbformjual'>
  <input type=hidden name='gos' value='KwiEdt1'>
  <tr><td class=ttl colspan=2><strong>Edit Kwitansi</strong></td></tr>
  <tr><td class=inp1>Nomer Kwitansi</td><td class=ul>: <input type=text name='pfj' value='$_SESSION[pfj]' size=20 maxlength=50></td></tr>
  <tr><td colspan=2><input type=submit name='Cari' value='Cari'></td></tr>
  </table>";
}
function KwiEdt1() {
  $w = GetFields('pmbformjual', "PMBFormJualID", $_REQUEST['pfj'], '*');
  if (empty($w)) {
    echo ErrorMsg("Kwitansi Tidak Ditemukan",
      "Nomer Kwitansi pembelian formulir PMB tidak ditemukan.");
    KwiEdt();
  }
  else {
    $arrf = GetFields('pmbformulir', "PMBFormulirID", $w['PMBFormulirID'], '*, format(Harga, 0) as HRG');
    $opt = GetOption2("pmbformulir", "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $arrf['PMBFormulirID'], "KodeID='$_SESSION[KodeID]'", 'PMBFormulirID');
    echo "<table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='pmbformjual'>
    <input type=hidden name='gos' value='KwiEdtSav'>
    <input type=hidden name='pfj' value='$_REQUEST[pfj]'>
    <tr><td class=ttl colspan=2><strong>Edit Kwitansi</td></tr>
    <tr><td class=inp1>Nomer Kwitansi</td><td class=ul>: <strong>$_REQUEST[pfj]</strong></td></tr>
    <tr><td class=inp1>Periode/Gelombang</td><td class=ul>: <input type=text name='gel' value='$w[PMBPeriodID]' size=20 maxlength=20></td></tr>
    <tr><td class=inp1>Bukti Setoran Bank</td><td class=ul>: <input type=text name='BuktiSetoran' value='$w[BuktiSetoran]' size=40 maxlength=50><font color=red>*)</td></tr>
    <tr><td class=inp1>Jenis Formulir</td><td class=ul>: <strong>$arrf[Nama]</td></tr>
    <tr><td class=inp1>Harga</td><td class=ul>: Rp. <strong>$arrf[HRG]</strong></td></tr>
    <tr><td class=inp1>Jumlah Pilihan</td><td class=ul>: <strong>$arrf[JumlahPilihan]</strong></td></tr>
    <tr><td class=inp1><font color=red>Ganti Formulir</td><td class=ul>: <select name='pmbfid'>$opt</select></td></tr>
    <tr><td class=inp1>Keterangan</td><td class=ul valign=top>: <textarea name='Keterangan' cols=30 rows=3>$w[Keterangan]</textarea></td></tr>
    <tr><td colspan=2><input type=submit name='Ganti' value='Ganti Formulir'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=pmbformjual'\"></td></tr>
    </form></table><br>
    <font color=red>*)</font> Jika ada penambahan Nomer Bukti Setoran baru, tambahkan tanda Koma (\",\") setelah Nomer Bukti Setoran Sebelumnnya";
  }
}
function KwiEdtSav() {
  $arrf = GetFields("pmbformulir", "PMBFormulirID", $_REQUEST['pmbfid'], '*');
  $s = "update pmbformjual set PMBFormulirID='$_REQUEST[pmbfid]', Jumlah=$arrf[Harga], CetakanKe=CetakanKe+1,
    BuktiSetoran='$_REQUEST[BuktiSetoran]'
    where PMBFormJualID='$_REQUEST[pfj]'";
  $r = _query($s);
  CetakKwitansi($_REQUEST['pfj']);
}

// *** Parameters ***
$pmbfid = GetSetVar('pmbfid');
$pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
$pfj = GetSetVar('pfj');
$_SESSION['pmbaktif'] = $pmbaktif;
$gos = (empty($_REQUEST['gos']))? 'TambahPenjualanFormulir' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Penjualan Formulir");
if (!empty($pmbaktif)) { 
  DispFormulir();
  $gos();
}
else echo ErrorMsg("Gagal",
  "Tidak ada periode PMB yang aktif. Hubungi Kepala Admisi untuk mengaktifkan periode/gelombang PMB.");
?>
