<?php
// Author: Emanuel Setio Dewo
// 19 Sept 2006

function ProsesSekarang() {
  //$arrPot = array('U'=>'5', 'S'=>'3');
  $s = "select *
    from _tagihan
    where POTNGMSTAG > 0 or POTMBMSTAG > 0
    order by NIMHSMSTAG";
  $r = _query($s); $jml = _num_rows($r);
  echo "<p>Data yg akan diproses: <font size=+1>$jml</font></p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $pot1 = number_format($w['POTNGMSTAG']);
    $pot2 = number_format($w['POTMBMSTAG']);
    echo "<li>$w[NIMHSMSTAG] &raquo; $pot1 &raquo; $pot2
    </li>";
    // ambil detail dari TRPTG
    $sp = "select * from _potongan where NIMHSTRPTG='$w[NIMHSMSTAG]' ";
    $rp = _query($sp);
    echo "<ul>";
    while ($wp = _fetch_array($rp)) {
      $jmlpot = number_format($wp['NLPOTTRPTG']);
      
      $bn = ($wp['JNPOTTRPTG'] == 'U')? 5 : 3;
      $ada = GetaField('bipotmhsw', 
        "TahunID='$wp[THSMSTRPTG]' 
        and MhswID='$wp[NIMHSTRPTG]' 
        and TrxID=-1
        and BIPOTNamaID", $bn, "BIPOTMhswID");
      if (empty($ada)) {
        $prc = "insert into bipotmhsw
          (PMBMhswID, TrxID, MhswID, TahunID, BIPOTNamaID,
          Jumlah, Besar, Dibayar, Catatan,
          LoginBuat, TanggalBuat)
          values (1, -1, '$wp[NIMHSTRPTG]', '$wp[THSMSTRPTG]', $bn,
          1, $wp[NLPOTTRPTG]+0, $wp[NLPOTTRPTG], '$wp[KETR1TRPTG] $wp[KETR2TRPTG]',
          'IMPORT-POT-20061', now())";
        $rprc = _query($prc);
      } else $prc = "(Sudah ada)";
      echo "<li>$wp[JNPOTTRPTG] &raquo; $jmlpot $prc</li>"; 
    }
    echo "</ul>";
  }
  echo "</ol>";
}

function TanyaDulu() {
  echo "<p>Script ini akan mengimport potongan dari tabel MSTAG dari program lama ke Sisfo Kampus.<br />
  Harap diperhatikan bahwa tabel temporary <b>_tagihan</b> harus sudah terisi dari tabel MSTAG.<br />
  Tekan tombol berikut ini untuk memulai proses:
  <input type=button name='Proses' value='Proses Potongan' onClick=\"location='?gos=ProsesSekarang'\">";
}

// *** Main ***
$gos = (empty($_REQUEST['gos']))? "TanyaDulu" : $_REQUEST['gos'];
include_once "sisfokampus.php";
HeaderSisfoKampus("Import Potongan Dari MSTAG");
TampilkanJudul("Import Potongan Dari MSTAG");
$gos();
?>
