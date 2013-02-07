<?php
// Author: Emanuel Setio Dewo
// 22 March 2006

// *** Functions ***
function TampilkanPencarian() {
  global $arrID;
  $optkelamin = GetOption2('kelamin', "concat(Kelamin, ' - ', Nama)", 'Kelamin', $_SESSION['inqKelamin'], '', 'Kelamin');
  $optjensek = GetOption2('jenissekolah', "concat(JenisSekolahID, ' - ', Nama)", 'JenisSekolahID', $_SESSION['inqJenisSekolahID'], '', 'JenisSekolahID');
  $opt = GetOption2("pmbformulir", "concat(Nama, ' (', JumlahPilihan, ' pilihan) : Rp. ', format(Harga, 0))",
    'PMBFormulirID', $_SESSION['inqpmbfid'], "KodeID='$_SESSION[KodeID]'", 'PMBFormulirID');
  $note = "<font color=red>*)</font>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='data'>
  <input type=hidden name='mnux' value='pmb.inq'>
  <input type=hidden name='gos' value='DftrPMB'>
  
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp>No PMB</td><td class=ul><input type=text name='inqPMBID' value='$_SESSION[inqPMBID]' size=20 maxlength=50> $note</td></tr>
  <tr><td class=inp>Nama</td><td class=ul><input type=text name='inqNama' value='$_SESSION[inqNama]' size=20 maxlength=50> $note</td></tr>
  <tr><td class=inp>No Bukti Setoran/Kwitansi</td>
    <td class=ul><input type=text name='inqBuktiSetoran' value='$_SESSION[inqBuktiSetoran]' size=20 maxlength=50> $note</td></tr>
  <tr><td class=inp>Jenis formulir</td><td class=ul><select name='inqpmbfid' onChange='this.form.submit()'>$opt</select></td></tr>
  <tr><td class=inp>Jenis Kelamin</td><td class=ul><select name='inqKelamin'>$optkelamin</select> $note</td></tr>
  <tr><td class=inp>Jenis Sekolah</td><td class=ul><select name='inqJenisSekolahID'>$optjensek</select></td></tr>
    
  <tr><td class=ul colspan=2><input type=submit name='Cari' value='Cari'>
    <input type=button name='Reset' value='Reset Parameter' onClick=\"location='?mnux=pmb.inq&gos=&inqPMBID=&inqNama=&inqBuktiSetoran=&inqKelamin=&inqJenisSekolahID='\">
    $note Kosongkan jika tidak ikut dicari.</td></tr>
  </form>
  </table></p>";
}
function TampilkanDetailPMB() {
  echo <<<EOF
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function DetailPMB(PMBID){
    lnk = "pmb.inq.det.php?PMBID="+PMBID;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
EOF;
}
function DftrPMB() {
  include_once "class/dwolister.class.php";
  TampilkanDetailPMB();
  $whr = array();
  //if (!empty($_SESSION['inqTahun'])) $whr[] = " p.PMBID like '$_SESSION[pmbaktif]%'";
  if (!empty($_SESSION['inqPMBID'])) $whr[] = " p.PMBID like '$_SESSION[inqPMBID]%' ";
  if (!empty($_SESSION['inqNama']))  $whr[] = " p.Nama like '%$_SESSION[inqNama]%' ";
  if (!empty($_SESSION['inqBuktiSetoran'])) $whr[] = "p.PMBFormJualID like '%$_SESSION[inqBuktiSetoran]%' ";
  if (!empty($_SESSION['inqKelamin'])) $whr[] = " p.Kelamin='$_SESSION[inqKelamin]' ";
  if (!empty($_SESSION['inqJenisSekolahID'])) $whr[] = " p.JenisSekolahID='$_SESSION[inqJenisSekolahID]' ";
  if (!empty($_SESSION['inqAgama'])) $whr[] = " p.Agama='$_SESSION[inqAgama]' ";
  if (!empty($_SESSION['inqpmbfid'])) $whr[] = " PMBFormulirID='$_SESSION[inqpmbfid]'";
  $strwhr = (empty($whr))? '' : "where " .implode(" and ", $whr);
  //echo $strwhr;

  $lst = new dwolister;
  $lst->tables = "pmb p
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    $strwhr
    order by p.PMBID";
  $lst->fields = "p.*,
    prg.Nama as PRG, prd.Nama as PRD";
  $lst->page = $_SESSION['inqPMBPage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=pmb.inq&gos=DftrPMB&inqPMBPage==PAGE='>=PAGE=</a>";

  $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>No PMB</th>
	<th class=ttl>NPM</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Program</th>
    <th class=ttl>Program Studi</th>
    <th class=ttl>No Kwitansi</th>
    <th class=ttl>Sex</th>
    <th class=ttl>Alamat</th>
    <th class=ttl>Kota</th>
    <th class=ttl>Telepon</th>
    <th class=ttl>Handphone</th>
    </tr>";
  $lst->footerfmt = "</table></p>";
//http://localhost/semarang/?mnux=pmbform&gos=PMBEdt0&md=0&pmbid=200620001
//<a href='?mnux=pmbform&gos=PMBEdt0&pmbid==PMBID=&md=0'>
  $lst->detailfmt = "<tr><td class=inp1>=NOMER=</td>
    <td class=ul nowrap><a href='javascript:DetailPMB(=PMBID=)'><img src='img/edit.png'>
    =PMBID=</a></td>
	<td class=ul>=NIM=&nbsp;</td>
    <td class=ul>=Nama=</td>
    <td class=ul>=ProgramID=-=PRG=</td>
    <td class=ul>=ProdiID=-=PRD=</td>
    <td class=ul>=PMBFormJualID=&nbsp;</td>
    <td class=ul>=Kelamin=&nbsp;</td>
    <td class=ul>=Alamat=&nbsp;</td>
    <td class=ul>=Kota= =KodePos=</td>
    <td class=ul>=Telepon=&nbsp;</td>
    <td class=ul>=Handphone=&nbsp;</td>
    </tr>";
  echo $lst->TampilkanData();
  echo "Halaman : ". $lst->TampilkanHalaman();
}


// *** Parameters ***
$inqPMBID = GetSetVar('inqPMBID');
$inqNama = GetSetVar('inqNama');
$inqBuktiSetoran = GetSetVar('inqBuktiSetoran');
$inqKelamin = GetSetVar('inqKelamin');
$inqJenisSekolahID = GetSetVar('inqJenisSekolahID');
$inqPMBPage = GetSetVar('inqPMBPage');
$inqpmbfid = GetSetVar('inqpmbfid');

$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Inquiry Peserta PMB");
TampilkanPencarian();
$gos();
?>
