<?php
// Author: Emanuel Setio Dewo
// Oktober 2005

function ResetLogin() {
  global $_defmnux;
  $_SESSION['mnux'] = "mhsw";
  $_SESSION['sub'] = 'DM';
  $_SESSION['mdlid'] = 0;
  $_SESSION['__Login'] = '';
  $_SESSION['__Nama'] = '';
  $_SESSION['__TahunID'] = '';  
  $_SESSION['__ProdiID'] = '';
  $_SESSION['__urt'] = '';
}

$mnux = GetSetVar('mnux', $_defmnux);
if (empty($mnux)) {
  $mnux = $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
}
if (empty($_SESSION['_Session'])) {
  $mnux = $_defmnux;
  $_SESSION['mnux'] = $_defmnux;
  $_SESSION['mdlid'] = 0;
}

?>
