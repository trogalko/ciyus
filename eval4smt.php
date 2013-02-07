<?php
// Author: Emanuel Setio Dewo
// 22 June 2006
// www.sisfokampus.net

// *** Functions ***

function TampilkanPilihanProdiAngkatan2($mnux) {
  global $arrID;
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', "ProdiID");
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=ul colspan=4><font size=+1>$arrID[Nama]</font></td></tr>
  <tr><td class=inp>Program Studi</td>
    <td class=ul colspan=3 align=left><select name='prodi' onChange='this.form.submit()'>$optprd</select></td>
    </tr>
  <tr><td class=inp>Dari Angkatan</td>
    <td class=ul><input type=text name='dariangk' value='$_SESSION[dariangk]' size=10 maxlength=20>
    <td class=inp>Sampai Angkatan</td>
    <td class=ul><input type=text name='sampaiangk' value='$_SESSION[sampaiangk]' size=10 maxlength=20>
    <input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table></p>";
}

function TampilkanHighlightIPS() {
  $TSY = ($_SESSION['TampilkanSemua'] == 'Y')? 'checked' : '';
  $TSN = ($_SESSION['TampilkanSemua'] == 'N')? 'checked' : '';
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <tr><td class=inp>Highlight IPS di bawah :</td>
    <td class=ul><input type=text name='MinIPS' value='$_SESSION[MinIPS]' size=5 maxlength=5></td>
    <td class=inp>Highlight IPK di bawah:</td>
    <td class=ul><input type=text name='MinIPK' value='$_SESSION[MinIPK]' size=5 maxlength=5></td></tr>
  <tr>
    <td class=inp>Tampilkan hanya:</td>
    <td class=ul><input type=text name='JumlahSesi' value='$_SESSION[JumlahSesi]' size=3 maxlength=3> Semester</td>
    <td class=inp>Hightlight Total SKS di bawah:</td>
    <td class=ul><input type=text name='JumlahSKS' value='$_SESSION[JumlahSKS]' size=3 maxlength=3> SKS</td></tr>
  <tr>
    <td class=inp>Filter</td>
    <td class=ul><input type=radio name='TampilkanSemua' value='Y' $TSY> Tampilkan Semua,
      <input type=radio name='TampilkanSemua' value='N' $TSN> Hanya yang tidak memenuhi syarat</td>
    <td class=ul><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
  </form></table></p>";
}
function Evaluasi4() {
  global $_lf;
  if (!empty($_SESSION['dariangk'])) {
    $_SESSION['sampaiangk'] = (empty($_SESSION['sampaiangk']))? $_SESSION['dariangk'] : $_SESSION['sampaiangk'];
    $_npm = "and $_SESSION[dariangk] <= left(m.TahunID, 4) and left(m.TahunID, 4) <= $_SESSION[sampaiangk] ";
  } else $_npm = '';
  TampilkanHighlightIPS();
  $whr = '';
  $insesi = array();
  for ($i=1; $i<=$_SESSION['JumlahSesi']; $i++) $insesi[] = $i;
  $_insesi = implode(',', $insesi);
  if ($_SESSION['TampilkanSemua'] != 'Y') $whr .= "and m.IPK < $_SESSION[MinIPK] and m.TotalSKS < $_SESSION[JumlahSKS]"; 
  $s = "select m.MhswID, m.Nama, k.TahunID, k.IPS, k.Sesi, m.IPK, m.TotalSKS, left(m.TahunID, 4) as Angkatan
    from khs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.Sesi in ($_insesi) $_npm
      and m.ProdiID='$_SESSION[prodi]' $whr
    order by k.TahunID, k.MhswID";
  $r = _query($s); $n = 0; $m = '';
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(12));
  
  // parameter
  $mxc = 114;
  $mxb = 50;
  $brs = 1;
  $grs = str_pad('-', $mxc, '-').$_lf;
  echo "<p><a href=dwoprn.php?f=$nmf>Cetak Laporan</a></p>";
  //echo "<pre>$s</pre>";
  $arr = array();
  $NProdi = GetaField("prodi", "ProdiID", $_SESSION['prodi'], 'Nama'); 
  fwrite($f, str_pad("LAPORAN EVALUASI 4 SEMESTER PERTAMA MAHASISWA", $mxc, ' ', STR_PAD_BOTH) . $_lf . $_lf);
  fwrite($f, "JURUSAN  : $NProdi". $_lf);
  fwrite($f, "ANGKATAN : $_SESSION[dariangk] sampai $_SESSION[sampaiangk]" . $_lf);
  while ($w = _fetch_array($r)) {
    if ($m != $w['MhswID']) {
      $n++;
      $m = $w['MhswID'];
      $arr[$w['MhswID']]['Nama'] = $w['Nama'];
      $arr[$w['MhswID']]['IPK'] = $w['IPK'];
      $arr[$w['MhswID']]['TotalSKS'] = $w['TotalSKS'];
    }
    $arr[$w['MhswID']][$w['Sesi']] .= $w['IPS'];
    
  }
  $_hdrsesi = '';
  for ($i=1; $i<=$_SESSION['JumlahSesi']; $i++) {
  $_hdrsesi .= "<th class=ttl>$i</th>";
  $_hdrsesicetak .= str_pad($i, 6, ' ');
  }  
  $hdr = $grs .
         str_pad("NO.",4) . 
         str_pad("NPM",12) .
         str_pad("NAMA", 33) .
         $_hdrsesicetak .
         str_pad("TOTAL SKS", 14) .
         str_pad("IPK",4) . $_lf . $grs;
  $hdrini = "<p><table class=box cellspacing=1><tr>
    <tr><th class=ttl>#</th>
    <th class=ttl>N P M</th>
    <th class=ttl>Nama</th>
    $_hdrsesi
    <th class=ttl>Total SKS</th>
    <th class=ttl>IPK</th>
    </tr>";
  echo $hdrini;  
  $n = 0;
  foreach ($arr as $MhswID=>$det) {
    $n++; $brs++;
    $detail = '';
    for ($i = 1; $i <= $_SESSION['JumlahSesi']; $i++) {
      $c = ($det[$i] < $_SESSION['MinIPS'])? "class=oke" : "class=ul";
      $detail .= "<td $c align=right>". $det[$i] . "</td>";
      //$detailcetak .= str_pad($det[$i],6,' ',STR_PAD_LEFT);
    }
    $csks = ($det['TotalSKS'] < $_SESSION['JumlahSKS'])? "class=wrn" : "class=ul";
    $cipk = ($det['IPK'] < $_SESSION['MinIPK'])? "class=wrn" : "class=ul";    
    //echo $hdrht;
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$MhswID</td>
    <td class=ul>$det[Nama]</td>
    $detail
    <td $csks align=right>&nbsp;$det[TotalSKS]</td>
    <td $cipk align=right>&nbsp;$det[IPK]</td>
    </tr>";
    
    $isi .= str_pad($n.'. ',4) . 
           str_pad($MhswID,12) .
           str_pad($det['Nama'],30);
           for ($i = 1; $i <= $_SESSION['JumlahSesi']; $i++) {
           $isi .= str_pad($det[$i],6, ' ', STR_PAD_LEFT);
           }
           $isi .=
           str_pad($det['TotalSKS'],12, ' ', STR_PAD_LEFT) .
           str_pad($det['IPK'], 8, ' ', STR_PAD_LEFT) . $_lf;
    if ($brs >= $mxb) {
      $isi .= $grs;
      $isi .= chr(12);
      $isi .= str_pad("LAPORAN EVALUASI 4 SEMESTER PERTAMA MAHASISWA", $mxc, ' ', STR_PAD_BOTH) . $_lf . $_lf;
      $isi .= "JURUSAN  : $NProdi". $_lf;
      $isi .= "ANGKATAN : $_SESSION[dariangk] sampai $_SESSION[sampaiangk]" . $_lf;
      $isi .= $hdr;
      $brs = 1;
    }
          
  }
  fwrite($f, $hdr);
  fwrite($f, $isi);
  fwrite($f, $grs);
  fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " . date("d-m-Y H:i"), 100) . str_pad("Akhir Laporan", 100).$_lf);
  fwrite($f, chr(12));
  fclose($f);
  echo "</table></p>";
}


// *** Parameters ***
$prodi = GetSetVar('prodi');
$dariangk = GetSetVar('dariangk');
$sampaiangk = GetSetVar('sampaiangk');
$MinIPS = GetSetVar('MinIPS', 2);
$MinIPK = GetSetVar('MinIPK', 2);
$JumlahSKS = GetSetVar('JumlahSKS', 15);
$JumlahSesi = GetSetVar('JumlahSesi', 4);
$TampilkanSemua = GetSetVar('TampilkanSemua', 'N');
$gos = (empty($_REQUEST['gos']))? "Evaluasi4" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Evaluasi 4 Semester Pertama Mahasiswa");
TampilkanPilihanProdiAngkatan2('eval4smt');
if (!empty($prodi) && !empty($dariangk)) $gos();
?>
