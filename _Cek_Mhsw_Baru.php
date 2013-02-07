<?php
// Author: Emanuel Setio Dewo

function TampilkanPesan() {
  echo "<p>Script ini akan melakukan pengecekan data mahasiswa baru pada tabel <b>_mhsw</b>:
  Apakah data mahasiswa tersebut sudah ada di tabel <b>mhsw</b> atau belum.
  Jika sudah ada, maka data di tabel _mhsw tersebut akan dihapus.</p>
  <p>Setelah data di tabel <b>_mhsw</b> bersih dari duplikasi di <b>mhsw</b>, 
  maka Anda dapat meng-export data dari _mhsw ke mhsw dengan sql.</p>
  
  <p>Tekan tombol berikut ini untuk memproses data: 
  <input type=button name='Proses' value='Proses Data _mhsw' onClick=\"location='_Cek_Mhsw_Baru.php?gos=ProsesMhswBaru'\"></p>";
}
function ProsesMhswBaru() {
  $s = "select MhswID, Nama
    from _mhsw
    order by MhswID";
  $r = _query($s);
  echo "<p>Berikut adalah daftar Mhsw yang dihapus dari tabel <b>_mhsw</b>.</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $ada = GetaField('mhsw', "MhswID", $w['MhswID'], 'MhswID');
    if (!empty($ada)) {
      echo "<li>$w[MhswID] &raquo; $ada. $w[Nama]
      </li>";
      $s1 = "delete from _mhsw where MhswID='$ada' ";
      $r1 = _query($s1);
    }
  }
  echo "</ol>";
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Data Mhsw Baru");
TampilkanJudul("Cek Data Mahasiswa Baru");
$gos();
?>
