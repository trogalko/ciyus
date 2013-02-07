<?php
// Author: Emanuel Setio Dewo
// 26 May 2006
// http://www.sisfokampus.net

function PrcLoginMhsw() {
  $Login = $_REQUEST['Login'];
  $Password = $_REQUEST['Password'];
  // cek
  $ada = GetFields('mhsw', "Password=LEFT(Password('$Password'), 10) and MhswID",
    $Login, "*");
  if (empty($ada)) {  
    echo ErrorMsg("Gagal Login",
      "NPM dan password yang Anda masukkan tidak ditemukan.<br />
      Anda tidak dapat login dengan NPM dan password ini.<br />
      Silakan hubungi Tata Usaha untuk memperoleh NPM & Password yang valid.
      <hr size=1>
      Pilihan: <a href='?mnux='>Login</a>");
  }
  else {
    // Simpan session
    $_SESSION['__Login'] = $ada['MhswID'];
    $_SESSION['__Nama'] = $ada['Nama'];
    $_SESSION['__Email'] = $ada['Email'];
    $_SESSION['mnux'] = 'mhsw';
    $_SESSION['sub'] = 'DM';
    
    // Welcome
    echo "<p><table class=bsc width=100%>
    <tr><td class=hdr align=center>Selamat datang <b>$_SESSION[__Nama]</b></td></tr>
    </table></p>";
  }
}

// *** Main ***
//PrcLoginMhsw();
?>
