<?php
// Author: Emanuel Setio Dewo
// 13/03/2007

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Daftar Mhsw Klinik yg Bebas");

// *** Main ***
TampilkanJudul("Daftar Mhsw Klinik yg Bebas");
$JdwlID = $_REQUEST['JadwalID']+0;
$jdwl = GetHeaderJadwalKlinik($JdwlID);
TampilkanMhswBebas($jdwl);

// *** Function ***
function GetHeaderJadwalKlinik($JdwlID=0) {
  $jdwl = GetFields('jadwal j', "j.JadwalID", $JdwlID, "j.*");
  $TM = FormatTanggal($jdwl['TglMulai']);
  $TS = FormatTanggal($jdwl['TglSelesai']);
  echo "<p><table class=box>
  <tr><td class=inp>ID</td>
    <td class=ul>$jdwl[JadwalID]</td>
    <td class=inp>RS</td>
    <td class=ul>$jdwl[RuangID]</td>
    <td class=inp>Kelas</td>
    <td class=ul>$jdwl[NamaKelas]</td>
    <td class=inp>Kapasitas</td>
    <td class=ul>$jdwl[Kapasitas]</td>
    </tr>
  <tr><td class=inp>Kode</td>
    <td class=ul>$jdwl[MKKode]</td>
    <td class=inp>Matakuliah</td>
    <td class=ul>$jdwl[Nama]</td>
    <td class=inp>SKS</td>
    <td class=ul>$jdwl[SKS]</td>
    <td class=inp>Periode</td>
    <td class=ul>$TM - $TS</td>
  </table></p>";
  
  return $jdwl;
}
function KembalikanNilai() {
  echo <<<END
  <script>
  function DaftarkanMhswBebas() {
    creator.PraKRSAdd.MhswID.value = data.Mhsw2.value;
    window.close();
  }
  function KomaMhsw(nm){
    ck = "";
    if (nm.checked == true) {
      var nilai = data.Mhsw2.value;
      if (nilai.match(nm.value+",") != nm.value+",") data.Mhsw2.value += nm.value + ",";
    }
    else {
      var nilai = data.Mhsw2.value;
      data.Mhsw2.value = nilai.replace(nm.value+",", "");
    }
  }
  </script>
END;
}
function TampilkanMhswBebas($jdwl) {
  $ProdiID = TRIM($jdwl['ProdiID'], '.');
  $s = "select k.*,
      m.Nama as NamaMhsw, m.TahunID as Angkatan
    from khs k
      left outer join mhsw m on m.MhswID=k.MhswID
    where k.TahunID='$jdwl[TahunID]'
      and k.ProdiID='$ProdiID'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<p>Jumlah: " . _num_rows($r) . "</p>";
  KembalikanNilai();
  $btn = "<input type=button name='Simpan' value='Daftarkan' onClick=\"DaftarkanMhswBebas()\">";
  echo "<form action='mhsw.klinik.bebas.pra.php' name='data' method=POST>
    <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>";
  echo "<input type=text name='Mhsw2' value='' size=100>";
  echo "<p><table class=box>
    <tr><th class=ttl>#</th>
    <th class=ttl>$btn</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Angkatan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul><input type=checkbox name='MhswID_$w[MhswID]' value='$w[MhswID]' onChange='javascript:KomaMhsw(data.MhswID_$w[MhswID])'></td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[NamaMhsw]</td>
      <td class=ul>$w[Angkatan]</td>
    </tr>";
  }
  echo "<tr><td class=ul>&nbsp;</td>
    <td class=ul>$btn</td>
    <td class=ul colspan=2>&nbsp;</td></tr>";
  echo "</table></p>";
  echo "</form>";
}
?>
