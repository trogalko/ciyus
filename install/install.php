<?php
session_start();

include "dwo.lib.php";

if (empty($_REQUEST['instl'])) $_REQUEST['instl'] = 'welcome';

function clsNav(){
    $nav = array("Selamat Datang",
                 "Konfigurasi Database",
                 "Dumping Database",
                 "Konfigurasi System",
                 "Selesai");
    $cls = $_REQUEST['step'];
    
    $a = '';
    for($i=0; $i<sizeOf($nav); $i++) {
        $act = ($cls == $i) ? "class='small cwSelectedTab'" : "class='small cwUnSelectedTab";
        $a .= "<tr><td $act align=right><div align='left'><b>$nav[$i]</b></div></td></tr>"; 
    }
    
    return $a;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>Smart Sisfo Kampus 3.4 Installation</title>
   <link href="css/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
   <br><br><br>

    <table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
        <tr>
            <td class="cwHeadBg" align=left>&nbsp;</td>
            <td class="cwHeadBg" align=right>&nbsp;</td>
        </tr>
    </table>
	
    <table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
        <tr>
            <td background="include/install/images/topInnerShadow.gif" align=left><img src="include/install/images/topInnerShadow.gif" ></td>
        </tr>
    </table>
	
    <table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
        <tr>
            <td class="small" bgcolor="#FF4200" align=center>
                <!-- Master display -->
                <table border=0 cellspacing=0 cellpadding=0 width=97%>
                    <tr>
                        <td width=20% valign=top>
                        <!-- Left side tabs -->
                            <table border=0 cellspacing=0 cellpadding=10 width=100%>
                               <?php
                                echo clsNav();
                               ?>
                            </table> 
                        </td>
                        <td width=80% valign=top class="cwContentDisplay" align=left>
                        <!-- Right side tabs -->
                            <div class=isi>
                                <?php
                                    if (file_exists($_REQUEST['instl'] . ".php")) {
                                        include $_REQUEST['instl'] . ".php";
                                    }
                                ?>
                            </div>
			</td>
                    </tr>
                </table>
                <!-- Master display stops -->
                <br>
            </td>
	</tr>
    </table>
    <table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
        <tr>
            <td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
    </table>
	
    <table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
	    <td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
    </table>
    	
    <table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
      	<tr>
            <td class=small align=center> <a href="http://www.sisfokampus.net" target="_blank">www.sisfokampus.net</a></td>
      	</tr>
    </table>
</body>
</html>