<?php
// Author: Emanuel Setio Dewo
// 07 Sept 2006
// www.sisfokampus.net
include_once "mhsw.hdr.php";
include_once "terbilang.php";

// *** Functions ***
function Ambilkan($mhsw) {
  $s = "select k.*
    from khs k
    where k.MhswID='$mhsw[MhswID]'
    order by k.Sesi";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>Sesi</th>
    <th class=ttl>Semester</th>
    <th class=ttl>Biaya</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Pembayaran</th>
    <th class=ttl>Tarikan</th>
    <th class=ttl>Balance</th>
    <th class=ttl>Ambilan</th>
    <th class=ttl>Detail Trx Pengambilan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $c = ($w['TahunID'] == $_SESSION['tahun'])? "class=ul" : "class=nac";
    $bia = number_format($w['Biaya']);
    $pot = number_format($w['Potongan']);
    $byr = number_format($w['Bayar']);
    $trk = number_format($w['Tarik']);
    $bal = -$w['Biaya'] -$w['Tarik'] +$w['Potongan'] +$w['Bayar'];
    $_bal = number_format($bal);
    $cb = ($bal >= 0)? "class=ul" : "class=wrn";
    $amb = ($bal > 0)? "<a href='?mnux=mhswkeu.ambil&gos=TrxAmbil&mhswid=$mhsw[MhswID]&khsid=$w[KHSID]'><img src='img/edit.png'> Ambil</a>" : "&nbsp;";
    $det = ($w['TahunID'] == $_SESSION['tahun'])? AmbilDetail($mhsw, $w) : '&nbsp;';
    echo "<tr><td class=inp>$w[Sesi]</td>
    <td $c>$w[TahunID]</td>
    <td $c align=right>$bia</td>
    <td $c align=right>$pot</td>
    <td $c align=right>$byr</td>
    <td $c align=right>$trk</td>
    <td $cb align=right><b>$_bal</b></td>
    <td $cb align=center>$amb</td>
    <td class=ul valign=top>$det</td>
    </tr>";
  }
  echo "</table></p>";
}
function AmbilDetail($mhsw, $khs) {
  $s = "select *
    from bayarmhsw bm
    where bm.TahunID='$khs[TahunID]' and bm.MhswID='$mhsw[MhswID]'
      and bm.TrxID=-1
    order by bm.Tanggal";
  $r = _query($s);
  $str = '';
  if (_num_rows($r) > 0) {
    $n = 0;
    $str = "<table class=bsc cellspacing=1 cellpadding=1>";
    while ($w = _fetch_array($r)) {
      $n++;
      $tgl = FormatTanggal($w['Tanggal']);
      $jml = number_format($w['Jumlah']);
      $proses = ($w['Proses'] == 0) ? "<a href='?mnux=mhswkeu.ambil&gos=TrxAmbilPros&khsid=$khs[KHSID]&bayarmhswid=$w[BayarMhswID]' title='Proses Pembayaran'><img src='img/gear.gif'> Proses</a>" : "<a href='?mnux=mhswkeu.ambil&gos=TrxAmbilPros&khsid=$khs[KHSID]&bayarmhswid=$w[BayarMhswID]&Rep=1' title='Proses Reprint'><img src='img/printer.gif'> Reprint</a>";
      $str .= "<tr><td class=inp>$n</td>
        <td class=ul valign=middle>$proses</td>
        <td class=ul>$w[BayarMhswID]</td>
        <td class=ul>$tgl</td>
        <td class=ul>$jml</td>
        </tr>";
    }
    $str .= "</table>";
  } 
  else $str = "&nbsp;";
  return $str;
}
function TrxAmbil($mhsw) {
  $khsid = $_REQUEST['khsid'];
  $Rep = $_REQUEST['rep']+0;
  $w = GetFields("khs", "KHSID", $khsid, "*");
  
  $bal = ($Rep == 0) ? -$w['Biaya'] -$w['Tarik'] +$w['Potongan'] +$w['Bayar'] : $w['Tarik'];
  $_bal = number_format($bal);
  $tgl = GetDateOption(date('Y-m-d'), "Tanggal");
  echo Konfirmasi("Transaksi Pengambilan Kelebihan Pembayaran Mhsw",
    "Berikut adalah data pengambilan kelebihan pembayaran mhsw:
    <p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=inp>N P M</td><td class=ul>$mhsw[MhswID]</td></tr>
    <tr><td class=inp>Nama Mahasiswa</td><td class=ul>$mhsw[Nama]</td></tr>
    <tr><td class=inp>Semester</td><td class=ul>$w[Sesi]. $w[TahunID]</td></tr>
    
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='mhswkeu.ambil'>
    <input type=hidden name='gos' value='TrxAmbilSav'>
    <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
    <input type=hidden name='khsid' value='$khsid'>
    <input type=hidden name='rep' value=$Rep>
    <tr><td class=inp>Tanggal Pengambilan</td><td class=ul>$tgl</td></tr>
    <tr><td class=inp>Jumlah Diambil</td><td class=ul><font size=+1>$_bal</td></tr>
    <tr><td class=inp>Keterangan</td><td class=ul><textarea name='Keterangan' cols=30 rows=3></textarea></td></tr>
    <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batalkan Transaksi' onClick=\"location='?mnux=mhswkeu.ambil&gos=Ambilkan'\"></td></tr>
    </form></table>");
}

function TrxAmbilPros($mhsw){
  $mhswid = $_REQUEST['mhswid'];
  $khsid = $_REQUEST['khsid'];
  $bayarmhswid = $_REQUEST['bayarmhswid'];
  $Rep = $_REQUEST['Rep']+0;
  $w = GetFields("khs", "KHSID", $khsid, "*");
  $bal = -$w['Biaya'] -$w['Tarik'] +$w['Potongan'] +$w['Bayar'];
  if ($Rep == 0) {      
    //Update bayarmhsw / pengambilan Mhsw = 1
    $s = "update bayarmhsw set proses = 1, TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]' 
          where BayarMhswID = '$bayarmhswid'";
    $r = _query($s);
    // update balance
    $s2 = "Update khs set Tarik = $bal where KHSID = $khsid";
    $r2 = _query($s2);
  } else {
  // Lakukan pencetakan
  $bal = $w['Tarik'];
  $Keterangan = "Reprint";
  CetakTrxAmbil($bayarmhswid, $bal, $Keterangan, $Rep);
  }
  // Kembali
  Ambilkan($mhsw);
}

function TrxAmbilSav($mhsw) {
  $mhswid = $_REQUEST['mhswid'];
  $khsid = $_REQUEST['khsid'];
  $Rep = $_REQUEST['rep']+0;
  $w = GetFields("khs", "KHSID", $khsid, "*");
  $bal = -$w['Biaya'] -$w['Tarik'] +$w['Potongan'] +$w['Bayar'];
  $_bal = number_format($bal);
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  
  $BPMID = GetNextBPM();
  $s = "insert into bayarmhsw (BayarMhswID, TahunID, MhswID, PMBMhswID, TrxID,
    Tanggal, Jumlah, Keterangan, LoginBuat, TanggalBuat)
    values ('$BPMID', '$w[TahunID]', '$mhsw[MhswID]', 1, -1,
    '$Tanggal', $bal, '$Keterangan', '$_SESSION[_Login]', now())";
  $r = _query($s);
  // update balance
  //$s2 = "Update khs set Tarik = $bal where KHSID = $khsid";
  //$r2 = _query($s2);
  // Lakukan pencetakan
  CetakTrxAmbil($BPMID, $bal, $Keterangan, $Rep);
  // Kembali
  Ambilkan($mhsw);
}
function CetakTrxAmbil($BPMID, $bal, $Keterangan, $Rep) {
  global $_lf;
  //include "terbilang.php";
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $maxcol = 80;
  //$div = str_pad('-',$maxcol,'-').$_;
  $f = fopen($nmf, 'w');
  $Nama = GetaField("mhsw", 'MhswID', $_SESSION['mhswid'], 'Nama');
  $_bal = Number_format($bal);
  fwrite($f, chr(27).chr(18) . chr(27).chr(108).chr(0));
  fwrite($f, str_pad("BUKTI KELEBIHAN PEMBAYARAN", $maxcol, ' ', STR_PAD_BOTH ). $_lf.$_lf);
  fwrite($f, str_pad("SEMESTER : " . NamaTahun($_SESSION['tahun']), $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf . $_lf);
  $isi = str_pad('NIM          : ' . $_SESSION['mhswid'], 50, ' ' ) . $_lf . $_lf .
         str_pad('NAMA         : ' . $Nama, 50, ' ') . $_lf . $_lf .
         str_pad('JUMLAH       : Rp. ' . $_bal, 50, ' '  ) . $_lf . $_lf .
         str_pad('TERBILANG    : ' . SpellNumberID($bal) . "Rupiah", 50, ' ') . $_lf . $_lf .
         str_pad('KETERANGAN   : ' . $Keterangan, 60, ' ') . $_lf . $_lf .
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

// *** Parameters ***
$tahun = GetSetVar('tahun');
$mhswid = GetSetVar('mhswid');
$gos = $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Pengambilan Kelebihan Bayar Mhsw");
TampilkanCariMhsw('mhswkeu.ambil', 'Ambilkan');
if (!empty($mhswid) && !empty($gos)) {
  $mhsw = GetFields("mhsw m
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID", 
    "m.MhswID", $mhswid, 
    "m.*, sm.Nama as STT, sm.Nilai, sm.Keluar, prg.Nama as PRG, prd.Nama as PRD,
    bpt.Nama as BPT");
  if (!empty($mhsw)) {
    if ($mhsw['Keluar'] == 'Y') echo ErrorMsg("Mahasiswa Telah $mhsw[STT]",
      "Status mahasiswa adalah <b>$mhsw[STT]</b> sehingga data tidak dapat diubah-ubah lagi."); 
    else {
      TampilkanHeaderBesar($mhsw, 'mhswkeu.ambil', '', 0);
      $gos($mhsw);
    }
  }
  else echo ErrorMsg("Mahasiwa Tidak Ada",
    "Mahasiswa dengan NPM <b>$mhswid</b> tidak ditemukan.");
}
?>
