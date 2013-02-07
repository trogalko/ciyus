<?php
// Author: Emanuel Setio Dewo
// 19 June 2006
// www.sisfokampus.net

// *** Functions ***
function DftrMhswKlinik() {
  include "sisfokampus.php";
  HeaderSisfoKampus();
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal j
    left outer join rumahsakit rs on j.RuangID=rs.RSID",
    "JadwalID", $JadwalID,
    "j.*, rs.Nama as NamaRS");
  // tampilkan header
  echo "<p><center><font size=+1>Daftar Peserta Matakuliah</font><br />
    <font size=+2>$jdwl[Nama] ($jdwl[MKKode])</font></center></p>";
  $TM = FormatTanggal($jdwl['TglMulai']);
  $TS = FormatTanggal($jdwl['TglSelesai']);
  echo "<p><center>Rumah Sakit: <font size=+1>$jdwl[NamaRS]</font><br />
    Periode: <font size=+1>$TM ~ $TS</font></center></p>";
  $s = "select k.KRSID, k.MhswID, m.Nama
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.JadwalID='$JadwalID'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<p align=center><table class=box cellspacing=1>
    <tr><th class=ttl>No</th>
    <th class=ttl>N P M</th>
    <th class=ttl>Nama Mahasiswa</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    </tr>";
  }
  echo "</table></p>";
  echo "<p align=center>Total Mahasiswa: <font size=+1>$n</font></p>";
  echo "<script>window.print()</script>";
}
function CetakNilaiKlinik() {
  include "sisfokampus.php";
  HeaderSisfoKampus();
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal j
    left outer join rumahsakit rs on j.RuangID=rs.RSID",
    "JadwalID", $JadwalID,
    "j.*, rs.Nama as NamaRS");
  // tampilkan header
  echo "<p><center><font size=+1>Daftar Nilai Matakuliah Klinik</font><br />
    <font size=+2>$jdwl[Nama] ($jdwl[MKKode])</font></center></p>";
  $TM = FormatTanggal($jdwl['TglMulai']);
  $TS = FormatTanggal($jdwl['TglSelesai']);
  echo "<p><center>Rumah Sakit: <font size=+1>$jdwl[NamaRS]</font><br />
    Periode: <font size=+1>$TM ~ $TS</font></center></p>";
  $s = "select k.KRSID, k.MhswID, m.Nama, k.GradeNilai, k.BobotNilai
    from krs k
      left outer join mhsw m on k.MhswID=m.MhswID
    where k.JadwalID='$JadwalID'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<p align=center><table class=box cellspacing=1>
    <tr><th class=ttl>No</th>
    <th class=ttl>N P M</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>Grade<br />Nilai</th>
    <th class=ttl>Bobot<br />Nilai</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=center>$w[GradeNilai]</td>
    <td class=ul align=right>$w[BobotNilai]</td>
    </tr>";
  }
  echo "</table></p>";
  echo "<p align=center>Total Mahasiswa: <font size=+1>$n</font></p>";
  echo "<script>window.print()</script>";
}

// *** Main ***
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
?>
