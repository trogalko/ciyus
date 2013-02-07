<?php
// Author: Emanuel Setio Dewo
// 15 Sept 2006

// *** Functions ***
include "header.dbf.php";
include "dbf.function.php";

function TampilkanHeaderProsesNomer() {
  $optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', "ProdiID");
  $bulan1 = GetMonthOption($_SESSION['bulan1']);
  $bulan2 = GetMonthOption($_SESSION['bulan2']);
  $tahun1 = GetNumberOption(date('Y')-10, date('Y'), $_SESSION['tahun1']);
  $tahun2 = GetNumberOption(date('Y')-10, date('Y')+1, $_SESSION['tahun2']);
  $TglYudisium = GetDateOption($_SESSION['TglYudisium']);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <tr><td class=ul colspan=2><font size=+1>Filter Mhsw</font> berdasarkan tanggal SK Yudisium</td></tr>
  <tr><td class=inp>Prodi :</td><td class=ul><select name='prodi'>$optprd</select> Kosongkan jika ingin melihat semua</td></tr>
  <tr><td class=inp>SK Yudisium bulan :</td><td class=ul>
    <select name='bulan1'>$bulan1</select><select name='tahun1'>$tahun1</select> s/d
    <select name='bulan2'>$bulan2</select><select name='tahun2'>$tahun2</select> <input type=submit name='Filter' value='Filter Daftar'></td></tr>
  </form>
  </table></p>";
}
function TampilkanDaftarKelulusan() {
  // Tampilkan Tombol Proses
  $TglSKKeluar = GetDateOption($_SESSION['TglSKKeluar'], 'TglSKKeluar');
  CheckFormScript('SKKeluar');
  echo " <script language='Javascript'>        
        function CheckAll(){
          $(\".chk input[@type='checkbox']\").each(function() {
	           this.checked = !this.checked;
          });
          
          return false;
        }
        </script>";
  echo " <form action='?' method=POST onSubmit=\"return CheckForm(this);\">
  <input type=hidden name='mnux' value='wisuda.nomer'>
  <input type=hidden name='gos' value='ProsesNomerKelulusan'>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr>
    <td class=inp>No SK Rektor</td>
    <td class=ul><input type=text name='SKKeluar' value='$_SESSION[SKKeluar]' size=50 maxlength=100 colspan=2></td>
  </tr>
  <tr>
    <td class=inp>Tanggal SK Rektor</td>
    <td class=ul>$TglSKKeluar</td>
    <td class=ul><input type=submit name='ProsesNomer' value='Proses Nomer Kelulusan'>
    &nbsp;
    <input type=button name='dbf' Value='Buat File Ijazah' onClick=\"location='?mnux=wisuda.nomer&gos=BuatDBFIjazah&prodi=$_SESSION[prodi]&bulan1=$_SESSION[bulan1]&bulan2=$_SESSION[bulan2]&tahun1=$_SESSION[tahun1]&tahun2=$_SESSION[tahun2]'\">
    &nbsp;
    <input type=button name='cetak' Value='Cetak' onClick=\"location='?mnux=wisuda.nomer&gos=cetak&prodi=$_SESSION[prodi]&bulan1=$_SESSION[bulan1]&bulan2=$_SESSION[bulan2]&tahun1=$_SESSION[tahun1]&tahun2=$_SESSION[tahun2]'\"></td>
  </tr>
  <tr>
    <td class=wrn colspan=3>Mahasiswa yg diproses hanya yg belum memiliki nomer Univ, Fakultas dan Prodi.
    Jika sudah, maka tidak akan diproses lagi.</td>
  </tr>
  </table></p>";
  echo "<span class=inp2><a href=# onClick=CheckAll()>Check All</a></span>";
  $whr = '';
  $whr .= (empty($_SESSION['prodi']))? '' : "and m.ProdiID='$_SESSION[prodi]'";
  $s = "select m.MhswID, m.Nama, m.NoIdentitas, m.NoFakultas, m.NoProdi,
    m.SKKeluar, date_format(m.TglSKKeluar, '%d/%m/%Y') as TglSKKeluar, NoIjazah,
    ta.SKYudisium, date_format(ta.TglSKYudisium, '%d/%m/%Y') as TglSKYudisium
    from ta ta
      left outer join mhsw m on m.MhswID=ta.MhswID
    where ta.Lulus='Y'
      and ('$_SESSION[tahun1]-$_SESSION[bulan1]-01' <= ta.TglSKYudisium)
      and (ta.TglSKYudisium <= '$_SESSION[tahun2]-$_SESSION[bulan2]-31')
      
      $whr
    order by m.MhswID, ta.TglSKYudisium";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>N.P.M</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>SK Yudisium</th>
    <th class=ttl>Tgl Yudisium</th>
    <th class=ttl>SK Rektor</th>
    <th class=ttl>Tgl SK Rektor</th>
    <th class=ttl>No Ijazah</th>
    <th class=ttl>No Univ</th>
    <th class=ttl>No Prodi/Fak</th>
    <th class=ttl>Proses</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $chk = (($w['SKKeluar'] == NULL) || ($w['SKKeluar'] == '')) ? "checked" : '';
    $dsbl = (!empty($w['NoIjazah'])) ? "disabled=true" : '';
    $n++;
    $_ni = ($w['NoIdentitas'] == 0)? 'class=ul' : 'class=nac';
    $_nf = ($w['NoFakultas'] == 0)? 'class=ul' : 'class=nac';
    $_np = ($w['NoProdi'] == 0)? 'class=ul' : 'class=nac';
    echo "<tr><td class=inp>$n</td
      <td class=ul>$w[MhswID]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[SKYudisium]&nbsp;</td>
      <td class=ul>$w[TglSKYudisium]</td>
      <td class=ul>$w[SKKeluar]&nbsp;</td>
      <td class=ul>$w[TglSKKeluar]</td>
      <td class=ul>$w[NoIjazah]&nbsp;</td>
      <td $_ni align=right>$w[NoIdentitas]</td>
      <td $_np align=right>$w[NoProdi]</td>
      <td id=dt class=ul align=center><input class=chk type=checkbox name='dtMhswID[]' value='$w[MhswID]' $chk $dsbl></td>
    </tr>";
  }
  echo "</table></p></form>";
}

