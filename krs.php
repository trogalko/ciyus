<?php
// Author: Emanuel Setio Dewo
// 14 March 2006
// www.sisfokampus.net

// Level yg dapat mengakses paket KRS
$_LevelPaketKRS = ".1.41.40.";
// Level yg dapat mengakses mundur
$_LevelMundurKRS = ".1.40.";

include_once "krs.lib.php";

// *** Parameters ***
if ($_SESSION['_LevelID'] == 120) {
  $mhswid = $_SESSION['_Login'];
}
else {
  $mhswid = GetSetVar('mhswid');
}
$tahun = GetSetVar('tahun');
$MKPaketID = GetSetVar('MKPaketID');
$NamaKelas = GetSetVar('NamaKelas');
$gos = (empty($_REQUEST['gos']))? 'DftrKRS' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Kartu Rencana Studi Mahasiswa");
TampilkanCariMhsw();
if (!empty($mhswid) && !empty($tahun)) {
  $mhsw = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID",
    "m.MhswID", $mhswid,
    "m.*, prg.Nama as PRG, prd.Nama as PRD,
    sm.Nama as SM, sm.Nilai as SMNilai, sm.Keluar");
  if (empty($mhsw)) {
    echo ErrorMsg("Mahasiswa Tidak Ditemukan",
      "Tidak ada mahasiswa dengan NPM: <b>$mhswid</b>");
  }
  else {
    $datatahun = GetFields('tahun',
      "KodeID='$_SESSION[KodeID]' and ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and TahunID",
      $tahun, '*');
//    $khs = GetFields("khs k
//      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID",
//      "k.MhswID='$mhswid' and k.TahunID", $tahun,
//      "k.*, sm.Nama as SM, sm.Nilai as SMNilai, sm.Keluar");
    $khs = GetFields("khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID",
      "k.MhswID='$mhswid' and k.TahunID", $tahun,
      "k.*, sm.Nama as SM, sm.Nilai as SMNilai, sm.Keluar");
    if (empty($khs) || empty($datatahun)) {
      echo ErrorMsg("Mahasiswa Tidak Terdaftar",
        "<p>Ada dua kemungkinan kesalahan:</p>
        <ol>
        <li>Mahasiswa <b>$mhsw[Nama]</b> ($mhswid) tidak terdaftar untuk
        sesi/semester <b>$tahun</b>.</li>
        <li>Fakultas/Jurusan belum mengaktifkan tahun akademik: <b>$tahun</b>.
        </ol>");
    }
    else {
      // Cek maksimum SKS
      if (($khs['Sesi'] <=1) and ($khs['MaxSKS'] == 0)) BuatDefaultMaxSKS($khs);
      elseif (($khs['Sesi'] > 1) and ($khs['MaxSKS'] == 0)) {
        $khsprev = GetFields('khs', "MhswID='$mhswid' and Sesi", $khs['Sesi']-1, '*');
        if (empty($khsprev)) BuatDefaultMaxSKS($khs);
        else {
          $MaxSKS = GetaField('maxsks', "DariIP <= $khsprev[IPS] and $khsprev[IPS] <= SampaiIP and ProdiID",
            $khs['ProdiID'], "SKS");
          $khs['MaxSKS'] = $MaxSKS;
          // Simpan
          //$s = "update khs set MaxSKS=$MaxSKS where KHSID=$khs[KHSID]";
          $s = "update khs set MaxSKS=18 where KHSID=$khs[KHSID]";
          $r = _query($s);
        }
      }
      HeaderKRSMhsw($mhsw, $datatahun, $khs);
      //echo "HITUNG SKS ULANG";
      //UpdateJumlahKRSMhsw($mhsw['MhswID'], $khs['KHSID']);
      if ($khs['CetakKRS'] > 0) echo Konfirmasi1("Mahasiswa sudah pernah cetak KRS sebanyak $khs[CetakKRS] kali.");
      if ($mhsw['BatasStudi'] >= $tahun) $gos($mhsw, $datatahun, $khs);
      else echo ErrorMsg("Tidak Dapat Mengisi KRS",
        "Mahasiswa <font size=+1>$mhsw[MhswID]</font> tidak dapat mengisi KRS karena
        telah melampuai batas studinya, yaitu: <font size=+1>$mhsw[BatasStudi]</font>");
    }
  }
}
function BuatDefaultMaxSKS(&$khs) {
  $MaxSKS = GetaField('prodi', 'ProdiID', $khs['ProdiID'], 'DefSKS')+0;
  $khs['MaxSKS'] = $MaxSKS;
  // Simpan
  $s = "update khs set MaxSKS=$MaxSKS where KHSID=$khs[KHSID] ";
  $r = _query($s);
}
?>
