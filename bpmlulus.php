<?php
// Author: Emanuel Setio Dewo
// 01/11/2006

include_once "mhsw.hdr.php";
include_once "mhswkeu.lib.php";
include_once "mhswkeu.sav.php";

// *** Functions ***
function BPMMhswLulus($mhsw) {
  // tampilkan menu
  echo "<p><a href='?mnux=bpmlulus&gos=BPMLulusAdd'>Tambahkan BPM</a></p>";
  // tampilkan BPM lama
  $s = "select *
    from bayarmhsw bm
    where MhswID='$_SESSION[mhswid]'
    order by TahunID desc";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>No. BPM</th>
    <th class=ttl>No. Rekening</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Jumlah Lain</th>
    <th class=ttl>Cetak BPM</th>
    <th class=ttl>Status</th>
		<th class=ttl>Edit</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $c = ($w['Proses'] == 0)? "class=ul" : "class=nac";
    $st = ($w['Proses'] == 0)? "<a href='?mnux=bpmlulus&gos=BPMLulusPrc&BPMID=$w[BayarMhswID]'><img src='img/N.gif' title='Belum Diproses'></a>" : "<img src='img/Y.gif' title='Sudah Diproses'>";
    $ctk = ($w['Proses'] == 0)? "<a href='bpmlulus.cetak.php?BPMID=$w[BayarMhswID]' target=_blank><img src='img/printer.gif'></a>" : "&nbsp;";
    $edt = ($w['Proses'] == 0)? "&nbsp;" : "<a href='?mnux=bpmlulus&gos=BPMLulusEdt&BPMID=$w[BayarMhswID]'><img src='img/edit.png' title='Edit BPM'></a>";
		$Jumlah = number_format($w['Jumlah']);
    $JumlahLain = number_format($w['JumlahLain']);
    $tgl = FormatTanggal($w['Tanggal']);
    echo "<tr>
      <td $c>$n</td>
      <td $c>$w[BayarMhswID]</td>
      <td $c>$w[RekeningID]</td>
      <td $c>$tgl</td>
      <td $c align=right>$Jumlah</td>
      <td $c align=right>$JumlahLain</td>
      <td $c align=center>$ctk</td>
      <td $c align=center>$st</td>
			<td $c align=center>$edt</td>
      </tr>";
  }
  echo "</table>";
}
function BPMLulusEdt($mhsw){
	$defrek = GetaField('rekening', "Def", 'Y', "RekeningID");
  $bbyr = GetFields('Bayarmhsw', 'BayarMhswID', $_REQUEST['BPMID'], '*');
	$opttgl = GetDateOption(date("Y-m-d"), 'Tanggal');
	echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='BPMLulus' method=POST>
  <input type=hidden name='mnux' value='bpmlulus'>
  <input type=hidden name='gos' value='BPMLulusSav'>
  <input type=hidden name='RekeningID' value='$defrek'>
  <input type=hidden name='MhswID' value='$mhsw[MhswID]'>
	<input type=hidden name='ByrID' value='$_REQUEST[BPMID]'>
	<input type=hidden name='md' value=1>
  
  <tr><th class=ttl colspan=2>Edit BPM</th></tr>
  <tr><td class=inp>Tahun</td><td class=ul><input type=text name='TahunBPM' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Rekening</td><td class=ul>$defrek</td></tr>
  <tr><td class=inp>Tanggal Bayar</td>
      <td class=ul>$opttgl</td></tr>
  <tr><td class=inp>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$bbyr[JumlahLain]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><input type=text name='Keterangan' size=50 maxlength=100 value='$bbyr[Keterangan]'></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?'\">
  </td></tr>
  </form></table></p>";
}

