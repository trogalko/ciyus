<?php
// Author: Emanuel Setio Dewo
// 29 Sept 2006
// www.sisfokampus.net

include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Presensi");

// *** Functions ***
function TampilkanAD() {
  $s = "select pm.Mhswid as MhswID, pm.Nilai as Nilai, jp.Nama
    from presensimhsw pm
    left outer join jadwal on jadwal.JadwalID = pm.jadwalID
    left outer join jenispresensi jp on pm.JenisPresensiID = jp.JenisPresensiID
    where jadwal.tahunID = '20061'
    and pm.JenisPresensiID in ('I','S')";
  $r = _query($s); $n = 0;
  $jumlahrec = _num_rows($r);
  echo "<b><p>Jumlah Record : $jumlahrec</p></b>";
  echo "<table><tr><td class=ul><a href=_proses_Presensi.php>Proses Presensi</a></td></tr></table>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>NIM</th>
    <th class=ttl>Kehadiran</th>
    <th class=ttl>Nilai</th>
    </tr>";
  while ($w = _fetch_array($r)) {
	$n++;

	echo "<tr><td class=inp>$n</td>
	  <td class=ul>$w[MhswID]</td>
	  <td class=ul>$w[Nama]
	  <td class=ul>$w[Nilai]</td>
	  </tr>";

  }
  echo "</table></p>";
}


// *** Parameters ***
$gos = empty($_REQUEST['gos'])? "TampilkanAD" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Cek Presensi");
$gos();
?>
