<?php
// Author: Emanuel Setio Dewo
// 22 Sept 2006
// Description: Untuk menambahkan KRS secara masal. 
//   Masukkan JadwalID dan karakter pertama MhswID

include "sisfokampus.php";
HeaderSisfoKampus("Input KRS Masal Berdasarkan Jadwal & Angkatan");

// *** Functions ***
function TampilkanInputJadwal() {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='gos' value='TampilkanDaftarMhsw'>
  <tr><td class=inp>ID Jadwal</td>
  <td class=ul><input type=text name='JadwalID' value='$_SESSION[JadwalID]' size=10 maxlength=10></td></tr>
  
  <tr><td class=inp>N.P.M (6 digit)</td>
  <td class=ul><input type=text name='MhswIDPart' value='$_SESSION[MhswIDPart]' size=20 maxlength=20></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Tampilkan' value='Tampilkan Daftar Mhsw'></td></tr>
  </form>
  </table></p>";
}
function TampilkanDaftarMhsw() {
  $s = "select MhswID, KHSID
    from khs
    where MhswID like '$_SESSION[MhswIDPart]%'
    order by MhswID";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='gos' value='ProsesSekarang'>
    <input type=hidden name='MhswIDPart' value='$_SESSION[MhswIDPart]'>
    <input type=hidden name='JadwalID' value='$_SESSION[JadwalID]'>
    <tr><td class=ul colspan=4><input type=submit name='Proses' value='Proses Sekarang'></td></tr>
    <tr><th class=ttl>#</th>
    <th class=ttl>Cek</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>KHSID</th>
    </tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=inp>$n</td>
      <td class=ul><input type=checkbox name='KHSID[]' value=$w[KHSID] checked></td>
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[KHSID]</td>
      </tr>";
  }
  echo "<tr><td class=ul colspan=4><input type=submit name='Proses' value='Proses Sekarang'></td></tr>";
  echo "</form></table>";
}
function ProsesSekarang() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  $MhswIDPart = $_REQUEST['MhswIDPart'];
  $KHSID = array();
  $KHSID = $_REQUEST['KHSID'];
  //foreach ($KHSID as $val) echo "$val<br />";
  $inKHSID = implode(',', $KHSID);
  $s = "select MhswID, KHSID
    from khs
    where KHSID in ($inKHSID)";
  $r = _query($s);
  echo "<ol>";
  while ($w = _fetch_array($r)) {
    $ada = GetaField("krs", "MhswID='$w[MhswID]' and JadwalID", $jdwl['JadwalID'], 'KRSID')+0;
    if ($ada == 0) {
      $str = "insert into krs
        (KHSID, MhswID, TahunID, JadwalID,
        MKID, MKKode, SKS, HargaStandar, Harga,
        Catatan, LoginBuat, TanggalBuat)
        values ($w[KHSID], '$w[MhswID]', '$jdwl[TahunID]', $jdwl[JadwalID],
        '$jdwl[MKID]', '$jdwl[MKKode]', '$jdwl[SKS]', '$jdwl[HargaStandar]', '$jdwl[Harga]',
        'DITERIMA', 'BATCH-0922', now())";
      $rstr = _query($str);
    }
    else $str = "SDH.";
    echo "<li>$w[MhswID] - $w[KHSID] &raquo; $str</li>";
  }
  echo "</ol>";
}

// *** Parameters ***
$JadwalID = GetSetVar('JadwalID');
$MhswIDPart = GetSetVar('MhswIDPart');
$gos = $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Input KRS Masal Berdasarkan Jadwal & Angkatan");
TampilkanInputJadwal();
if (!empty($gos)) $gos();
?>
