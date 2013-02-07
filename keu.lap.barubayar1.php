<?php
function GetBipot2($pmb, $bipotid, &$total) {
  global $_lf;
  $s0 = "select b2.*, bn.Nama, bn.DefJumlah, bn.DefBesar, bn.Diskon
    from bipot2 b2
    left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$bipotid' and b2.SaatID=1
      and INSTR(b2.StatusAwalID, '.$pmb[StatusAwalID].')>0
    order by b2.Prioritas";
  $r0 = _query($s0);
  $thn = substr($w['PMBID'], 0, 4);
  $a = ''; $n = 0; $total = 0;
  while ($w0 = _fetch_array($r0)) {
    if ($w0['Jumlah'] == 0) {}
    elseif ($w0['GunakanGradeNilai'] == 'Y') {
      if (strpos($w0['GradeNilai'], ".$pmb[GradeNilai].") === false) {}
      else {
        $n++;
        $a .= InsertBIPOT($n, $w0, $tot, $bipotid, $pmb);
        $total += $tot;
      }
    }
    else {
    $n++;
    $a .= InsertBIPOT($n, $w0, $tot, $bipotid, $pmb);
    $total += $tot;
    }
  }
  $strtotal = str_pad(' ', 57, ' '). str_pad('-', 15, '-').$_lf;
  $strtotal .= str_pad('Total :', 57, ' ', STR_PAD_LEFT) .
    str_pad(number_format($total), 15, ' ', STR_PAD_LEFT);
  return $total;
}

function InsertBIPOT($n, $w, &$tot, $bipotid, $pmb) {
  global $_lf;
  $a = str_pad($n, 5, ' ', STR_PAD_LEFT) .'. ';
  $a .= str_pad($w['Nama'], 30, ' ');
  if ($w['DefJumlah'] > 1) {
    // Jika BPP SKS
    if ($w['BIPOTNamaID'] == 5) {
      $detbipot = GetFields('bipot', "BIPOTID", $bipotid, "*");
      $_prd = $detbipot['ProdiID'];
      $w['DefJumlah'] = GetaField('prodi', 'ProdiID', $_prd, "DefSKS");
    }
    $det = $w['DefJumlah']." x ".number_format($w['Jumlah']);
    $jml = $w['DefJumlah'] * $w['Jumlah'];
    $a .= str_pad($det, 15, ' ', STR_PAD_LEFT);
  }
  else {
    $a .= str_pad(' ', 15, ' ');
    $jml = $w['Jumlah'];
  }
  $tot = $jml;
  $a .= str_pad(number_format($jml), 20, ' ', STR_PAD_LEFT);
  return $a . $_lf;
}

function BiayaBipot($PMBID){
  $s = "select Dibayar from bipotmhsw where TrxID = 1 and PMBID = '$PMBID'";
  
  $r = _query($s);
  while($w = _fetch_array($r)){
    $JML += $w['Dibayar'];
  }
  
  return $JML;
}

