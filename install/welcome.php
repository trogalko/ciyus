<?php
// Welcome

function welcome(){
    echo "<div class=box><h1 class=title>Selamat Datang di Instalasi Aplikasi Smart Sisfo Kampus</h1></div>";
    echo "<div class=content>Proses instalasi ini akan menbuat data-data konfigurasi untuk aplikasi Smart Sisfo Kampus.
          Proses instalasi akan berlangsung beberapa menit. Klik INSTALL jika anda sudah siap.</div>";
          
    if (CekPermission()) {
        echo "<center><input type=button name=install value=INSTALL onClick=\"location='install.php?instl=conf&foc=frmDB&step=1'\"><center>";
    } else {
        echo "<center><p><font color=red><b>PERHATIAN : Ubah terlebih dahulu permisson Folder Anda untuk dapat akses Write.</b></p></font></center>";
    }
}

function CekPermission(){
    if (is_writable(realpath("index.php"))) return true;
    else return false;
}

welcome();
?>