<?php
// Author: Emanuel Setio Dewo
// 25 Sept 2006
// www.sisfokampus.net

// *** Functions ***
function PilihanCetakTidak(){
  global $pilihanYN;
  $a = '';
  for ($i=0; $i<sizeof($pilihanYN); $i++) {
    $sel = ($i == $_SESSION['_pilihanYN'])? 'selected' : '';
    $v = explode('~', $pilihanYN[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=post>
  <input type=hidden name='mnux' value='akd.lap.mhswkss'>
  <input type=hidden name='gos' value='Daftar'>
  <tr><td class=inp>Cetak Berdasarkan: </td>
  <td class=ul><select name='_pilihanYN' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

function Daftar() {
  global $pilihanYN, $_lf;
  $_u = explode('~', $pilihanYN[$_SESSION['_pilihanYN']]);
        $_key = $_u[1];
  $cek = (empty($_key))? '' : "and k.Cetak = '$_key'";
  $s = "select k.MhswID, m.TahunID as ANGK, m.Nama, k.StatusMhswID, m.ProdiID,
    k.TotalSKS, k.JumlahMK, sm.Nilai
    from khs k
      left outer join mhsw m on k.MhswID=m.MhswID
      left outer join prodi p on m.ProdiID=p.ProdiID
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.TahunID='$_SESSION[tahun]' and p.FakultasID='$_SESSION[fakid]' $cek
	      and m.StatusMhswID not in ('L','D','K')
    order by m.ProdiID, m.TahunID, k.MhswID";
  $r = _query($s); $n = 0; $angk = '0000'; $prd = 'qwertyuiop';
  $total = _num_rows($r)+0;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(10));
  $maxkol = 114;
  $div = str_pad('-', $maxkol, '-').$_lf;
  $brs = 0;
  $maxbrs = 50;
  
  $ThnAKA = NamaTahun($_SESSION['tahun']); 
  $laporan = ($_key == 'Y') ? "Mahasiswa Cetak Kartu" : "Mahasiswa Tidak Cetak Kartu";
  $hdr = str_pad("** Daftar Mahasiswa Cetak KSS SEMESTER $ThnAKA **", $maxkol, ' ', STR_PAD_BOTH).$_lf.$_lf .
         "Laporan   : " . $laporan . $_lf .
         $div;
  fwrite($f, $hdr);
   echo "<p><a href=dwoprn.php?f=$nmf>Cetak Laporan</a></p>";
  //echo "<p><a href=?mnux=akd.lap.mhswkss&gos=daftar&prn=1>Cetak Laporan</a></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
    while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs > $maxbrs) {
      $isi .= $div;
      $isi .= str_pad("Bersambung...", $maxkol, ' ', STR_PAD_LEFT);
      //$isi .= str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf;
      $hal++; $brs =1;
	    $isi .= chr(12).$_lf;
      $isi .= $hdr;
    }
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $_prd = GetaField('prodi', 'ProdiID', $prd, 'Nama');
      echo "<tr><td class=ttl colspan=6><font size=+1>$_prd</font></td></tr>";
      echo "<tr><th class=ttl>#</th>
        <th class=ttl>N.P.M</th>
        <th class=ttl>Nama</th>
        <th class=ttl>Status</th>
        <th class=ttl>MK</th>
        <th class=ttl>SKS</th>
        </tr>";
      $isi = str_pad("FAKULTAS : ". $_prd,30) . $_lf . $div .
             str_pad("No.",5) . str_pad("NIM",10).str_pad("NAMA",30).str_pad("STATUS",8).str_pad("MK",3).str_pad("SKS",4).$_lf.$div ;
             
    }
    if ($angk != $w['ANGK']) {
      $angk = $w['ANGK'];
      echo "<tr><td class=ul colspan=6><font size=+1>$angk</font></td></tr>";
      $isi .= str_pad("»  ANGKATAN ".$angk,30) . $_lf . $_lf ;
      $n = 1;
    }
    
    echo "<tr><td class=inp>$n</td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[StatusMhswID]</td>
      <td class=ul align=right>$w[JumlahMK]</td>
      <td class=ul align=right>$w[TotalSKS]</td>
      </tr>";
      $isi .= str_pad($n.'. ',5) .
              str_pad($w['MhswID'],10) .
              str_pad($w['Nama'],30).
              str_pad($w['StatusMhswID'],6) .
              str_pad($w['JumlahMK'],4, ' ',STR_PAD_LEFT) .
              str_pad($w['TotalSKS'], 4, ' ', STR_PAD_LEFT) . $_lf;
  }
  echo "</table></p>";
  $_total = number_format($total);
  echo "<p>Total: <font size=+1>$_total</font></p>";
  fwrite($f, $isi);
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],87,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),27,' ').$_lf);
  fwrite($f, chr(12));
  fclose($f);
  //if ($_REQUEST['prn'] == 1) {
    //include_once "dwoprn.php";
    echo "<p><a href=dwoprn.php?f=$nmf>Cetak Laporan</a></p>";
    //DownloadDWOPRN($nmf);
  //}
}


// *** Parameters ***
$fakid = GetSetVar('fakid');
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$pilihanYN = array(0=>"Cetak KSS~Y", 1=>"Tidak Cetak KSS~N");
$_pilihanYN = GetSetVar('_pilihanYN', 0);
// *** Main ***
TampilkanJudul("Daftar Mahasiswa Mencetak KSS");
TampilkanPilihanFakultas('akd.lap.mhswkss', 'Daftar');
PilihanCetakTidak();
if (!empty($fakid) && !empty($tahun)) Daftar();
?>