function GenerateNoIjazah($prodi, $tglsk, $NoIndukFak, $NoIndukKOP){
  global $FormatNoIjazah;
  
  $tmp = $FormatNoIjazah;
  // Replace ~PRD~ dengan ProdiID
  $tmp = str_replace('~PRD~', $prodi, $tmp);
  // Replace No Induk Fakultas 
  $tmp = str_replace('~NOINDUKFAK~', str_pad($NoIndukFak, 4, '0', STR_PAD_LEFT), $tmp);
  // Replace No Induk Kopertis
  $tmp = str_replace('~NOINDUKKOP~', str_pad($NoIndukKOP, 5, '0', STR_PAD_LEFT), $tmp);
  
  $tahun = substr($tglsk, 0, 4);
  $bulan = substr($tglsk, 5, 2);
  
  $tglsk = $bulan .'/'. $tahun;
  
  // Replace bulan/tahun
  $tmp = str_replace('~DATE~', $tglsk, $tmp);
  
  return $tmp;
}

function ProsesNomerKelulusan() {
  global $FormatNoIjazah;
  
  $dtMhswID = array();
  $dtMhswID = $_REQUEST['dtMhswID'];
  $jml = sizeof($dtMhswID);
  
  for ($i = 0; $i < $jml; $i++) {
    $MhswID = $dtMhswID[$i];
    $mhsw = GetFields('mhsw', 'MhswID', $MhswID, '*');
    $noProdi = 0;
    $noIdentitas = 0;
    
    $Kode = $_SESSION['_KodeID'];
    
    $noProdi = GetaField('prodi', 'ProdiID', $mhsw['ProdiID'], 'NoProdi')+0;
    $noIdentitas = GetaField('identitas', 'Kode', $Kode, 'NoIdentitas')+0;
    
    // Counting No Induk Fakultas/Prodi
    $noProdi++;
    // counting No Induk Kopertis
    $noIdentitas++;
    
    $NoSeriIjazah = GenerateNoIjazah($mhsw['ProdiID'], $_SESSION['TglSKKeluar'], $noProdi, $noIdentitas);
    
    // Update data Mahasiswa
    $s = "update mhsw set SKKeluar='$_SESSION[SKKeluar]', TglSKKeluar='$_SESSION[TglSKKeluar]', 
                          NoIjazah='$NoSeriIjazah', NoProdi = '$noProdi', NoFakultas='$noProdi', NoIdentitas= '$noIdentitas'
         where MhswID='$MhswID' ";
    $r = _query($s);
    
    // Update Wisuda counter NoProdi
    $s0 = "update prodi set NoProdi=" . (int)$noProdi . " where ProdiID='$mhsw[ProdiID]'";
    $r0 = _query($s0);

    $Kode = $_SESSION['_KodeID'];
        
    // Update Wisuda counter NoIdentitas
    $s1 = "update identitas set NoIdentitas=" . (int)$noIdentitas . " where Kode='$Kode'";
    $r1 = _query($s1);
  }
  if ($jml > 0) echo Konfirmasi("SK Rektor dan Tanggal SK Rektor Telah Diset",
    "Tanggal dan SK Rektor sudah diset pada <font size=+1>$jml</font> mhsw.");
  TampilkanDaftarKelulusan();
}

