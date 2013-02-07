<?php
function DownCSV($f){
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"DataMhsw.csv\"");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Pragma: public");  readfile($f);
}

DownCSV($_REQUEST['f']);

?>
