<?php

function TampilkanFilterBlanko() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='blanko'>
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

function DftrBlankoMhsw() {
  if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and '$_SESSION[DariNPM]' <= khs.MhswID and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
    $Stat = "('A','P','C', 'T')";
  } else {
      $_npm = '';
      $Stat = "('A', 'P', 'C', 'T')";
  }
  //$TahunLalu = $_SESSION['tahun'] - 1;
  $s = "select khs.KHSID, 
    m.Nama as NamaMhsw, khs.MhswID, khs.StatusMhswID, m.BatasStudi
    from khs khs
      left outer join mhsw m on khs.MhswID=m.MhswID
    where khs.TahunID='$_SESSION[tahun]'
	  and khs.StatusMhswID in $Stat
      and m.ProgramID='$_SESSION[prid]'
      and m.ProdiID='$_SESSION[prodi]'
      $_npm
    order by khs.MhswID";
  $r = _query($s); $n = 0;
  //echo "<pre>$s</pre>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' name='Cetakblanko' method=POST>
	<input type=hidden name='mnux' value='blanko'>
	<input type=hidden name='gos' value='CetakBlankoMhsw'>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Batas Studi</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $chk = ($w['BatasStudi'] >= $_SESSION['tahun']) ? "checked" : '';
    $warn = ($w['BatasStudi'] >= $_SESSION['tahun']) ? "class=ul" : 'class=wrn';
    echo "<tr><td class=inp1>$n</td>
      <td class=inp>$w[MhswID]</td>
      <td class=ul>$w[NamaMhsw]</td>
      <td $warn align=right>$w[BatasStudi]</td>
      <td $warn><input type=checkbox name='khsid[]' value='$w[KHSID]' $chk>
        <a href='?mnux=blanko&gos=CetakBlankoMhsw&khsid[]=$w[KHSID]'>Cetak</a></td></tr>
      </tr>";
  }
  echo "<tr><td colspan=4></td>
    <td><input type=submit name='Cetak' value='Cetak'></td></tr>";
  echo "</form></table></p>";
}

function CetakBlankoMhsw() {
  // Parameter
  $khsid = array();
  $khsid = $_REQUEST['khsid'];
  $_SESSION['khsid'] = $khsid;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $jml = sizeof($khsid);
  $_SESSION['BLANKO-FILE'] = $nmf;
  $_SESSION['BLANKO-POS'] = 0;
  $_SESSION['BLANKO-MAX'] = $jml;
  // Buat file
  $f = fopen($nmf, 'w');
  fwrite($f, '');
  fclose($f);

  echo "<p>Anda akan mencetak <font size=+2>$jml</font> mahasiswa</p>";
  // buat IFrame
  echo "<p><iframe src='blanko.cetak.php' frameborder=0>
  
  </iframe></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$mhswid1 = GetSetVar('mhswid1');
$gos = (empty($_REQUEST['gos']))? "DftrBlankoMhsw" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Cetak Blanko Mahasiswa");
TampilkanFilterBlanko();
if (!empty($tahun) && !empty($prid) && !empty($prodi)) {
  $gos();
  }
?>
