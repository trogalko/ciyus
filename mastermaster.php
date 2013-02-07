<?php
  // Author: E. Setio Dewo, setio_dewo@telkom.net, Juli 2003


  $arrmaster = array('statusawalmhsw', 'statusmhsw', 'statusmkmhsw', 'statuspotongan', 'hari');
  // *** Fungsi2 ***
  function GetTableProp($tbl, &$fld, &$typ, &$def) {
	$sf = "show columns from $tbl";
	$rf = mysql_query($sf) or die("$strCantQuery: $sf");
	while ($rwf = mysql_fetch_array($rf)) {
	  $fld[] = $rwf['Field'];
	  $typ[] = $rwf['Type'];
	  $def[] = $rwf['Default'];
	}
  }
  function DispMasterTable($arr, $tbl) {
    $a = "<table class=basic cellspacing=0 cellpadding=2>
	  <tr><th class=ttl colspan=2>Tabel</th></tr>";
    for ($i=0; $i < sizeof($arr); $i++) {
	  if ($arr[$i] == $tbl) {
	    $cls = 'class=nac';
		$img = "<img src='image/bullet001.gif' border=0>";
	  }
	  else {
	    $cls = 'class=lst';
		$img = "&nbsp;";
	  }
	  $a .= "<tr><td $cls width=10>$img</td><td $cls><a href='sysfo.php?syxec=mastermaster&tbl=$arr[$i]'>$arr[$i]</a></td></tr>";
	}
	return $a . "</table>";
  }
  function DispForMasterTable($tbl = '') {
    global $strCantQuery;
    if (empty($tbl)) return '';
	else {
	  GetTableProp($tbl, $fld=array(), $typ=array(), $def=array());
	  $a = "<a href='sysfo.php?syxec=mastermaster&tbl=$tbl&md=1&kd=0' class=lst>Tambah Data</a><br>
	    <table class=basic cellspacing=0 cellpadding=2><tr>";
	  for ($i=0; $i < sizeof($fld); $i++) $a .= "<th class=ttl>$fld[$i]</th>";
	  $a .= "</tr>";
	  $sr = "select * from $tbl";
	  $rr = mysql_query($sr) or die("$strCantQuery: $sr");
	  while ($rrf = mysql_fetch_array($rr)) {
	    $a .= "<tr>";
	    for ($j=0; $j < sizeof($fld); $j++) {
		  $field = $fld[$j];
		  if ($j==0) $a .= "<td class=lst><a href='sysfo.php?syxec=mastermaster&md=0&tbl=$tbl&kd=$rrf[$field]'>$rrf[$field]</a></td>";
		  else $a .= "<td class=lst>$rrf[$field]</td>";
		}
		$a .= "</tr>";
	  }
	  return $a . "</table>";
	}
  }
  function FieldNilai($typ) {
    $pj = substr($typ, strpos($typ, '('), strlen($typ));
	$pj = str_replace('(', '', $pj); $pj = str_replace(')', '', $pj);
	$pj = str_replace("'", '', $pj);
	return $pj;
  }
  function GetTipe($typ) {
    $p = strpos($typ, '('); 
	if ($p == 0) $p = strlen($typ); 
	return substr($typ, 0, $p);
  }
  function GetEditor($fld, $typ, $def) {
    $tipe = GetTipe($typ);
	$e = '';
	if ($tipe == 'enum') {
	  $arr = explode(',', FieldNilai($typ));
	  for ($i=0; $i < sizeof($arr); $i++) {
	    if ($arr[$i] == $def) $e .= "<input type=radio name=$fld value=$arr[$i] checked>$arr[$i] &nbsp";
		else $e .= "<input type=radio name=$fld value=$arr[$i]>$arr[$i] &nbsp";
	  }
	}
	elseif ($tipe == 'date') $e = "<input type=text name='$fld' value='$def' size=20 maxlength=12>";
	elseif ($tipe == 'varchar' || $tipe == 'char' || $tipe == 'integer' || $tipe == 'smallint') {
	  $pj = FieldNilai($typ);
	  $sz = $pj; if ($sz > 30) $sz = 30;
	  $e = "<input type=text name='$fld' value='$def' size=$sz maxlength=$pj>";
	}
	elseif ($tipe == 'text') $e = "<textarea name='$fld'>$def</textarea>";
	//else return
	return "$e <font color=gray>$tipe</font>";
  }
  function EditMasterTable($tbl, $md=0, $kd='') {
    global $strCantQuery;
	GetTableProp($tbl, $fld=array(), $typ=array(), $def=array());
	if ($md == 0) {
	  $dat = GetFields($tbl, $fld[0], $kd, '*');
	  $jdl = 'Edit Data';
	}
	else {
	  for ($i=0; $i<sizeof($fld); $i++) {
	    $nm = $fld[$i];
		if (isset($def[$i])) $dat[$nm] = $def[$i]; else $dat[$nm] = '';
	  }
	  $jdl = 'Tambah Data';
	}
	$a = "<table class=basic cellspacing=1 cellpadding=2>
	  <form action='sysfo.php' method=POST>
	  <input type=hidden name='syxec' value='mastermaster'>
	  <input type=hidden name='tbl' value='$tbl'>
	  <input type=hidden name='kd' value='$kd'>
	  <input type=hidden name='md' value='$md'>
	  <tr><th class=ttl colspan=2>$jdl</th></tr>";
	for ($i=0; $i < sizeof($fld); $i++) {
	  $nm = $fld[$i];
	  $edt = GetEditor($fld[$i], $typ[$i], $dat[$nm]);
	  $a .= "<tr><td class=lst>$nm</td><td class=lst>$edt</td></tr>";
	}
	$sid = session_id();
	return $a . <<<EOF
	 <tr><td class=lst colspan=2><input type=submit name='prcmaster' value='Simpan'>&nbsp;
	  <input type=reset name='reset' value='Reset'>&nbsp;
	  <input type=button name='Batal' value='Batal' onclick="location='sysfo.php?syxec=mastermaster&tbl=$tbl&PHPSESSID=$sid'"></td></tr>
	  </form></table>
EOF;
  }
  function PrcMaster($tbl) {
    global $strCantQuery;
    GetTableProp($tbl, $fld=array(), $typ=array(), $def=array());
	$dat = array();
	for ($i=0; $i < sizeof($fld); $i++) {
	  $nm = $fld[$i];
	  $dat[$i] = $_REQUEST[$nm];
	}
	$md = $_REQUEST['md'];
	if ($md == 0) {
	  $kd = $_REQUEST['kd'];
	  $f = '';
	  for ($j=0; $j < sizeof($fld); $j++) {
	    if ($j == 0) $f .= "$fld[$j]='$dat[$j]'";
		else $f .= ",$fld[$j]='$dat[$j]'";
	  }
	  $s = "update $tbl set $f where $fld[0]='$kd'";
	  $r = mysql_query($s) or die("$strCantQuery: $s");
	}
	else {
	  $f = ''; $v = '';
	  for ($j=0; $j < sizeof($fld); $j++) {
	    if ($j==0) {
		  $f .= "($fld[$j]";
		  $v .= "('$dat[$j]'";
		}
		elseif ($j==sizeof($fld)-1) {
		  $f .= ", $fld[$j])";
		  $v .= ", '$dat[$j]')";
		}
		else {
		  $f .= ", $fld[$j]";
		  $v .= ", '$dat[$j]'";
		}
	  }
	  $s = "insert into $tbl $f values $v";
	  $r = mysql_query($s) or die("$strCantQuery: $s");
	}
    return -1;
  }
  
  // *** Parameter2 ***
  if (isset($_REQUEST['tbl'])) $tbl = $_REQUEST['tbl']; else $tbl = '';
  if (isset($_REQUEST['md'])) $md = $_REQUEST['md']; else $md = -1;
  if (isset($_REQUEST['kd'])) $kd = $_REQUEST['kd']; else $kd = '';
  
  // *** Bagian Utama ***
  DisplayHeader($fmtPageTitle, 'Master-master Tabel');
  if (isset($_REQUEST['prcmaster'])) $md = PrcMaster($tbl);
  $kiri = DispMasterTable($arrmaster, $tbl);
  if ($md == -1) $kanan = DispForMasterTable($tbl);
  else $kanan = EditMasterTable($tbl, $md, $kd);
  echo <<<EOF
  <table class=basic cellspacing=1 cellpadding=2>
  <tr><td valign=top style='border-right: 1px silver solid'>$kiri</td>
  <td valign=top>$kanan</td></tr>
  </table>
EOF;
?>