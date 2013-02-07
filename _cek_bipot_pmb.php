<?php

function filter(){
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='KodeID' value='$arrID[Kode]'>
  <input type=hidden name='mnux' value='_cek_bipot_pmb'>
  <input type=hidden name='gos'  value='daftar'>
  <tr><td class=ul colspan=2><b>Universitas Kristen Krida Wacana</b></td></tr>
  <tr><td class=inp1>Gelombang :</td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=15></td></tr>
  <tr>
    <td class=ul colspan=3>
    <input type=submit name='Cari' value='PMBID'>

  </form></table><p>";
}

function daftar(){
  $s = "select * from pmb where
        PMBPeriodID = '$_SESSION[tahun]'
        order by LulusUjian, PMBID";
        
  $r = _query($s);
  
  $n = 0;
  echo "<table class=box cellpadding=4 cellspacing=1><tr>
        <th class=ttl>No.</th><th class=ttl>PMBID</th>
        <th class=ttl>NIM/NPM</th>
        <th class=ttl>Surat Pembayaran</th>
        <th class=ttl>Bipot</th>
        <th class=ttl>Bayar</th>
        <th class=ttl>Prodi</th>
        <th class=ttl>Program</th>
        <th class=ttl>Status</th>
        <th class=ttl>Lulus?</th></tr>";
  while($w = _fetch_array($r)){
    $n++;
    //$tot = 0;
    $bipot2 = GetBipot2($w, $w['BIPOTID'], $tot);
    $byrmhsw = TampilkanBIPOTCAMA1($w['PMBID'], $_SESSION['tahun']);
    $arr = explode('|', $byrmhsw);
    $cl = ($bipot2 != $arr[0] && $arr[0] > 0) ? "class = wrn" : "class = ul";
    echo "<tr><td class=inp>$n</td>
          <td $cl><a href=?mnux=mhswbaru&gos=ImprtPMB&trm=$w[PMBID] target=_blank>$w[PMBID]</td>
          <td $cl>$w[NIM]</td>
          <td $cl align=right>$bipot2</td>
          <td $cl align=right>$arr[0]</td>
          <td $cl align=right>$arr[1]</td>
          <td $cl align=center>$w[ProdiID]</td>
          <td $cl align=center>$w[ProgramID]</td>
          <td $cl align=right>$w[StatusAwalID]</td>
          <td $cl align = center>$w[LulusUjian]</td>
          </tr>";
  }
  
  echo "</table>";
}

function GetBipot2($pmb, $bipotid, &$total) {
  global $_lf;
  $s0 = "select b2.*, bn.Nama, bn.DefJumlah, bn.DefBesar, bn.Diskon
    from bipot2 b2
    left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$bipotid' and b2.SaatID=1
      and INSTR(b2.StatusAwalID, '.$pmb[StatusAwalID].')>0
    order by b2.Prioritas";
  $r0 = _query($s0);
  $thn = substr($w['PMBID'], 0, 4);
  $a = ''; $n = 0; $total = 0;
  while ($w0 = _fetch_array($r0)) {
    if ($w0['Jumlah'] == 0) {}
    elseif ($w0['GunakanGradeNilai'] == 'Y') {
      if (strpos($w0['GradeNilai'], ".$pmb[GradeNilai].") === false) {}
      else {
        $n++;
        $a .= InsertBIPOT($n, $w0, $tot, $bipotid, $pmb);
        $total += $tot;
      }
    }
    else {
    $n++;
    $a .= InsertBIPOT($n, $w0, $tot, $bipotid, $pmb);
    $total += $tot;
    }
  }
  $strtotal = str_pad(' ', 57, ' '). str_pad('-', 15, '-').$_lf;
  $strtotal .= str_pad('Total :', 57, ' ', STR_PAD_LEFT) .
    str_pad(number_format($total), 15, ' ', STR_PAD_LEFT);
  return number_format($total);
}

function InsertBIPOT($n, $w, &$tot, $bipotid, $pmb) {
  global $_lf;
  $a = str_pad($n, 5, ' ', STR_PAD_LEFT) .'. ';
  $a .= str_pad($w['Nama'], 30, ' ');
  if ($w['DefJumlah'] > 1) {
    // Jika BPP SKS
    if ($w['BIPOTNamaID'] == 5) {
      $detbipot = GetFields('bipot', "BIPOTID", $bipotid, "*");
      $_prd = $detbipot['ProdiID'];
      $w['DefJumlah'] = GetaField('prodi', 'ProdiID', $_prd, "DefSKS");
    }
    $det = $w['DefJumlah']." x ".number_format($w['Jumlah']);
    $jml = $w['DefJumlah'] * $w['Jumlah'];
    $a .= str_pad($det, 15, ' ', STR_PAD_LEFT);
  }
  else {
    $a .= str_pad(' ', 15, ' ');
    $jml = $w['Jumlah'];
  }
  $tot = $jml;
  $a .= str_pad(number_format($jml), 20, ' ', STR_PAD_LEFT);
  return $a . $_lf;
}

function TampilkanBIPOTCAMA1($PMBID, $thn) {
  $s = "select bp.*
    from bipotmhsw bp
    where bp.PMBID='$PMBID' and bp.TahunID='$thn' and TrxID = 1";
  $r = _query($s);
  $n = 0;
  //$ttl = 0; $byr = 0;
  //echo "<pre>$s</pre>";
  while ($w = _fetch_array($r)) {
    $ttl += $w['Jumlah'] * $w['Besar'];
    $byr += $w['Dibayar'];
  }
  $strttl = number_format($ttl, 0);
  $strbyr = number_format($byr, 0);
  return $strttl . '|' . $strbyr;
}

$tahun = GetSetVar('tahun');
TampilkanJudul("Mengecek Bipot Mahasiswa Baru");
Filter();
if (!empty($tahun)) daftar();
?>
