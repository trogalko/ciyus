<?php
// Author: Emanuel Setio Dewo
// 05 March 2006

include_once "mhswkeu.lib.php";
include_once "mhsw.hdr.php";

// *** Functions ***

// *** Tampilan Sesi Keuangan Mhsw ***
function DaftarSesiKeu($mhsw) {
  $s = "select k.*, sm.Nama as SM, sm.Nilai,
    format(k.Biaya, 0) as BIA,
    format(k.Bayar, 0) as BYR,
    format(k.Potongan, 0) as POT,
    format(k.Tarik, 0) as TRK,
    (k.Biaya-k.Bayar-k.Potongan+k.Tarik) as TOT,
    format(k.Biaya-k.Bayar-k.Potongan+k.Tarik, 0) as TOTS
    from khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.MhswID='$mhsw[MhswID]'
    order by k.TahunID";
  $r = _query($s);
  $BuatSesi = "<a href='?mnux=mhswakd&mhswid=$mhsw[MhswID]&gos=MhswAkdEdt'>Buat Sesi</a>";
  if (_num_rows($r) > 0) {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=8>$BuatSesi</td></tr>
    <tr><th class=ttl>Sesi</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Status</th>
    <th class=ttl>Biaya</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Bayar</th>
    <th class=ttl>Tarik</th>
    <th class=ttl>Total</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $optsm = GetOption2('statusmhsw', "concat(StatusMhswID, ' - ', Nama)",
      "StatusMhswID", $w['StatusMhswID'], '', 'StatusMhswID');
    //$c = "class=ul";
    $c = ($w['Nilai'] == 1)? "class=ul" : "class=nac";
    echo "
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='mhswkeu.det'>
    <input type=hidden name='gos' value='StatusSav'>
    <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
    <input type=hidden name='khsid' value='$w[KHSID]'>
    <tr><td $c><a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&mhswid=$mhsw[MhswID]&khsid=$w[KHSID]'><img src='img/bookN.gif'>
      $w[Sesi]</a></td>
    <td $c>$w[TahunID]</td>
    <td $c><select name='StatusMhswID'>$optsm</select> <input type=submit name='Simpan' value='Simpan'></td>
    <td $c align=right>$w[BIA]</td>
    <td $c align=right>$w[POT]</td>
    <td $c align=right>$w[BYR]</td>
    <td $c align=right>$w[TRK]</td>
    <td $c align=right>$w[TOTS]</td>
    </form>
    </tr>";
  }
  echo "</table></p>";
  }
  else echo Konfirmasi("Masih Kosong",
    "Mahasiswa ini belum dibuatkan sesi/semester.
    <hr size=1 color=silver>
    Pilihan: $BuatSesi |
    <a href='?mnux=mhswkeu'>Kembali ke Daftar</a>");
}
function StatusSav($mhsw) {
  $s = "update khs
    set StatusMhswID='$_REQUEST[StatusMhswID]'
    where KHSID='$_REQUEST[khsid]'";
  $r = _query($s);
  DaftarSesiKeu($mhsw);
}
// *** Data Sesi ***
function TampilkanHeaderKHS($mhsw, $khs) {
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=wrn><b>Data</b></td>
    <td class=inp1>Sesi</td><td class=ul>$khs[Sesi]</td>
    <td class=inp1>Tahun Akd</td><td class=ul>$khs[TahunID]</td>
    <td class=inp1>Status</td><td class=ul>$khs[SM]</td>
    <td class=inp1>Jumlah MK</td><td class=ul>$khs[JumlahMK]</td>
    <td class=inp1>SKS</td><td class=ul>$khs[TotalSKS]</td>
    <td class=ul><input type=submit name='Kembali' value='Kembali' onClick=\"location='?mnux=mhswkeu.det'\"></td>
  </tr>
  </table></p>";
	$aktifkah = (($khs['StatusMhswID'] == 'P') || ($khs['KaliCetak'] <= 0)) ? 1 : 0;
	$tampilkan =  "<p><table class=box cellpadding=4 cellspacing=1><tr><td class=inp>Keterangan</td><td class=ul>Kewijiban ini masih sementara, karena yang bersangkutan belum cetak KSS</td></tr></table></p>";
	if ($aktifkah == 0) {}
  else	echo $tampilkan; 
}
function MhswKeuSesi($mhsw) {
  $khs = GetFields("khs k
    left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID",
    'k.KHSID', $_REQUEST['khsid'],
    "k.*, sm.Nama as SM,
    format(k.Biaya, 0) as BIA,
    format(k.Bayar, 0) as BYR,
    format(k.Potongan, 0) as POT,
    format(k.Tarik, 0) as TRK");
  TampilkanHeaderKHS($mhsw, $khs);
  $mnki = "<a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&slnt=mhswkeu.sav&slntx=PrcBIPOTSesi'>Proses Biaya & Potongan</a> |
    <a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&ka=BipotMhswEdt&trx=1&khsid=$khs[KHSID]&md=1&mhswid=$mhsw[MhswID]'>Tambahkan Biaya</a> |
    <a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&ka=BipotMhswEdt&trx=-1&khsid=$khs[KHSID]&md=1&mhswid=$mhsw[MhswID]'>Tambahkan Potongan</a>
    ";
  $mnkaxxxxx = "<a href='?mnux=mhswkeu.det&gos=BayarEdt&gosto=MhswKeuSesi&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&md=1'>Trx Pembayaran</a> |
    Trx Penarikan";
  $mnka = '';

  $_ka = (empty($_REQUEST['ka']))? 'Kanan' : $_REQUEST['ka'];
  $_ki = (empty($_REQUEST['ki']))? 'TampilkanBiayaPotongan' : $_REQUEST['ki'];
  $ki = $_ki($mhsw, $khs);
  $ka = $_ka($mhsw, $khs);
  // Tampilkan
  echo "<p><table class=bsc cellspacing=1 cellpadding=4 width=100%>
  <tr>
  <td class=inp1 width=50%>$mnki</td>
  <td class=inp1>$mnka</td>
  </tr>
  <tr>
  <td width=50% valign=top style='border-right: silver 1px dotted'>$ki</td>
  <td valign=top>$ka</td>
  </tr>
  </table></p>";
  
  // Summary
  TampilkanSummaryKeuMhsw($mhsw, $khs);
}

function Kanan($mhsw, $khs) {
  $a = DaftarInstallment($mhsw, $khs);
  $a .= DaftarPembayaran($mhsw, $khs);
  return $a;
}
function DaftarInstallment($mhsw, $khs, $PMBMhswID=1) {
  $lnk = "<a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&ki=CicilanEdt&md=1&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]'>Tambah Cicilan/Installment</a>";
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=ul colspan=6><b>Daftar Cicilan (Installment)</b> » $lnk</td></tr>
    <tr><th class=ttl>No.</th>
    <th class=ttl>Judul</th>
    <th class=ttl>Tgl Bayar</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Sdh<br />Dibayar?</th>
    <th class=ttl>Dibayar</th>
    <th class=ttl>Keterangan</th>
    </tr>";
  $s = "select cm.*,
    date_format(cm.DariTanggal, '%d/%m/%Y') as DR,
    date_format(cm.SampaiTanggal, '%d/%m/%Y') as SMP,
    format(cm.Jumlah, 0) as JML
    from cicilanmhsw cm
    where MhswID='$mhsw[MhswID]' and TahunID='$khs[TahunID]'
    order by cm.Urutan";
  $r = _query($s);
  $tot = 0;
  while ($w = _fetch_array($r)) {
    if ($w['SudahDibayar'] == 'Y') {
      $c = 'class=nac';
      $editkan = '';
      $bayarkan = '';
      $exp = $c;
    }
    else {
      $c = 'class=ul';
      $editkan = "<a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&md=0&ki=CicilanEdt&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&CicilanID=$w[CicilanID]'><img src='img/edit.png'>";
      $bayarkan = "<a href='?mnux=mhswkeu.det&gos=BayarEdt&md=1&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&CicilanID=$w[CicilanID]'>Bayar</a>";
      $exp = ($w['SampaiTanggal'] <= date('Y-m-d'))? 'class=wrn' : $c;
    }
    $tot += $w['Jumlah'];
    $rs = "rowspan=2";
    $a .= "<tr>
      <td $c nowrap $rs>
      $editkan $w[Urutan]</a></td>
      <td $c $rs>$w[Judul]</td>
      <td $c>$w[DR]</td>
      <td $c $rs align=right>$w[JML]</td>
      <td $c $rs align=center><img src='img/$w[SudahDibayar].gif'> $bayarkan</td>
      <td $c align=right>$w[Dibayar]</td>
      <td $c $rs>$w[Keterangan]&nbsp;</td>
      </tr>
      <tr><td $exp>$w[SMP]</td></tr>";
  }
  $_tot = number_format($tot);
  return $a . "
    <tr><td colspan=4 align=right>Total :</td><td class=ul align=right><b>$_tot</b></td></tr>
    </table></p>";
}

function CicilanEdt($mhsw, $khs) {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $cclid = $_REQUEST['CicilanID'];
    $w = GetFields('cicilanmhsw', "CicilanID", $cclid, '*');
    $Jdl = 'Edit Cicilan';
  }
  else {
    $w = array();
    $w['CicilanID'] = 0;
    $w['TahunID'] = $khs['TahunID'];
    $w['MhswID'] = $mhsw['MhswID'];
    $w['Urutan'] = 0;
    $w['DariTanggal'] = date('Y-m-d');
    $w['SampaiTanggal'] = date('Y-m-d');
    $w['Judul'] = '';
    $w['Keterangan'] = '';
    $w['Jumlah'] = 0;
    $w['SudahDibayar'] = 'N';
    $Jdl = 'Tambah Cicilan';
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $DR = GetDateOption($w['DariTanggal'], 'DariTanggal');
  $SMP = GetDateOption($w['SampaiTanggal'], 'SampaiTanggal');
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='mhswkeu.det'>
    <input type=hidden name='gos' value='MhswKeuSesi'>
    <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
    <input type=hidden name='khsid' value='$khs[KHSID]'>
    <input type=hidden name='slnt' value='mhswkeu.sav'>
    <input type=hidden name='slntx' value='CicilanMhswSav'>
    <input type=hidden name='md' value='$md'>
    <input type=hidden name='CicilanID' value='$w[CicilanID]'>

  <tr><th class=ttl colspan=2>$Jdl</th></tr>
  <tr><td class=ul>Urutan</td><td class=ul><input type=text name='Urutan' value='$w[Urutan]' size=3 maxlength=3></td></tr>
  <tr><td class=ul>Judul</td><td class=ul><input type=text name='Judul' value='$w[Judul]' size=50 maxlength=50></td></tr>
  <tr><td class=ul>Dari Tanggal</td><td class=ul>$DR</td></tr>
  <tr><td class=ul>Sampai Tanggal</td><td class=ul>$SMP</td></tr>
  <tr><td class=ul>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20 maxlength=20></td></tr>
  <tr><td class=ul>Keterangan</td><td class=ul><textarea cols=30 rows=4 name='Keterangan'>$w[Keterangan]</textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswkeu.det&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&gos=MhswKeuSesi'\"></td></tr>
  </form></table></p>";
  return $a;
}


// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$gos = (empty($_REQUEST['gos']))? "DaftarSesiKeu" : $_REQUEST['gos'];
$UkuranHeader = GetSetVar('UkuranHeader', 'Besar');

// *** Main ***
TampilkanJudul("Keuangan Mahasiswa");
if (!empty($mhswid)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID",
    'MhswID', $mhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT,
    sm.Nama as SM, sm.Keluar");
  $TampilkanHeader = "TampilkanHeader$UkuranHeader";
  $TampilkanHeader($mhsw, 'mhswkeu');
  if ($mhsw['Keluar'] == 'Y' && $_SESSION['_LevelID'] != 1) {
    echo ErrorMsg("Data tidak dapat diakses lagi",
      "Status mahasiswa adalah: <b>$mhsw[SM]</b> yang berarti datanya
      sudah tidak dapat diakses lagi.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=mhswkeu'>Kembali</a>");
  }
  else {
    $gos($mhsw);
  }
}
?>
