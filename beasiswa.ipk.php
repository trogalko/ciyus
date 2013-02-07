<?php
// Author: Emanuel Setio Dewo
// 26 April 2006
// www.sisfokampus.net

function TampilkanFilterIPKIPS($mnux) {
  $arrUrut = array("IPS", "IPK");
  $optUrut = '';
  for ($i = 0; $i < sizeof($arrUrut); $i++) {
    $isi = $arrUrut[$i];
    $sel = ($isi == $_SESSION['IPUrut'])? 'selected' : '';
    $optUrut .= "<option value='$isi' $sel>$isi</option>";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <tr><td class=inp1><b>Filter</b></td>
    <td class=inp>Minimal IPS</td>
    <td class=ul><input type=text name='IPSMin' value='$_SESSION[IPSMin]' size=4 maxlength=4></td>
    <td class=inp>Minimal IPK</td>
    <td class=ul><input type=text name='IPKMin' value='$_SESSION[IPKMin]' size=4 maxlength=4></td>
    <td class=inp>Urutkan</td>
    <td class=ul><select name='IPUrut'>$optUrut</select></td>
    <td class=ul><input type=submit name='Simpan' value='Simpan'></td>
  </form></table></p>";
}
function DftrIPK() {
  // filter
  $arrTableUrut = array("IPS"=>"k", "IPK"=>"m");
  $whr = array();
  if (!empty($_SESSION['prid'])) $whr[] = "m.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['prodi'])) $whr[] = "m.ProdiID='$_SESSION[prodi]'";
  $_whr = implode(" and ", $whr);
  $_whr = (empty($_whr))? '' : " and ". $_whr;
  $aktif = GetArrayTable("select concat('\"', StatusMhswID, '\"') as SM
    from statusmhsw where Nilai='1' 
    order by StatusMhswID", 'SM', 'SM');
  // ambil jenis account keuangan yg bisa dikenakan potongan beasiswa
  $s0 = "select BIPOTNamaID, Nama from bipotnama where DipotongBeasiswa='Y' order by BIPOTNamaID";
  $r0 = _query($s0);
  $bn_nama = array(); $bn_id = array(); $bn_tot = array();
  while ($w0 = _fetch_array($r0)) {
    $bn_nama[] = $w0['Nama'];
    $bn_id[] = $w0['BIPOTNamaID'];
    $bn_tot[] = 0;
  }
  $whr_bn_id = implode(',', $bn_id);
  // ambil data
  $Urutan = $arrTableUrut[$_SESSION['IPUrut']].'.'.$_SESSION['IPUrut'];
  $s = "select m.Nama, m.MhswID, m.TotalSKS, m.IPK, m.ProdiID,
    k.IPS, k.TotalSKS as AmbilSKS
    from khs k 
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.TahunID='$_SESSION[tahun]'
      and k.StatusMhswID in ($aktif)
      and m.IPK >= $_SESSION[IPKMin]
      and k.IPS >= $_SESSION[IPSMin]
      $_whr
    order by m.ProdiID, $Urutan desc";
  $r = _query($s); $n = 0; $prd = '';
  $cips = ($_SESSION['IPUrut'] == 'IPS')? 'class=nac' : 'class=ul';
  $iips = ($_SESSION['IPUrut'] == 'IPS')? ' ^ ' : '';
  $cipk = ($_SESSION['IPUrut'] == 'IPK')? 'class=nac' : 'class=ul';
  $iipk = ($_SESSION['IPUrut'] == 'IPK')? ' ^ ' : '';
  // Buat header
  $bn_hdr = '';
  foreach($bn_nama as &$jdl) $bn_hdr .= "<th class=ttl>$jdl</th>";
  $hdr = "<tr><th class=ttl>#</th><th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Ambil SKS</th>
    <th class=ttl>IPS$iips</th>
    <th class=ttl>Total SKS</th>
    <th class=ttl>IPK$iipk</th>
    $bn_hdr
    <th class=ttl>Total</th>
    </tr>";
  $TOTS = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $nmprd = GetaField('prodi', 'ProdiID', $prd, 'Nama');
      echo "<tr><td class=ul colspan=5><b>$prd - $nmprd</td></tr>". $hdr;
    }
    
    $n++;
    // ambil detail biaya
    $sb = "select BIPOTNamaID, (Jumlah*Besar)+0 as JML from bipotmhsw 
      where MhswID='$w[MhswID]' and TahunID='$_SESSION[tahun]'
        and BIPOTNamaID in ($whr_bn_id)";
    $rb = _query($sb); $TOT1 = 0;
    $bn_mhsw = array();
    while ($wb = _fetch_array($rb)) {
      $bn_mhsw[$wb['BIPOTNamaID']] = $wb['JML'];
      $TOT1 += $wb['JML']+0;
    }
    $bn_str = '';
    for ($i=0; $i < sizeof($bn_id); $i++) {
      $jml = $bn_mhsw[$bn_id[$i]];
      $bn_tot[$i] += $jml;
      $bn_str .= "<td class=ul align=right>" . number_format($jml) . "</td>";
    }
    $TOTS += $TOT1;
    $bn_str .= "<td class=inp1 align=right>&nbsp;" . number_format($TOT1) . "</td>";
    // tampilkan
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$w[AmbilSKS]</td>
    <td $cips align=right>$w[IPS]</td>
    <td class=ul align=right>$w[TotalSKS]</td>
    <td $cipk align=right>$w[IPK]</td>
    $bn_str
    </tr>";
  }
  // Tampilkan Total
  echo "<tr><td class=ul colspan=7 align=right><b>TOTAL :</td>";
  foreach ($bn_tot as &$tot) echo "<td class=ul align=right><b>". number_format($tot) . "</td>";
  echo "<td class=inp1 align=right><font size=+1>" . number_format($TOTS) . "</td>";
  echo "</table></p>"; 
}

//TampilkanPilihanProdiProgram('beasiswa', '');
TampilkanTahunProdiProgram('beasiswa', '');
TampilkanFilterIPKIPS('beasiswa');
?>
