<?php
// Author: Emanuel Setio Dewo
// 16 April 2006
// Selamat Paskah

// *** Functions ***
function DftrLapAkd() {
  global $arrAkdLap;
  $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th><th class=ttl>Nama Laporan</th>
    <th class=ttl>Script</th></tr>";
  for ($i=0; $i<sizeof($arrAkdLap); $i++) {
    $n++;
    $lap = explode('->', $arrAkdLap[$i]);
    echo "<tr><td class=inp>$n</td>
      <td class=ul><a href='?mnux=akd.lap.$lap[1]&bck=akd.lap'>$lap[0]</a></td>
      <td class=ul>$lap[1]</td></tr>";
  }
  echo "</table></p>";
}

// *** Parameters & Variables ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$arrAkdLap = array("Daftar KRS Mahasiswa->krsmhsw",
  "Daftar Mahasiswa Terdaftar KRS->dftrkrsmhsw",
	"Daftar Mahasiswa Terdaftar KRS Tidak Cetak KSS->dftrkrsmhswtidakkss",
	"Rekapitulasi Mahasiswa Terdaftar KRS->rekapmhswkrs",
  "Daftar Mahasiswa Terdaftar KRS Dispensasi->krsmhsw.dispen",
  "Daftar Mahasiswa Bolos KRS->krsmhswbolos",
  "Daftar Mahasiswa Semester Aktif->mhswakd",
  "Daftar Mahasiswa DO/Keluar->mhswdokeluar",
  "Daftar Mahasiswa DO->mhswakd&status=D",
  "Daftar Mahasiswa Keluar->mhswakd&status=K",
  "Daftar Mahasiswa Bolos->statuscutibolos",
  "Daftar Mhsw yg Cetak KSS->mhswkss",
  "Daftar Mahasiswa Cuti Kuliah->mhswcuti",
  "Daftar Mhsw yg Hanya Ambil Skripsi/Tugas Akhir->mhswta",
  "Rekapitulasi Mahasiswa Terdaftar Semester->rekapmhsw",
  "Daftar Pembimbing Akademik & Mahasiswa->pamhsw",
  "Daftar IPK/IPS Mahasiswa->ipkipsmhsw",
  "Daftar Mahasiswa Habis Masa Studi->habismasa",
  "Rekap Nilai Tertinggi per Matakuliah->tingginilai.cetak",
  "History Nilai Matakuliah per Mahasiswa->historynilaisesi",
  "Perolehan SKS Mhsw (masal)->perolehansks",
  "Filter Jumlah SKS Mahasiswa->jumlahsks",
  "Rekapitulasi Jumlah Nilai Mahasiswa->rekapjmlnilai",
  "Rekapitulasi Nilai & Prasyarat Mahasiswa->rekapnilaiprasyaratmhsw",
  "Laporan Koreksi Nilai->koreksinilai",
  "Daftar Dosen yg Mengajar->dosenaktif",
	"Daftar Aktifitas Mengajar Dosen->aktifitasdosen",
  "Daftar Semua Dosen Honorer (urut Nama)->dosenhonorer",
  "Daftar Semua Dosen Honorer (urut Kode)->dosenhonorer&Urut=1",
  "Daftar Penilaian Porto Folio dan Performance->portoperformance",
  "Daftar Nilai Mahasiswa yang D dan E->nilaiedand3");
$gos = (empty($_REQUEST['gos']))? "DftrLapAkd" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Laporan Akademik");
$gos();
?>
