<?php
// Author: Emanuel Setio Dewo
// 12 June 2006
// www.sisfokampus.net

include_once "klinik.lib.php";

// *** Functions ***

function Biyar() {
  $mhsw = GetFields('mhsw', 'MhswID', $_SESSION['MhswID'], '*');
  TampilkanHeaderMhswKlinik($mhsw);
  TampilkanMenuMhswKlinik($mhsw);
  TampilkanDetailMhswKlinik($mhsw);
}
function TampilkanMenuMhswKlinik($mhsw) {
  echo "<p>Opsi: <a href='klinik.bpm.mhsw.php?MhswID=$mhsw[MhswID]' target=_blank>Daftar BPM Lengkap</a>
  </p>";
}
function TampilkanDetailMhswKlinik($mhsw) {
  $kurid = GetaField('kurikulum', "NA='N' and ProdiID", $mhsw['ProdiID'], 'KurikulumID');
  $krs = array();
  $krs = GetKRSMhswKlinik($mhsw);
  $s = "select MKID, MKKode, Nama
    from mk
	  where KurikulumID='$kurid' and NA='N'
	  order by MKKode";
  $r = _query($s); $n = 0;
  echo "<p><table class=box>
    <tr><th class=ttl>#</th>
	  <th class=ttl>Kode</th>
	  <th class=ttl>Matakuliah</th>
	  </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
	  $BYR = GetBiyarMhsw($krs, $w['MKKode']);
    echo "<tr><td class=inp>$n</td>
	  <td class=ul>$w[MKKode]</td>
	  <td class=ul>$w[Nama]</td>
	  <td class=ul>$BYR &nbsp;</td>
	  </tr>";
  }
  echo "</table></p>";
}
function GetBiyarMhsw($arr, $kode) {
  $a = '';
  for ($i = 0; $i < sizeof($arr); $i++) {
    $str = explode('~', $arr[$i]);
	  if ($str[3] == $kode) {
	    $ket = "$str[3] &raquo; Grade: $str[4], Bobot: $str[5]";
	    if ($str[6] == $str[7]) {
	      $c = "class=inp1";
	      $byr = "";
	    }
	    else {
	      $c = "class=wrn";
	      $byr = "<a href='?mnux=klinik.biyar.trx&MhswID=$_SESSION[MhswID]&MKKode=$kode&KRS=$str[0]'>Bayarkan?</a>";
	    }
	    $a .= "<font $c title='$ket'><a href='klinik.biyar.detail.php?KRS=$str[0]' target=_blank>". 
        $str[1] ."</a></font> ($str[4]) $byr &nbsp;";
	  }
  }
  return $a;
}
function GetKRSMhswKlinik($mhsw) {
  $s = "select KRSID, TahunID, MKID, MKKode, GradeNilai, BobotNilai, Harga, Bayar
    from krs
	  where MhswID='$mhsw[MhswID]'
	  order by MKKode";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    $isi = "$w[KRSID]~$w[TahunID]~$w[MKID]~$w[MKKode]~$w[GradeNilai]~$w[BobotNilai]~$w[Harga]~$w[Bayar]";
    $arr[] = $isi;
  }
  //for ($i=0; $i<sizeof($arr); $i++) echo $arr[$i]."<br />";
  return $arr;
}
function TampilkanDaftarMhsw1() {
  TampilkanDaftarMhsw("?mnux=klinik.biyar&gos=Biyar&MhswID==MhswID=");
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$klinpage = GetSetVar('klinpage');
$crkey = GetSetVar('crkey');
$crval = GetSetVar('crval');
$MhswID = GetSetVar('MhswID');
$gos = (empty($_REQUEST['gos']))? "TampilkanDaftarMhsw1" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Biaya dan Pembayaran Mahasiswa");
TampilkanCariMhsw1('klinik.biyar', 'TampilkanDaftarMhsw1', 1);
$gos();
?>
