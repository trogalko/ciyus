<?php
function viefnsh(){
    echo "<div class=box><h1 class=title>Proses Instalasi Smart Sisfo Kampus Selesai</h1></div>";
    echo "<div class=content>Selamat!! Anda telah berhasil melakukan instalasi Smart Sisfo Kampus. Selanjutnya silakan Anda delete file install.php sebagai langkah pengamanan.</br>
          Selanjutnya ada dapat menggunakan Smart Sisfo Kampus di <a href=index.php>Sini</a>.<br />
          </div>";
}

$gos = (empty($_REQUEST['foc'])) ? "viefnsh" : $_REQUEST['foc'];
$gos();
?>