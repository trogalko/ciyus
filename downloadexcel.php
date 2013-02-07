<?php
  $fn = $_REQUEST['fn'];
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"autodebet.csv\"");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Pragma: public");
  readfile($fn);
?>
