<?php
function frmKonf(){
    CheckFormScript("kode,nama,username,pass1,pass2");
echo <<<HTML
<div class=box><h1 class=title>Konfigurasi System</h1></div>
<div class=content>
    <form action="?" name=data method=post onSubmit="return CheckForm(this);">
    <input type='hidden' name='instl' value='param'>
    <input type='hidden' name='foc' value='wrData'>
    <input type='hidden' name='step' value='3'>
    <fieldset>
      <legend>Intitusi</legend>
      <ol>
        <li>
          <label for="Kode">Kode Institusi :</label>
          <input id="kode" name="kode" class="text" type="text" value='$_SESSION[inst]' />
        </li>
        <li>
          <label for="Nama">Nama Institusi :</label>
          <input id="nama" name="nama" class="text" type="text" value='$_SESSION[nmins]' />
        </li>
      </ol>
    </fieldset>
    <fieldset>
      <legend>Login System</legend>
      <ol>
        <li>
          <label for="username">Superuser :</label>
          <input id="username" name="username" class="text" type="text" value='$_SESSION[usrnm]' />
        </li>
        <li>
          <label for="nama_user">Nama :</label>
          <input id="nama_user" name="nama_user" class="text" type="text" />
        </li>
        <li>
          <label for="pass1">Password :</label>
          <input id="pass1" name="pass1" class="text" type="password" />
        </li>
        <li>
          <label for="pass2">Ulangi Password :</label>
          <input id="pass2" name="pass2" class="text" type="password" />
        </li>
      </ol>
    </fieldset>         
     <fieldset class="submit">
       <input class="submit" type="submit" value="Kirim" />
     </fieldset>
   </form>
</div>
HTML;
}

function wrData(){
    $kd_institusi = sqling($_REQUEST['kode']);
    $nm_institusi = sqling($_REQUEST['nama']);
    
    $username = sqling($_REQUEST['username']);
    $nama_user= sqling($_REQUEST['nama_user']);
    $pass1    = sqling($_REQUEST['pass1']);
    $pass2    = sqling($_REQUEST['pass2']);
    
    if ($pass1 != $pass2) {
        $_SESSION['inst'] = $kd_institusi;
        $_SESSION['nmins'] = $nm_institusi;
        $_SESSION['usrnm'] = $username;
        
        echo ErrorMsg("Password Tidak sama", "Password yang Anda masukkan tidak sama silakan ulangi kembali.<hr />
                      <a href=?instl=param&foc=frmKonf>Kembali</a>");
    } else {
        $s = "INSERT INTO identitas (Kode, Nama) VALUES ('$kd_institusi', '$nm_institusi')";
        $r = _query($s);
        
        $s0 = "INSERT INTO karyawan (Login, KodeID, Nama, LevelID, Password) VALUES ('$username', '$kd_institusi', '$nama_user', 1, PASSWORD('$pass1'))";
        $r0 = _query($s0);
        
        // Buka template
        $tpl = "./install/parameter.php.txt";
        if (file_exists($tpl)) {
            $ft = fopen($tpl, 'r');
            $tpldb = fread($ft, filesize($tpl));
            fclose($ft);
            
            // Buka file konfigurasi
            $file = "parameter.php";
            $f = fopen($file, "w");
            
            $_t = $tpldb . chr(12);
            $_t = str_replace('~INSTITUSI~', $nm_institusi, $_t);
            $_t = str_replace('~KODEINSTITUSI~', $kd_institusi, $_t);
            
            // tulis
            fwrite($f, $_t);
            fclose($f);
            
        } else {
            echo "failed";
        }
        
       echo "<div class=box><h1 class=title>Konfigurasi System Berhasil</h1></div>";
       echo "<div class=content>
        <div style=text-align:center;margin-top:50px;>
        Proses pembuatan file Konfigurasi berhasil dilakukan. Silakan lanjutkan proses selanjutnya.
        </div>
        </div>";
        
       echo "<center><input type=button name=step value='  Step 4  ' onClick=\"location='install.php?instl=finish&foc=viefnsh&step=4'\"><center>";
    }    
}

$gos = (empty($_REQUEST['foc'])) ? "frmKonf" : $_REQUEST['foc'];
$gos();
?>