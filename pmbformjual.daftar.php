<?php
// Author: Emanuel Setio Dewo
// 03 Feb 2006

// *** Functions ***
function DftrFrm() {
  $snm = session_name(); $sid = session_id();
  $tgl = GetDateOption($_SESSION['pmbtgl'], 'pmbtgl');
  $_check = ($_SESSION['pmbtglfilter'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=GET>
  <input type=hidden name='mnux' value='pmbformjual.daftar'>
  <input type=hidden name='gos' value='DftrFrm'>
  <tr><td class=ul colspan=2><b>Filter</b></td></tr>
  <tr><td class=ul>Formulir Tanggal: </td><td class=ul>
    <input type=checkbox name='pmbtglfilter' value='Y' $_check>
    $tgl 
    <input type=submit name='Tampilkan' value='Tampilkan'>
    <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=pmbformjual'\"></td></tr>
  <tr><td class=ul>Cari Formulir:</td>
    <td class=ul><input type=text name='crfrm' value='$_SESSION[crfrm]'>
    <input type=submit name='crfrmkey' value='Nomer Formulir'>
    <input type=submit name='crfrmkey' value='Bukti Setoran'>
    <input type=submit name='crfrmkey' value='Reset'>
    </td></tr>
  </form></table><p>";
  
  if (!empty($_SESSION['pmbtgl'])) DftrFrm1();
}
function DftrFrm1() {
  global $_defmaxrow;
  include_once "class/lister.class.php";
  $arrNamaField = array('Nomer Formulir'=>'pfj.PMBFormJualID',
    'Bukti Setoran'=>'pfj.BuktiSetoran',
    'Reset'=>'');
  
  $pagefmt = "<a href='?mnux=pmbformjual.daftar&gos=DftrFrm&FRMSR==STARTROW='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $whr = array();
  if (!empty($_SESSION['crfrmkey'])) {
    $NamaField = $_SESSION[crfrmkey];
    $whr[] = "$arrNamaField[$NamaField] like '%$_SESSION[crfrm]%' ";
  }
  if ($_SESSION['pmbtglfilter'] == 'Y') {
    $whr[] = "pfj.Tanggal='$_SESSION[pmbtgl]'";
  }
  $strwhr = implode(' and ', $whr);
  $strwhr = (empty($strwhr))? '' : "where $strwhr";
  
  $lister = new lister;
  $lister->tables = "pmbformjual pfj
    left outer join pmbformulir pf on pfj.PMBFormulirID=pf.PMBFormulirID
    $strwhr
    order by pfj.PMBFormJualID desc";
	//echo $lister->tables;
  $lister->fields = "pfj.*, date_format(pfj.Tanggal, '%d-%m-%Y') as TGL,
    format(pfj.Jumlah, 0) as JML, pf.Nama as FRM, pf.JumlahPilihan ";
  $lister->startrow = $_REQUEST['FRMSR']+0;
  $lister->maxrow = $_defmaxrow;
  $lister->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
      <tr>
	  <th class=ttl>No.</th>
	  <th class=ttl>Gel/<br />Period</th>
	  <th class=ttl>Tanggal</th>
	  <th class=ttl>No. Kwitansi</th>
	  <th class=ttl>No. Bukti<br />Setoran</th>
	  <th class=ttl>Pembeli</th>
	  <th class=ttl>Formulir</th>
	  <th class=ttl>Jumlah<br>Pilihan</th>
	  <th class=ttl>Harga</th>

      </tr>";
//	  <th class=ttl>Ambil<br>Form?</th>
  $lister->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
	  <td class=cnn=OK=>=PMBPeriodID=</td>
	  <td class=cnn=OK=>=TGL=</td>
      <td class=cnn=OK=>=PMBFormJualID=</td>
      <td class=cnn=OK=>=BuktiSetoran=</td>
	  <td class=cnn=OK=>=Nama=</a></td>
	  <td class=cnn=OK=>=FRM=</td>
	  <td class=cnn=OK= align=center>=JumlahPilihan=</td>
	  <td class=cnn=OK= align=right>=JML=</td>
	  </tr>";
// 	  <td class=cnn=OK= align=center>
//	    <a href='?mnux=pmbformjual.daftar&gos=frmok&pfjid==PMBFormJualID=&stt==OK='><img src='img/=OK=.gif' border=0></a></td>

  $lister->footerfmt = "</table></p>";
  $halaman = $lister->WritePages ($pagefmt, $pageoff);
  $TotalNews = $lister->MaxRowCount;
  $usrlist = $lister->ListIt () .
    "<p>Halaman: $halaman<br>
    Total: $TotalNews</p>";
  echo $usrlist;
}
function frmok() {
  $s = "update pmbformjual set OK='Y' where PMBFormJualID='$_REQUEST[pfjid]' ";
  $r = _query($s);
  DftrFrm();
}

// *** Tanggal ***
if (isset($_REQUEST['Tampilkan'])) {
  $pmbtglfilter = GetSetVar('pmbtglfilter', 'Y');
  if (empty($_REQUEST['pmbtglfilter'])) {
    $pmbtglfilter = 'N';
    $_SESSION['pmbtglfilter'] = 'N';
  }
}
$pmbtgl_y = GetSetVar('pmbtgl_y', date('Y'));
$pmbtgl_m = GetSetVar('pmbtgl_m', date('m'));
$pmbtgl_d = GetSetVar('pmbtgl_d', date('d'));
$_SESSION['pmbtgl'] = "$pmbtgl_y-$pmbtgl_m-$pmbtgl_d";

// *** Filter ***
$crfrm = GetSetVar('crfrm');
$crfrmkey = GetSetVar('crfrmkey');
if ($crfrmkey == 'Reset') {
  $crfrmkey = ''; $crfrm = '';
  $_SESSION['crfrmkey'] = '';
  $_SESSION['crfrm'] = '';
}

// *** Parameters ***
$pmbfid = GetSetVar('pmbfid');
$pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
$gos = (empty($_REQUEST['gos']))? 'DftrFrm' : $_REQUEST['gos'];

// *** Main ***
$NTahunPMB = NamaTahunPMB($pmbaktif);
TampilkanJudul("Daftar Formulir $NTahunPMB");
$gos();
?>
