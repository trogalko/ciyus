<?php
// Author: Emanuel Setio Dewo
// 20 March 2006

include_once "mhsw.hdr.php";
include_once "mhswkeu.lib.php";
include_once "mhswkeu.sav.php";

// *** Functions ***
function BPMMhsw() {
  global $mhsw, $khs;
  include_once "mhswkeu.lib.php";
  $cbpm = TampilkanCetakBPM($mhsw, $khs);
  $kbpm = PembayaranBPM($mhsw, $khs, 1);
  echo "<p><table class=bsc cellspacing=1 cellpadding=4 width=100%>
  <tr><td valign=top>$cbpm</td>
  <td valign=top>$kbpm</td></tr>
  </table></p>";
  
  $ka = DaftarPembayaran($mhsw, $khs, 'bpm', 'BayarBPM', 'BPMMhsw');
  $ki = TampilkanBiayaPotongan($mhsw, $khs);
  
  // Tampilkan
  if ($_SESSION['TampilkanDetail'] == 1) {
    echo "<p><table class=bsc cellspacing=1 cellpadding=4>
      <tr><td valign=top>$ki</td>
      <td valign=top>$ka</td>
      </tr></table></p>";
  }
  // Tampilkan Summary
  TampilkanSummaryKeuMhsw($mhsw, $khs);
}


// *** Parameters ***
$crmhsw = GetSetVar('crmhsw');
$crmhswid = GetSetVar('crmhswid');
$rekid = GetSetVar('rekid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'BPMMhsw' : $_REQUEST['gos'];
$UkuranHeader = GetSetVar('UkuranHeader', 'Kecil');
$TampilkanDetail = GetSetVar('TampilkanDetail', 1);
$bpmblank = GetSetVar('bpmblank', 0);

// *** Main ***
TampilkanJudul("Bukti Pembayaran Mahasiswa - BPM");
TampilkanPencarianMhswTahun('bpm', 'BPMMhsw', 1);
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
    $Thnaktif = GetaField('tahun', "ProdiID = '$mhsw[ProdiID]' and ProgramID = '$mhsw[ProgramID]' and TahunID", $tahun, 'NA');
    $TampilkanHeader = "TampilkanHeader$UkuranHeader";
    $TampilkanHeader($mhsw, 'bpm');
    $khs = GetFields("khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID", 
      "k.MhswID='$mhswid' and k.TahunID", $tahun, 
      "k.*, sm.Nama as SM, sm.Keluar");
    if (!empty($khs)) {
      if ($Thnaktif == 'Y') echo ErrorMsg('Periode Akademik Sudah Lewat',
      "Anda tidak dapat melakukan pembayaran pada periode <b>$tahun</b> karena periode ini sudah ditutup.
      <hr size=1 color=silver>
      Silakan anda masukkan periode akademik aktif saat ini.");
      else $gos();
    }
    else echo ErrorMsg("Data Tidak Ada",
      "Mahasiswa tidak mengambil semester di tahun akademik: <b>$tahun</b>.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=mhswakd&gos=MhswAkdEdt&mhswid=$mhsw[MhswID]&tahun=$tahun'>Buat Tahun Akademik</a>");
  } else echo ErrorMsg("Data Tidak Ditemukan",
      "Mahasiswa dengan NPM: <b>$mhswid</b> tidak ditemukan.<br />
      NPM harus sesuai dengan yang tertera di KSM (Kartu Studi Mahasiswa).");
}
?>