function daftar(){
  global $_lf, $urutanstts;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "ProgramID='$_SESSION[prid]'";
  $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;

 	$_u = explode('~', $urutanstts[$_SESSION['_urutanstts']]);
  $_key = $_u[0];
  //var_dump($_u);
  
  if ($_key == "Calon Mahasiswa") $key = "and StatusMundur = 'N'";
  else if ($_key == "Calon Mahasiswa Mundur") $key = "and StatusMundur = 'Y'"; 
  else $key = '';      
  $s = "select PMBID, ProdiID, left(Alamat, 57) as Alamat ,TotalBiayaMhsw, 
        TotalSetoranMhsw, Kota, BIPOTID, Kodepos, GradeNilai, StatusMundur, 
        left(Telepon, 18) as Telepon, NIM, left(Nama,32) as Nama, Concat(RT,'/',RW) as RTRW, StatusAwalID
        from pmb
          where 
            PMBPeriodID  = '$_SESSION[tahun]' 
        and LulusUjian   = 'Y' 
        and StatusAwalID in ('B', 'P', 'S')
        $_whr
        $key
        group by PMBID, StatusMundur
        order by StatusMundur ASC, ProdiID, NIM DESC, PMBID";
  $r = _query($s);
 // echo "<pre>$s</pre>";
  $maxcol = 232;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(77).chr(27).chr(108).chr(10));
  $div  = str_pad('-', $maxcol, '-').$_lf;
  $div2 = str_pad('=', $maxcol, '=').$_lf;

  $n = 0; $hal = 0; $nprd = 0; 
  $brs = 56;
  $maxbrs = 49;
	
	$jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	$prodi = "";
	$first = 1;
	$fm = 1;
	$mundur = 'N';
	while($w = _fetch_array($r)) {
		$_prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
		
		$n++; $brs++;
		if ($brs > $maxbrs) {
			if ($first == 0) {
				fwrite($f, $div.chr(12));
			}
			$hd = Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal);
			fwrite($f, $hd);
			$brs = 0;
			$first = 0;
			$prodi = $w['ProdiID'];
		} 		
		elseif ($prodi != $w['ProdiID']) {
        $prodi = $w['ProdiID'];
				if ($first == 0){
					fwrite($f, $div);
					fwrite($f, str_pad("## TOTAL PER PRODI : ", 53) . str_pad(number_format($sum),140, ' ', STR_PAD_LEFT).$_lf);
          fwrite($f, $div);
				}
				fwrite($f, chr(12));
				fwrite($f, Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal));
				$brs=0;
				$sum = 0;
				$n=1;
      } 
    elseif ($mundur != $w['StatusMundur']){
      $mundur = $w['StatusMundur'];
      if ($first == 0) {
        fwrite($f, $div);
        fwrite($f, str_pad("## TOTAL : ", 53) . str_pad(number_format($sum),140, ' ', STR_PAD_LEFT).$_lf);
        fwrite($f, $div);
      }
      fwrite($f, $div);
      fwrite($f, str_pad("## TOTAL : ", 53) . str_pad(number_format($sum),140, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, $div);
      fwrite($f, chr(12));
      fwrite($f, HeaderMUN($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal));;
      $brs = 0;
      $n=1;
    }
    $Alamat = str_replace(chr(13), ' ', $w['Alamat']);
    $Alamat = str_replace(chr(10), '', $Alamat);
    $BiayaPMB = GetBipot2($w, $w['BIPOTID'], $tot);
    //echo $BiayaPMB;
		$isi_ = AmbilBPM($_SESSION['tahun'], $w['PMBID'], $brs);
		$dskn = AmbilDiskon($w['PMBID']);
		$BIAYA = BiayaBipot($w['PMBID']);
		$STATUS = GetaField('statusawal', 'StatusAwalID', $w['StatusAwalID'], 'Nama');
		$Balance = -$BiayaPMB + $dskn + $BIAYA+0;
		$TOTBAL += $Balance;
		$_Balance = number_format($Balance);
		$isi = str_pad($n, 4).
					 str_pad($w['PMBID'], 11).
					 str_pad($w['NIM'], 11).
					 str_pad($w['Nama'], 33) .
					 str_pad($Alamat, 60).
					 str_pad($w['Telepon'], 20) .
					 str_pad($STATUS, 6) .
					 str_pad(number_format($BiayaPMB), 12, ' ', STR_PAD_LEFT) .
					 str_pad(number_format($dskn), 12, ' ', STR_PAD_LEFT) .
					 str_pad(number_format($BIAYA+0), 12, ' ', STR_PAD_LEFT) .
					 str_pad($_Balance, 12, ' ', STR_PAD_LEFT). '  ' .
					 $isi_[0].
					 //str_pad(number_format($pot['POT']), 25, ' ', STR_PAD_LEFT).
					 //str_pad(number_format($sum).'    '.number_format($sumtotal), 23, ' ', STR_PAD_LEFT).
					 $_lf;
		
		fwrite($f, $isi);
		if (count($isi_) > 1) {
		  for ($i=1; $i<=count($isi_); $i++){
		    $ct = str_pad(' ', 195) . $isi_[$i] . $_lf;
		    fwrite($f, $ct);
		  }
		}
		//fwrite($f, $isi_);
		$sum += $Balance;
	}
	fwrite($f, $div);
	fwrite($f, str_pad("## TOTAL PER PRODI : ", 53) . str_pad(number_format($sum),140, ' ', STR_PAD_LEFT).$_lf);
	fwrite($f, $div2);
	fwrite($f, str_pad("## GRAND TOTAL : ", 53) . str_pad(number_format($TOTBAL), 140, ' ', STR_PAD_LEFT).$_lf);
	fwrite($f, $div2);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],176,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),30,' ').$_lf);
  fwrite($f, str_pad("Akhir laporan", 200, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}

function AmbilDiskon($PMBID){
  $s = "select Besar from bipotmhsw where TrxID = -1 and PMBID = '$PMBID'";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $w = _fetch_array($r);
  
  return $w['Besar'] + 0;
}

function AmbilBPM($tahun, $PMBID, &$brs){
  global $_lf;
  $s0 = "select *
    from bayarmhsw
    where 
       PMBID = '$PMBID'
    order by PMBID ";
  
  $r0 = _query($s0);
  $a = array();
  while ($t = _fetch_array($r0)){
    $brs++;
    if ($t['Proses'] == 1) {
      $a[] = str_pad($t['BayarMhswID'], 12) .
            str_pad($t['Tanggal'], 12) .
            str_pad(number_format($t['Jumlah']), 12, ' ', STR_PAD_LEFT);
    }
  }
  
  return $a;
}

function Headerxx($tahun, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** DAFTAR PEMBAYARAN MAHASISWA BARU **', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahunPMB($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 160, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 4) . 
            str_pad("PMBID", 11) . 
            str_pad("NIM", 11) .
            str_pad("NAMA", 33) . 
            str_pad('ALAMAT', 60) .
            str_pad('TELEPON/HP', 20) .
            str_pad('STATUS', 9) .
            str_pad('KEWAJIBAN', 15) .
            str_pad('DISKON', 8) .
            str_pad('PEMBAYARAN', 14) .
            str_pad('BALANCE', 12) .
            str_pad('NO BPM', 12) .
            str_pad('TGL', 16) .
            str_pad('NILAI', 12) . $_lf;
		$hdr .= $div;
		
		return $hdr;
}
function HeaderMUN($tahun, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** DAFTAR PEMBAYARAN MAHASISWA BARU (STATUS MUNDUR) ***', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahunPMB($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 160, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 4) . 
            str_pad("PMBID", 11) . 
            str_pad("NIM", 11) .
            str_pad("NAMA", 33) . 
            str_pad('ALAMAT', 60) .
            str_pad('TELEPON/HP', 20) .
            str_pad('STATUS', 9) .
            str_pad('KEWAJIBAN', 15) .
            str_pad('BIAYA REG', 8) .
            str_pad('PEMBAYARAN', 14) .
            str_pad('BALANCE', 12) .
            str_pad('NO BPM', 12) .
            str_pad('TGL', 16) .
            str_pad('NILAI', 12) . $_lf;
		$hdr .= $div;
		
		return $hdr;
}
function TampilkanPilihanStatus() {
  global $urutanstts;
  $a = '';
  for ($i=0; $i<sizeof($urutanstts); $i++) {
    $sel = ($i == $_SESSION['_urutanstts'])? 'selected' : '';
    $v = explode('~', $urutanstts[$i]);
    $_v = $v[0];
    $a .= "<option value='$i' $sel>$_v</option>\r\n";
  }
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='keu.lap.barubayar1'>
  <input type=hidden name='gos' value='Daftar'>
  <tr><td class=inp>Tampilkan berdasarkan: </td>
  <td class=ul><select name='_urutanstts' onChange='this.form.submit()'>$a</select></td></tr>
  </form></table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$urutanstts = array(0=>"Semua", 1=>"Calon Mahasiswa", 2=>"Calon Mahasiswa Mundur");
  
$_urutanstts = GetSetVar('_urutanstts', 1);

// *** Main ***
TampilkanJudul("Daftar Pembayaran Mahasiswa");
TampilkanTahunProdiProgram('keu.lap.barubayar1', 'Daftar');
TampilkanPilihanStatus();
if (!empty($tahun)) Daftar();

?>
