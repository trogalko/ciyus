<?php
// Author: Dewo
// 18/12/2006
// Perbaiki tahun lulus mhsw

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Perbaikan Tahun Lulus");

TampilkanJudul("Proses Perbaikan Tahun Lulus");
$gos = $_REQUEST['gos'];
if (empty($gos))
  echo Konfirmasi("Konfirmasi Proses Tahun Lulus",
    "Anda akan mulai memproses tahun lulus?<br />
    Proses ini melibatkan tabel <b>_mhswlulus</b> dan field <b>Sudah1</b>.<br />
    Pastikan tabel dan field ini sudah diset dengan benar.
    <hr size=1>
    Pilihan: <input type=button name='Proses' value='Proses' onClick=\"location='_perbaiki_tahun_lulus.php?gos=PerbaikiTahun'\">");
else $gos();
// *** Functions ***

function PerbaikiTahun() {
  $s = "select *
    from _mhswlulus
    where Sudah1=0
    order by NIMHSMSMHS";
  $r = _query($s);
  $jml = _num_rows($r);
  echo "<p>Ada: <font size=+1>$jml</font> data.</p>";
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    // update tabel mhsw
    $sx = "update mhsw set TahunKeluar='$w[REGAKMSMHS]' where MhswID='$w[NIMHSMSMHS]'";
    $rx = _query($sx);
    // update tabel _mhswlulus
    $sy = "update _mhswlulus set Sudah1=Sudah1+1 where NIMHSMSMHS='$w[NIMHSMSMHS]' ";
    $ry = _query($sy);
    echo "<li><font size=+1>$w[NIMHSMSMHS]</font> &raquo; $w[REGAKMSMHS]<br />$sx</li>";
  }
  echo "</ol>";
  echo "<p>Sudah selesai</p>";
}
?>
