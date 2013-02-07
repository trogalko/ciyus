<?php
// Author: Emanuel Setio Dewo
// 2006-01-08

// *** Functions ***
function DftrLapPMB() {
  global $_arrpmblap;
  $n=0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th><th class=ttl>Jenis Laporan</th></tr>";
  for ($i=0; $i<sizeof($_arrpmblap); $i++) {
    $n++;
    $lap = explode('->', $_arrpmblap[$i]);
    echo "<tr><td class=inp1>$n</td>
    <td class=ul><a href='?mnux=pmblap&gos=$lap[1]'>$lap[0]</a>
    </td></tr>";
  }
  echo "</table></p>";
}
function RekapAsalSekolah() {
  global $_lf, $_maxbaris, $kembali, $divider, $divider1, $_pmbaktif, $_HeaderPrn, $_EjectPrn;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $s = "select p.JenisSekolahID, count(p.PMBID) as JML, sum(p.NilaiUjian)/count(p.PMBID) as RATA,
    ask.Nama as AsalSek
    from pmb p
    left outer join asalsekolah ask on p.AsalSekolah=ask.SekolahID
    where PMBPeriodID='$_pmbaktif'
    group by p.JenisSekolahID, ask.SekolahID";
  $r = _query($s);
  $jen = ''; $n =0; $brs = 0; $ttl = 0;
  
  // Tulis ke file
  $f = fopen($nmf, "w");
  fwrite($f, $_HeaderPrn);
  $header = $_lf.$_lf;
  $header .= str_pad("Rekap PMB Per Asal Sekolah", 79, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Tahun: ".substr($_pmbaktif, 0, 4)." Gelombang: ".substr($_pmbaktif, 4, 1), 79, ' ', STR_PAD_BOTH).$_lf;
  $header .= $divider;
  $header .=  "      Nama Sekolah                                    Jumlah Rata2 USM$_lf".$divider1;
  
  
  // Tuliskan header
  fwrite($f, $header);
  while ($w = _fetch_array($r)) {
    if ($brs >= $_maxbaris) {
      fwrite($f, chr(12));
      fwrite($f, $header);
      $brs = 0;
    }
    if ($jen != $w['JenisSekolahID']) {
      $jen = $w['JenisSekolahID'];
      fwrite($f, $jen.$_lf);
      $n = 0;
      $brs++;
    }
    $n++;
    $brs++;
    $ttl += $w['JML'];
    fwrite($f, str_pad($n, 4, ' ', STR_PAD_LEFT).'. ');
    fwrite($f, str_pad($w['AsalSek'], 50, ' '));
    fwrite($f, str_pad($w['JML'], 4, ' ', STR_PAD_LEFT));
    // pembulatan
    $rata = number_format($w['RATA'], 2);
    fwrite($f, str_pad($rata, 10, ' ', STR_PAD_LEFT));
    fwrite($f, $_lf);
  }
  fwrite($f, $divider);
  $_ttl = str_pad(number_format($ttl), 10, ' ', STR_PAD_LEFT);
  fwrite($f, "                                 Total Mahasiswa: $_ttl $_lf");
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}
function AsalSekolah() {
  global $_lf, $kembali, $divider, $divider1, $_pmbaktif, $arrID, $_HeaderPrn;
  $_maxbaris = 50;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $s = "select p.JenisSekolahID, p.PMBID, ask.Nama as AsalSek, p.Nama, p.NilaiUjian
    from pmb p
    left outer join asalsekolah ask on p.AsalSekolah=ask.SekolahID
    where PMBPeriodID='$_pmbaktif'
    order by p.JenisSekolahID, ask.Nama, p.Nama";
  $r = _query($s);
  $jen = ''; $sek = ''; $n = 0; $brs = 0;
  $jml = 0;
  
  // Tulis ke file
  $f = fopen($nmf, "w");
  fwrite($f, $_HeaderPrn);
  $header = $_lf.$_lf;
  $header .= str_pad($arrID['Nama'], 79, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Calon Mahasiswa per Asal Sekolah ($_pmbaktif)", 79, ' ', STR_PAD_BOTH).$_lf.$divider;
  $header .= "      PMB ID         Nama Calon                                         Nilai".$_lf.$divider1;
  // Tuliskan header
  fwrite($f, $header);
  while ($w = _fetch_array($r)) {
    if ($brs >= $_maxbaris) {
      fwrite($f, $divider1);
      fwrite($f, chr(12));
      fwrite($f, $header);
      $brs = 0;
    }
    /*if ($jen != $w['JenisSekolahID']) {
      $jen = $w['JenisSekolahID'];
      fwrite($f, $jen.' - '.$sek.$_lf);
      $n = 0;
      $brs++;
    }*/
    if ($sek != $w['AsalSek']) {
      $sek = $w['AsalSek'];
      //fwrite($f, $divider1);
      fwrite($f, $_lf);
      fwrite($f, $w['JenisSekolahID'].' - '.$sek.$_lf);
      $n = 0;
      $brs+=2;
    }
    $n++;
    $brs++;
    $jml++;
    fwrite($f, str_pad($n, 4, ' ', STR_PAD_LEFT).'. ');
    fwrite($f, str_pad($w['PMBID'], 15, ' '));
    fwrite($f, str_pad($w['Nama'], 50, ' '));
    fwrite($f, str_pad($w['NilaiUjian'], 6, ' ', STR_PAD_LEFT));
    fwrite($f, $_lf);
  }
  fwrite($f, $divider);
  $_jml = number_format($jml);
  fwrite($f, "Jumlah Mahasiswa: " . $_jml);
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}
function PerProdi() {
  TampilkanPilihanProdi('pmblap', 'PerProdi');
  if (!empty($_SESSION['prodi'])) TampilkanPerProdi();
}
function TampilkanPerProdi() {
  global $_lf, $_maxbaris, $divider, $divider1, $_pmbaktif, $arrID, $_HeaderPrn;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $s = "select p.PMBID, p.Nama, p.AsalSekolah, p.JenisSekolahID, p.NilaiUjian
    from pmb p
    where p.ProdiID='$_SESSION[prodi]' and PMBPeriodID='$_pmbaktif'
    order by p.Nama";
  $r = _query($s);
  
  // Tulis ke file
  $NamaProdi = GetaField('prodi', "ProdiID", $_SESSION['prodi'], 'Nama');
  $f = fopen($nmf, "w");
  fwrite($f, $_HeaderPrn);
  $header = $_lf.$_lf;
  $header .= str_pad($arrID['Nama'], 79, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Calon Mahasiswa per Program Studi - $NamaProdi (".$_pmbaktif.")", 79, ' ', STR_PAD_BOTH).$_lf.$divider;
  $header .= "      PMBID          Nama                                       Nilai USM".$_lf.$divider1;
  fwrite($f, $header);
  while ($w = _fetch_array($r)) {
    if ($brs >= $_maxbaris) {
      fwrite($f, chr(12));
      fwrite($f, $header);
      $brs = 0;
    }
    $n++;
    $brs++;
    fwrite($f, str_pad($n, 4, ' ', STR_PAD_LEFT).'. ');
    fwrite($f, str_pad($w['PMBID'], 15, ' '));
    fwrite($f, str_pad($w['Nama'], 46, ' '));
    fwrite($f, str_pad($w['NilaiUjian'], 6, ' ', STR_PAD_LEFT));
    fwrite($f, $_lf);
  }
  fwrite($f, $divider);
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}
// Cetak laporan Ranking USM
function RankingUSM() {
  TampilkanPilihanProdi('pmblap', 'RankingUSM');
  if (!empty($_SESSION['prodi'])) RankingUSM1();
}
// OBSOLETE
function HeaderRankingUSM_x($NamaProdi='-', $maxcol, $hal=1) {
  global $arrID, $_pmbaktif, $_lf;
  // Header
  $dvd = str_pad('-', $maxcol, '-').$_lf;
  $tgl = date('d-m-Y H:i').', Hal: '.$hal;
  $header .= $_lf.$_lf;
  $header .= str_pad($arrID['Nama'], $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Ranking USM per Program Studi - $NamaProdi ($_pmbaktif)", $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad($tgl, $maxcol, ' ', STR_PAD_LEFT).$_lf.$dvd;
  $header .= 'Rank  ';
  $header .= str_pad('No. PMB', 15);
  $header .= str_pad('Nama', 30, ' ');
  $header .= "Kel|Agm|WNG|";
  $header .= str_pad("Asal Sekolah", 22);
  $header .= str_pad("Kota Sek.", 12);
  $header .= str_pad("Lulus", 5);
  $header .= str_pad("Jurusan", 10);
  $header .= 'Sts';
  $header .= 'Nilai';
  $header .= 'Grd Pil2 ';
  $header .= str_pad('Alamat', 18);
  $header .= str_pad('Kota', 10);
  $header .= str_pad('Telepon', 15);
  $header .= $_lf.$dvd;
  return $header;
}
function HeaderRankingUSM($NamaProdi='-', $maxcol, $hal=1, $arrTest, $arrNamaTest) {
  global $arrID, $_pmbaktif, $_lf;
  // Buat legend test
  $_strTest[] = array();
  for ($i=0; $i<sizeof($arrTest); $i++) {
    $_strTest[] = $arrTest[$i]. ": ".$arrNamaTest[$i];
  }
  $_LegendTest = implode(', ', $_strTest);
  $_LegendTest = "USM: $_LegendTest";
  
  // Header
  $dvd = str_pad('-', $maxcol, '-').$_lf;
  $tgl = date('d-m-Y H:i').', Hal: '.$hal;
  $header .= $_lf.$_lf;
  $header .= str_pad($arrID['Nama'], $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Ranking USM per Program Studi - $NamaProdi ($_pmbaktif)", $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad($tgl, $maxcol, ' ', STR_PAD_LEFT).$_lf;
  $header .= str_pad($_LegendTest, $maxcol, ' ', STR_PAD_LEFT).$_lf;
  $header .= $dvd;
  $header .= 'Rank  ';
  $header .= str_pad('No. PMB', 15);
  $header .= str_pad('Nama', 20, ' ');
  $header .= "Kel|Agm|WNG|";
  $header .= str_pad("Asal Sekolah", 25);
  $header .= str_pad("Kota Sek.", 20);
  $header .= str_pad("Lulus", 5);
  $header .= str_pad("Jurusan", 10);
  $header .= 'Sts';
  //$header .= 'Pil. ';

  for ($i=0; $i< sizeof($arrTest); $i++) {
    $header .= str_pad($arrTest[$i], 5, ' ', $STR_PAD_LEFT);
  }
  $header .= '   USM    Grade';

/*  $header .= str_pad('Alamat', 18);
  $header .= str_pad('Kota', 10);
  $header .= str_pad('Telepon', 15);
*/
  $header .= $_lf.$dvd;
  return $header;
}
function RankingUSM1() {
  global $_lf, $divider, $divider1, $_pmbaktif, $arrID, $_HeaderPrn;
  $strTanpaTest = array('Y'=>'Tanpa Test', 'N'=>'Dengan Test Masuk');
  $maxcol = 144;
  $_maxbaris = 50;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $s = "select p.PMBID, LEFT(p.Nama, 20) as Nama,
      LEFT(asek.Nama, 25) as AsalSekolah,
      LEFT(asek.Kota, 20) as KotaSekolah,
      p.Pilihan2,
      p.JenisSekolahID, p.NilaiUjian, p.GradeNilai, p.DetailNilai,
      p.Kelamin, p.Agama, LEFT(p.Kebangsaan, 3) as BANGSA,
      p.TahunLulus, p.StatusAwalID, sa.TanpaTest,
      LEFT(js.NamaJurusan, 10) as JurusanSekolah,
      LEFT(p.Alamat, 30) as Alamat,
      LEFT(p.Kota, 10) as Kota,
      LEFT(p.Telepon, 15) as Telephone
    from pmb p
      left outer join asalsekolah asek on p.AsalSekolah=asek.SekolahID
      left outer join jurusansekolah js on p.JurusanSekolah=js.JurusanSekolahID
      left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
    where PMBPeriodID='$_pmbaktif' and p.ProdiID='$_SESSION[prodi]'
    order by sa.TanpaTest desc, p.NilaiUjian desc, p.PMBID ASC";
  $r = _query($s);
  $dvd = str_pad('-', $maxcol, '-').$_lf;
    
  $hal = 1;
  
  // Tulis ke file
  $NamaProdi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  // Buat array Test
  $stest = "select pru.PMBUSMID, pru.ProdiUSMID, pu.Nama
    from prodiusm pru
    left outer join pmbusm pu on pru.PMBUSMID=pu.PMBUSMID
    where pru.ProdiID='$_SESSION[prodi]' and pru.PMBPeriodID='$_pmbaktif'
    order by pru.Urutan";
  $rtest = _query($stest);
  $arrTest = array();
  $arrNamaTest = array();
  while ($wtest = _fetch_array($rtest)) {
    $arrTest[] = $wtest['PMBUSMID'];
    $arrNamaTest[] = $wtest['Nama'];
  }
  
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(15));
  
  $TanpaTest = '';
  $hdr = HeaderRankingUSM($NamaProdi, $maxcol, $hal, $arrTest, $arrNamaTest);
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    if ($TanpaTest != $w['TanpaTest']) {
      $TanpaTest = $w['TanpaTest'];
      if ($brs > 0) fwrite($f, $_lf);
      //GetArrayTable($sql, $key, $label, $separator=', ') {
      $arrStatusAwal = GetArrayTable("select Nama from statusawal where TanpaTest='$TanpaTest'
        order by Nama", "StatusAwalID", "Nama", ', ');
      fwrite($f, $strTanpaTest[$TanpaTest]. ' ('.$arrStatusAwal.') '.$_lf);
      $brs++;
      $n=0;
    }
    if ($brs >= $_maxbaris) {
      fwrite($f, chr(12));
      $hdr = HeaderRankingUSM($NamaProdi, $maxcol, $hal, $arrTest, $arrNamaTest);
      fwrite($f, $hdr);
      $brs = 0;
      $hal++;
    }
    $n++;
    $brs++;
	$w['Alamat'] = str_replace("\n", ' ', $w['Alamat']);
	$w['Alamat'] = str_replace("\r", ' ', $w['Alamat']);
    fwrite($f, str_pad($n, 4, ' ', STR_PAD_LEFT).'. ');
    fwrite($f, str_pad($w['PMBID'], 15, ' '));
    fwrite($f, str_pad($w['Nama'], 20, ' '));
    fwrite($f, str_pad($w['Kelamin'], 3, ' ', STR_PAD_BOTH).'|');
    fwrite($f, str_pad($w['Agama'], 3, ' ', STR_PAD_BOTH).'|');
    fwrite($f, str_pad($w['BANGSA'], 3, ' ', STR_PAD_BOTH).'|');
    fwrite($f, str_pad($w['AsalSekolah'], 25, ' '));
    fwrite($f, str_pad($w['KotaSekolah'], 20, ' '));
    fwrite($f, str_pad($w['TahunLulus'], 5, ' '));
    fwrite($f, str_pad($w['JurusanSekolah'], 10, ' '));
    fwrite($f, str_pad($w['StatusAwalID'], 3, ' ', STR_PAD_BOTH));
    //fwrite($f, str_pad($w['Pilihan2'], 6, ' ', STR_PAD_BOTH));
    // Extract Detail Nilai
    $_DetailNilai = trim($w['DetailNilai'], '.');
    $arrDetailNilai = explode('.', $_DetailNilai);
    $arrNilai = array();
    for ($i=0; $i<sizeof($arrDetailNilai); $i++) {
      $_arrDetailNilai = explode(':', $arrDetailNilai[$i]);
      $__test = $_arrDetailNilai[0];
      $__nil = $_arrDetailNilai[1];
      $key = array_search($__test, $arrTest);
      $arrNilai[$key] = str_pad($__nil + 0, 5, ' ', STR_PAD_LEFT);
    }
    for ($i=0; $i<sizeof($arrTest); $i++) fwrite($f, $arrNilai[$i]);
	fwrite($f, str_pad($w['NilaiUjian'] + 0, 5, ' ', STR_PAD_LEFT));
    fwrite($f, str_pad($w['GradeNilai'], 5, ' ', STR_PAD_LEFT));

    //fwrite($f, $w['DetailNilai']);
    
    /*fwrite($f, str_pad($w['Alamat'], 20, ' '));
    fwrite($f, str_pad($w['Kota'], 10, ' '));
    fwrite($f, str_pad($w['Telephone'], 15, ' '));
    */
    fwrite($f, $_lf);
  }
  fwrite($f, $dvd);
  fwrite($f, str_pad('Akhir laporan.', $maxcol, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}

function DataCAMAPerProdi() {
  global $_lf, $_maxbaris, $divider, $divider1, $_pmbaktif, $arrID;
  TampilkanPilihanProdi('pmblap', 'DataCAMAPerProdi');
  
  $maxcol = 235;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77).chr(15).chr(27).chr(108).chr(5));
  $whr = (!empty($_SESSION['prodi']))? "and p.ProdiID='$_SESSION[prodi]'" : '';
  $s = "select p.PMBID, LEFT(p.Nama, 20) as Nama, p.ProdiID, p.PSSBID, 
    LEFT(asek.Nama, 50) as AsalSekolah,
    LEFT(asek.Kota, 15) as KotaSekolah,
    p.Pilihan2,
    p.JenisSekolahID, p.NilaiUjian, p.GradeNilai, p.DetailNilai,
    p.Kelamin, p.Agama, LEFT(p.Kebangsaan, 3) as BANGSA,
    p.TahunLulus, p.StatusAwalID,
    LEFT(js.NamaJurusan, 10) as JurusanSekolah,
    LEFT(p.Alamat, 60) as Alamat,
    LEFT(p.Kota, 25) as Kota,
    LEFT(p.Telepon, 15) as Telephone
    from pmb p
    left outer join asalsekolah asek on p.AsalSekolah=asek.SekolahID
    left outer join jurusansekolah js on p.JurusanSekolah=js.JurusanSekolahID
    where PMBPeriodID='$_pmbaktif' $whr
    order by p.ProdiID, p.PSSBID, p.PMBID, p.NilaiUjian desc";
  $r = _query($s);
  $dvd = str_pad('-', $maxcol, '-').$_lf;
  // Buat header
  $header .= $_lf.$_lf;
  $header .= str_pad($arrID['Nama'], $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Data Calon Mhsw per Program Studi ($_pmbaktif)", $maxcol, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad($tgl, $maxcol, ' ', STR_PAD_LEFT).$_lf;
  $header .= str_pad($_LegendTest, $maxcol, ' ', STR_PAD_LEFT).$_lf;
  $header .= $dvd;
  $header .= 'No.  ';
  $header .= str_pad('No. PMB', 15);
  $header .= str_pad('PSSBID', 15);
  $header .= str_pad('Nama', 30, ' ');
  $header .= "Kel|Agm|WNG|";
  $header .= str_pad("Asal Sekolah", 31);
  $header .= str_pad("Kota Sek.", 16);
  $header .= str_pad("Jurusan", 11);
  $header .= 'Alamat ';
  $header .= $_lf;
  fwrite($f, $header);
  fwrite($f, $dvd);
  // Buat isinya
  $nmr = 0; $brs = 0; $max = 45;
  //$mrg = '     ';
  $prd = ''; $first = true;
  while ($w = _fetch_array($r)) {
    $brs++;
    if ($prd != $w['ProdiID']) {
      if (!empty($prd)) {
		fwrite($f, $dvd);
		fwrite($f, chr(12));
	    fwrite($f, $header);
	    fwrite($f, $dvd);
	  }
	  //fwrite($f, $dvd);
      $prd = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "ProdiID", $prd, 'Nama');
      fwrite($f, $NamaProdi.$_lf);
      $nmr = 0; $brs = 0;
	  $first = false;
    }
    if ($brs >= $max) {
	    $brs = 0;
	    fwrite($f, chr(12));
	    fwrite($f, $dvd);
	    fwrite($f, $header);
      fwrite($f, $dvd);
    }
    $nmr++;
	//$w['Alamat'] = substr($w['Alamat'], 1, 30);
	$w['Alamat'] = str_replace("\n", ' ', $w['Alamat']);
	$w['Alamat'] = str_replace("\r", '', $w['Alamat']);
    $isi = str_pad($nmr.'.', 5, ' '). str_pad($w['PMBID'], 15, ' ').
      str_pad($w['PSSBID'], 15).
      str_pad($w['Nama'], 30, ' ') . str_pad($w['Kelamin'], 4, ' ', STR_PAD_BOTH).
      str_pad($w['Agama'], 4, ' ', STR_PAD_BOTH) . str_pad($w['BANGSA'], 4, ' ', STR_PAD_BOTH).
      str_pad($w['AsalSekolah'], 30).' '.
      str_pad($w['KotaSekolah'], 15).' '.
      str_pad($w['JurusanSekolah'], 10). ' '.
      str_pad($w['Alamat'], 65, ' '). ' '.
	  str_pad($w['Kota'], 18) . ' '.
	  str_pad($w['Telephone'], 15).
      $_lf;
    fwrite($f, $isi);
  }
  
  // Download file
  fwrite($f, $dvd);
  fwrite($f, "Dicetak Oleh : " . $_SESSION['_Login'].', '.Date("d-m-Y H:i").$_lf);
  fwrite($f, str_pad('Akhir laporan.', $maxcol, ' ', STR_PAD_LEFT));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}
function PSSB() {
  global $_lf, $_maxbaris, $divider, $divider1, $_pmbaktif, $arrID, $_HeaderPrn;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $s = "select p.PMBID, p.Nama, p.ProdiID, ask.Nama as AsalSek
    from pmb p
    left outer join asalsekolah ask on p.AsalSekolah=ask.SekolahID
    where p.StatusAwalID='S'
    order by p.ProdiID, p.Nama";
  $r = _query($s);
  $pr = '';
  // Tulis ke file
  $f = fopen($nmf, 'w');
  fwrite($f, $_HeaderPrn);
  $header = str_pad($arrID['Nama'], 79, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Calon Mahasiswa PSSB", 79, ' ', STR_PAD_BOTH).$_lf.$divider;
  $header .= "      PMBID          Nama                          Asal Sekolah".$_lf.$divider1;
  fwrite($f, $header);
  while ($w = _fetch_array($r)) {
    if ($brs >= $_maxbaris) {
      fwrite($f, chr(12));
      fwrite($f, $_lf.$_lf);
      fwrite($f, $header);
      $brs = 0;
    }
    if ($pr != $w['ProdiID']) {
      $pr = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "ProdiID", $pr, 'Nama');
      fwrite($f, $NamaProdi.$_lf);
      $brs++;
    }
    $n++;
    $brs++;
    fwrite($f, str_pad($n, 4, ' ', STR_PAD_LEFT).'. ');
    fwrite($f, str_pad($w['PMBID'], 15, ' '));
    fwrite($f, str_pad($w['Nama'], 30, ' '));
    fwrite($f, str_pad($w['AsalSek'], 30, ' '));
    fwrite($f, $_lf);
  }
  fwrite($f, $divider);
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}
// *** Menghitung berapa CAMA yg konfirmasi mjd Mhsw
function KonfNIM() {
  global $_lf, $_maxbaris, $divider, $divider1, $_pmbaktif, $arrID, $_HeaderPrn;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  // Header
  $f = fopen($nmf, 'w');
  fwrite($f, $_HeaderPrn);
  $header = str_pad($arrID['Nama'], 79, ' ', STR_PAD_BOTH).$_lf;
  $header .= str_pad("Konfirmasi NIM Gelombang $_pmbaktif", 79, ' ', STR_PAD_BOTH).$_lf.$divider;
  $header .= str_pad("Program Studi", 38).
    str_pad("Sblmnya", 12, ' ', STR_PAD_LEFT).
    str_pad($_SESSION['pmbaktif'], 12, ' ', STR_PAD_LEFT). 
    str_pad("Total", 15, ' ', STR_PAD_LEFT).
    $_lf . $divider1;
  fwrite($f, $header);
  $mrg = str_pad(' ', 3, ' ');
  
  // Buat array program studi
  $s = "select prd.ProdiID, prd.Nama as PRD, fak.FakultasID, fak.Nama as FAK
    from prodi prd
    left outer join fakultas fak on prd.FakultasID=fak.FakultasID
    order by prd.FakultasID, prd.ProdiID";
  $r = _query($s);
  $arrPrd = array();
  $_fak = '';
  // apakah ada periode sebelumnya?
  $thn = substr($_SESSION['pmbaktif'], 0, 4)+0;
  $ssa = substr($_SESSION['pmbaktif'], 4, 1)+0;
  $JmlSblm = 0; $TotSblm = 0;
  $JmlSkrg = 0; $TotSkrg = 0;
  $jml = 0; $tot = 0;
  while ($w = _fetch_array($r)) {
    if ($_fak != $w['FakultasID']) {
      if (!empty($_fak)) fwrite($f, $_lf);
      $_fak = $w['FakultasID'];
      fwrite($f, $w['FakultasID']. '. ' . $w['FAK'].$_lf);
    }
    fwrite($f, $mrg . str_pad($w['ProdiID'] . '. '.$w['PRD'], 35, ' '));
    // hitung periode sebelumnya
    if ($ssa > 1) {
      $jml0 = GetaField('pmb', "PMBPeriodID like '$thn%' and PMBPeriodID<'$_SESSION[pmbaktif]' and NIM<>'' and ProdiID",
        $w['ProdiID'], "count(*)")+0;
      $tot0 = GetaField('pmb', "PMBPeriodID like '$thn%' and PMBPeriodID<'$_SESSION[pmbaktif]' and ProdiID",
        $w['ProdiID'], "count(*)")+0;
      $JmlSkrg += $jml0;
      $TotSkrg += $tot0;
        fwrite($f, str_pad($jml0.'/'.$tot0, 12, ' ', STR_PAD_LEFT));
    } else fwrite($f, str_pad(' ', 12, ' '));
    
    // hitung periode ini
    $jml1 = GetaField('pmb', "PMBPeriodID='$_SESSION[pmbaktif]' and NIM<>'' and ProdiID",
      $w['ProdiID'], "count(*)")+0;
    $tot1 = GetaField('pmb', "PMBPeriodID='$_SESSION[pmbaktif]' and ProdiID",
      $w['ProdiID'], "count(*)")+0;
    fwrite($f, str_pad($jml1.'/'.$tot1, 12, ' ', STR_PAD_LEFT));
    $JmlSkrg += $jml1;
    $TotSkrg += $tot1;
    // hitung total kanan
    $jmlx = $jml0 + $jml1;
    $jml += $jmlx;
    $totx = $tot0 + $tot1;
    $tot += $totx;
    fwrite($f, str_pad($jmlx.'/'.$totx, 15, ' ', STR_PAD_LEFT));
    fwrite($f, $_lf);
  }
  fwrite($f, $divider);
  // Hitung Total
  fwrite($f, str_pad("Total: ", 38, ' ', STR_PAD_LEFT).
    str_pad($JmlSblm.'/'.$TotSblm, 12, ' ', STR_PAD_LEFT).
    str_pad($JmlSkrg.'/'.$TotSkrg, 12, ' ', STR_PAD_LEFT).
    str_pad($jml.'/'.$tot, 15, ' ', STR_PAD_LEFT).
    $_lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'pmblap');
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
//$_pmbaktif = GetaField('pmbperiod', 'NA', 'N', 'PMBPeriodID');
$_pmbaktif = GetSetVar('_pmbaktif');
$pmbfid = GetSetVar('pmbfid');
$_arrpmblap = array(
  "Rekap Mahasiswa per Asal Sekolah->RekapAsalSekolah",
  "Calon Mahasiswa per Asal Sekolah->AsalSekolah",
  "Calon Mahasiswa Per Program Studi->PerProdi",
  "Data Calon Mahasiswa per Program Studi->DataCAMAPerProdi",
  "Ranking Ujian Saringan Masuk->RankingUSM",
  "Mahasiswa PSSB->PSSB",
  "Konfirmasi NIM->KonfNIM"
  );
$gos = (empty($_REQUEST['gos']))? 'DftrLapPMB' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Laporan PMB");
TampilkanPeriodePMBLaporan('pmblap');
$gos();
?>
