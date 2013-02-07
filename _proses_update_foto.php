<?php

include_once "sisfokampus.php";
HeaderSisfoKampus("Proses Update Foto");

$s = "select MhswID, TahunID from mhsw";
$r = _query($s);

while ($w = _fetch_array($r)){
	$s0 = "update mhsw set foto = 'foto/$w[TahunID]/$w[MhswID].jpg' where MhswID = '$w[MhswID]'";
	$r0 = _query($s0);
}

echo "Tabel Mhsw berhasil di Update";

?>