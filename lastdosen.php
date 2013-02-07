<?php

function GetLastIDDosen($last){
	$s = "select max(d.Login) as Login from dosen d
          left outer join statusdosen sd on sd.StatusDosenID=d.StatusDosenID
        where sd.StatusDosenID = '$last'";
	$r = _query($s);
	$w = _fetch_array($r);
	return $w['Login'];
}

?>
