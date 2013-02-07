<?php
// Author: Emanuel Setio Dewo, 27 Jan 2006
session_destroy();

ResetLogin();
echo Konfirmasi("Logout", "Anda telah Logout dari sistem.<hr size=1 color=silver>
  Pilihan: <a href='index.php?mnux='>Login</a>");
?>