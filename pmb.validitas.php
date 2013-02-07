<?php
function TampilCariPeriode($mnux='',$gos=''){
    echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='$mnux'>
    <input type=hidden name='gos' value='$gos'>
    <tr><td class=ul>Periode PMB</td>
      <td class=ul><input type=text name='pmbperiod' value='$_SESSION[pmbperiod]' size=10 maxlength=50>
      <input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
    </form></table></p>";
}

function Daftar(){
  global $_lf;
  $s = "select p.Nama, pm.* from pmbmundur pm
        left outer join pmb p on p.PMBID = pm.PMBID
        where pm.PMBPeriodID = '$_SESSION[pmbperiod]'
        order by pm.PMBID";
  $r = _query($s);
  
  $maxcol = 120;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  $n=0; $brs=0; $maxbrs=50;
  
  $hdr  = str_pad('*** Validitas PMB Mundur ***', $maxcol, ' ', STR_PAD_BOTH) . $_lf . $_lf;
  $hdr .= "Periode : " . NamaTahunPMB($_SESSION['pmbperiod']) . $_lf;
  $hdr .= $div;
  $hdr .= str_pad('NO.', 4) .
          str_pad('PMBID', 12) .
          str_pad('NAMA', 30) .
          str_pad('TGL PROSES', 12) .
          str_pad('NO SURAT', 10) .
          str_pad('TGL SURAT', 12) .
          str_pad('ALASAN', 20) .
          str_pad('BIAYA ADMINISTRASI', 18) .
          $_lf . $div;
  fwrite($f, $hdr);
  
  while($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs >= $maxbrs) {
      fwrite($f, $div);
      fwrite($f, "Bersambung ke ...");
      fwrite($f, chr(12));
      $brs = 1;
      fwrite($f, $hdr);
    }
    $isi = str_pad($n.'.', 4) .
           str_pad($w['PMBID'], 12) .
           str_pad($w['Nama'], 30) .
           str_pad($w['TglProses'], 12) .
           str_pad($w['NoSurat'], 10) .
           str_pad($w['TglSurat'], 12) .
           str_pad($w['Alasan'], 20) .
           str_pad(number_format($w['BiayaAdministrasi']), 18, ' ', STR_PAD_LEFT) .
           $_lf;
    fwrite($f, $isi);
  }
  
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak Oleh : " . $_SESSION['_Login'] . ', ' . date("d-m-Y H:i"), 40) . str_pad("Akhir Laporan", 73, ' ', STR_PAD_LEFT) . $_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "?");
}

$pmbperiod = GetSetVar('pmbperiod');

TampilkanJudul('Validitas PMB Mundur');
TampilCariPeriode('pmb.validitas', 'Daftar');
if (!empty($pmbperiod)) {
  Daftar();
}
?>
