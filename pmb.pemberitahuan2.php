<?php
// Author: Emanuel Setio Dewo
// 07 March 2006

$arr = array($_REQUEST['arrpmbid']);
if (!empty($arr)) {
  for ($i=0; $i < sizeof($arr); $i++) {
    $_REQUEST['pmbid'] = $arr[$i];
    include_once "pmb.pemberitahuan1.php";
  }
}


?>
