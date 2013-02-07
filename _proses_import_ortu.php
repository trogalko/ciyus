<?php
	include_once "sisfokampus.php";
	HeaderSisfoKampus("Import Orang Tua Mahasiswa");
	$NamaFiledbf = "MHP2006.dbf";
	$nmf = "file/$NamaFiledbf";
	$x=0;
	$tot = 0;
	if (file_exists($nmf)){
			$conn = dbase_open($nmf, 0);
			if ($conn) {
				$dbfrec = dbase_numrecords($conn);
				if ($dbfrec) {
					for ($i=1;$i<=$dbfrec;$i++){
						$row = dbase_get_record_with_names($conn, $i);
						$x++;
						  $PRD = substr($row['NIMHSMSMHP'], 0, 2);
							$MhswID = trim($row['NIMHSMSMHP']);
              $NAMAAYAH = trim($row['NMAYAMSMHP']);
							$AGAMAAYAH = trim($row['AGAYAMSMHP']);
							$PENDIDIKANAYAH = trim($row['PDAYAMSMHP']);
							$PEKERJAANAYAH = trim($row['PKAYAMSMHP']);
							$HIDUPAYAH = trim($row['STAYAMSMHP']);
							$NAMAIBU = trim($row['NMIBUMSMHP']);
							$AGAMAIBU = trim($row['AGIBUMSMHP']);
							$PENDIDIKANIBU = trim($row['PDIBUMSMHP']);
							$PEKERJAANIBU = trim($row['PKIBUMSMHP']);
							$HIDUPIBU = trim($row['STIBUMSMHP']);
							
							$_SESSION['ADUP'.$x] = "$MhswID~$NAMAAYAH~$AGAMAAYAH~$PENDIDIKANAYAH~$PEKERJAANAYAH~$HIDUPAYAH~$NAMAIBU~$AGAMAIBU~$PENDIDIKANIBU~$PEKERJAANIBU~$HIDUPIBU";
              //var_dump($_SESSION['ADUP'.$x]); echo "$x"; exit;
              /*$s = "update mhsw set 
									NamaAyah = '$NAMAAYAH', AgamaAyah='$AGAMAAYAH', PendidikanAyah='$PENDIDIKANAYAH', 
                  PekerjaanAyah='$PEKERJAANAYAH', HidupAyah='HIDUPAYAH', NamaIbu='$NAMAIBU', AgamaIbu='$AGAMAIBU', 
                  PendidikanIbu='$PENDIDIKANIBU', PekerjaanIbu='$PEKERJAANIBU', HidupIbu='$HIDUPIBU'
									where Login = '$MhswID'";
							$r = _query($s);
							echo $tot . "\n";
							$tot++;*/
					}
					$_SESSION['ADUPPOS'] = 1;
					$_SESSION['ADUPPOSX'] = $x;
					echo "<p>File yang diupload akan diproses. Terdapat <b>$x</b> data yg akan diupload.</p>
								<p><IFRAME src='pro_goortu.php frameborder=0 height=300 width=300>
								</IFRAME></p>";
				} else die("Gagal Membuka File DBF");
			} else die("Gagal Membuat Koneksi Ke DBF");
	} else die("File Tidak Ditemukan");
?>
