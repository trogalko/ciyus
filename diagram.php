<?php
// Author: Emanuel Setio Dewo
$arrDiagram = array(
  "Prosedur Mahasiswa Baru~000 Mhsw Baru.png",
  "Prosedur Proses Pembuatan NPM Mhsw Baru~001 Buat NPM Baru.png",
  "Prosedur Kegiatan Semester~100 Semester.png",
  "Prosedur Pembuatan Semester Baru~101 Buat Semester.png",
  "Prosedur Perkuliahan~102 Kuliah.png",
  "Prosedur Pengajuan Cuti~110 Cuti.png",
  "Prosedur Pengisian Nilai~111 Isi Nilai.png"
  );

$arrPanduan = array(
  "Memulai Sisfo Kampus~Memulai Sisfo Kampus.doc",
  "Panduan PMB~ManualBookPMB.doc",
  "Modul Admisi~Modul Admisi.doc",
  "Modul Akademik~Modul Akademik.doc",
  "Panduan Mahasiswa~PanduanMhsw.doc",
  "Status Mahasiswa~Status Mahasiswa.doc",
  "Panduan Modul Bugs dan Error~Modul Bugs Error.doc"
  );

TampilkanJudul("Daftar Manual dan Diagram Desain Sistem");
TampilkanDaftarDiagram();
TampilkanDaftarPanduan();

function TampilkanDaftarDiagram() {
  global $arrDiagram;
  echo "<p><h3>Daftar Diagram</h3></p>";
  echo "<ol>";
  for ($i = 0; $i < sizeof($arrDiagram); $i++) {
    $a = Explode('~', $arrDiagram[$i]);
    echo "<li><a href='desain/" . $a[1] . "' target=_blank>" .
      $a[0] . "</a>".
      "</li>";
  }
  echo "</ol>";
}
function TampilkanDaftarPanduan() {
  global $arrPanduan;
  echo "<p><h3>Daftar Panduan</h3></p>";
  echo "<ol>";
  for ($i = 0; $i < sizeof($arrPanduan); $i++) {
    $a = Explode('~', $arrPanduan[$i]);
    echo "<li><a href='desain/" . $a[1] . "' target=_blank>" .
      $a[0] .
      "</li>";
  }
  echo "</ol>";
}
?>
