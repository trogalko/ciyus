<?php

function CariCama(){
  global $arrID;
  echo "<p><form action='?' method='POST'>
        <input type=hidden name='mnux' value='pmbmundur'>
        <input type=hidden name='gos' value='PMBMundur'>
        <table class=box cellpadding=4 cellspacing=1>
        <tr><th class=ttl colspan=2>$arrID[Nama]</th></tr>
        <tr><td class=inp>Periode</td><td class=ul><input type=text name=periode value='$_SESSION[periode]'></td></tr>
        <tr><td class=inp>No PMB</td><td class=ul><input type=text name=pmbid value='$_SESSION[pmbid]'></td></tr>
        <tr><td class=ul colspan=2><input type=submit name=submit Value='Cari'></td></tr>
        </table></form></p>";
}

function PMBMundur(){
   echo "<p><a href='?mnux=pmbmundur&gos=AddPMBMundur&pmbid=$_SESSION[pmbid]'>Tambah PMB Mundur</a></p>";
   
  $s = "select * from pmbmundur
        where PMBID = '$_SESSION[pmbid]'
          and PMBPeriodID = '$_SESSION[periode]'";
  
  $r = _query($s);
  
  echo "<p><table class=box cellpadding=4 cellspacing=1>
        <tr><th class=ttl>#</th>
        <th class=ttl>PMBID</th>
        <th class=ttl>Tanggal Proses</th>
        <th class=ttl>No Surat</th>
        <th class=ttl>Tanggal Surat</th>
        <th class=ttl>Alasan</th>
        <th class=ttl>Biaya Administrsi</th>
        <th class=ttl>Yang Dikembalikan</th>
        <th class=ttl>Bukti Pengembalian</td>
        <th class=ttl>Edit</td></tr>";
  
  while ($w = _fetch_array($r)) {
    $n++;
    $edt = ($w['Proses'] == 1) ? "<a href='?mnux=pmbmundur&gos=EdtPMBMundur&pmbmundurid=$w[PMBMundurID]'><img src=img/edit.png></a>" : '';
    $Bal = CekBipot($w['PMBID'], $_SESSION['periode'], $w['BiayaAdministrasi']);
    $w['BiayaAdministrasi'] = number_format($w['BiayaAdministrasi']);
    $ds = array();
    $ds = explode('|', $Bal);
    //echo $Bal;
    $_Bal = number_format($ds[0]);
    $ctkbkt = "<a href='?mnux=pmbmundur&gos=CtkBukti&pmbid=$w[PMBID]&tahun=$w[PMBPeriodID]&biaya=$ds[0]&regs=$w[BiayaAdministrasi]&byr=$ds[1]'><img src='img/edit.png'> Cetak Bukti</a>";
    echo "<tr><td class=inp>$n.</td>
          <td class=ul>$w[PMBID]</td>
          <td class=ul>$w[TglProses]</td>
          <td class=ul>$w[NoSurat]</td>
          <td class=ul>$w[TglSurat]</td>
          <td class=ul>$w[Alasan]</td>
          <td class=ul align=right>$w[BiayaAdministrasi]</td>
          <td class=ul align=right>$_Bal</td>
          <td class=ul>$ctkbkt</td>
          <td class=ul>$edt</td></tr>";
  }
  echo "</table></p>";
} 

function Terbilang($x)
{
  $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
  if ($x < 12)
    return " " . $abil[$x];
  elseif ($x < 20)
    return Terbilang($x - 10) . "belas";
  elseif ($x < 100)
    return Terbilang($x / 10) . " puluh" . Terbilang($x % 10);
  elseif ($x < 200)
    return " seratus" . Terbilang($x - 100);
  elseif ($x < 1000)
    return Terbilang($x / 100) . " ratus" . Terbilang($x % 100);
  elseif ($x < 2000)
    return " seribu" . Terbilang($x - 1000);
  elseif ($x < 1000000)
    return Terbilang($x / 1000) . " ribu" . Terbilang($x % 1000);
  elseif ($x < 1000000000)
    return Terbilang($x / 1000000) . " juta" . Terbilang($x % 1000000);
}

