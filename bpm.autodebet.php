<?php
  
include "mhswkeu.lib.php";
//include "mhswkeu.det.php";
include_once "mhsw.hdr.php";

function TampilkanDaftar(){
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)","RekeningID", $_SESSION['rekid'], '', 'RekeningID');
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='bpm.autodebet'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> s/d
      <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
      </td></tr>
  <tr><td class=inp>No. Rekening</td><td class=ul><select name='rekid'>$optrek</select></td></tr>
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>
END;
}

function DftrBPMMhsw() {
  if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= khs.MhswID and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
  $s = "select khs.*, m.Nama as NamaMhsw
    from khs khs
      left outer join mhsw m on m.MhswID=khs.MhswID
	    where khs.TahunID='$_SESSION[tahun]'
			and khs.Autodebet=0
      and m.ProgramID='$_SESSION[prid]'
      and khs.ProdiID='$_SESSION[prodi]'
	  and JumlahMK > 0
		and m.TahunID >= '2002'
      $_npm
    order by khs.MhswID";
	
  $r = _query($s); $n = 0;
  
  $Kode = $_SESSION['_KodeID'];
  
  $rekid = Getafield('rekening','Nama',$Kode,'RekeningID');
  $balance = $w['Bayar'] - $w['Biaya'] + $w['Potongan'] - $w['Tarik'];
  $blc = ($balance < 0)? 'class=wrn' : 'class=ul';
  // format tampilan
  $_balance = number_format($balance);
  $BIA = number_format($w['Biaya']);
  $BYR = number_format($w['Bayar']);
  $POT = number_format($w['Potongan']);
  $TRK = number_format($w['Tarik']);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' name='Cetakblanko' method=POST>
	<input type=hidden name='mnux' value='bpm.autodebet'>
	<input type=hidden name='gos' value='CetakBPMMhsw'>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
	<th class=ttl>Nama</th>
	<th class=ttl>Total Biaya</th>
	<th class=ttl>Balance</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
	$lnk[] = "rekid=$_REQUEST[rekid]&pmbid=$_REQUEST[pmbid]&mhswid=$_REQUEST[mhswid]&khsid=$_REQUEST[khsid]&pmbmhswid=$_REQUEST[pmbmhswid]&bpmblank=$_REQUEST[bpmblank]";
	$balance = $w['Bayar'] - $w['Biaya'] + $w['Potongan'] - $w['Tarik'];
  $blc = ($balance < 0)? 'class=wrn' : 'class=ul';
  // format tampilan
  $_balance = number_format($balance);
  $BIA = number_format($w['Biaya']);
  $BYR = number_format($w['Bayar']);
  $POT = number_format($w['Potongan']);
  $TRK = number_format($w['Tarik']);
	//$sum = TampilkanSummaryKeuMhsw($w['MhswID'],$w);
    echo "<tr><td class=inp1>$n</td>
	  <td class=inp>$w[MhswID]</td>
	  
      <td class=inp>$w[NamaMhsw]</td>
	  <td class=inp>$BIA</td>
	  <td class=inp>$_balance</td>
	  <td class=ul><input type=checkbox name='khsid[]' value='$w[KHSID]' checked>
        <a href='?mnux=bpm.autodebet&gos=CetakBPMMhsw&khsid[]=$w[KHSID]'>Cetak</a></td></tr>
      </tr>";
  }
  echo "<tr><td colspan=3></td>
    <td><input type=submit name='Cetak' value='Cetak'></td></tr>";
  echo "</form></table></p>";
}

function CetakBPMMhsw() {
  // Parameter
  $khsid = array();
  $khsid = $_REQUEST['khsid'];
  $_SESSION['khsid'] = $khsid;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $jml = sizeof($khsid);
  $_SESSION['BPM-FILE'] = $nmf;
  $_SESSION['BPM-POS'] = 0;
  $_SESSION['BPM-MAX'] = $jml;
  // Buat file
  $f = fopen($nmf, 'w');
  fwrite($f, '');
  fclose($f);

  echo "<p>Anda akan mencetak <font size=+2>$jml</font> mahasiswa</p>";
  // buat IFrame
  echo "<p><iframe src='bpm.autodebet.cetak.php' frameborder=0>
  
  </iframe></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$mhswid1 = GetSetVar('mhswid1');
$gos = (empty($_REQUEST['gos']))? "DftrBPMMhsw" : $_REQUEST['gos'];
$rekid = GetSetVar('rekid');


// *** Main ***
TampilkanJudul("Cetak BPM Mahasiswa");
TampilkanDaftar();
if (!empty($tahun) && !empty($prid) && !empty($prodi)) {
  $gos();
  }
  
?>