function BPMLulusAdd($mhsw) {
  $defrek = GetaField('rekening', "Def", 'Y', "RekeningID");
  $opttgl = GetDateOption(date('Y-m-d'), 'Tanggal');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='BPMLulus' method=POST>
  <input type=hidden name='mnux' value='bpmlulus'>
  <input type=hidden name='gos' value='BPMLulusSav'>
  <input type=hidden name='RekeningID' value='$defrek'>
  <input type=hidden name='MhswID' value='$mhsw[MhswID]'>
	<input type=hidden name='md' value=0>
  
  <tr><th class=ttl colspan=2>Tambah BPM</th></tr>
  <tr><td class=inp>Tahun</td><td class=ul><input type=text name='TahunBPM' value='$_SESSION[tahun]' size=10 maxlength=10></td></tr>
  <tr><td class=inp>Rekening</td><td class=ul>$defrek</td></tr>
  <tr><td class=inp>Tanggal Bayar</td>
      <td class=ul>$opttgl</td></tr>
  <tr><td class=inp>Jumlah</td><td class=ul><input type=text name='Jumlah' value='0' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><input type=text name='Keterangan' size=50 maxlength=100></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?'\">
  </td></tr>
  </form></table></p>";
}
function BPMLulusSav($mhsw) {
  $bpmid = GetNextBPM();
  $RekeningID = $_REQUEST['RekeningID'];
  $MhswID = $_REQUEST['MhswID'];
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $TahunBPM = $_REQUEST['TahunBPM'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);
	$md = $_REQUEST['md']+0;
	$BayarMhswID = $_REQUEST['ByrID'];
  if ($md == 0){
		// simpan
		$s = "insert into bayarmhsw
			(BayarMhswID, TahunID, RekeningID, Tanggal, MhswID, Autodebet,
			TrxID, PMBMhswID, JumlahLain, Keterangan,
			LoginBuat, TanggalBuat)
			values ('$bpmid', '$TahunBPM', '$RekeningID', '$Tanggal', '$MhswID', 0,
			1, 1, $Jumlah, '$Keterangan',
			'$_SESSION[_Login]', now())";
		$r = _query($s);
		echo Konfirmasi("BPM Sudah Disimpan",
			"BPM sudah disimpan dengan nomer: <font size=+1>$bpmid</font>.<br />
			Silakan dibayar di Bank terlebih dahulu ke rekening: <b>$RekeningID</b>.
			<hr size=1>
			Pilihan: <a href='bpmlulus.cetak.php?BPMID=$bpmid' target=_blank>Cetak BPM</a> | <a href='?'>Kembali</a>");
	} elseif ($md == 1) {
			$s = "update bayarmhsw set JumlahLain = '$Jumlah' where BayarMhswID = '$BayarMhswID'";
			$r = _query($s);
			echo Konfirmasi("BPM Sudah Diedit",
				"BPM dengan nomer <font size=+1>$BayarMhsw</font> telah berhasil diupdate.<br />
				<hr size=1>
				Pilihan : <a href='?'>Kembali</a>");
	}
}
function BPMLulusPrc($mhsw) {
  $BPMID = $_REQUEST['BPMID'];
  $bpm = GetFields('bayarmhsw', "BayarMhswID", $BPMID, "*");
  $NamaRek = GetaField('rekening', "RekeningID", $bpm['RekeningID'], "Nama");
  $opttgl = GetDateOption(date("Y-m-d"), 'Tgl');
  
  // tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='bpmlulus'>
  <input type=hidden name='gos' value='BPMLulusPrcSav'>
  <input type=hidden name='BPMID' value='$BPMID'>
  <input type=hidden name='TahunBPM' value='$bpm[TahunID]'>
  
  <tr><th class=ttl colspan=2>Proses Pembayaran - BPM</th></tr>
  <tr><td class=inp>Nomer BPM</td><td class=ul><b>$BPMID</b></td></tr>
  <tr><td class=inp>No Rekening</td><td class=ul>$bpm[RekeningID] - <b>$NamaRek</b></td></tr>
  <tr><td class=inp>Tanggal Disetor ke Bank</td><td class=ul>$opttgl</td></tr>
  <tr><td class=inp>Jumlah</td><td class=ul><input type=text name='JumlahLain' value='$bpm[JumlahLain]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Keterangan</td><td class=ul><input type=text name='Keterangan' value='$bpm[Keterangan]' size=50 maxlength=100></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location=?\"></td></tr>
  </form></table></p>";
}
/*
include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($mhswid, $khs['KHSID']);
*/
function BPMLulusPrcSav($mhsw) {
  $BPMID = $_REQUEST['BPMID'];
  $TahunBPM = $_REQUEST['TahunBPM'];
  $Tgl = "$_REQUEST[Tgl_y]-$_REQUEST[Tgl_m]-$_REQUEST[Tgl_d]";
  $JumlahLain = $_REQUEST['JumlahLain']+0;
  $Keterangan = $_REQUEST['Keterangan'];
  // prosesing
  $s = "update bayarmhsw set Tanggal='$Tgl', JumlahLain=$JumlahLain, Keterangan='$Keterangan', Proses=1,
    LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
    where BayarMhswID='$BPMID' ";
  $r = _query($s);
  // hitung
  $khsid = GetaField('khs', "MhswID='$mhsw[MhswID]' and TahunID", $TahunBPM, "KHSID")+0;
  if ($khsid > 0) {
    include_once "mhswkeu.lib.php";
    HitungBiayaBayarMhsw($mhsw['MhswID'], $khsid);
  }
  echo "<script>window.location = '?'</script>";
}

// *** Parameters ***
$crmhsw = GetSetVar('crmhsw');
$crmhswid = GetSetVar('crmhswid');
$rekid = GetSetVar('rekid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'BPMMhswLulus' : $_REQUEST['gos'];
$UkuranHeader = GetSetVar('UkuranHeader', 'Kecil');
$TampilkanDetail = GetSetVar('TampilkanDetail', 1);
$bpmblank = GetSetVar('bpmblank', 0);

// *** Main ***
TampilkanJudul("BPM Mhsw yg Telah Lulus");
TampilkanPencarianMhswTahun('bpmlulus', 'BPMMhswLulus', 1);
// Cari
if (!empty($crmhswid)) {
  $mhswid = $_SESSION['crmhswid'];
  $_SESSION['mhswid'] = $mhswid;
  $mhsw = GetFields("mhsw m
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID",
    "MhswID", $mhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD, sm.Nama as SM, sm.Keluar, bpt.Nama as BPT");
  if (!empty($mhsw)) {
    $TampilkanHeader = "TampilkanHeader$UkuranHeader";
    $TampilkanHeader($mhsw, 'bpmlulus');
    if ($mhsw['Keluar'] == 'N')
      echo ErrorMsg("Mahasiswa Belum Lulus", "Mahasiswa masih aktif. 
      Jika masih aktif gunakan fasilitas <b>Cetak BPM</b>.");
    else {
      $gos($mhsw);
    } 
  } 
  else echo ErrorMsg("Data Tidak Ditemukan",
      "Mahasiswa dengan NPM: <b>$mhswid</b> tidak ditemukan.<br />
      NPM harus sesuai dengan yang tertera di KSM (Kartu Studi Mahasiswa).");
}
?>
