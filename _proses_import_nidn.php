<?php
	include_once "sisfokampus.php";
	HeaderSisfoKampus("Import NIDN Dosen");
	$NamaFiledbf = "MSDOS.dbf";
	$nmf = "file/$NamaFiledbf";
	$tot = 0;
	if (file_exists($nmf)){
			$conn = dbase_open($nmf, 0);
			if ($conn) {
				$dbfrec = dbase_numrecords($conn);
				if ($dbfrec) {
					for ($i=1;$i<=$dbfrec;$i++){
						$row = dbase_get_record_with_names($conn, $i);
							$KTP = trim($row['NOKTPMSDOS']);
							$GELAR = trim($row['GELARMSDOS']);
							$TEMPATLAHIR = trim($row['TPLHRMSDOS']);
							$JABATAN = trim($row['KDJANMSDOS']);
							$JENJANG = trim($row['KDPDAMSDOS']);
							$STATUS = trim($row['KDSTAMSDOS']);
							$NIPPNS = trim($row['NIPNSMSDOS']);
							$INDUK = trim($row['PTINDMSDOS']);
							$DosenID = trim($row['NODOS_']);
							$s = "update dosen set 
									KTP = '$KTP', Gelar='$GELAR', JabatanID='$JABATAN', JenjangID='$JENJANG',
									StatusKerjaID='$STATUS', NIPPNS='$NIPPNS', HomebaseInduk='$INDUK', TempatLahir='$TEMPATLAHIR'
									where Login = '$DosenID'";
							$r = _query($s);
							echo $tot . "\n";
							$tot++;
					}
				} else die("Gagal Membuka File DBF");
			} else die("Gagal Membuat Koneksi Ke DBF");
	} else die("File Tidak Ditemukan");
?>