function BuatDBFIjazah() {
	global $HeaderIjazah;
	
  $whr .= (empty($_REQUEST['prodi']))? '' : "and m.ProdiID='$_REQUEST[prodi]'";
  $s = "select m.MhswID
    from ta ta
      left outer join mhsw m on m.MhswID=ta.MhswID
    where ta.Lulus='Y'
      and ('$_REQUEST[tahun1]-$_REQUEST[bulan1]-01' <= ta.TglSKYudisium)
      and (ta.TglSKYudisium <= '$_REQUEST[tahun2]-$_REQUEST[bulan2]-31')
      $whr
    order by m.MhswID, ta.TglSKYudisium";
  $r = _query($s); $n = 0;
	
	$ran = rand(1, 1000);
	$DBFName = "tmp/ijazah-$_REQUEST[prodi]-$ran.DBF";
	DBFCreate($DBFName, $HeaderIjazah);
	
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION["DBF-MHSWID-$n"] = $w['MhswID'];
  }
	$_SESSION["DBF-FILES"] = $DBFName;
  $_SESSION["DBF-POS"] = 0;
  $_SESSION["DBF-MAX"] = $n;
  echo "<p>Akan diproses <font size=+1>$n</font> data.</p>";
  echo "<p><IFRAME src='ijazah.import.php' frameborder=0 height=400 width=600>
  </IFRAME></p>";
}

function Cetak(){
  global $_lf;
  $whr .= (empty($_REQUEST['prodi']))? '' : "and m.ProdiID='$_REQUEST[prodi]'";
  $s = "select m.MhswID, ta.Judul, m.Nama, ta.GradeNilai, ta.TahunID, date_format(ta.TglSKYudisium, '%d-%m-%y') as TglSKYudisium,
        m.NoProdi, m.NoIdentitas, m.ProdiID, date_format(m.TglSKKeluar, '%d-%m-%Y') as TglSKKeluar
    from ta ta
      left outer join mhsw m on m.MhswID=ta.MhswID
    where ta.Lulus='Y'
      and ('$_REQUEST[tahun1]-$_REQUEST[bulan1]-01' <= ta.TglSKYudisium)
      and (ta.TglSKYudisium <= '$_REQUEST[tahun2]-$_REQUEST[bulan2]-31')
      and ta.NA = 'N'
      $whr
    order by m.ProdiID, m.MhswID, ta.TglSKYudisium";
  $r = _query($s);
  
  $maxcol = 200;
  $rand = rand(1, 1000);
  $nmf = "tmp/$_SESSION[_Login].$rand.dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(77).chr(27).chr(15).chr(27).chr(108).chr(10)).$_lf;
  $div = str_pad('-', $maxcol, '-').$_lf;
  $stt = array('10' => "K-S.Ked", '11' => "D-Dokter", '61' => "T-Tesis");
  $n = 0; $hal = 0; $nprd = 0; 
  $brs = 72;
  $maxbrs = 70;
	
	$jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
	$prodi = "";
	$first = 1;
	while($w = _fetch_array($r)) {
	  $ss = GetFields("krs krs
    left outer join mk mk on krs.MKID=mk.MKID
    left outer join jenispilihan jp on mk.JenisPilihanID=jp.JenisPilihanID",
    "jp.TA='Y' and krs.TahunID='$w[TahunID]' and krs.MhswID", $w['MhswID'], "mk.Nama as MKNama, mk.MKKode as MKKode");
    
    $_prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
		$fak = substr($w['ProdiID'], 0, 1);
    $_fak   = GetaField('fakultas', 'FakultasID', $fak, 'Nama');
		$n++; $brs++;
		
		if ($brs > $maxbrs) {
			if ($first == 0) {
				fwrite($f, $div.chr(12));
			}
			$hd = Headerxx($_fak, $_prodi, $div, $maxcol, $hal);
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
				fwrite($f, Headerxx($_fak, $_prodi, $div, $maxcol, $hal));
				$brs=0;
				$n=1;
      } 
		$Judul = PutuskanJudul($w['Judul']);
		$jd = array();
		for ($j = 0; $j < sizeof($Judul); $j++) {
      $kirinya = ($j == 0)? "" : str_pad(' ', 127, ' '); 
      $sp = ($j == 0) ? '' : $_lf;
      $jd[$j] = $kirinya . $Judul[$j] . $sp;
      $brs++;
    }
    $st = $stt[$w['ProdiID']];
    $st = (empty($st)) ? "S-Skripsi" : $st;
    //var_dump($jd);exit;
		$isi = str_pad($n, 5).
					 str_pad($w['TglSKKeluar'], 12).
					 str_pad($w['MhswID'], 12).
					 str_pad($w['Nama'], 40).
					 str_pad($ss['MKKode'], 10) .
					 str_pad($ss['MKNama'], 16) .
					 str_pad($st, 14) .
					 str_pad($w['GradeNilai'], 6, ' ', STR_PAD_BOTH) .
					 str_pad($w['TglSKYudisium'], 12) .
					 str_pad($jd[0], 50) .
					 str_pad($w['NoProdi'], 5, ' ', STR_PAD_LEFT) .
					 str_pad($w['NoIdentitas'], 8, ' ', STR_PAD_LEFT) .
					 $_lf;
		fwrite($f, $isi);
		fwrite($f, $jd[1]);
		fwrite($f, $jd[2]);
		fwrite($f, $jd[3]);
		$Juduls = '';
	}
	fwrite($f, $div);
  fwrite($f, str_pad("Dicetak oleh : ".$_SESSION['_Login'],30,' ').str_pad("Dicetak : ".date("d-m-Y H:i"),170,' ', STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad("Akhir laporan", 200, ' ', STR_PAD_LEFT));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "wisuda.nomer");
}

