<?php
// Author: Emanuel Setio Dewo, setio_dewo@sisfokampus.net
// 2005-12-30

// *** Functions ***
function TampilkanPeriodeUSM() {
  echo "<form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian'>
  Periode USM: <input type=text name='pmbperiod' value='$_SESSION[pmbperiod]' size=10 maxlength=20>
  <input type=submit name='Refresh' value='Refresh'>
  </form>";
}
function ValidPMBPeriod($pr='') {
  $period = array();
  $period['NA'] = 'Y';
  $period = GetFields('pmbperiod', 'PMBPeriodID', $pr, '*');
  if ($period['NA'] != 'N') {
    echo ErrorMsg("Periode Tidak Aktif", "Period: <b>$pr - $period[Nama]</b> tidak aktif.<br>
      Pilih period PMB yang aktif.");
  }
  return ($period['NA'] == 'N');
}
function DefUSM() {
  $ki = DaftarPesertaUSM();
  $ka = DaftarRuangUSM();
  echo "<table class=bsc cellspacing=1 cellpadding=4>
  <tr><td valign=top>$ki</td>
  <td valign=top>$ka</td></tr>
  </table>";
}
function DaftarPesertaUSM() {
  global $_defmaxrow;
  include_once "class/lister.class.php";
  // Tampilkan jenis formulir
  $optfrm = GetOption2('pmbformulir', "Nama", 'Nama', $_SESSION['pmbfid'], '', 'PMBFormulirID');
  $c = 'class=ul';
  
  $a = "<table class=box cellspacing=1 cellpadding=4>";
  $a .= "<form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian'>
  <input type=hidden name='gos' value='DefUSM'>
  <tr><td colspan=2 $c>Formulir:</td>
    <td colspan=3 $c><select name='pmbfid' onChange='this.form.submit()'>$optfrm</select>
    </td></tr></form>";
  
  $a .= "<tr><th class=ttl>#</th><th class=ttl>PMB ID</th>
  <th class=ttl>Nama</th><th class=ttl># Kursi</th></tr>";
  
  $pagefmt = "<a href='?mnux=pmbujian&SRUSM==STARTROW='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $lister = new lister;
  $lister->tables = "pmb p
    where p.PMBFormulirID='$_SESSION[pmbfid]' and 
    p.PMBPeriodID='$_SESSION[pmbperiod]' and p.RuangID is NULL
    order by p.PMBID";
	//echo $lister->tables;
  $lister->fields = "p.PMBID, p.Nama, p.NA";
  $lister->startrow = $_REQUEST['SRUSM']+0;
  $lister->maxrow = $_defmaxrow;
  $lister->headerfmt = "";
  $lister->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
      <td class=cna=NA=>=PMBID=</td>
	  <td class=cna=NA= nowrap>=Nama=</td>
	  <td class=cna=NA= nowrap>
	  <form action='?' method=GET>
	  <input type=hidden name='mnux' value='pmbujian'>
	  <input type=hidden name='gos' value='AddUSM'>
	  <input type=hidden name='pmbid' value='=PMBID='>
	  <input type=text name='pmbnmr' size=4 maxlength=4><input type=submit name='Simpan' value='>'>
	  </td>
	  </form></tr>";
  $lister->footerfmt = "</table>";
  $halaman = $lister->WritePages ($pagefmt, $pageoff);
  $TotalNews = $lister->MaxRowCount;
  $usrlist = $lister->ListIt () .
	  "<br>Halaman: $halaman<br>
	  Total: $TotalNews";
  return $a. $usrlist;
}
function DaftarRuangUSM() {
  $rg = GetFields('ruang', 'RuangID', $_SESSION['rgid'], '*');
  $opt = GetOption2('ruang', "concat(RuangID, ' - ', Nama)", 'RuangID', $_SESSION['rgid'], "UntukUSM='Y'", 'RuangID');
  // Tampilkan filter ruang
  $a = "<table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='pmbujian'>
  <tr><td class=ul colspan=$rg[KolomUjian]>Ruang USM:
    <select name='rgid' onChange='this.form.submit()'>$opt</select>
    <a href='print.php?mnux=pmbujian.prn&rgid=$_SESSION[rgid]&pmbperiod=$_SESSION[pmbperiod]' target=_blank>Cetak Ruang</a></td></tr>
  </form>";
  
  // Tampilkan kolom ruang
  if (!empty($_SESSION['rgid'])) {
    $a .= '<tr>';
    for ($i=1; $i<=$rg['KolomUjian']; $i++) {
      $a .= "<th class=ttl>Kolom$i</th>";
    }
    $a .= "</tr>";
  
    // Data ruang
    $baris = ceil($rg['KapasitasUjian'] / $rg['KolomUjian']);
    // setup array
    $data = array();
    for ($i=0; $i<=($baris*$rg['KolomUjian']); $i++) $data[$i] = '.<br>.';
    // ambil dari tabel
    $s = "select PMBID, Nama, RuangID, NomerUjian
      from pmb
      where PMBPeriodID='$_SESSION[pmbperiod]' and RuangID='$_SESSION[rgid]'
      order by NomerUjian";
    $r = _query($s);
    while ($w = _fetch_array($r)) {
      $NomerUjian = $w['NomerUjian'];
      $data[$NomerUjian] = "<a href='?mnux=pmbujian&gos=DelUSM&pmbid=$w[PMBID]'>$w[PMBID] <img src='img/del.gif' border=0></a>
      <br>$w[Nama]";
    }
  
    $nmr = 0;
    // Tampilkan isi ruang
    $a .= "<tr>";
  
    // Kolom ke-n
    for ($col=1; $col<= $rg['KolomUjian']; $col++) {
      $a .= "<td valign=top>
        <table class=bsc cellspacing=1 cellpadding=4>";
      for ($i=1; $i<=$baris; $i++) {
        $nmr++;
        $a .= "<tr><td class=inp1>$nmr</td>
        <td class=ul nowrap>$data[$nmr]</td></tr>";
      }
    $a .= "</table></td>";
  }
  // end kolom ke-n
  $a .= "</tr>";
  }
  return $a. "</table>";
}
function AddUSM() {
  if (empty($_SESSION['rgid'])) echo ErrorMsg("Error", "Tentukan ruang USM terlebih dahulu.");
  else {
    $sukses = true;
    if (empty($_REQUEST['pmbnmr'])) $NMR = GetaField("pmb", "PMBPeriodID='$_SESSION[pmbperiod]' and RuangID", $_SESSION['rgid'], "Max(NomerUjian)")+1;
    else {
      $NMR = $_REQUEST['pmbnmr']+0;
      $ada = GetFields("pmb", "PMBPeriodID='$_SESSION[pmbperiod]' and RuangID='$_SESSION[rgid]' and NomerUjian",
        $NMR, "PMBID, Nama");
      if (!empty($ada)) {
        $sukses = false;
        echo ErrorMsg("Gagal penempatan",
          "Nomer duduk: <b>$NMR</b> di ruang <b>$_SESSION[rgid]</b> telah ditempati oleh: <b>$ada[PMBID] : $ada[Nama]</b>.<br>
          Gunakan nomer duduk lain.");
      }
    }
    if ($sukses) {
      $s = "update pmb set RuangID='$_SESSION[rgid]', NomerUjian='$NMR' where PMBID='$_REQUEST[pmbid]' ";
      $r = _query($s);
    }
  }
  DefUSM();
}
function DelUSM() {
  $s = "update pmb set RuangID=NULL, NomerUjian=0 where PMBID='$_REQUEST[pmbid]'";
  $r = _query($s);
  DefUSM();
}

// *** Parameters ***
$pmbperiod = GetSetVar('pmbperiod');
$pmbfid = GetSetVar('pmbfid');
$rgid = GetSetVar('rgid');
$gos = (empty($_REQUEST['gos']))? 'DefUSM' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Ujian Saringan Masuk");
TampilkanPeriodeUSM();
if (!empty($pmbperiod)) {
  if (ValidPMBPeriod($pmbperiod)) $gos();
}
?>