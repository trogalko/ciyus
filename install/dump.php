<?php
function frmdmp(){
echo <<<HTML
    <div class=box><h1 class=title>Konfigurasi Database</h1></div>
    <div class=content>
        <form action="?" name=data method=post onSubmit="return CheckForm(this);">
        <input type='hidden' name='instl' value='dump'>
        <input type='hidden' name='foc' value='getQuery'>
        <input type='hidden' name='step' value='2'>
        <p>
        <div style=text-align:center;margin-top:50px>
            Proses ini akan mengimport database yang akan digunakan aplikasi Smart Sisfo Kampus. Proses ini akan berlansung beberapa menit tergantung kecepatan komputer Anda.
            <p><input class="submit" type="submit" value="PROSES" />
            </p>
        </div>
        </p>
        </form>
    </div>
HTML;
}

function getQuery(){
    $sqlfile = "./install/semarang35.sql";
    $text = file_get_contents($sqlfile);
    $created = count(explode('CREATE', $text));
    $insert = count(explode('INSERT', $text));
    $tot = $created + $insert;
    $dump = file($sqlfile);
    $tmpsql = '';
    $n = 0;
    foreach ($dump as $linesql_num => $linesql) {
        //lanjutkan jika masih syntax sql
        if (substr($linesql, 0, 2) !== '--' && $linesql != '' && substr($linesql, 0, 2) !== '/*') {
            $tmpsql .= $linesql;
            //jika sudah akhir query (;), maka buat query
            if (substr(trim($linesql), -1, 1) == ';') {
                $n++;
                $_SESSION['DB-SQL' . $n] = $tmpsql;
                $tmpsql = '';
            }
        }
    }
    
    $_SESSION['DB-Max'] = $tot;
    $_SESSION['DB-Pos'] = 0;
    
    echo "<center><p bgcolor=#ff77ff><IFRAME src='restoredb.php?gos=go' frameborder=0 height=300width=600></IFRAME></p></center>";

}

$gos = (empty($_REQUEST['foc'])) ? "frmdmp" : $_REQUEST['foc'];
$gos();
?>