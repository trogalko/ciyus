<?php
function Berhasil($user, $pass, $host, $name) {
echo "
<div class=box><h1 class=title>Konfigurasi Database</h1></div>
<div class=content>
    <fieldset>
      <legend>Database</legend>
      <ol>
        <li>
          <label for=\"Host\">Host :</label>
          <span><b>$host</b></span>
        </li>
        <li>
          <label for=\"Username\">Username :</label>
          <span><b>$user</b></span>
        </li>
        <li>
          <label for=\"Password\">Password :</label>
        <span><b>$pass</b></span>
        </li>
        <li>
          <label for=\"Nama\">Nama Database :</label>
          <span><b>$name</b></span>
        </li>
      </ol>
    </fieldset>
    <div>
        File Konfigurasi untuk database telah berhasil dibuat. Lanjutkan ke proses selanjutnya? <br /><br /><br />
         <center><input type=button name=install value='    Step 2    ' onClick=\"location='?instl=dump&foc=frmdmp&step=2'\"><center>
    </div>
</div>";
}

function frmDB(){
    CheckFormScript("host,username,nama");
echo <<<HTML
<div class=box><h1 class=title>Konfigurasi Database</h1></div>
<div class=content>
    <form action="?" name=data method=post onSubmit="return CheckForm(this);">
    <input type='hidden' name='instl' value='conf'>
    <input type='hidden' name='foc' value='wrconf'>
    <input type='hidden' name='step' value='1'>
    <fieldset>
      <legend>Database</legend>
      <ol>
        <li>
          <label for="Host">Host :</label>
          <input id="host" name="host" class="text" type="text" value="localhost" />
        </li>
        <li>
          <label for="Username">Username :</label>
          <input id="username" name="username" class="text" type="text" value='root' />
        </li>
        <li>
          <label for="Password">Password :</label>
          <input id="password" name="password" class="text" type="password" />
        </li>
        <li>
          <label for="Nama">Nama Database :</label>
          <input id="nama" name="nama" class="text" type="text" />
        </li>
      </ol>
    </fieldset>
     <fieldset class="submit">
       <input class="submit" type="button" onClick="javascript: history.go(-1)" value="    Kembali    " />&nbsp;&nbsp;
       <input class="submit" type="submit" value="    Proses    " />
     </fieldset>
   </form>
</div>
HTML;
}

function createDB($host, $user, $pass, $dbname){
    $s = mysql_connect($host, $user, $pass);
    if ($s) {
        if (mysql_select_db($dbname, $s)) {
            return false;
        } else {
            $qu = "CREATE DATABASE $dbname";
            if (mysql_query($qu, $s)) {}
        }
    }
}

function wrconf(){
    $Host = $_REQUEST['host'];
    $User = $_REQUEST['username'];
    $Pass = $_REQUEST['password'];
    $Name = $_REQUEST['nama'];
    
    createDB($Host, $User, $Pass, $Name);
    
    // Buka template
    $tpl = "./install/connectdb.php.txt";
    if (file_exists($tpl)) {
        $ft = fopen($tpl, 'r');
        $tpldb = fread($ft, filesize($tpl));
        fclose($ft);
        
        // Buka file konfigurasi
        $file = "connectdb.php";
        $f = fopen($file, "w");
        
        $_t = $tpldb . chr(12);
        $_t = str_replace('~Host~', $Host, $_t);
        $_t = str_replace('~User~', $User, $_t);
        $_t = str_replace('~Pass~', $Pass, $_t);
        $_t = str_replace('~Name~', $Name, $_t);
        
        // tulis
        fwrite($f, $_t);
        fclose($f);
        
        Berhasil($User, $Pass, $Host, $Name);
    } else {
        echo "failed";
    }
}

$gos = (empty($_REQUEST['foc'])) ? "frmDB" : $_REQUEST['foc'];
$gos();
?>