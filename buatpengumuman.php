<?php
// Created By Sugeng.s
// Juni 2006

function TampilCariPeriode($mnux='',$gos=''){
    global $arrID;
    $optstst = GetOption2('statusawal', "Concat(StatusAwalID, ' - ', Nama)", "StatusAwalID", $_SESSION['status'], "StatusAwalID in ('B', 'P', 'S')", "StatusAwalID");
    echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='$mnux'>
    <input type=hidden name='gos' value='$gos'>
    <tr><th class=ttl colspan=2>$arrID[Nama]</th></tr>
    <tr><td class=inp1>Periode PMB</td>
      <td class=ul><input type=text name='pmbperiod' value='$_SESSION[pmbperiod]' size=10 maxlength=50></tr>
    <tr><td class=inp1>Status</td>
      <td class=ul><select name=status>$optstst</select></td></tr>
      <tr><td class=ul colspan=2><input type=submit name='Tampilkan' value='Tampilkan'></td></tr>
    </form></table></p>";
}

function TampilPengumuman(){
  global $_lf;
  
  $css = "<style>BODY {
  color: black;
  margin: 0 5 0 5;
  padding: 0 0 0 0;
  width: auto;
  font-family : sans, tahoma;
  font-weight : normal;
  font-size: 10pt;
}
.Judul {
  clear: both;
  font-size: 2em;
  font-family: Times;
  color: gray;
}
.box {
  font-size: 1em;
  border: 1px solid silver;
}
.ul {
  border-bottom: 1px solid #ddd;
  padding: 4px;
}</style>";

  if(empty($_SESSION['pmbperiod'])){
  	echo ErrorMsg("Priode Belum Ditentukan","Masukkan priode yang ingin ditampilkan");
  }
  else{
  	$q = "SELECT PMBRef, Nama, PMBID FROM pmb WHERE PMBPeriodID = '$_SESSION[pmbperiod]' 
          AND LulusUjian = 'Y' and StatusAwalID = 'B'
		  ORDER BY PMBID, Nama";
	$_q = _query($q);
	$nmf = "tmp/$_SESSION[pmbperiod].html";
	$n = 0;
	$f = fopen($nmf,'w');
	$tam = "<html><head><title>Pengumuman Penerimaan Mahasiswa</title>$css</head>$_lf<body>";
	$tam .= "<center class=Judul><p><font size=+3>Pengumuman Penerimaan Mahasiswa Baru</font><font size=+2><br>Gelombang Ke : $_SESSION[pmbperiod]</font></p></center>";
	$tam .= "<div align=center><table cellspacing=1 cellpadding=3 border=1 width=760 class=box>
			<tr bgcolor=#FFFF9C><th class=ul>No. Urut</th><th>No. Tes/PMB</th><th>Nama Calon Mahasiswa</th><th>PMB REF</th></tr>$_lf";
	fwrite($f,$tam);
	while($w=_fetch_array($_q)){
		$n++;
		if($n%2 == 0){$col="#D6D3CF";}else{$col="#EFEFEF";}
		$tam_ = "<tr bgcolor=$col><td width=20>$n.</td><td width=180 align=center><b>$w[PMBID]</b></td><td width=300>$w[Nama]</td><td width=180 align=center>$w[PMBRef]</td></tr>$_lf";
		fwrite($f,$tam_);
	}
	$tam = "</table></div><br><br></body></html>";
	fwrite($f,$tam);
	fclose($f);
	//echo PopUpMsg($nmf);
  }
}

