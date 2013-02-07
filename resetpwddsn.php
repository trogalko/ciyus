<?php
// *** Functions ***
function TampilkanFormHeaderResetPasswordDosen() {
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
  <input type=hidden name='mnux' value='resetpwddsn'>
  <input type=hidden name='gos' value='ResetPwdDsn'>
  
  <tr><td class=ul colspan=2><b>$_SESSION[KodeID]</b></td></tr>
  <tr><td class=inp>Hak akses</td><td class=ul>$_SESSION[_ProdiID]</td></tr>
  <tr><td class=inp>Dosen ID</td><td class=ul><input type=text id='DosenID' name='DosenID' value='$_SESSION[DosenID]' size=20 maxlength=50>
  <tr><td class=inp>Password Baru</td><td class=ul><input type=password name='PWD1' size=6 maxlength=6></td></tr>
  <tr><td class=inp>Konfirmasi Password (sekali lagi)</td><td class=ul><input type=password name='PWD2' size=6 maxlength=6></td></tr>
  <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Reset Password Dosen'></td></tr>
  </form></table></p>";
}
function ResetPwdDsn() {
  $dsn = GetFields('dosen', 'Login', $_REQUEST['DosenID'], '*');
  if (!empty($dsn)) {
      if (!empty($_REQUEST['PWD1'])) {
        if ($_REQUEST['PWD1'] != $_REQUEST['PWD2']) 
          echo ErrorMsg("Gagal Reset Password",
            "Password dengan konfirmasi password tidak sama. Masukkan password baru 2 kali!");
        else {
          $s = "update dosen set Password=PASSWORD('$_REQUEST[PWD1]') where Login='$dsn[Login]'";
          $r = _query($s);
          echo Konfirmasi("Password telah direset",
            "Password untuk Dosen 
            <font size=+2>$dsn[Nama]</font> ($dsn[Login])</font> telah direset.");
        }
      }
      else echo ErrorMsg("Gagal Reset Password",
        "Anda harus memasukkan password baru. Password baru tidak boleh blank.<br />
        Password Baru harus dimasukkan 2 kali.");
  }
  else echo ErrorMsg("Gagal", "Gagal reset. Dosen tidak ditemukan.");
  TampilkanFormHeaderResetPasswordDosen();
}

// *** Parameters ***
$DosenID = GetSetVar('DosenID');
$gos = (empty($_REQUEST['gos']))? "TampilkanFormHeaderResetPasswordDosen" : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Reset Password Dosen");
if (!empty($gos)) $gos();
?>