function Headerxx($fakultas, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** DAFTAR POSTING SKRIPSI/SARJANA ***', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "JURUSAN        : $fakultas" . $_lf;
		$hdr .= "PRODI          : $prodi" . str_pad('Halaman : ' . $hal, 160, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NOMOR INDUK LULUSAN",197, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= str_pad("NO", 5) . 
            str_pad("TGL ENTRY", 12) . 
            str_pad("NIM", 12) . 
            str_pad('NAMA MAHASISWA', 40) . 
            str_pad("KODE MK", 10) . 
            str_pad("NAMA MK", 16) .
            str_pad("STATUS LULUS", 14) .
            str_pad("NILAI", 6) .
            str_pad("TGL LULUS", 12) .
            str_pad("JUDUL SKRIPSI", 50) .
            str_pad("FAK/JUR", 8) .
            str_pad("UNIKA KOPERTIS", 8) .
            $_lf;
		$hdr .= $div;
		
		return $hdr;
}

function PutuskanJudul($judul = '') {
  $max = 50;
  $judul = TRIM($judul);
  $len = strlen($judul);
  $arr = array();
  if ($len <= $max) $arr[] = $judul;
  else {
    $aw = 0;
    $ak = $max;
    $str = $judul;
    while (strlen($str) > 0) {
      $panjang = strlen($str);
      if ($panjang > $max) {
        $sub = substr($str, 0, $max);
        $sub = TRIM($sub);
        $pos = strrpos($sub, ' ');
        $ak = ($pos === false)? $ak : $pos;
        $sub = substr($str, 0, $ak);
        $sub = TRIM($sub);
        $arr[] = $sub;
            
        $str = substr($str, $ak+1, $len);
        $str = TRIM($str);
      }
      else {
        $arr[] = TRIM($str);
        $str = '';
      }
    }
  }
  return $arr;
}

// *** Parameters ***
$bulan1 = GetSetVar('bulan1', date('m'));
$bulan2 = GetSetVar('bulan2', date('m'));
$tahun1 = GetSetVar('tahun1', date('Y'));
$tahun2 = GetSetVar('tahun2', date('Y'));
$prodi = GetSetVar('prodi');
$SKYudisium = GetSetVar('SKYudisium');

$TglYudisium_d = GetSetVar('TglYudisium_d', date('d'));
$TglYudisium_m = GetSetVar('TglYudisium_m', date('m'));
$TglYudisium_y = GetSetVar('TglYudisium_y', date('Y'));
$TglYudisium = "$TglYudisium_y-$TglYudisium_m-$TglYudisium_d";
$_SESSION['TglYudisium'] = $TglYudisium;

$SKKeluar= GetSetVar('SKKeluar');

$TglSKKeluar_d = GetSetVar('TglSKKeluar_d', date('d'));
$TglSKKeluar_m = GetSetVar('TglSKKeluar_m', date('m'));
$TglSKKeluar_y = GetSetVar('TglSKKeluar_y', date('Y'));
$TglSKKeluar = "$TglSKKeluar_y-$TglSKKeluar_m-$TglSKKeluar_d";
$_SESSION['TglSKKeluar'] = $TglSKKeluar;

$gos = (empty($_REQUEST['gos']))? "TampilkanDaftarKelulusan" : $_REQUEST['gos'];


// *** Main ***
TampilkanJudul("Pemrosesan Nomer Kelulusan");
TampilkanHeaderProsesNomer();
$gos();
?>