function Headerxx($tahun, $status__, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
		if ($status__ == 'B') $TTL = "DITERIMA";
    elseif ($status__ == 'P') $TTL = "PINDAHAN";
    else $TTL = "PSSB"; 
	  $hdr .= str_pad('*** PENGUMUMAN PENERIMAAN MAHASISWA BARU **', $maxcol, ' ', STR_PAD_BOTH) . $_lf;
		$hdr .= str_pad("--- STATUS :  $TTL ---", $maxcol, ' ', STR_PAD_BOTH) . $_lf . $_lf;
    $hdr .= "Periode : " . NamaTahunPMB($tahun);
		$hdr .= str_pad('Halaman : ' . $hal, 42, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO. URUT", 10) . str_pad("NO. TES", 12) . str_pad("NAMA CALON MAHASISWA", 35) . $_lf;
		$hdr .= $div;
		
		return $hdr;
}

function Daftar(){
  global $_lf;
  if(empty($_SESSION['pmbperiod'])){
  	echo ErrorMsg("Priode Belum Ditentukan","Masukkan priode yang ingin ditampilkan");
  }
  else{
    $_stst = (empty($_SESSION['status'])) ? "StatusAwalID in ('B', 'P', 'S')" : "StatusAwalID = '$_SESSION[status]'";
  	$q = "SELECT PMBID, Nama, GradeNilai, ProdiID, StatusAwalID
    FROM pmb WHERE PMBPeriodID = '$_SESSION[pmbperiod]' AND LulusUjian = 'Y'
     and $_stst
		ORDER BY StatusAwalID, ProdiID, PMBID";
	$_q = _query($q);
	//echo "<pre>$q</pre>";
	$nmf = "tmp/$_SESSION[pmbperiod].dwoprn";
	$n = 0; $brs = 41;
	$maxcol = 80;
	$div = str_pad('-',$maxcol,'-').$_lf;
	$maxbrs = 40;
	$first = 1;
	$f = fopen($nmf,'w');
	$status_ = '';
	fwrite($f, chr(27).chr(18));
	while ($w = _fetch_array($_q)) {
		$n++; $brs++;
		if ($brs > $maxbrs) {
			if ($first == 0) {
				fwrite($f, $div.chr(12));
			}
			$hd = Headerxx($_SESSION['pmbperiod'], $w['StatusAwalID'], $div, $maxcol, $hal);
			fwrite($f, $hd);
			$brs = 0;
			$first = 0;
			$status_ = $w['StatusAwalID'];
		} 		
		elseif ($status_ != $w['StatusAwalID']) {
        $status_ = $w['StatusAwalID'];
				echo $w['StatusAwalID'];
        if ($first == 0){
					fwrite($f, $div);
					fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " .date("d-m-Y H:i"), 66, ' ').str_pad("Akhir Laporan", 54).$_lf.$_lf);
          fwrite($f, str_pad(' ', 50) . str_pad("Jakarta, " . date("d-m-Y"), 60, ' ' ).$_lf);
          fwrite($f, str_pad(' ', 52) . str_pad("Wakil Rektor I", 60, ' ').$_lf.$_lf.$_lf.$_lf);
          fwrite($f, str_pad(' ', 50) . str_pad("Kho I Eng, Dipl.-Inform",60, ' ').$_lf);
          fwrite($f, chr(12));
				}
				//echo "$brs";
				fwrite($f, chr(12));
				fwrite($f, Headerxx($_SESSION['pmbperiod'], $w['StatusAwalID'], $div, $maxcol, $hal));
				$brs=0;
				$n=1;
      }
     
     if ($pr != $w['ProdiID']) {
      $pr = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "ProdiID", $pr, 'Nama');
      fwrite($f, '   >> ' .$NamaProdi.$_lf);
      $brs++;
    }
     $isi = str_pad($n.'.',10) . 
            str_pad($w['PMBID'], 12) . 
            str_pad($w['Nama'], 35) .
            $_lf;
    fwrite($f, $isi);
  }                                                                                                                      
  fwrite($f, $div);
  fwrite($f, str_pad("Dicetak Oleh : $_SESSION[_Login], " .date("d-m-Y H:i"), 66, ' ').str_pad("Akhir Laporan", 54).$_lf.$_lf);
  fwrite($f, str_pad(' ', 50) . str_pad("Jakarta, " . date("d-m-Y"), 60, ' ' ).$_lf);
  fwrite($f, str_pad(' ', 52) . str_pad("Wakil Rektor I", 60, ' ').$_lf.$_lf.$_lf.$_lf);
  fwrite($f, str_pad(' ', 50) . str_pad("Kho I Eng, Dipl.-Inform",60, ' '));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
 }
}

$pmbperiod = GetSetVar('pmbperiod');
$status = GetSetVar('status');

TampilkanJudul("Buat Pengumuman");
TampilCariPeriode("buatpengumuman","TampilPengumuman");
if(!empty($_SESSION['pmbperiod']) and $Tampilkan = 'Tampilkan') {
//TampilPengumuman();
//echo "<p><a href=dwoprn.php?f=$nmf>Cetak Laporan</a></p>";
Daftar();
}  
?>
