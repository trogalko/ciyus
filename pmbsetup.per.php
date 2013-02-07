<?php
// Author: Emanuel Setio Dewo
// 2005-12-17

function DftrPer() {
  global $_defmaxrow, $mnux, $pref, $tokendef;
  include_once "class/lister.class.php";
  $s = "select * from pmbperiod where KodeID='$_SESSION[KodeID]' order by PMBPeriodID desc limit $_SESSION[periodpg],$_defmaxrow";

  $pagefmt = "<a href='?mnux=$mnux&$pref=Per&sub=DftrPer&SR==STARTROW='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $_KodeID = GetOption2('identitas', "concat(Kode, ' - ', Nama)", "Kode", $_SESSION['KodeID'], '', 'Kode');
  
  $lister = new lister;
  $lister->tables = "pmbperiod where KodeID='$_SESSION[KodeID]' order by PMBPeriodID desc ";
	//echo $lister->tables;
    $lister->fields = "*, 
      concat(date_format(TglMulai, '%d/%m/%Y'), '<br />', date_format(TglSelesai, '%d/%m/%Y')) as TglDaftar, 
      concat(date_format(UjianMulai, '%d/%m/%Y'), '<br />', date_format(UjianSelesai, '%d/%m/%Y')) as TglUSM,
      concat(date_format(BayarMulai, '%d/%m/%Y'), '<br />', date_format(BayarSelesai, '%d/%m/%Y')) as TglBayar ";
    $lister->startrow = $_REQUEST['SR']+0;
    $lister->maxrow = $_defmaxrow;
    $lister->headerfmt = "<table class=box cellspacing=1 cellpadding=4>
      <tr>
        <form action='?' method=POST>
        <input type=hidden name='mnux' value='$mnux'>
        <input type=hidden name='$pref' value='Per'>
        <td class=ul colspan=8>Institusi : <select name='KodeID' onChange='this.form.submit()'>$_KodeID</select></td></tr>
        </form>
      <tr>
	  <th class=ttl>#</th><th class=ttl>Kode</th>
	  <th class=ttl>Nama</th>
      <th class=ttl nowrap>Tgl Pendaftaran</th>
      <th class=ttl nowrap>Tgl Ujian</th>
      <th class=ttl nowrap>Tgl Bayar</th>
      <th class=ttl nowrap>Teliti Pembayaran utk Prodi</th>
	  <th class=ttl>NA</th>
      </tr>";
    $lister->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=><a href=\"?mnux=$mnux&$pref=Per&sub=PerEdt&md=0&pmbperiod==PMBPeriodID=\"><img src='img/edit.png' border=0>
      =PMBPeriodID=</a></td>
	  <td class=cna=NA=>=Nama=</a></td>
	  <td class=cna=NA=>=TglDaftar=</td>
	  <td class=cna=NA=>=TglUSM=</td>
	  <td class=cna=NA=>=TglBayar=</b>
	  <td class=cna=NA=>=TelitiBayarProdi=</td>
	  <td class=cna=NA=><center><img src='img/book=NA=.gif' border=0></td></tr>";
    $lister->footerfmt = "</table>";
    $halaman = $lister->WritePages ($pagefmt, $pageoff);
    $TotalNews = $lister->MaxRowCount;
    $usrlist = "<a href=\"?mnux=$mnux&$pref=Per&sub=PerEdt&md=1\">Buat Period</a>".
    $lister->ListIt () .
	  "Halaman: $halaman<br>
	  Baris: $TotalNews</p>";
    echo $usrlist;
}

