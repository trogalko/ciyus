<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Selamat Ulang Tahun Ibu

// *** Functions ***
function getAjax(){
  echo <<<EOF
  <script language='JavaScript'>
    <!--
    function carikelajax(data) {
      var data = $('#MhswID').val();
      $.ajax({  
        type: "POST",
        url: "getpass.php?MhswID="+data,
        data: "MhswID="+data,
        success: function(msg){
          $('#PASLM').val(msg);
          }
      });
    }
  -->
  </script>
EOF;
}

function TampilkanFormHeaderResetPasswordMahasiswa() {
  getAjax();
  
  echo "<script>
  function CheckPwd(frm) {
    var pjg = frm.PWD1.value.length;
    if (pjg != 6) alert('Panjang password harus 6 karakter');
    var hsl = false;
    hsl = pjg == 6;
    if (hsl) {
      hsl = frm.PWD1.value == frm.PWD2.value;
      if (!hsl) alert('Password dan Konfirmasi Password tidak sama.');
    }
    return hsl;
  }
  </script>
  
  <p><table class=box cellspacing=1>
  <form action='?' method=GET onSubmit=\"return CheckPwd(this)\">
  <input type=hidden name='mnux' value='resetpwdmhsw'>
  <input type=hidden name='gos' value='ResetPwdMhsw'>
  
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</b></td></tr>
  <tr><td class=inp>Hak akses</td><td class=ul>$_SESSION[_ProdiID]</td></tr>
  <tr><td class=inp>NPM</td><td class=ul><input type=text id='MhswID' name='MhswID' value='$_SESSION[MhswID]' size=20 maxlength=50>&nbsp;&nbsp;<input type=button name=paslama value='Lihat Password Lama' onClick='carikelajax(this)'></td></tr>
  <tr><td class=inp>Password Lama</td><td class=ul><input type=text id='PASLM' name='PWDLM' value='$PWDLM' size=16 maxlength=50 disabled=true></td></tr>
  <tr><td class=inp>Password Baru</td><td class=ul><input type=password name='PWD1' size=6 maxlength=6></td></tr>
  <tr><td class=inp>Konfirmasi Password (sekali lagi)</td><td class=ul><input type=password name='PWD2' size=6 maxlength=6></td></tr>
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Reset Password Mahasiswa'></td></tr>
  </form></table></p>";
}
function ResetPwdMhsw() {
  $mhsw = GetFields('mhsw', 'MhswID', $_REQUEST['MhswID'], '*');
  if (!empty($mhsw)) {
    if (strpos($_SESSION['_ProdiID'], $mhsw['ProdiID']) === false) {
      echo ErrorMsg("Gagal Reset Password",
        "Anda tidak berhak mengubah mahasiswa dengan NPM <b>$mhsw[MhswID]</b> ini
        karena Anda tidak memiliki wewenang pada Program Studi <b>$mhsw[ProdiID]</b>.");
    }
    else {
      if (!empty($_REQUEST['PWD1'])) {
        if ($_REQUEST['PWD1'] != $_REQUEST['PWD2']) 
          echo ErrorMsg("Gagal Reset Password",
            "Password dengan konfirmasi password tidak sama. Masukkan password baru 2 kali!");
        else {
          $s = "update mhsw set Password=PASSWORD('$_REQUEST[PWD1]'), KDPIN = '$_REQUEST[PWD1]' where MhswID='$mhsw[MhswID]'";
          $r = _query($s);
          echo Konfirmasi("Password telah direset",
            "Password untuk mahasiswa 
            <font size=+2>$mhsw[Nama]</font> ($mhsw[MhswID])</font> telah direset.");
        }
      }
      else echo ErrorMsg("Gagal Reset Password",
        "Anda harus memasukkan password baru. Password baru tidak boleh blank.<br />
        Password Baru harus dimasukkan 2 kali.");
    }
  }
  else echo ErrorMsg("Gagal", "Gagal reset. Mahasiswa tidak ditemukan.");
  TampilkanFormHeaderResetPasswordMahasiswa();
}

// *** Parameters ***
$MhswID = GetSetVar('MhswID');
$gos = (empty($_REQUEST['gos']))? "TampilkanFormHeaderResetPasswordMahasiswa" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Reset Password Mahasiswa");
if (!empty($gos)) $gos();
?>
