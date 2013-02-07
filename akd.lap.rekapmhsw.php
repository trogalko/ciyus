<?php
// Author: Emanuel Setio Dewo
// 07 May 2006
// www.sisfokampus.net

// *** Functions ***
function Daftar() {
  $MaxThn = date('Y');
  $MinThn = $MaxThn -9;
  $arrAgama = GetArrayNilai("select concat(Agama, '~', Nama) as NILAI from agama order by Agama", "NILAI");
  $arrKelamin = GetArrayNilai("select concat(Kelamin, '~', Nama) as NILAI from kelamin order by Kelamin", "NILAI");
  $arrStatus = GetArrayNilai("select concat(StatusMhswID, '~', Nama) as NILAI from statusmhsw where StatusMhswID in ('A', 'C', 'T', 'W') order by StatusMhswID", "NILAI");
  $whr = (empty($_SESSION['fakid']))? 'where prd.FakultasID <> 9' : "where prd.FakultasID='$_SESSION[fakid]' ";
  // Tampilkan
  $s = "select prd.ProdiID, prd.Nama as PRD,
    prd.FakultasID, fak.Nama as FAK
    from prodi prd
      left outer join fakultas fak on prd.FakultasID=fak.FakultasID
    $whr
    order by prd.FakultasID, prd.ProdiID";
  $r = _query($s);
  $TOT = 0;
  $banyakagama = sizeof($arrAgama);
  $banyakkelamin = sizeof($arrKelamin);
  $banyakstatus = sizeof($arrStatus);
  echo "<p><a href='?mnux=akd.lap'>Kembali</a> | <a href='?mnux=akd.lap.rekapmhsw1&bck=akd.lap'>Cetak</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>Prd</th>
    <th class=ttl rowspan=2>Angkatan</th>
    <th class=ttl rowspan=2>Total</th>
    <th class=ttl colspan=$banyakkelamin>Kelamin</th>
    <th class=ttl colspan=$banyakagama>Agama</th>
    <th class=ttl colspan=$banyakstatus>Status</th>
    </tr>";
  $hdrkel = '';
  for ($i=0; $i< $banyakkelamin; $i++) {
    $str = explode('~', $arrKelamin[$i]);
    $hdrkel .= "<th class=ttl title='$str[1]'>$str[0]</th>";
  }
  $hdragm = '';
  for ($i=0; $i< sizeof($arrAgama); $i++) {
    $str = explode('~', $arrAgama[$i]);
    $hdragm .= "<th class=ttl title='$str[1]'>$str[0]</th>";
  }
  $hdrsta = '';
  for ($i=0; $i<sizeof($arrStatus); $i++) {
    $str = explode('~', $arrStatus[$i]);
    $hdrsta .= "<th class=ttl title='$str[1]'>$str[0]</th>";
  }
  echo "<tr>$hdrkel $hdragm $hdrsta</tr>";
  $TOT = 0; $TOTK = array(); $TOTA = array(); $TOTS = array();
  while ($w = _fetch_array($r)) {
    $arrKel1 = $arrKelamin;
    $arrAgm1 = $arrAgama;
    $arrSta1 = $arrStatus;
    $angk = AmbilDataAngkatan($w['ProdiID'], $w['PRD'], $_TOT, $MinThn, $MaxThn, $arrKel1, $arrAgm1, $arrSta1);
    $TOT += $_TOT;
    for ($i=0; $i < $banyakkelamin; $i++) $TOTK[$i] += $arrKel1[$i];
    for ($i=0; $i < sizeof($arrAgama); $i++) $TOTA[$i] += $arrAgm1[$i];
    for ($i=0; $i < $banyakstatus; $i++) $TOTS[$i] += $arrSta1[$i];
    echo $angk;
  }
  $_TOT = number_format($TOT);
  // Tampilkan jumlah total kelamin
  $totk = '';
  for ($i =0; $i < $banyakkelamin; $i++) {
    $_totk = number_format($TOTK[$i]);
    $totk .= "<td class=inp2 align=right><b>$_totk</b></td>";
  }
  // Tampilkan jumlah total agama
  $tota = '';
  for ($i = 0; $i < $banyakagama; $i++) {
    $_tota = number_format($TOTA[$i]);
    $tota .= "<td class=inp2 align=right><b>$_tota</b></td>";
  }
  // Tampilkan jumlah total status
  $tots = '';
  for ($i = 0; $i < $banyakstatus; $i++) {
    $_tots = number_format($TOTS[$i]);
    $tots .= "<td class=inp2 align=right><b>$_tots</b></td>";
  }
  echo "<tr><td colspan=2 class=inp2 align=right>Grand Total :</td>
    <td class=inp2 align=right><b>$_TOT</td>
    $totk $tota $tots
    </tr>";
  echo "</tr></table></p>";
}
function AmbilDataAngkatan($prd, $PRD, &$TOTAL, $MinThn, $MaxThn, &$arrKelamin, &$arrAgama, &$arrStatus) {
  $arr = array();
  $kel = array(); $_kel = array();
  $agm = array(); $_agm = array();
  $sta = array(); $_sta = array();
  for ($i = 0; $i < sizeof($arrKelamin); $i++) {
    $str = explode('~', $arrKelamin[$i]);
    $arrKelamin[$i] = $str[0];
  }
  for ($i = 0; $i < sizeof($arrAgama); $i++) {
    $str = explode('~', $arrAgama[$i]);
    $arrAgama[$i] = $str[0];
  }
  for ($i = 0; $i < sizeof($arrStatus); $i++) {
    $str = explode('~', $arrStatus[$i]);
    $arrStatus[$i] = $str[0];
  }
  // isikan array
  for ($i=$MinThn; $i<=$MaxThn; $i++) $arr[$i] = "<tr>";
  // Ambil data
	$kecuali = "and k.TahunID='$_SESSION[tahun]' and k.StatusMhswID in ('A', 'C', 'W', 'T')";
	//echo $PRD;
	if ($prd == 61 || $prd == 11) {
		$kecuali = "and k.TahunID='$_SESSION[tahun]' and k.StatusMhswID not in ('D', 'K', 'L')";
	} elseif (($prd == 10)){
		$kecuali = "and if (m.TahunID = '2006',k.TahunID='$_SESSION[tahun]' and k.StatusMhswID not in ('D', 'K', 'L'), k.TahunID='$_SESSION[tahun]' and k.StatusMhswID not in ('D', 'K', 'L'))";
	}
  $s0 = "select m.TahunID, m.Agama, m.Kelamin, k.StatusMhswID,
    count(k.MhswID) as JML,
    count(m.Kelamin) as JMLK,
    count(m.Agama) as JMLA,
    count(k.StatusMhswID) as JMLS
    from khs k 
      left outer join mhsw m on k.MhswID=m.MhswID
    where m.ProdiID='$prd' 
			$kecuali
    group by m.TahunID, m.Kelamin, m.Agama, m.StatusMhswID, k.MhswID";
  
	$s1 = "select m.TahunID, m.Agama, m.Kelamin, m.StatusMhswID,
    count(m.MhswID) as JML,
    count(m.Kelamin) as JMLK,
    count(m.Agama) as JMLA,
    count(m.StatusMhswID) as JMLS
    from mhsw m
    where m.ProdiID='$prd' 
    group by m.TahunID, m.Kelamin, m.Agama, m.StatusMhswID, m.MhswID";
  $r = _query($s0);
  $TOTAL = 0;
  while ($w = _fetch_array($r)) {
    $TOTAL += $w['JML'];
    $thn = substr($w['TahunID'], 0, 4);
    $keyk = array_search($w['Kelamin'], $arrKelamin);
    $keya = array_search($w['Agama'], $arrAgama);
    $keys = array_search($w['StatusMhswID'], $arrStatus);
    if ($keya === false) $keya = array_search('L', $arrAgama);
    if ($keys === false) $keys = array_search('P', $arrStatus);
    if ($thn <= $MinThn) {
      $arr[$MinThn] += $w['JML'];
      $kel[$MinThn][$keyk] += $w['JMLK'];
      $agm[$MinThn][$keya] += $w['JMLA'];
      $sta[$MinThn][$keys] += $w['JMLS'];
    }
    else {
      $arr[$thn] += $w['JML'];
      $kel[$thn][$keyk] += $w['JMLK'];
      $agm[$thn][$keya] += $w['JMLA'];
      $sta[$thn][$keys] += $w['JMLS'];
    }
    $_kel[$keyk] += $w['JMLK'];
    $_agm[$keya] += $w['JMLA'];
    $_sta[$keys] += $w['JMLS'];
  }
  // Tampilkan nilainya
  $a = '';
  for ($i = $MinThn; $i <= $MaxThn; $i++) {
    $jumlah = number_format($arr[$i]);
    $jmla = number_format($agm[$i]);
    $str = ($i == $MinThn)? "<= $i" : $i;
    // ambil data kelamin
    $kelamin = '';
    for ($j = 0; $j < sizeof($arrKelamin); $j++) {
      $_kelamin = $kel[$i][$j] +0;
      $kelamin .= "<td class=ul align=right>". $_kelamin . "</td>"; 
    }
    // ambil data agama
    $agama = '';
    for ($j = 0; $j < sizeof($arrAgama); $j++) {
      $_agama = $agm[$i][$j] +0;
      $agama .= "<td class=ul align=right>". $_agama . "</td>";
    }
    // ambil data status
    $status = '';
    for ($j = 0; $j < sizeof($arrStatus); $j++) {
      $_status = $sta[$i][$j] +0;
      $status .= "<td class=ul align=right>". $_status . "</td>";
    }
    $a .= "<tr><td></td>
      <td class=ul>$str</td>
      <td class=ul align=right>$jumlah</td>
      $kelamin $agama $status
      </tr>";
  }
  $_TOTAL = number_format($TOTAL);
  // data kelamin
  $_strkel = '';
  for ($i=0; $i<sizeof($arrKelamin); $i++) {
    $brp = $_kel[$i]+0;
    $_strkel .= "<td class=inp1 align=right>$brp</td>";
  }
  // data agama
  $_stragm = '';
  for ($i=0; $i<sizeof($arrAgama); $i++) {
    $brp = $_agm[$i]+0; 
    $_stragm .= "<td class=inp1 align=right>$brp</td>";
  }
  // data status
  $_strsta = '';
  for ($i=0; $i<sizeof($arrStatus); $i++) {
    $brp = $_sta[$i]+0;
    $_strsta .= "<td class=inp1 align=right>$brp</td>";
  }
  $arrKelamin = $_kel;
  $arrAgama = $_agm;
  $arrStatus = $_sta;
  return "<tr><td class=inp1><b>$prd</b></td>
    <td class=inp1>$PRD</td>
    <td class=inp1 align=right>$_TOTAL</td>".
    $_strkel . $_stragm . $_strsta .
    "</tr>" . $a;
}
function GetArrayNilai($sql, $nilai) {
  $a = array();
  $r = _query($sql);
  while ($w = _fetch_array($r)) {
    $a[] = $w[$nilai];
    //echo $w[$nilai]. "<br />";
  }
  return $a;
}

// *** Parameters ***
$fakid = GetSetVar('fakid');
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Daftar Mahasiswa Terdaftar KRS");
TampilkanPilihanFakultas('akd.lap.rekapmhsw', 'Daftar');
if (!empty($tahun)) Daftar();
?>
