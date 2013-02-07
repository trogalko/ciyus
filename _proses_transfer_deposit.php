<?php
// Author: Emanuel Setio Dewo
// Proses transfer deposit

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

include_once "sisfokampus.php";
HeaderSisfoKampus("Transfer Deposit Keuangan Mhsw");
TampilkanJudul("Transfer Deposit Keuangan Mhsw");
$gos();

function TampilkanPesan() {
  echo "<p>Anda akan memproses transfer deposit mahasiswa.<br />
  Pastikan bahwa tabel <code>_transferdeposit</code> telah diisi data yg valid.<br />
  Dalam proses sistem akan mengecek apakah transfer deposit sudah pernah dilakukan atau belum.<br />
  Jika belum, maka data akan ditambahkan. Jika sudah, maka data tidak akan diproses.
  <hr size=1 color=silver>
  Pilihan: <input type=button name='proses' value='Proses Transfer Deposit' onClick=\"location='?gos=ProsesTransferDep'\">
  </p>";
}
function ProsesTransferDep() {
  $s = "select *
    from _transferdeposit
    order by TahunID, MhswID";
  $r = _query($s);
  $jml = _num_rows($r);
  echo "<p>Ada <font size=+2>$jml</font> data yg akan diproses.</p>";
  echo "<ol>";
  $bn_dep = 32; // transfer deposit. Lihat di BipotNama
  $bn_nm = "Transfer Deposit";
  while ($w = _fetch_array($r)) {
    $_dep = number_format($w['Jumlah']);
    $ada = GetFields('bipotmhsw', "TahunID='$w[TahunID]' and BIPOTNamaID=$bn_dep and MhswID",
      $w['MhswID'], "*");
    if (empty($ada)) {
      $str = "Diproses ";
      $s0 = "insert into bipotmhsw
        (PMBMhswID, MhswID, TahunID, 
        BIPOTNamaID, Nama, TrxID, Draft,
        Jumlah, Besar, Dibayar,
        Catatan, LoginBuat, TanggalBuat)
        values (1, '$w[MhswID]', '$w[TahunID]',
        $bn_dep, '$bn_nm', -1, 'N',
        1, $w[Jumlah], $w[Jumlah], 
        'import-061030', 'import-061030', now())";
      //$str .= $s0;
      $r0 = _query($s0);
    }
    else {
      $str = "<font color=red>Sudah</font>";
    }
    echo "<li>$w[MhswID] - $w[TahunID] &raquo; $_dep &raquo; $str</li>";
  }
  echo "</ol>";
}
?>
