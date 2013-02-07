<?php
// Author: Emanuel Setio Dewo
// 25 May 2006
// Kenaikan Yesus Kristus ke Surga
// Damai dan sejahtera beserta kita semua

function HeaderSisfoKampus($title='') {
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  echo "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">
  <HEAD><TITLE>$title</TITLE>
  <META content=\"Emanuel Setio Dewo\" name=\"author\">
  <META content=\"Sisfo Kampus\" name=\"description\">
  <link href=\"index.css\" rel=\"stylesheet\" type=\"text/css\">
  ";
}
?>
