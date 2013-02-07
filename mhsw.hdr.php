<?php
// Author: Emanuel Setio Dewo
// 20 March 2006

function TampilkanHeaderBesar($w, $mnux='mhswkeu.det', $gos='', $pil=1) {
  global $arrID;
  $foto = FileFotoMhsw($w['MhswID'], $w['Foto']);
  $strpil = ($pil == 0) ? '' : "<tr><td class=ul>Pilihan</td><td class=ul><a href='?mnux=$mnux'>Kembali ke Daftar Mhsw</a> |
    <a href='?$_SERVER[QUERY_STRING]&UkuranHeader=Kecil'>Kecilkan</a>
    </td></tr>";
  // Tampilkan
  $pa = GetaField('dosen', 'Login', $w['PenasehatAkademik'], "concat(Nama, ', ', Gelar, ' (', Login, ')')");
  echo "<p><table class=box cellspacing=2 cellpadding=4>
  <tr><td class=inp1 colspan=4><b>Data Mahasiswa</b></td>
    <td class=box rowspan=7 style='padding: 2pt'><img src='$foto' width=120 height=150></td></tr>
  <tr><td class=inp>NPM</td><td class=ul><b>$w[MhswID]</b></td>
    <td class=inp>Nama</td><td class=ul><b>$w[Nama]</b></td></tr>
  <tr><td class=inp>Program</td><td class=ul><b>$w[PRG]</b> ($w[ProgramID])</td>
    <td class=inp>Program Studi</td><td class=ul><b>$w[PRD]</b> ($w[ProdiID])</td></tr>
  <tr><td class=inp>Master Biaya & Potongan</td><td class=ul><b>$w[BPT]&nbsp;</td>
    <td class=inp>Penasehat Akademik</td><td class=ul><b>$pa</b>&nbsp;</td></tr>
  $strpil
  </table></p>";
}
function TampilkanHeaderKecil($w, $mnux='mhswkeu.det', $gos='', $pil=1) {
  global $arrID;
  $foto = FileFotoMhsw($w['MhswID'], $w['Foto']);
  $strpil = ($pil == 0) ? '' : "<tr><td class=ul colspan=2>Pilihan: <a href='?mnux=$mnux'>Kembali ke Daftar Mhsw</a> |
    <a href='?$_SERVER[QUERY_STRING]&UkuranHeader=Besar'>Besarkan</a>";

  // Tampilkan
  echo "<p><table class=box cellspacing=2 cellpadding=4>
  <tr><th class=ttl>Mahasiswa</th><th class=ttl>Program/Program Studi</th></tr>
  <tr><td class=ul>$w[MhswID]: <b>$w[Nama]</b></td>
    <td class=ul><b>$w[PRG]</b> ($w[ProgramID]) - <b>$w[PRD]</b> ($w[ProdiID])</td></tr>
    $strpil
    </td></tr>
  </table></p>";
}
?>