function PerEdt() {
  global $mnux, $pref;
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('pmbperiod', 'PMBPeriodID', $_SESSION['pmbperiod'], '*');
    $edtpmbperiod = "<input type=hidden name='pmbperiod' value='$w[PMBPeriodID]'><b>$w[PMBPeriodID]</b>";
    $jdl = "Edit Periode PMB";
  }
  else {
    $w['PMBPeriodID'] = $_REQUEST['pmbperiod'];
    $w['Nama'] = '';
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $w['UjianMulai'] = date('Y-m-d');
    $w['UjianSelesai'] = date('Y-m-d');
    $w['BayarMulai'] = date('Y-m-d');
    $w['BayarSelesai'] = date('Y-m-d');
    $w['TelitiBayarProdi'] = '';
    $w['NA'] = 'N';
    $edtpmbperiod = "<input type=text name='pmbperiod' value='$w[PMBPeriodID]' size=20 maxlength=20>";
    $jdl = "Tambah Periode PMB";
  }
  $mul = GetDateOption($w['TglMulai'], 'TglMulai');
  $sel = GetDateOption($w['TglSelesai'], 'TglSelesai');
  $umul = GetDateOption($w['UjianMulai'], 'UjianMulai');
  $usel = GetDateOption($w['UjianSelesai'], 'UjianSelesai');
  $bmul = GetDateOption($w['BayarMulai'], 'BayarMulai');
  $bsel = GetDateOption($w['BayarSelesai'], 'BayarSelesai');
  $_prodi = GetCheckboxes('prodi', 'ProdiID', "concat(ProdiID, ' - ', Nama) as PRD", 'PRD', $w['TelitiBayarProdi']);
  $yn = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("pmbperiod,Nama");
  echo "<table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
    <input type=hidden name='mnux' value='$mnux'>
    <input type=hidden name='$pref' value='Per'>
    <input type=hidden name='sub' value='PerSav'>
    <input type=hidden name='md' value='$md'>
    <tr><th colspan=2 class=ttl>$jdl</th></tr>
    <tr><td class=inp1>Kode Periode</td><td class=ul>$edtpmbperiod</td></tr>
    <tr><td class=inp1>Nama Periode</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=30 maxlength=100></td></tr>
    <tr><td class=inp1>Tidak Aktif</td><td class=ul><input type=checkbox name='NA' value='Y' $yn></td></tr>
    
    <tr><th colspan=2 class=ttl>Pendaftaran</th></tr>
    <tr><td class=inp1>Tgl Mulai</td><td class=ul>$mul</tr>
    <tr><td class=inp1>Tgl Selesai</td><td class=ul>$sel</td></tr>
    
    <tr><th colspan=2 class=ttl>Ujian Saringan Masuk</th></tr>
    <tr><td class=inp1>Tgl Mulai</td><td class=ul>$umul</tr>
    <tr><td class=inp1>Tgl Selesai</td><td class=ul>$usel</td></tr>
    
    <tr><th colspan=2 class=ttl>Pembayaran & Pendaftaran Ulang</th></tr>
    <tr><td class=inp1>Tgl Mulai</td><td class=ul>$bmul</tr>
    <tr><td class=inp1>Tgl Selesai</td><td class=ul>$bsel</td></tr>
    <tr><td class=inp1>Terapkan pada Prodi</td><td class=ul>$_prodi</td></tr>
    
    <tr><td colspan=2><input type=submit name='Simpan' Value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$mnux&$pref=Per'\">
    </td></tr>
    </table>";
}
function PerSav() {
  $pmbperiod = $_REQUEST['pmbperiod'];
  $md = $_REQUEST['md'] +0;
  $Nama = FixQuotes($_REQUEST['Nama']);
  $na = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  $mul = "$_REQUEST[TglMulai_y]-$_REQUEST[TglMulai_m]-$_REQUEST[TglMulai_d]";
  $sel = "$_REQUEST[TglSelesai_y]-$_REQUEST[TglSelesai_m]-$_REQUEST[TglSelesai_d]";
  $umul = "$_REQUEST[UjianMulai_y]-$_REQUEST[UjianMulai_m]-$_REQUEST[UjianMulai_d]";
  $usel = "$_REQUEST[UjianSelesai_y]-$_REQUEST[UjianSelesai_m]-$_REQUEST[UjianSelesai_d]";
  $bmul = "$_REQUEST[BayarMulai_y]-$_REQUEST[BayarMulai_m]-$_REQUEST[BayarMulai_d]";
  $bsel = "$_REQUEST[BayarSelesai_y]-$_REQUEST[BayarSelesai_m]-$_REQUEST[BayarSelesai_d]";
  $_prodi = array();
  $_prodi = $_REQUEST['ProdiID'];
  $ProdiID = implode(',', $_prodi);
  
  // Simpan
  if ($md == 0) {
    $s = "update pmbperiod set Nama='$Nama', TglMulai='$mul', TglSelesai='$sel', 
      BayarMulai='$bmul', BayarSelesai='$bsel', TelitiBayarProdi='$ProdiID',
      UjianMulai='$umul', UjianSelesai='$usel', NA='$na' where PMBPeriodID='$pmbperiod'";
    _query($s);
  }
  else {
    $ada = GetFields('pmbperiod', 'PMBPeriodID', $pmbperiod, '*');
    if (empty($ada)) {
      $s = "insert into pmbperiod(PMBPeriodID, Nama, KodeID, TglMulai, TglSelesai, UjianMulai, UjianSelesai, 
        BayarMulai, BayarSelesai, TelitiBayarProdi, NA)
        Values('$pmbperiod', '$Nama', '$_SESSION[KodeID]', '$mul', '$sel', '$umul', '$usel', 
        '$bmul', '$bsel', '$ProdiID', '$na')";
      _query($s);
    }
    else echo ErrorMsg('Periode Telah Ada', "Periode <b>$pmbperiod</b> tidak dapat ditambahkan
      karena Periode tersebut sudah dibuat.");
  }
  // Tidak aktifkan
  if ($na == 'N') {
    $s = "update pmbperiod set NA='Y' where NA='N' and PMBPeriodID<>'$pmbperiod' ";
    _query($s);
  }
  DftrPer();
}
?>
