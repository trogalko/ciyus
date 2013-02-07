<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 18 Agustus 2006 // Selamat ulang tahun kemerdekaan RI ke-61

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Cetak Daftar Pemohon Beasiswa");

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$BeasiswaID = GetSetVar('BeasiswaID');

AmbilDataPemohon($tahun, $prodi, $BeasiswaID);

// *** Functions ***
function AmbilDataPemohon($tahun, $prodi='', $BeasiswaID) {
  global $KodeID, $arrID;
  // Ambil header
  $s0 = "select bn.BIPOTNamaID, bn.Nama
    from bipotnama bn
    where bn.DipotongBeasiswa='Y'
    order by bn.Nama";
  $r0 = _query($s0); $arrBN = array(); $arrNama = array();
  while ($w0 = _fetch_array($r0)) {
    $arrBN[] = $w0['BIPOTNamaID'];
    $arrNama[] = $w0['Nama'];
  }
  $hdrbn = '';
  for ($i = 0; $i < sizeof($arrNama); $i++) $hdrbn .= "<th class=ttl>". $arrNama[$i] ."</th>";
  // Prasyarat
  $hdrprs = '';
  if (!empty($_SESSION['BeasiswaID'])) {
    $beas = GetFields('beasiswa', 'BeasiswaID', $_SESSION['BeasiswaID'], '*');
    $prs = TRIM($beas['Prasyarat'], '~');
    if (!empty($prs)) {
      $hdrprs .= "<td class=ul>&raquo;</td>";
      $_prs = explode('~', $prs);
      foreach ($_prs as &$v) {
        $v = trim($v);
        $_v = str_replace(' ', "<br />", $v);
        $hdrprs .= "<th class=ttl>$_v</th>";
      }
    }
  }
  // Data
  $Bea = GetFields('beasiswa', 'BeasiswaID', $BeasiswaID, '*');
  $Beasiswa = $Bea['Nama'];
  $whr = '';
  if (!empty($prodi)) $whr .= "and m.ProdiID='$prodi' ";
  if (empty($prodi)) $hdrprd = "<b>Semua Program Studi</b>";
  else {
    $_prodi = GetaField('prodi', 'ProdiID', $prodi, 'Nama');
    $hdrprd = "<b>Program Studi: $_prodi</b>";
  }
  echo "<p><center><font size=+1>Daftar Pemohon<br />$Beasiswa $tahun</font><br />$hdrprd</center></p>";
  $s = "select bm.*, m.ProdiID, m.Nama
    from beasiswamhsw bm
      left outer join mhsw m on bm.MhswID=m.MhswID
    where bm.TahunID='$tahun'
      and bm.KodeID='$KodeID'
      and bm.BeasiswaID='$BeasiswaID'
      and bm.NA='N' 
      $whr
    order by m.ProdiID, m.MhswID";
  $r = _query($s); $n = 0; $_prd = "qwertyuiop0123456789"; $_tot = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  $hdr = "<tr><th class=ttl>#</th>
    <th class=ttl>N P M</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>IPS</th>
    <th class=ttl>IPK</th>
    <th class=ttl>Hutang<br />Smg Lalu</th>
    <th class=ttl>Total<br />Permohonan</th>
    $hdrbn
    $hdrprs
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($_prd != $w['ProdiID']) {
      $_prd = $w['ProdiID'];
      $prd = GetaField('prodi', 'ProdiID', $_prd, 'Nama');
      echo "<tr><td class=ul colspan=10><font size=+1>$prd</font></td></tr>";
      echo $hdr;
    }
    $DetailBeasiswa = AmbilDetailBeasiswa($arrBN, $w['BeasiswaMhswID']);
    $bsr = number_format($w['Besar']);
    $_tot += $w['Besar'];
    $Prasyarat = AmbilPrasyaratBeasiswa($_prs, $w);
    $Hutang = number_format($w['Hutang']);
    echo "<tr><td class=inp>$n</td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$w[IPS]</td>
      <td class=ul align=right>$w[IPK]</td>
      <td class=ul align=right>$Hutang</td>
      <td class=ul align=right>$bsr</td>
      $DetailBeasiswa
      $Prasyarat
      </tr>";
  }
  $tot = number_format($_tot);
  echo "</tr><td class=ul align=right colspan=3>Total :</td>
    <td class=ul align=right><b>$tot</td></tr>";
  echo "</table></p>";
}
function AmbilDetailBeasiswa($arrBN, $BMID) {
  $det = '';
  $arr = array();
  if (!empty($arrBN)) {
    $in = implode(',', $arrBN);
    $s = "select BIPOTNamaID, BeasiswaMhswDetailID, sum(Beasiswa) as JML
      from beasiswamhswdetail
      where BeasiswaMhswID=$BMID
      group by BIPOTNamaID";
    $r = _query($s);
    while ($w = _fetch_array($r)) {
      $key = array_search($w['BIPOTNamaID'], $arrBN);
      $arr[$key] = $w['JML'];
    }
    for ($i = 0; $i < sizeof($arrBN); $i++)
      $det .= "<td class=ul align=right>" . number_format($arr[$i]) . "</td>";
  }
  return $det;
}
function AmbilPrasyaratBeasiswa($arr, $w) {
  global $pref, $token;
  $ret = "";
  if (!empty($arr)) {
    $jml = sizeof($arr);
    $ret .= "<td class=ul>&raquo;</td>";
    foreach ($arr as $i=>$v) {
      $idx = $v[$i];
      $ada = $w['Prasyarat'][$i];
      $_y = ($ada == 'Y')? 'selected' : '';
      $_n = ($ada == 'Y')? '' : 'selected';
      $ret .= "<td class=ul align=center><img src='img/$ada.gif'></td>";
    }
  }
  return $ret;
}

?>
<SCRIPT>
//window.print();
</SCRIPT>
