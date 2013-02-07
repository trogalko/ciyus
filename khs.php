<?php
// Author: Emanuel Setio Dewo
// 19 April 2006

// *** Functions ***
function TampilkanFilterKHS() {
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo <<<END
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='khs'>
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
function DftrKHSMhsw() {
  if (!empty($_SESSION['DariNPM'])) {
    $_SESSION['SampaiNPM'] = (empty($_SESSION['SampaiNPM']))? $_SESSION['DariNPM'] : $_SESSION['SampaiNPM'];
    $_npm = "and khs.MhswID >= '$_SESSION[DariNPM]'   and khs.MhswID <= '$_SESSION[SampaiNPM]' ";
  } else $_npm = '';
//  $s = "select khs.KHSID, 
//    m.Nama as NamaMhsw, khs.MhswID, khs.JumlahMK
//    from khs khs
//      left outer join mhsw m on khs.MhswID=m.MhswID
//      left outer join statusmhsw sm on khs.StatusMhswID=sm.StatusMhswID
//    where khs.TahunID='$_SESSION[tahun]'
//      and khs.ProgramID='$_SESSION[prid]'
//      and khs.ProdiID='$_SESSION[prodi]'
//      and khs.JumlahMK > 0
//      $_npm
//      and sm.Nilai=1
//    order by khs.MhswID";
  $s = "select khs.KHSID, 
    m.Nama as NamaMhsw, khs.MhswID, khs.JumlahMK
    from khs khs
      left outer join mhsw m on khs.MhswID=m.MhswID      
    where khs.TahunID='$_SESSION[tahun]'
      and khs.ProgramID='$_SESSION[prid]'
      and khs.ProdiID='$_SESSION[prodi]'
      and khs.JumlahMK > 0
      $_npm      
    order by khs.MhswID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' name='CetakKHS' method=POST>
	<input type=hidden name='mnux' value='khs'>
	<input type=hidden name='gos' value='CetakKHSMhsw'>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Jml MK</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp1>$n</td>
      <td class=inp>$w[MhswID]</td>
      <td class=ul>$w[NamaMhsw]</td>
      <td class=ul align=right>$w[JumlahMK]</td>
      <td class=ul><input type=checkbox name='khsid[]' value='$w[KHSID]' checked>
        <a href='?mnux=khs&gos=CetakKHSMhsw&khsid[]=$w[KHSID]'>Cetak</a></td></tr>
      </tr>";
  }
  echo "<tr><td colspan=4></td>
    <td><input type=submit name='Cetak' value='Cetak'></td></tr>";
  echo "</form></table></p>";
}
function CetakKHSMhsw() {
  // Parameter
  $khsid = array();
  $khsid = $_REQUEST['khsid'];
  $_SESSION['khsid'] = $khsid;
  $nmf = "tmp/$_SESSION[_Login].khs.dwoprn";
  $jml = sizeof($khsid);
  $_SESSION['KHS-FILE'] = $nmf;
  $_SESSION['KHS-POS'] = 0;
  $_SESSION['KHS-MAX'] = $jml;
  // Buat file
  $f = fopen($nmf, 'w');
  fwrite($f, '');
  fclose($f);

  echo "<p>Anda akan mencetak <font size=+2>$jml</font> mahasiswa</p>";
  // buat IFrame
  echo "<p><iframe src='khs.cetak.php' frameborder=0>
  </iframe></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
$mhswid1 = GetSetVar('mhswid1');
$gos = (empty($_REQUEST['gos']))? "DftrKHSMhsw" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Cetak KHS Mahasiswa");
TampilkanFilterKHS();
if (!empty($tahun) && !empty($prid) && !empty($prodi)) {
  $gos();
}
?>
