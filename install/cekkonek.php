<?php
function checkConection(){
    $host = $_REQUEST['hst'];
    $user = $_REQUEST['usr'];
    $pass = $_REQUEST['pwd'];
    
    $lnk = mysql_connect($host, $user, $pass);
    
    if ($lnk) {
        echo "Koneksi Berhasil";
    } else {
        echo "Koneksi Gagal";
    }
}

checkConection();
?>