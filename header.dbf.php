<?php

$HeaderMSHS = array (
					array("KDPTIMSMHS", "C", 6),  // Kode Perguruan Tinggi
					array("KDPSTMSMHS", "C", 5),  // Kode Program Studi
					array("KDJENMSMHS", "C", 1),  // Kode Jenjang Studi
					array("NIMHSMSMHS", "C", 15), // NIM
					array("NMMHSMSMHS", "C", 30), // Nama
					array("TPLHRMSMHS", "C", 20), // Tempat Lahir
					array("TGLHRMSMHS", "D"),     // Tanggal Lahir
					array("KDJEKMSMHS", "C", 1),  //  Jenis Kelamin
					array("TAHUNMSMHS", "C", 4),	// Tahun masuk
					array("SMAWLMSMHS", "C", 5),	// Semester awal terdaftar
					array("BTSTUMSMHS", "C", 5),	// Batas Studi
					array("ASSMAMSMHS", "C", 2),	// Kode Propinsi Pendidikan terakhir
					array("TGMSKMSMHS", "D"),			// Tanggal Masuk
					array("TGLLSMSMHS", "D"),			// Tanggal Lulus
					array("STMHSMSMHS", "C", 1),	// Kode Status Mhsw
					array("STPIDMSMHS", "C", 1),	// Kode Status Awal (Baru/Pindahan) 
					array("SKSDIMSMHS", "N", 3, 0),	// Jumlah SKS Pindahan
					array("ASNIMMSMHS", "C", 15),	// NIM asal perguruan tinggi (Pindahan)
					array("ASPTIMSMHS", "C", 6),  // Kode Perguruan tinggi sebelumnya (Pindahan)
					array("ASJENMSMHS", "C", 1),	// Kode Jenjang studi sebelumnya (Pindahan)
					array("ASPSTMSMHS", "C", 5), 	// Kode Program Studi sebelumnya (Pindahan)
					array("BISTUMSMHS", "C", 1), 	// Kode Biaya studi (S3)
					array("PEKSBMSMHS", "C", 1), 	// Kode Pekerjaan (S3)
					array("NMPEKMSMHS", "C", 40),	// Nama Tenpat bekerja jika bukan dosen (S3)
					array("PTPEKMSMHS", "C", 6),	// Kode PT tempat bekerja bila dosen (S3)
					array("PSPEKMSMHS", "C", 5),	// Kode PS tempat bekerja bila dosen (S3)
					array("NMPRMMSMHS", "C", 10),	// NIDN Promotor #
					array("NOKP1MSMHS", "C", 10),	// NIDN Promotor 1
					array("NOKP2MSMHS", "C", 10),	// NIDN Promotor 2
					array("NOKP3MSMHS", "C", 10),	// NIDN Promotor 3
					array("NOKP4MSMHS", "C", 10));	// NIDN Promotor 4
					
$HeaderMSDOS = array (
					array("KDPTIMSDOS", "C", 6),	// Kode Perguruan Tinggi
					array("KDPSTMSDOS", "C", 5),	// Kode Program Studi
					array("KDJENMSDOS", "C", 1),	// Kode Jenjang Studi
					array("NOKTPMSDOS", "C", 25),	// No KTP dosen
					array("NODOSMSDOS", "C", 10),	// Nomor dosen (NIDN)
					array("NMDOSMSDOS", "C", 30),	// Nama dosen
					array("GELARMSDOS", "C", 10),	// Gelas Akademik
					array("TPLHRMSDOS", "C", 20),	// Tempat Lahir
					array("TGLHRMSDOS", "D"),			// Tanggal Lahir
					array("KDJEKMSDOS", "C", 1),	// Kode Jenis Kelamin
					array("KDJANMSDOS", "C", 1),	// Kode Jabatan akademik
					array("KDPDAMSDOS", "C", 1),	// Kode Pendidikan Tertinggi
					array("KDSTAMSDOS", "C", 1),	// Kode Ikatan status kerja
					array("STDOSMSDOS", "C", 1),	// Kode Aktifitas Dosen
					array("MLSEMMSDOS", "C", 1),	// Semester Dosen Mulai
					array("NIPNSMSDOS", "C", 9),	// NIP PNS
					array("PTINDMSDOS", "N", 6, 0));	// Homebase

$HeaderAKTFMHS = array (
					array("THSMSTRAKM", "C", 5),	// Tahun Semester Pelaporan data
					array("KDPTITRAKM", "C", 6),	// Kode Perguruan Tinggi
					array("KDPSTTRAKM", "C", 5),	// Kode Program Studi
					array("KDJENTRAKM", "C", 1),	// Kode Jenjang Studi
					array("NIMHSTRAKM", "C", 15),	// Nomor Induk Mhsw
					array("NLIPSTRAKM", "N", 4, 2),	// Nilai IPS
					array("SKSEMTRAKM", "N", 3, 0),	// SKS yang diambil
					array("NLIPKTRAKM", "N", 4, 2),	// Nilai IPK
					array("SKSTTTRAKM", "N", 3, 0)	// SKS Total diperoleh
					);
					
