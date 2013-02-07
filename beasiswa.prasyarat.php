<?php
// Author: Emanuel Setio Dewo
// 19 Agustus 2006
// www.sisfokampus.net

include_once "db.mysql.php";
include_once "connectdb.php";
  $BMID = $_REQUEST['BMID'];
  $JML = $_REQUEST['JML']+0;
  $str = '';
  for ($i = 0; $i < $JML; $i++) {
    $str .= $_REQUEST[$i];
  }
  //echo "$JML -- $str";
  $s = "update beasiswamhsw set Prasyarat='$str' where BeasiswaMhswID=$BMID";
  $r = _query($s);
?>
Telah Disimpan.
<SCRIPT>
window.close();
</SCRIPT>
