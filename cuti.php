<?php
// Author: Emanuel Setio Dewo
// 16 April 2006

include_once "mhsw.hdr.php";

// *** Functions ***

function Cutikan($mhswid, $mhsw) {
  DaftarSesiMhsw($mhswid, $mhsw);
  //CetakFormulirCuti($mhswid, $mhsw);
}
function DaftarSesiMhsw($mhswid, $mhsw) {
  // Hitung sudah berapa kali cuti
  $MaxCuti = 4;
  $JmlCuti = GetaField('khs', "MhswID='$mhswid' and StatusMhswID", 'C', "count(KHSID)")+0;
  if ($JmlCuti >= $MaxCuti ) echo Konfirmasi1("Mahasiswa telah cuti sebanyak $JmlCuti. Tidak dapat mengajukan cuti lagi.");
  $s = "select k.*, sm.Nama as STA, sm.Nilai,
    format(k.SaldoAwal, 0) as SAWAL,
    format(k.Biaya, 0) as BIA,
    format(k.Bayar, 0) as BYR,
    format(k.Tarik, 0) as TRK,
    format(k.Potongan, 0) as POT,
    (k.SaldoAwal - k.Biaya + k.Bayar - k.Tarik + k.Potongan) as SALK,
    format(k.SaldoAwal-k.Biaya+k.Bayar-k.Tarik+k.Potongan, 0) as _SALK
    from khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.MhswID='$mhswid'
    order by k.Sesi";
  $r = _query($s); $tot = 0;
  echo "<p><table class=box cellspacing=1>";
  echo "<tr><th class=ttl>Smt</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Saldo Awal</th>
    <th class=ttl>Biaya2</th>
    <th class=ttl>Potongan2</th>
    <th class=ttl>Bayar2</th>
    <th class=ttl>Tarikan2</th>
    <th class=ttl>Total</th>
    <th class=ttl>Status</th>
    <th class=ttl>Frm Cuti</th>
    <th class=ttl>SK Cuti</th>
    <th class=ttl>KSS</th>
    <th class=ttl>Keterangan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $tot += $w['SALK'];
    $c = ($w['SALK'] == 0)? 'class=ul' : 'class=wrn';
    $st = ($w['Nilai'] == 1) ? 'class=ul' : 'class=nac';
    if ($w['StatusMhswID'] == 'C') {
      //GetArrayTable($sql, $key, $label, $separator=', ') {
      $ket = GetArrayTable("select TahunID from cuti where MhswID='$mhswid' order by TahunID",
        'TahunID', 'TahunID');
      $btn = '&nbsp;';
      $sk = $w['NoSurat'];
      $ctk1 = "<a href='?mnux=kss&gos=cekkss&tahun=$w[TahunID]&mhswid=$w[MhswID]&khsid=$w[KHSID]'>
        <img src='img/printer.gif'></a>";
    } 
    else {
      $ket = '&nbsp;';
      $ctk1 = '&nbsp;';
      if ($JmlCuti >= $MaxCuti) {
        $btn = "&times;";
        $sk = $w['NoSurat'];
      }
      else{
        $btn = "<a href='cuti.cetak.php?mhswid=$mhswid&tahun=$w[TahunID]' target=_blank title='Cetak Formulir Cuti'><img src='img/printer.gif'></a>";
        $sk = "<a href='?mnux=cuti&gos=SKCuti&mhswid=$mhswid&tahun=$w[TahunID]&khsid=$w[KHSID]' title='Buat SK Cuti Kuliah'><img src='img/gear.gif' width=20></a>";
      }
    }
    echo "<tr>
      <td class=inp>$w[Sesi]</td>
      <td class=ul>$w[TahunID]</td>
      <td class=ul align=right>$w[SAWAL]</td>
      <td class=ul align=right>$w[BIA]</td>
      <td class=ul align=right>$w[BYR]</td>
      <td class=ul align=right>$w[TRK]</td>
      <td class=ul align=right>$w[POT]</td>
      <td $c align=right>$w[_SALK]</td>
      <td $st>$w[STA]</td>
      <td class=ul align=center>$btn</td>
      <td class=ul align=center>$sk&nbsp;</td>
      <td class=ul align=center>$ctk1</td>
      <td class=ul>$ket&nbsp;</td>
      </tr>";
  }
  $_tot = number_format($tot);
  $c = ($tot >= 0) ? 'class=ul' : 'class=wrn';
  echo "<tr><td colspan=7 align=right>Saldo Akhir :</td>
    <td $c align=right><b>$_tot</b></td></tr>";
  echo "</table></p>";
}
function SKCuti($mhswid, $mhsw) {
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields("khs", "KHSID", $khsid, '*');
  $bal = $khs['Biaya'] - $khs['Bayar'] - $khs['Potongan'] + $khs['Tarik'];
  $_bal = number_format($bal);
  $c = ($bal > 0)? "class=wrn" : "class=ul";
  $BIA = number_format($khs['Biaya']);
  $BYR = number_format($khs['Bayar']);
  $POT = number_format($khs['Potongan']);
  $TRK = number_format($khs['Tarik']);
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='cuti'>
  <input type=hidden name='gos' value='SKCutiSav'>
  <input type=hidden name='crmhswid' value='$mhswid'>
  <input type=hidden name='khsid' value='$khsid'>
  
  <tr><th class=ttl colspan=2>Cutikan Mahasiswa</th></tr>
  <tr><td class=ul colspan=2>Benar Anda akan mencutikan mahasiswa pada semester ini?</td></tr>
  <tr><td class=inp>Tahun Akd</td><td class=ul>$khs[TahunID]</td></tr>
  <tr><td class=inp>Biaya-biaya</td><td class=ul align=right>$BIA</td></tr>
  <tr><td class=inp>Potongan</td><td class=ul align=right>$POT</td></tr>
  <tr><td class=inp>Pembayaran</td><td class=ul align=right>$BYR</td></tr>
  <tr><td class=inp>Penarikan</td><td class=ul align=right>$TRK</td></tr>
  <tr><td class=inp>Balance</td><td $c align=right>$_bal</td></tr>
  <tr><td class=ul colspan=2><b>Surat Keputusan</b></td></tr>
  <tr><td class=inp>Nomer Surat Keputusan</td><td class=ul><input type=text name='NoSurat' value='$w[NoSurat]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Keterangan Cuti</td><td class=ul><textarea name='Keterangan' rows=4 cols=30>$w[Keterangan]</textarea></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=cuti'\"></td></tr>
  </form></table></p>";
}
function SKCutiSav($mhswid, $mhsw) {
  $khsid = $_REQUEST['khsid'];
  $NoSurat = sqling($_REQUEST['NoSurat']);
  $Ket = sqling($_REQUEST['Keterangan']);
  if (!empty($khsid) && !empty($NoSurat)) {
    $s = "update khs set StatusMhswID='C', NoSurat='$NoSurat', Keterangan='$Ket'
      where KHSID='$khsid' ";
    $r = _query($s);
    echo Konfirmasi("Telah Disimpan", "Data cuti telah disimpan");
  } 
  else echo ErrorMsg('Gagal Simpan', "Data tidak lengkap. Data cuti tidak disimpan.");
  Cutikan($mhswid, $mhsw);
}

// *** Parameters ***
$crmhswid = GetSetVar('crmhswid');
$gos = (empty($_REQUEST['gos']))? 'Cutikan' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Permohonan Cuti");
TampilkanPencarianMhsw('cuti', 'Cutikan', 1);
if (!empty($crmhswid)) {
  $m = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID", 
    'm.MhswID', $crmhswid, 
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT");
  TampilkanHeaderBesar($m, 'cuti', '', 0);
  $gos($crmhswid, $m);
}
?>
