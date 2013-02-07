<?php
function TampilkanHeader($w) {
  $foto = FileFotoMhsw($w, $w['Foto']);
  echo "<p><table class=box cellspacing=2 cellpadding=4>
  <tr><td colspan=2 class=ul><b>Data Mahasiswa</td>
    <td rowspan=7 class=box style='padding: 2pt'><img src='$foto'></td></tr>
  <tr><td class=ul>NPM</td><td class=ul><b>$w[MhswID]</td></tr>

  <tr><td class=ul>Nama</td><td class=ul><b>$w[Nama]</td></tr>
  <tr><td class=ul>Program</td><td class=ul><b>$w[ProgramID]</td></tr>
  <tr><td class=ul>Program Studi</td><td class=ul><b>$w[ProdiID]</td></tr>
  <tr><td class=ul>File Foto</td><td class=ul>$w[Foto]</td></tr>
  <tr><td class=ul>Pilihan</td><td class=ul><a href='?mnux=mhsw.edt&mhswid=$w[MhswID]'>Kembali ke data mahasiswa</a></td></tr>
  </table></p>";
}
function TampilkanUploadFoto($w) {
  $MaxFileSize = 50000;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='index.php' enctype='multipart/form-data' method=POST>
  <input type=hidden name='MAX_FILE_SIZE' value='$MaxFileSize' />
  <input type=hidden name='mnux' value='mhsw.foto'>
  <input type=hidden name='gos' value='aplodFoto'>
  <input type=hidden name='mhswid' value='$w[MhswID]'>
  <tr><td class=inp1>Nama File Foto</td>
    <td class=ul><input type=file name='foto' size=50></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Upload' value='Upload File Foto'></td></tr>
  </form></table></p>";
}
function aplodFoto() {
  $MhswID = $_REQUEST['mhswid'];
  $upf = $_FILES['foto']['tmp_name'];
  $arrNama = explode('.', $_FILES['foto']['name']);
  $tipe = $_FILES['foto']['type'];
  $arrtipe = explode('/', $tipe);
  $extensi = $arrtipe[1];
  $dest = "foto/" . $MhswID . '.' . $extensi;
  //echo $dest;
  if (move_uploaded_file($upf, $dest)) {
    $s = "update mhsw set Foto='$dest' where MhswID='$MhswID' ";
    $r = _query($s);
  }
  else echo ErrorMsg("Gagal Upload Foto",
    "Tidak dapat meng-upload file foto.<br />
    Periksa file yg di-upload, karena besar file dibatasi cuma: <b>$_REQUEST[MAX_FILE_SIZE]</b> byte.");
  //print_r($_FILES);
}

$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];
$gos();
$w = GetFields('mhsw', 'MhswID', $_REQUEST['mhswid'], '*');

// *** Main ***
TampilkanHeader($w);
TampilkanUploadFoto($w);
?>
