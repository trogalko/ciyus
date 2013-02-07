<?php

function DaftarJadwal() {
  $s = "select JadwalID, MKKode, Nama, NamaKelas
    from jadwal 
    where INSTR(ProdiID, '.$_SESSION[prodi].')>0
      and TahunID='$_SESSION[tahun]'
    order by MKKode";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $jml = GetaField('krs', "StatusKRSID='A' and JadwalID", $w['JadwalID'], "count(*)")+0;
    $jmlkrs = GetaField('krstemp', "StatusKRSID='A' and JadwalID", $w['JadwalID'], "Count(*)")+0;
    $sx = "update jadwal set JumlahMhsw=$jml, JumlahMhswKRS=$jmlkrs where JadwalID=$w[JadwalID] ";
    $rx = _query($sx);  
  }
}

function daftar(){
  global $_lf;
	DaftarJadwal();
  $s = "SELECT k.MKKode, mk.Nama, j.NamaKelas, j.JumlahMhswKRS as Jumlah, j.JumlahMhsw, 
				(j.JumlahMhswKRS - j.JumlahMhsw) as Selisih
				FROM krstemp k
				LEFT OUTER JOIN jadwal j ON j.JadwalID = k.JadwalID
				LEFT OUTER JOIN mhsw m ON k.MhswID = m.MhswID
				LEFT OUTER JOIN mk ON k.MKID = mk.MKID
				WHERE k.tahunid = $_SESSION[tahun]
				AND m.ProdiID = '$_SESSION[prodi]'
				
				AND j.JadwalSer = '0'
				AND j.JenisJadwalID = 'K'
				GROUP BY j.JadwalID
				ORDER BY k.MKKode ASC";
				
  $r = _query($s);
	
	$MaxCol = 114;
  $nmf = "tmp/$_SESSION[_Login].$_SESSION[prodi].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $div = str_pad('-', $MaxCol, '-').$_lf;
  $_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $n = 0; $hal = 1; $n2 = 0;
  $brs = 0;
  $maxbrs = 50;
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	
	echo "<p><a href='?mnux=akd.lap'>Kembali</a> | <a href=dwoprn.php?f=$nmf>Cetak Laporan</a></p>";
	echo "<p><font color=red>*  </font><i>Jumlah Mahasiswa yang mendaftar KRS</i></p>";
	echo "<p><font color=red>** </font><i>Jumlah Mahasiswa yang sudah mencetak KSS</i></p>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
    <th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Kelas</th>
		<th class=ttl>Jml Terdaftar KRS<font color=red> *</font></th>
		<th class=ttl>Jml Terdaftar Kuliah<font color=red> **</font></th>
		<th class=ttl>Selisih</th>
    </tr>";
  
	$hdr = str_pad("*** Laporan Rekap Jumlah Mahasiswa KRS dan Terdaftar KRS ***", $MaxCol, ' ', STR_PAD_BOTH).$_lf.$_lf.$_lf;
  $hdr .= "Periode   : " . NamaTahun($_SESSION['tahun']) . $_lf;
  $hdr .= "Prodi     : $_prodi".$_lf;
  $hdr .= "Program   : $_prid" . $_lf;
	$hdr .= "*  Jumlah Mahasiswa yang mendaftar KRS" . $_lf;
	$hdr .= "** Jumlah Mahasiswa yang sudah mencetak KSS" . $_lf;
  $hdr .= $div;
  $hdr .= str_pad("NO", 4) . str_pad('KODE', 8) . str_pad('NAMA', 40) . str_pad('KELAS', 6). str_pad('JML TERDAFTAR KRS *', 22, ' ', STR_PAD_LEFT).str_pad('JML TERDAFTAR KULIAH **', 26, ' ', STR_PAD_LEFT).$_lf.$div;
  fwrite($f, $hdr);
	
  while ($w = _fetch_array($r)){
	  $n++;
		$_selisih = $w['Jumlah'] - $w['JumlahMhsw'];
		$selisih = ($_selisih == 0) ? "&nbsp;" : $_selisih;
		echo "<tr>
		<td class=inp>$n</td>
		<td class=ul>$w[MKKode]</td>
		<td class=ul>$w[Nama]</td>
		<td class=ul>$w[NamaKelas]</td>
		<td class=ul align=right>$w[Jumlah]</td>
		<td class=ul align=right>$w[JumlahMhsw]</td>
		<td class=ul align=right>$selisih</td></tr>";
	  $brs++;
		if($brs > $maxbrs){
          $isi .= $div;
          $isi .= str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf;
          $hal++; $brs = 1;
          $isi .= chr(12).$_lf;
          $isi .= $hdr;
    }
		
		if ($kdmk != $w['MKKode']) {
      $kdmk = $w['MKKode'];
      $_kdmk = $kdmk;
      $n2++;
    } else { 
        $_kdmk = '';
    }
    if ($nmmk != $w['Nama']) {
      $nmmk = $w['Nama'];
      $_nmmk = $nmmk;
    } else { 
        $_nmmk = '';
    }
    if ($n_ != $n2) {
      $n_ = $n2;
      $_n_ = $n_.".";
    } else {
    $_n_ = '';
    }
    $isi .= str_pad($_n_, 4) .
            str_pad($_kdmk, 8) .
            str_pad($_nmmk, 40) .
            str_pad($w['NamaKelas'], 6) .
						str_pad($w['Jumlah'], 22, ' ',STR_PAD_LEFT) . 
						str_pad($w['JumlahMhsw'], 26, ' ',STR_PAD_LEFT).$_lf;
	}
	fwrite($f, $isi);
  fwrite($f, $div);
  fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad('Dicetak oleh : '.$_SESSION['_Login'],85,' ').str_pad('Dibuat : '.date("d-m-Y H:i"),29,' '));
  fwrite($f, chr(12));
  fclose($f);
	echo "</table></p>";  		
}

$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');

TampilkanJudul("Rekapitulasi Jumlah Mahasiswa Terdaftar KRS");
TampilkanTahunProdiProgram('akd.lap.rekapmhswkrs', 'daftar');
if(!empty($tahun) and !empty($prodi)) daftar();

?>
