<?php
// Betulkan KRS
// 17/01/2007
include_once "sisfokampus.php";
HeaderSisfoKampus("Fix KRS Serial");

$tahun = '20062';
$prodi = '.10.';

$s = "select *
  from jadwal
  where JadwalSer > 0
    and TahunID='$tahun'
    and ProdiID='$prodi'
  group by JadwalSer";
$r = _query($s);
$tot = 0;
echo "<ol>";
while ($w = _fetch_array($r)) {
  $jdwl = GetFields('jadwal', "JadwalID", $w['JadwalSer'], "*");
  echo "<li>$jdwl[MKKode], $jdwl[JumlahMhswKRS]<br />";
  // Jika ada lebih dari 0 mhsw
  if ($jdwl['JumlahMhswKRS'] > 0) {
    $sm = "select * from krstemp where JadwalID=$jdwl[JadwalID] order by MhswID";
    $rm = _query($sm);
    while ($wm = _fetch_array($rm)) {
      // Ada mhsw yg mengambilnya. Cek dulu serialnya.
      $s_serial = "select * from jadwal where JadwalSer=$jdwl[JadwalID] order by HariID";
      $r_serial = _query($s_serial);
      while ($w_serial = _fetch_array($r_serial)) {
        $sdh = GetaField("krstemp", "TahunID='$tahun' and MhswID='$wm[MhswID]' and JadwalID",
          $w_serial['JadwalID'], "count(*)")+0;
        if ($sdh == 0) {
          $skrs = "insert into krstemp (KHSID, MhswID,
            TahunID, JadwalID,
            MKID, MKKode, SKS, HargaStandar, Harga,
            StatusKRSID, NA, CatatanError,
            LoginBuat, TanggalBuat)
            values ('$wm[KHSID]', '$wm[MhswID]',
            '$wm[TahunID]', '$w_serial[JadwalID]',
            '$w_serial[MKID]', '$w_serial[MKKode]', 0, '$w_serial[HargaStandar]', '$w_serial[Harga]',
            'S', 'N', 'SERIAL',
            'PROSES SERIAL', now())";
          $rkrs = _query($skrs);
          echo "- $wm[MhswID]: Diproses<br />";
        }
      }
    }
  }
}
echo "</ol>";
?>
