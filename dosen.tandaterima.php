<?php

function DftrTandaTerima(){
   global $arrID;
   $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], "KodeID='$arrID[Kode]'", 'ProdiID');
   $optprg = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['prid'], "KodeID='$arrID[Kode]'", 'ProgramID');
   $optjns = GetOption2('jenisjadwal', "concat(JenisJadwalID, '-', Nama)", 'JenisJadwalID', $_SESSION['jenis'], '', 'JenisJadwalID');
  echo "<p>
    <form action='?' method=post>
    <input type=hidden name=mnux value='dosen.tandaterima'>
    <input type=hidden name=gos value='daftar'>
    <table class=box cellpadding=4 cellspacing=1>
    <tr><th class=ttl colspan=3>$arrID[Nama]</th></tr>
    <tr><td class=inp1>Periode</td><td class=inp1>:</td><td class=ul><input type=text name=tahun value=$_SESSION[tahun]></td></tr>
    <tr><td class=inp1>Program</td><td class=inp1>:</td><td class=ul><select name=prid>$optprg</select></td></tr>
    <tr><td class=inp1>Prodi</td><td class=inp1>:</td><td class=ul><select name=prodi>$optprd</select></td></tr>
    <tr><td class=inp1>Jenis</td><td class=inp1>:</td><td class=ul><select name=jenis>$optjns</select></td></tr>
    <tr><td class=inp1>Kode MK</td><td class=inp1>:</td><td class=ul><input type=text name=Kode value=$_SESSION[Kode]></td></tr>
    <tr><td class=inp1>Seksi</td><td class=inp1>:</td><td class=ul><input type=text name=seksi value=$_SESSION[seksi]></td></tr>
    <tr><td class=ul colspan=3><input type=submit name=submit value='Kirim'></td></tr>
    </table></form></p>";
}

function Headerxx($tahun, $prodi, $jenis, $div, $maxcol, &$hal){
  global $_lf;
		$hal++;
		$NamaJenis = ($jenis == 'K') ? "" : '(RESPONSI)';
	  $hdr = str_pad("*** TANDA TERIMA DISKET NILAI $NamaJenis ***", $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahun($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 85, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 4) . 
            str_pad("KODE MK", 8) . 
            str_pad("NAMA MK", 35) . 
            str_pad('KELAS', 6) . 
            str_pad("JML MHSW", 9) . 
            str_pad("NMR", 5) .
            str_pad("NAMA DOSEN", 35) .
            str_pad("| DISKET", 10) .
            str_pad("| FINAL", 10) . 
            $_lf;
		$hdr .= $div;
		
		return $hdr;
}

function daftar(){
  global $_lf;
  $_prodi = (!empty($_SESSION['prodi'])) ? "and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0" : '';
  $_prid  = (!empty($_SESSION['prid'])) ? "and INSTR(j.ProgramID, '.$_SESSION[prid].')>0" : '';
  $_jenis = (!empty($_SESSION['jenis'])) ? "and j.JenisJadwalID = '$_SESSION[jenis]'" : '';
  $_Kode  = (!empty($_SESSION['Kode'])) ? "and j.MKKode = '$_SESSION[Kode]'" : '';
  $_seksi = (!empty($_SESSION['seksi'])) ? "and j.NamaKelas = '$_SESSION[seksi]'" : '';
  $s = "select j.MKKode, mk.Nama as MKNAMA, j.NamaKelas, j.DosenID, d.Nama as DSNNAMA, j.JadwalID, j.JenisJadwalID, mk.ProdiID
        from jadwal j 
        left outer join mk on j.MKID = mk.MKID
        left outer join dosen d on j.DosenID = d.Login
        where j.TahunID = '$_SESSION[tahun]'
        and j.JadwalSer = 0
        $_prodi $_prid $_jenis $_Kode $_seksi
        group by MKKode, NamaKelas
        order by mk.ProdiID, j.MKKode, j.NamaKelas
        ";
  $r = _query($s);
  
  $maxcol = 121;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(10));
  $div = str_pad('-', $maxcol, '-').$_lf;

  $n = 0; $hal = 0; $nprd = 0; 
  $brs = 26;
  $maxbrs = 25;
	
	$jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	$prodi = "";
	$first = 1;
	$ctt = 1;
	
	while($w = _fetch_array($r)) {
		$_nprodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
		
		
		if ($brs > $maxbrs) {
			if ($first == 0) {
				fwrite($f, $div.chr(12));
			}
			$hd = Headerxx($_SESSION['tahun'], $_nprodi, $w['JenisJadwalID'], $div, $maxcol, $hal);
			fwrite($f, $hd);
			$brs = 0;
			$first = 0;
			$prodi = $w['ProdiID'];
		} 		
		elseif ($prodi != $w['ProdiID']) {
        $prodi = $w['ProdiID'];
				if ($first == 0){
					fwrite($f, $div);
				}
				fwrite($f, chr(12));
				fwrite($f, Headerxx($_SESSION['tahun'], $_nprodi, $w['JenisJadwalID'], $div, $maxcol, $hal));
				$brs=0;
				$n=1;
      } 
		$Jml = GetaField('krs', "JadwalID", $w['JadwalID'], "count(MhswID)")+0;
		if ($Jml > 0 ) {
		$n++; $brs++;
		$isi = str_pad($n, 4).
					 str_pad($w['MKKode'], 8).
					 str_pad($w['MKNAMA'], 35).
					 str_pad($w['NamaKelas'], 6).
					 str_pad($Jml, 9, ' ', STR_PAD_BOTH) .
					 str_pad($w['DosenID'], 5).
					 str_pad($w['DSNNAMA'], 35).
					 str_pad('|', 10) .
					 str_pad('|', 10) .
					 $_lf . $div;
		
		fwrite($f, $isi);
		}
	}
	//fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],95,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),70,' ').$_lf);
  fwrite($f, str_pad("Akhir laporan", 120, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid  = GetSetVar('prid');
$jenis = GetSetVar('jenis');
$Kode  = GetSetVar('Kode');
$seksi = GetSetVar('seksi');

TampilkanJudul("Tanda Terima Disket Nilai");
DftrTandaTerima();
if (!empty($tahun)) daftar();

?>
