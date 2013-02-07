<?php
  // file: connectdb.php
  // author: E. Setio Dewo, Maret 2003

  $db_username = "root";
  $db_hostname = "localhost:4040";
  $db_password = "";
  $db_name = "sisfokampus35";

  $con = _connect($db_hostname, $db_username, $db_password);
  $db  = _select_db($db_name, $con);

?>
