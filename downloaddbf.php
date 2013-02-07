<?php
  $fn = $_REQUEST['fn'];
  $nm = $_REQUEST['nm'];
  $nm = (empty($nm)) ? $fn : $nm.".dbf";
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"$nm\"");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Pragma: public");
  header("Content-Description: Download Data");
  header("Content-EQUIV: refresh; URL=\"http://localhost/?\" ");
  readfile($fn);
?>