$HeaderAKTFDSN = array (
					array("THSMSTRAKD", "C", 5),	// Tahun Semester Pelaporan data
					array("KDPTITRAKD", "C", 6),	// Kode Perguruan Tinggi
					array("KDPSTTRAKD", "C", 5),	// Kode Program Studi
					array("KDJENTRAKD", "C", 1),	// Kode Jenjang Studi
					array("NODOSTRAKD", "C", 10),	// NIDN Dosen
					array("KDKMKTRAKD", "C", 10),	// Kode Mata Kuliah
					array("KELASTRAKD", "C", 2),	// Kode Kelas Pararel
					array("TMRENTRAKD", "N", 2, 0),	// Tatap Muka yang direncanakan
					array("TMRELTRAKD", "N", 2, 0)	// Tatap Muka Realisasi
					);
					
$HeaderTRNLM = array (
					array("THSMSTRNLM", "C", 5),	// Tahun Semester Pelaporan data
					array("KDPTITRNLM", "C", 6),	// Kode Perguruan Tinggi
					array("KDPSTTRNLM", "C", 5),	// Kode Program Studi
					array("KDJENTRNLM", "C", 1),	// Kode Jenjang Studi
					array("NIMHSTRNLM", "C", 15),	// NIM Mahasiswa
					array("KDKMKTRNLM", "C", 10),	// Kode Mata Kuliah
					array("NLAKHTRNLM", "C", 2),	// Nilai Berupa A B C D E
					array("BOBOTTRNLM", "N", 4, 2)	// Bobot Nilai
					);
					
$HeaderAlamat = array (
          array("No", "N", 5, 0),
          array("MhswID", "C", 10),
          array("Nama", "C", 35),
          array("Alamat", "C", 65),
          array("RT/RW", "C", 8),
          array("Kota", "C", 25),
          array("Kode Pos", "C", 8),
          array("Telepon", "C", 15)
          );

$HeaderTBKMK = array (
					array("THSMSTBKMK", "C", 5),  // Tahun Semester Pelaporan Data
					array("KDPTITBKMK", "C", 6),  // Kode Perguruan Tinggi
					array("KDPSTTBKMK", "C", 5),  // Kode Program Studi
					array("KDJENTBKMK", "C", 1),  // Kode Jenjang Studi
					array("KDKMKTBKMK", "C", 10), // Kode Matakuliah yang digunakan masing2 PT
					array("NAKMKTBKMK", "C", 40), // Nama Matakuliah
					array("SKSMKTBKMK", "N", 1),  // SKS Matakuliah sesuai dengan Kurikulum
					array("SKSTMTBKMK", "N", 1),  // SKS Tatap muka
					array("SKSPRTBKMK", "N", 1),	// TSKS Praktikum
					array("SKSLPTBKMK", "N", 1),	// SKS Praktek Lapangan
					array("SEMESTBKMK", "C", 2),	// Semester
					array("KDKELTBKMK", "C", 1),	// Kode Kelompok Matakuliah
					array("KDKURTBKMK", "C", 1),	// Kode Kurikulum Inti/Institusi
					array("KDWPLTBKMK", "C", 1),	// Kode Matakuliah Wajib/Pilihan
					array("NODOSTBKMK", "C", 10),	// Nomor Dosen Pengampu Matakuliah
					array("JENJATBKMK", "C", 1),	// Jenjang Program Studi Pengampu
					array("PRODITBKMK", "C", 5),	// Program Studi Pengampu
					array("STKMKTBKMK", "C", 1),	// Status Matakuliah Aktif/Hapus
					array("SLBUSTBKMK", "C", 1),  // Silabus
					array("SAPPPTBKMK", "C", 1),	// Satuan Acara Perkuliahan
					array("BHNAJTBKMK", "C", 1), 	// Bahan Ajar
					array("DIKTTTBKMK", "C", 1)); // KDiktat
					
$HeaderIjazah = array (
					array("NIMHS", "C", 15),	// NIM Mahasiswa
					array("NIRMH", "C", 15),	// NIRM
					array("NMMHS", "C", 50),	// Nama Mahasiswa
					array("TPLHR", "C", 25),	// Tempat Lahir
					array("TGLHR", "C", 25),  // Tanggal Lahir
          array("NOMOR", "C", 30),	// Nomor seri Ijazah
					array("TGLLS", "C", 30),	// Tanggal Lulus/SK Yudisium
					array("TGLRE", "C", 25),	// Tanggal SK Rektor
					array("NOSKR", "C", 30),	// Nomor SK Rektor
					array("MASUK", "C", 4),		// Masuk/Angkatan
					array("NMREK", "C", 50),	// Nama Rektor
					array("NMDEK", "C", 50),	// Nama Dekan
					array("NOBAN", "C", 35),	// No BAN (Akreditasi)
					array("TGBAN", "C", 25));	// Tanggal BAN
?>
