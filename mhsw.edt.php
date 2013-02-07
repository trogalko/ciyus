<?php
// Author: Emanuel Setio Dewo
// 28 Feb 2006

// *** Functions ***
function TampilkanHeaderMhsw($w) {
  $foto = FileFotoMhsw($w['MhswID'], $w['Foto']);
  // Tampilkan
  echo "<p><table class=box cellspacing=2 cellpadding=4>
  <tr><td class=ul colspan=2><b>Data Mahasiswa</b></td>
    <td class=box rowspan=6 style='padding: 2pt'><img src='$foto' width=120 height=150></td></tr>
  <tr><td class=ul>NPM</td><td class=ul><b>$w[MhswID]</b></td></tr>

  <tr><td class=ul>Nama</td><td class=ul><b>$w[Nama]</b></td></tr>
  <tr><td class=ul>Program</td><td class=ul>$w[ProgramID] - <b>$w[PRG]</b></td></tr>
  <tr><td class=ul>Program Studi</td><td class=ul>$w[ProdiID] - <b>$w[PRD]</b></td></tr>
  <tr><td class=ul>Pilihan</td><td class=ul><a href='?mnux=mhsw'>Kembali ke Daftar Mhsw</a> |
    <a href='?mnux=mhsw.foto&mhswid=$w[MhswID]'>Ganti Foto</a>
    </td></tr>
  </table>";
}
function pri() {
  include_once "mhsw.edt.pri.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmPribadi';
  $sub();
}
function almt() {
  include_once "mhsw.edt.almt.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmAlamat';
  $sub();
}
function akd() {
  include_once "mhsw.edt.akd.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmAkademik';
  $sub();
}
function sek() {
  include_once "mhsw.edt.sek.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmSekolah';
  $sub();
}
function ortu() {
  include_once "mhsw.edt.ortu.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmOrtu';
  $sub();
}
function bank() {
  include_once "mhsw.edt.bank.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmBank';
  $sub();
}
function pt() {
  include_once "mhsw.edt.pt.php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'frmPT';
  $sub();
}

function mb(){
	include_once "mhsw.edt.masterbipot.php";
	$sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : 'BPTEDT'; 
	$sub();
}

// *** Parameters ***
$arrmhswpg = array('Pribadi->pri',
  'Alamat Tetap->almt',
  'Akademik->akd',
  'Orang Tua->ortu',
  'Asal Sekolah->sek',
  'Asal Perguruan Tinggi->pt',
  'Bank->bank');
	//'Master Bipot->mb');
$mhswid = GetSetVar('mhswid');
$mhswpg = GetSetVar('mhswpg', 'pri');
$mnux = 'mhsw.edt';
$pref = 'mhswedt';
$token = GetSetVar($pref, 'pri');

// *** Main ***
TampilkanJudul("Data Mahasiswa");
if (!empty($_SESSION['mhswid'])) {
  $datamhsw = GetFields("mhsw m
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join program prg on m.ProgramID=prg.ProgramID",
    'm.MhswID', $mhswid,
    "m.*, prd.Nama as PRD, prg.Nama as PRG");
  if (!empty($datamhsw)) {
    TampilkanHeaderMhsw($datamhsw);
    TampilkanSubMenu($mnux, $arrmhswpg, $pref, $token);
    if (!empty($token)) $token();
  }
  else echo ErrorMsg("Kesalahan",
    "Terjadi kesalahan. Mahasiswa dengan NPM: <b>$mhswid</b> tidak ditemukan.");
}
?>
