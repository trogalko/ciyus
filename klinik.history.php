<?php
// Author: Emanuel Setio Dewo
// 19 June 2006
// www.sisfokampus.net

include_once "klinik.lib.php";

// *** Functions ***
function DetailHistory() {
  $MhswID = $_SESSION['MhswID'];
  $mhsw = GetFields('mhsw', 'MhswID', $MhswID, '*');
  if (!empty($mhsw)) {
    TampilkanHeaderMhswKlinik($mhsw);
    TampilkanDetailHistory($mhsw);
  }
  else echo ErrorMsg("Terjadi Kesalahan",
    "Mahasiswa dengan NPM: <font size=+1>$MhswID</font> tidak ditemukan.<br />
    Hubungi bagian MIS untuk informasi lebih lanjut.
    <hr size=1 color=silver>
    Pilihan: <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=klinik.history'\">");
}
function TampilkanDetailHistory($mhsw) {
  global $urutanklinik;
  TampilkanPilihanUrutanKlinik();
  
  $_u = explode('~', $urutanklinik[$_SESSION['_urutanklinik']]);
  $_key = $_u[1];
  $s = "select k.*, mk.Nama, j.RuangID, j.TglMulai, j.TglSelesai
    from krs k
      left outer join mk on k.MKID=mk.MKID
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where MhswID='$mhsw[MhswID]'
    order by $_key";
  $r = _query($s);
  $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>No</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Rumah<br />Sakit</th>
    <th class=ttl>Periode</th>
    <th class=ttl>Grade</th>
    <th class=ttl>Bobot</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $TM = FormatTanggal($w['TglMulai']);
    $TS = FormatTanggal($w['TglSelesai']);
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[RuangID]</td>
    <td class=ul>$TM~$TS</td>
    <td class=ul align=center>$w[GradeNilai]</td>
    <td class=ul align=right>$w[BobotNilai]</td>
    </tr>";
  }
  echo "</table></p>";
}
function TampilkanPilihanUrutanKlinik() {
  global $urutanklinik;
  $a = '';
  for ($i=0; $i<sizeof($urutanklinik); $i++) {
    $sel = ($i == $_SESSION['_urutanklinik'])? 'selected' : '';
    $v = explode('~', $urutanklinik[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='klinik.history'>
  <input type=hidden name='gos' value='DetailHistory'>
  <tr><td class=inp>Urut berdasarkan: </td>
  <td class=ul><select name='_urutanklinik' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}
function TampilkanDaftarMhsw1() {
  TampilkanDaftarMhsw('?mnux=klinik.history&MhswID==MhswID=&gos=DetailHistory');
}

// *** Parameters ***
$urutanklinik = array(0 =>"Kode Matakuliah~k.MKKode", 
  1=>"Rumah Sakit~j.RuangID", 
  2=>"Periode~j.TglMulai",
  3=>"Kode & Periode~k.MKKode,j.TglMulai");
$_urutanklinik = GetSetVar('_urutanklinik', 3);
$klinpage = GetSetVar('klinpage');
$crkey = GetSetVar('crkey');
$crval = GetSetVar('crval');
$MhswID = GetSetVar('MhswID');
$gos = (empty($_REQUEST['gos']))? "TampilkanDaftarMhsw1" : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("History Matakuliah Mhsw");
TampilkanCariMhsw1('klinik.history');
$gos();
?>
