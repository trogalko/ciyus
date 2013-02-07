<?php
// Author: Emanuel Setio Dewo
// 13 Sept 2006

// *** Functions ***
function TampilkanHeaderYudisium() {
}
function TampilkanDaftarYudisium() {
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', "ProdiID");
  $bulan1 = GetMonthOption($_SESSION['bulan1']);
  $bulan2 = GetMonthOption($_SESSION['bulan2']);
  $tahun1 = GetNumberOption(date('Y')-10, date('Y'), $_SESSION['tahun1']);
  $tahun2 = GetNumberOption(date('Y')-10, date('Y')+1, $_SESSION['tahun2']);
  $TglYudisium = GetDateOption($_SESSION['TglYudisium'], 'TglYudisium');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <tr><td class=ul colspan=2><font size=+1>Filter</font></td></tr>
  <tr><td class=inp>Prodi :</td><td class=ul><select name='prodi'>$optprd</select> Kosongkan jika ingin melihat semua</td></tr>
  <tr><td class=inp>Lulus bulan :</td><td class=ul>
    <select name='bulan1'>$bulan1</select><select name='tahun1'>$tahun1</select> s/d
    <select name='bulan2'>$bulan2</select><select name='tahun2'>$tahun2</select> <input type=submit name='Filter' value='Filter Daftar'></td></tr>
  </form>
  
  <tr><td class=ul colspan=2><font size=+1>SK Yudisium</font></td></tr>
  <tr><td colspan=2>Set mahasiswa yang bertanda centang dalam daftar di bawah ini dengan SK Yudisium berikut ini:</td></tr> 
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='skyudisium'>
  <input type=hidden name='gos' value='SKYSAV'>
  <tr><td class=inp>No SK Yudisium :</td><td class=ul><input type=text name='SKYudisium' value='$_SESSION[SKYudisium]' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Tanggal Yudisium :</td><td class=ul>$TglYudisium <input type=submit name='Simpan' value='Set SK Semua yg Dicentang'></td></tr>
  </table></p>";
  
  if ($_SESSION['prodi'] != '10') {
    $_whr = "('$_SESSION[tahun1]-$_SESSION[bulan1]-01' <= ta.TglUjian) 
      and (ta.TglUjian <= '$_SESSION[tahun2]-$_SESSION[bulan2]-31') and ";
  } else $_whr = "";
  
  $s = "select ta.*, m.Nama
    from ta ta
      left outer join mhsw m on ta.MhswID=m.MhswID
    where ta.Lulus='Y'
      and m.ProdiID = '$_SESSION[prodi]'
      
    order by ta.MhswID";
  
  $r = _query($s); $n = 0; $TotalSKY = _num_rows($r)+0;
  echo "<input type=hidden name='TotalSKY' value=$TotalSKY>
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>Tgl Ujian</th>
    <th class=ttl>Judul</th>
    <th class=ttl>SK Yudisium</th>
    <th class=ttl>Tgl Yudisium</th>
    <th class=ttl>Centang</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $TglSKYudisium = FormatTanggal($w['TglSKYudisium']);
    echo "<tr><td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[TglUjian]</td>
    <td class=ul>$w[Judul]</td>
    <td class=ul>$w[SKYudisium]&nbsp;</td>
    <td class=ul>$TglSKYudisium</td>
    <td class=ul><input type=checkbox name='SKY[]' value='$w[TAID]'></td>
    </tr>";
  }
  echo "</table></form></p>";
}
function SKYSAV() {
  $SKY = array();
  $SKY = $_REQUEST['SKY'];
  $jml = sizeof($SKY);
  for ($i = 0; $i < $jml; $i++) {
    $TAID = $SKY[$i];
    $mhsw = GetaField('ta', 'TAID', $TAID, 'MhswID');
    if ($_SESSION['prodi'] == '10') $ss = ", TglUjian='$_SESSION[TglYudisium]'";
    
    $s = "update ta set SKYudisium='$_SESSION[SKYudisium]', TglSKYudisium='$_SESSION[TglYudisium]' $ss
      where TAID='$TAID' ";
    $r = _query($s);
    
    $s0 = "update mhsw set StatusMhswID = 'L' where MhswID = '$mhsw'";
    $r0 = _query($s0);
  }
  if ($jml > 0) echo Konfirmasi("SK Yudisium Telah Diset",
    "SK dan tanggal Yudisium sudah diset pada <font size=+1>$jml</font> mhsw.");
  TampilkanDaftarYudisium();
}

// *** Parameters ***
$bulan1 = GetSetVar('bulan1', date('m'));
$bulan2 = GetSetVar('bulan2', date('m'));
$tahun1 = GetSetVar('tahun1', date('Y'));
$tahun2 = GetSetVar('tahun2', date('Y'));
$prodi = GetSetVar('prodi');
$SKYudisium = GetSetVar('SKYudisium');

$TglYudisium_d = GetSetVar('TglYudisium_d', date('d'));
$TglYudisium_m = GetSetVar('TglYudisium_m', date('m'));
$TglYudisium_y = GetSetVar('TglYudisium_y', date('Y'));
$TglYudisium = "$TglYudisium_y-$TglYudisium_m-$TglYudisium_d";
$_SESSION['TglYudisium'] = $TglYudisium;
$gos = (empty($_REQUEST['gos']))? "TampilkanDaftarYudisium" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("SK Yudisium");
TampilkanHeaderYudisium();
$gos();
?>
