<?php
// Author: Emanuel Setio Dewo
// 21 Agustus 2006
// www.sisfokampus.net

// *** Functions ***
function BatalkanBeasiswa($mhsw, $beas) {
  if ($beas['Proses'] == 'Y') {
    if ($beas['NA'] == 'N') {
      echo Konfirmasi("Konfirmasi Pembatalan Beasiswa",
      "Benar Anda akan membatalkan beasiswa mahasiswa ini?<br />
      Membatalkan beasiswa berarti melakukan hal berikut ini:
      <ol>
      <li>Membatalkan potongan beasiswa di keuangan mahasiswa.</li>
      <li>Mengeset data beasiswa menjadi dibatalkan.</li>
      </ol><hr size=1>
      Pilihan: <input type=button name='Batalkan' value='Batalkan Beasiswa' onClick=\"location='?mnux=beasiswa.batal&mhswid=$mhsw[MhswID]&tahun=$beas[TahunID]&gos=BeasBtl&btl=$beas[BeasiswaMhswID]'\">");
    }
    else {
      echo ErrorMsg("Beasiswa Sudah Pernah Dibatalkan",
      "Beasiswa untuk mahasiswa <font size=+1>$mhsw[Nama]</font> untuk tahun <font size=+1>$beas[TahunID]</font>
      sudah pernah dibatalkan. Pembatalan tidak diproses.");
    }
  }
  else {
    echo Konfirmasi("Konfirmasi Penghapusan Permohonan Beasiswa",
      "Mahasiswa <font size=+1>$mhsw[Nama]</font> ($mhsw[MhswID]) masih dalam taraf pengajuan beasiswa.<br />
      Anda dapat langsung menghapus pemohonan beasiswa mahasiswa ini.
      <hr size=1>
      Pilihan: <a href='?mnux=beasiswa.batal&gos=BeasDel&del=$beas[BeasiswaMhswID]'>Hapus?</a>");
  }
}
function BeasBtl($mhsw, $beas) {
  $btl = $_REQUEST['btl'];
  $beas1 = GetFields('beasiswamhsw', 'BeasiswaMhswID', $btl, '*');
  $b = GetFields('beasiswa', 'BeasiswaID', $beas1['BeasiswaID'], '*');
  // Batalkan Beasiswa
  $s = "update beasiswamhsw set NA='Y' where BeasiswaMhswID=$btl";
  $r = _query($s);
  // Enolkan potongan di BIPOTMhsw
  $s1 = "update bipotmhsw set Jumlah=0, Dibayar=0 
    where MhswID='$mhsw[MhswID]'
      and BIPOTNamaID='$b[BIPOTNamaID]'
      and TahunID='$beas1[TahunID]' ";
  $r1 = _query($s1);
  // Hitung balance
  include_once "mhswkeu.lib.php";
  $KHSID = GetaField('khs', "TahunID='$beas1[TahunID]' and MhswID", $beas1['MhswID'], 'KHSID');
  HitungBiayaBayarMhsw($beas1['MhswID'], $KHSID);
  
  // Tampilkan pesan
  echo Konfirmasi("Beasiswa Sudah Dibatalkan",
    "Beasiswa untuk <font size=+1>$mhsw[Nama]</font> pada tahun <font size=+1>$beas1[TahunID]</font>
    sudah dibatalkan.<br />
    Harap periksa keuangan mahasiswa untuk tahun <font size=+1>$beas1[TahunID]</font>");
}
function BeasDel($mhsw, $beas) {
  $del = $_REQUEST['del'];
  $s = "delete from beasiswamhsw where BeasiswaMhswID=$del";
  $r = _query($s);
  echo Konfirmasi("Permohonan Beasiswa Sudah Dihapus",
    "Data permohonan untuk mahasiswa <font size=+1>$mhsw[MhswID]</font> tahun <font size=+1>$beas[TahunID]</font>
    telah dihapus.");
}

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? "BatalkanBeasiswa" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Pembatalan Beasiswa");
TampilkanCariMhsw('beasiswa.batal', 'BatalkanBeasiswa', 1);
if (!empty($tahun) && !empty($gos)) {
  $mhsw = GetFields("mhsw", "MhswID", $mhswid, "MhswID, Nama, ProdiID, ProgramID");
  if (!empty($mhsw)) {
    // apakah menerima beasiswa?
    $beas = GetFields("beasiswamhsw",
      "MhswID='$mhswid' and TahunID", $tahun, "*");
    if (!empty($mhsw)) $gos($mhsw, $beas);
    else echo ErrorMsg("Data Tidak Ditemukan",
      "Mahasiswa <font size=+1>$mhswid</font> tidak menerima beasiswa pada tahun <font size=+1>$tahun</font>.<br />
      Tidak dapat membatalkan beasiswa.");
  }
  // Mahasiswa tidak ada
  else echo ErrorMsg("Mahasiswa Tidak Ditemukan",
    "Mahasiswa dengan NPM <font size=+1>$mhswid</font> tidak ditemukan.");
}
?>
