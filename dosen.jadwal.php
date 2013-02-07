<?php
// Author: Emanuel Setio Dewo
// 15 March 2006

// *** Functions ***
function TampilkanHeaderJadwalDosen() {
  $optdsn = GetOption2('dosen', "concat(Login, ' - ', Nama, ', ', Gelar)",
    "Nama", $_SESSION['dosen'], '', 'Login');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='dosen.jadwal'>
  <tr><td class=wrn>$_SESSION[KodeID]</td>
    <td class=inp1>Tahun Akd.</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=10></td>
    <td class=inp1>Dosen</td><td class=ul><select name='dosen'>$optdsn</select></td>
    <td class=ul><input type=submit name='Kirim' value='Kirim'></td></tr>
  </form></table></p>";
}
function TampilkanJadwalDosen() {
  $s = "select j.*, h.Nama as HR
    from jadwal j
      left outer join hari h on j.HariID=h.HariID
    where j.DosenID='$_SESSION[dosen]'
    and j.TahunID='$_SESSION[tahun]'
    order by j.HariID, j.JamMulai, j.MKKode";
  $r = _query($s);
  // Tampilkan
  $nomer = 0; $hari = -1; $totsks = 0;
  $hdrjdwl = "<tr><th class=ttl>No</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Ruang</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Prodi</th>
    <th class=ttl>Dosen</th>
    <th class=ttl title='Presensi'>Prs</th>
    <th class=ttl>Link</th>
    </tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    if ($hari != $w['HariID']) {
      $hari = $w['HariID'];
      echo "<tr><td class=ul colspan=12><b>$w[HR]</b></td></tr>";
      echo $hdrjdwl;
    }
    $nomer++;
    $totsks += $w['SKS'];
    // Array dosen
    $arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $_dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", '<br />');
    // Array prodi
    $arrprodi = explode('.', TRIM($w['ProdiID'], '.'));
    $strprodi = implode(',', $arrprodi);
    $_prodi = (empty($strprodi))? '' : GetArrayTable("select Nama from prodi where ProdiID in ($strprodi) order by ProdiID",
      "ProdiID", "Nama", '<br />');

    echo "<tr><td class=inp1>$nomer</td>
      <td class=ul>$w[JamMulai]-$w[JamSelesai]</td>
      <td class=ul>$w[RuangID]</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]&nbsp;</td>
      <td class=ul>$w[SKS] ($w[SKSAsli])</td>
      <td class=ul>$_prodi</td>
      <td class=ul>$_dosen</td>
      <td class=ul align=right>$w[Kehadiran]</td>
      <td class=ul><a href='?mnux=dosen.nilai&tahun=$_SESSION[tahun]&jadwalid=$w[JadwalID]&dosen=$_SESSION[dosen]'>Nilai</a></td>
      </tr>";
  }
  echo "<tr><td colspan=6 align=right>Total SKS :</td><td class=cnnY align=right><b>$totsks</b></td></tr>
    </table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$dosen = GetSetVar('dosen');

// *** Main ***
TampilkanJudul("Jadwal Mengajar Dosen");
echo '<iframe style="width:100%;height:600px" src="http://localhost:4727/post.frmJadwalDosen.wgx?tahun='.$tahun.'&dosen='.$dosen.'" ></iframe>';
//TampilkanHeaderJadwalDosen();
//if (!empty($dosen) && !empty($tahun)) TampilkanJadwalDosen();
?>