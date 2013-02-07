<?php

function TampilkanFilterKss() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='kss.autodebet'>
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
  <tr><td class=inp>Dari NPM</td>
      <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50> s/d
      <input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50>
      </td></tr>
  
  <tr><td colspan=2><input type=submit name='Filter' value='Filter'></td></tr>
  </form></table></p>
END;
}

function TampilkanPilihanAutodebet() {
  global $_auto;
  $a = '';
  for ($i=0; $i<sizeof($_auto); $i++) {
    $sel = ($i == $_SESSION['_urutanauto'])? 'selected' : '';
    $v = explode('~', $_auto[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='kss.autodebet'>
  <input type=hidden name='gos' value='CetakKSS'>
  <tr><td class=inp>Urut berdasarkan: </td>
  <td class=ul><select name='_urutanauto' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}
  
function CetakKSS() {
  global $_auto;
  $_u = explode('~', $_auto[$_SESSION['_urutanauto']]);
        $_key = $_u[1];
  //var_dump($_key);
  $ngauto = ($_key == '3') ? '' : "and m.autodebet = '$_key'";
  if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= khs.MhswID and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
  $s = "select khs.*, sm.Nama as STT, m.Nama
    from khs khs
      left outer join statusmhsw sm on khs.StatusMhswID=sm.StatusMhswID
	  left outer join mhsw m on khs.MhswID = m.MhswID
    where khs.TahunID='$_SESSION[tahun]'
      and m.ProgramID='$_SESSION[prid]'
      and khs.ProdiID='$_SESSION[prodi]'
      $ngauto
	  and m.BatasStudi >= '$_SESSION[tahun]'
	  and khs.JumlahMK > 0
	    $_npm
    order by khs.mhswid, khs.Sesi";
  $r = _query($s);
  $n = 1;
  //echo "<pre>$s</pre>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<form action='?' method=POST>
	<input type=hidden name='mnux' value='kss.autodebet'>
	<input type=hidden name='gos' value='CetakKssMhsw'>
	<tr><th class=ttl>#</th>
	<th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Tahun Akd</th>
    <th class=ttl>SKS</th>
    <th class=ttl>MK</th>
    <th class=ttl>Status</th>
    <th class=ttl>Biaya</th>
    <th class=ttl>Bayar</th>
    <th class=ttl>Tarik</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Balance</th>
    <th class=ttl>Gagal<br />KRS</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if ($w['TahunID'] == $_SESSION['tahun']) {
      $c = "class=ul";
      //$ctk = "<a href='kss.cetak.php?tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]' target=_blank><img src='img/printer.gif'></a>";
      $ctk1 = "<a href='?mnux=kss&gos=cekkss&tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'>
        <img src='img/printer.gif'></a>";
      
    }
    else {
      $c = "class=ul";
      $ctk = "&nbsp;";
    }
	  $bia = number_format($w['Biaya']);
    $byr = number_format($w['Bayar']);
    $trk = number_format($w['Tarik']);
    $pot = number_format($w['Potongan']);
    $balance = $w['Bayar'] - $w['Biaya'] + $w['Potongan'] - $w['Tarik'];
    $bal = number_format($balance);
    $cbal = ($bal < 0)? 'class=wrn' : 'class=ul';  
	  
    //$ggl = GetaField('krs', "KHSID", $w['KHSID'], "count(KRSID)")+0;
    $ggl = ($w['TahunID'] == $_SESSION['tahun'])? GetaField("krs", "TahunID = '$_SESSION[tahun]' and NA='Y' and KHSID", $khsid, "count(KRSID)")+0 : "&nbsp;";
    $cggl = ($ggl > 0)? 'class=wrn' : 'class=ul';
    if ($w['TahunID'] == $_SESSION['tahun']) {
      $ctk = ($ggl > 0) ? "<img src='img/check.gif' title='Tidak dapat dicetak karena ada KRS gagal.'>" : 
        "<a href='kss.cetak.php?tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'><img src='img/printer.gif'></a>";
    } else $ctk = '&nbsp;';
	$_chk = GetaField('bayarmhsw','MhswID',$w['MhswID'],'Proses');
	$chk = (($_chk == 1) and ($balance == 0)) ? "checked" : '';
    echo "<tr><td class=inp>$n</td>
	  <td $c>$w[MhswID]</td>
    <td $c>$w[Nama]</td>
	  <td $c>$w[TahunID]</td>
      <td $c align=right>$w[TotalSKS]</td>
      <td $c align=right>$w[JumlahMK]</td>
      <td $c>$w[STT]</td>
      <td $c align=right>$bia</td>
      <td $c align=right>$byr</td>
      <td $c align=right>$pot</td>
      <td $c align=right>$trk</td>
      <td $cbal align=right><b>$bal</b></td>
      <td $cggl align=right><b>$ggl</b></td>
      <td $c align=center><input type=checkbox name='khsid[]' value='$w[KHSID]' $chk>$ctk</td>
      </tr>";
	  $n++;
  }
  echo "</form></table></p>";
}

function RadioAuto(){
  echo "<p><form action='?' method='post'>
        <input type=hidden name=mnux value='kss.autodebet'>
        <input type=hidden name=gos value='cetakKSS'>
        <table><tr><td class=ul><input type=radio name=radio value=1 $chkr>Autodebet</td>
        <td class=ul><input type=radio name=radio value=0 $chkr>Bukan Autodebet</td>
        <td class=ul><input type=radio name=radio value=2 $chkr>Semua</td></tr></table></form></p>";
}

function CetakKssMhsw() {
  // Parameter
  $khsid = array();
  $khsid = $_REQUEST['khsid'];
  $_SESSION['khsid'] = $khsid;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $jml = sizeof($khsid);
  $_SESSION['KSS-FILE'] = $nmf;
  $_SESSION['KSS-POS'] = 0;
  $_SESSION['KSS-MAX'] = $jml;
  // Buat file
  $f = fopen($nmf, 'w');
  fwrite($f, '');
  fclose($f);

  echo "<p>Anda akan mencetak <font size=+2>".$jml."</font> mahasiswa</p>";
  // buat IFrame
  echo "<p><iframe src='kss.autodebet.cetak.php' frameborder=0>
  </iframe></p>";
}

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');
$tahun = GetSetVar('tahun');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$mhswid1 = GetSetVar('mhswid1');
$gos = (empty($_REQUEST['gos']))? "CetakKSS" : $_REQUEST['gos'];

$_auto = array(0=>"Autodebet~Y", 1=>"Bukan Autodebet~N",2=>"Semua~3");
  
$_urutanauto = GetSetVar('_urutanauto', 0);

// *** Main ***
TampilkanJudul("Cetak Kartu Studi Semester (KSS)");
TampilkanFilterKSS();
TampilkanPilihanAutodebet();
//RadioAuto();
if (!empty($tahun) && !empty($prid) && !empty($prodi)) {
  $gos();
}
  
?>