function CtkBukti() {
  global $_lf;
  //include "terbilang.php";
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $maxcol = 80;
  //$div = str_pad('-',$maxcol,'-').$_;
  $f = fopen($nmf, 'w');
  $Periode = $_REQUEST['tahun'];
  $PMBID = $_REQUEST['pmbid'];
  $bal = $_REQUEST['biaya'];
  $BYR = $_REQUEST['byr'];
  $REG = $_REQUEST['regs'];
  $_bal = number_format($bal);
  $x = GetFields("pmb left outer join prodi on prodi.ProdiID = pmb.ProdiID", 'PMBID', $PMBID, 'pmb.Nama as NMHS, prodi.Nama as PRD');
  fwrite($f, chr(27).chr(18) . chr(27).chr(108).chr(0) . $_lf);
  fwrite($f, str_pad("BUKTI PENGEMBALIAN PEMBAYARAN P.M.B.", $maxcol, ' ', STR_PAD_BOTH ). $_lf.$_lf);
  fwrite($f, str_pad("PERIODE " . NamaTahunPMB($Periode), $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf . $_lf);
  $isi = str_pad('PMBID                  : ' . $PMBID, 50, ' ' ) . $_lf . $_lf .
         str_pad('NAMA                   : ' . $x['NMHS'], 50, ' ') . $_lf . $_lf .
         str_pad('PRODI                  : ' . $x['PRD'], 50, ' ') . $_lf . $_lf .
         str_pad('BIAYA YG SUDAH DIBAYAR : Rp. ' . number_format($BYR), 50, ' ') . $_lf . $_lf .
         str_pad('BIAYA ADMINISTRASI     : Rp. ' . $REG, 50, ' ') . $_lf . $_lf .
         str_pad('NILAI YG DIKEMBALIKAN  : Rp. ' . $_bal, 50, ' '  ) . $_lf . $_lf .
         str_pad('TERBILANG              :' . ucwords(Terbilang($bal)) . "Rupiah", 50, ' ') . $_lf . $_lf .
         str_pad('KETERANGAN             : ' . $Keterangan, 60, ' ') . $_lf . $_lf .
         str_pad('Jakarta, '. date("d-m-Y"),50, ' ') . $_lf . $_lf .
         str_pad('Bagian Keuangan,', 50, ' '). $_lf. $_lf. $_lf. $_lf. $_lf. $_lf. $_lf;;
  fwrite($f, $isi);
  fwrite($f, "Dicetak Oleh : " . $_SESSION['_Login']);
  //fwrite($f, $div);
  fwrite($f, chr(12));
  fclose($f);
  
  echo "<iframe src='dwoprn.php?f=$nmf' height=0 width=0 frameborder=0>
    </iframe>";
}

function updateBiaya($PMBID, $Periode, $Biaya, $md){
  if ($md == 0) {
    $s = "insert into bipotmhsw
      (PMBMhswID, PMBID, TahunID, BIPOT2ID, BIPOTNamaID,
      Nama, TrxID, Jumlah, Besar, 
      Catatan, LoginBuat, TanggalBuat)
      values
      (0, '$PMBID', '$Periode', 0, 45, 
      'Registrasi Mundur', -1, 1, $Biaya,
      'Registrasi Biaya Mundur Mahasiswa', '$_SESSION[_Login]', now() 
      )";
    $r = _query($s);
 } else if ($md == 1) {
    $s = "update bipotmhsw set Jumlah='$Biaya' where PMBID='$PMBID' and TahunID='$Periode' and BipotNamaID = 45";
    $r = _query($s);
 }
}

function DataCama($pmb){
  echo "<p><table class=box cellpadding=4 cellspacing=1>
        <tr><th colspan=4 class=ttl>Data Calon Mahasiswa</th></tr>
        <tr>
          <td class=inp>PMBID</td><td class=ul><b>$pmb[PMBID]</b></td>
          <td class=inp>Nama</td><td class=ul><b>$pmb[Nama]<b></td>
        </tr>
        <tr>
          <td class=inp>Program</td><td class=ul><b>$pmb[PRG]</b></td>
          <td class=inp>Program Studi</td><td class=ul><b>$pmb[PRD]</b>($pmb[ProdiID])</td>
        </tr>
        <tr>
          <td class=inp>Master Biaya</td><td class=ul colspan=3><b>$pmb[BPT]</b></td>
        </tr>
        </table></p>";
}

function AddPMBMundur(){
  $optprs = GetDateOption(date('Y-m-d'), 'TanggalProses');
  $optsrt = GetDateOption(date('Y-m-d'), 'TanggalSurat');

  $PMBID = $_REQUEST['pmbid'];
  $Periode = $_SESSION['periode'];
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='BPMMundur' method=POST>
  <input type=hidden name='mnux' value='pmbmundur'>
  <input type=hidden name='gos' value='PMBMundurSav'>
  <input type=hidden name='pmbid' value='$PMBID'>
  <input type=hidden name='Periode' value='$Periode'>
	<input type=hidden name='md' value=0>
  
  <tr><th class=ttl colspan=2>Proses PMB Mundur</th></tr>
  <tr><td class=inp>Periode</td><td class=ul><b>$Periode</b></td></tr>
  <tr><td class=inp>PMBID</td><td class=ul><b>$PMBID</b></td></tr>
  <tr><td class=inp>Tanggal Proses</td>
      <td class=ul>$optprs</td></tr>
  <tr><td class=inp>No Surat</td><td class=ul><input type=text name='NoSurat' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Tanggal Surat</td>
      <td class=ul>$optsrt</td></tr>
  <tr><td class=inp>Alasan</td><td class=ul><input type=text name='alasan' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Biaya Administrasi</td><td class=ul><input type=text name='BiayaAdministrasi' size=20 maxlength=35></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?'\">
  </td></tr>
  </form></table></p>";
}

function EdtPMBMundur(){
  $pmbmundurid = $_REQUEST['pmbmundurid'];
  $PMD = GetFields('pmbmundur', 'PMBMundurID', $pmbmundurid, '*');
  
  $optprs = GetDateOption(date('Y-m-d'), 'TanggalProses');
  $optsrt = GetDateOption($PMD['TglSurat'], 'TanggalSurat');
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='BPMMundur' method=POST>
  <input type=hidden name='mnux' value='pmbmundur'>
  <input type=hidden name='gos' value='PMBMundurSav'>
  <input type=hidden name='pmbid' value='$PMD[PMBID]'>
  <input type=hidden name='Periode' value='$Periode'>
  <input type=hidden name='pmbmundurid' value='$pmbmundurid'>
	<input type=hidden name='md' value=1>
  
  <tr><th class=ttl colspan=2>Edit PMB Mundur</th></tr>
  <tr><td class=inp>Periode</td><td class=ul><b>$PMD[PMBPeriodID]</b></td></tr>
  <tr><td class=inp>PMBID</td><td class=ul><b>$PMD[PMBID]</b></td></tr>
  <tr><td class=inp>Tanggal Proses</td>
      <td class=ul>$optprs</td></tr>
  <tr><td class=inp>No Surat</td><td class=ul><input type=text name='NoSurat' value='$PMD[NoSurat]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Tanggal Surat</td>
      <td class=ul>$optsrt</td></tr>
  <tr><td class=inp>Alasan</td><td class=ul><input type=text Value='$PMD[Alasan]' name='alasan' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Biaya Administrasi</td><td class=ul><input type=text Value='$PMD[BiayaAdministrasi]' name='BiayaAdministrasi' size=20 maxlength=35></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?'\">
  </td></tr>
  </form></table></p>";
}

function PMBMundurSav() {
  $PMBID = $_REQUEST['pmbid'];
  $TanggalProses = "$_REQUEST[TanggalProses_y]-$_REQUEST[TanggalProses_m]-$_REQUEST[TanggalProses_d]";
  $TanggalSurat = "$_REQUEST[TanggalSurat_y]-$_REQUEST[TanggalSurat_m]-$_REQUEST[TanggalSurat_d]";
  $NoSurat = sqling($_REQUEST['NoSurat']);
  $Periode = $_REQUEST['Periode'];
  $BiayaAdministrasi = $_REQUEST['BiayaAdministrasi']+0;
  $Alasan = sqling($_REQUEST['alasan']);
	$md = $_REQUEST['md']+0;
	$PMBMundurID = $_REQUEST['pmbmundurid'];
  if ($md == 0){
		// simpan
		$s = "insert into pmbmundur
			(PMBID, PMBPeriodID, TglProses, NoSurat, TglSurat, Proses,
			Alasan, BiayaAdministrasi, 
			LoginBuat, TglBuat)
			values ('$PMBID', '$Periode', '$TanggalProses', '$NoSurat', '$TanggalSurat', 1,
			'$Alasan', '$BiayaAdministrasi',
			'$_SESSION[_Login]', now())";
		$r = _query($s);
		echo Konfirmasi("PMB Mundur Sudah Disimpan",
			"Proses PMB Mundur untuk Calon Mahasiswa dengan PMBID : $PMBID telah berhasil dilakukan.
			<hr size=1>
			Pilihan: <a href='?'>Kembali</a>");
	} elseif ($md == 1) {
			$s = "update pmbmundur set TglProses = '$TanggalProses', NoSurat = '$NoSurat', TglSurat = '$TanggalSurat', 
            BiayaAdministrasi = '$BiayaAdministrasi', Alasan = '$Alasan'
            where PMBMundurID = '$PMBMundurID'";
			$r = _query($s);
			echo Konfirmasi("PMB Mundur Telah Diedit",
				"PMB Mundur untuk Calon Mahasiswa dengan Nomor PMB <font size=+1>$PMBID</font> telah berhasil diupdate.<br />
				<hr size=1>
				Pilihan : <a href='?'>Kembali</a>");
	}
	updateBiaya($PMBID, $Periode, $BiayaAdministrasi, $md);
	$mn = "update pmb set StatusMundur = 'Y' where PMBID='$PMBID'";
	$rm = _query($mn);
}

function CekBipot($PMBID, $Periode, $REG){
  $s = "Select (Dibayar) as tot from bipotmhsw
        where PMBID = '$PMBID' 
        and TahunID = '$Periode'
        and TrxID = 1";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $TOTS += $w['tot'];
  }
  $BAL = $TOTS - $REG;
  return "$BAL|$TOTS";
}

// *** Parameters ***

$pmbid = GetSetVar('pmbid');
$periode = GetSetVar('periode');
$gos = (empty($_REQUEST['gos']))? 'PMBMundur' : $_REQUEST['gos'];

//Cari
TampilkanJudul('Proses Calon Mahasiswa Mundur');
CariCama();
if (!empty($pmbid)) {
  $pmbid = $_SESSION['pmbid'];
  $_SESSION['pmbid'] = $pmbid;
  $pmb = GetFields("pmb p
    left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join prodi prd on p.ProdiID=prd.ProdiID
    left outer join bipot bpt on p.BIPOTID=bpt.BIPOTID",
    "PMBID", $pmbid,
    "p.*, prg.Nama as PRG, prd.Nama as PRD, sa.Nama as SM, bpt.Nama as BPT");
  if (!empty($pmb)) {
    DataCama($pmb);
    if ($pmb['LulusUjian'] == 'N')
      echo ErrorMsg("Calon Mahasiswa Tidak Lulus", "Calon Mahasiswa tidak Lulus Ujian Masuk.");
    else {
      $gos();
    } 
  } 
  else echo ErrorMsg("Data Tidak Ditemukan",
      "Calon Mahasiswa dengan PMBID: <b>$pmbid</b> tidak ditemukan.<br />
      PMBID harus sesuai dengan yang tertera di Kartu Ujian.");
}

?>
