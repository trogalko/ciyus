<?php
// Author: Emanuel Setio Dewo
// 12 May 2006
// www.sisfokampus.net

//include "dwo.lib.php";

function Greet() {
  $optprg = GetOption2("program", "concat(ProgramID,  ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  echo <<<END
  <p>Selamat datang di fasilitas Autodebet.</p> 
  <p>Modul ini dibagi menjadi beberapa 2 tahap, yaitu:
  <ol>
  <li><b>Proses Keuangan untuk Autodebet</b>, proses ini terdiri dari beberapa sub proses, yaitu:</li> 
    <ul>
    <li><b>Inisialisasi</b> &raquo; buffer data mahasiswa</li> 
    <li><b>Proses Keuangan</b> &raquo; memproses biaya2 dan membuat BPM</li>
    <li><b>Download file</b> &raquo; membuat file DBF untuk bank</li>
    </ul><br />
  <li><b>Upload file Autodebet dari Bank</b>, setelah file autodebet diproses oleh bank, maka kita dapat meng-<i>upload</i> file ke sistem.</li>
  </ol>
  </p>
  <p>Mungkin setiap tahap proses akan memakan waktu agak lama tergantung jumlah data yang ada.
  Sebaiknya selama proses berlangsung, server tidak diganggu dengan tugas-tugas lain.
  Atau lakukan proses autodebet pada jam-jam tertentu saat sistem tidak sibuk.</p>
  
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='autodebet'>
  <input type=hidden name='WZRD' value='init0'>
  <tr><td class=ul colspan=2><b>1. Proses Autodebet</b></td></tr>
  <tr><td class=inp1>Tahun Akd</td><td class=ul><input type=text name='tahun' value='$_SESSION[tahun]'></td></tr>
  <tr><td class=inp1>Program</td><td class=ul><select name='prid'>$optprg</select></td></tr>
  <tr><td class=inp1>Program Studi</td><td class=ul><select name='prodi'>$optprd</select></td></tr>
  <tr><td class=inp1>Proses</td><td class=ul><input type=submit name='Mulai' value='Mulai'></td></tr>
  </form></table></p>
  
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' enctype='multipart/form-data' method=POST>
  <input type=hidden name='mnux' value='autodebet'>
  <input type=hidden name='WZRD' value='aplod0'>
  <tr><td class=ul colspan=2><b>2. Upload File Autodebet</b></td></tr>
  <tr><td class=inp1>File Autodebet</td><td class=ul><input type=file name='nmf'></td></tr>
  <tr><td class=inp1>Proses</td><td class=ul><input type=submit name='Upload' value='Upload'></td></tr>
  </form></table></p>
END;
}
function init0() {
  echo "
  <p>
  <font size=+2><b>Inisialisasi</b></font>
  &raquo; Proses Keuangan
  &raquo; Buat File Bank
  </p>
  <p>Proses ini akan mengambil data mahasiswa yang telah disetup untuk autodebet.</p>
  <p><IFRAME src='autodebet1.php?WZRD=init0' frameborder=0 height=300 width=300>
  </IFRAME></p>
  <p>Jika data di dalam box telah benar dan tersedia, maka Anda dapat memproses autodebet
  ke langkah selanjutnya. Jika data kosong atau gagal mengambil data mahasiswa, maka
  Anda dapat mengulangi proses ini.</p>
  <hr size=1 color=silver>
  Pilihan: <input type=button name='Ulangi' value='Ulangi Proses' onClick=\"window.location.reload()\">
    <input type=button name='Tahap2' value='Tahap2: Proses Keuangan' onClick=\"location='?mnux=autodebet&WZRD=prc0'\">  
";
}
function prc0() {
  $_SESSION['ADPOS'] = 0;
  $max = $_SESSION['MaxData']-1;
  $md = $_SESSION['AD'.$max];
  echo "<p>
  Inisialisasi
  &raquo; <font size=+2><b>Proses Keuangan</font></b>
  &raquo; Buat File Bank
  </p>
  <p>Proses ini akan memproses data keuangan mahasiswa sekaligus membuat BPM.
  Proses akan berhenti saat mencapai data ke-<b>$max</b> untuk NPM: <b>$md</b></p>
  <p><IFRAME src='autodebet1.php?WZRD=prc0' frameborder=0 height=300 width=300>
  </IFRAME></p>
  
  Jika proses telah selesai, lanjutkan ke proses buat file.<br />
  <hr>Pilihan:
  <input type=button name='Tahap3' value='Tahap3: Buat File Bank' onClick=\"location='?mnux=autodebet&WZRD=file0'\">";
}
function file0() {
  $_SESSION['ADPOS'] = 0;
  $max = $_SESSION['MaxData'];
  $md = $_SESSION['AD'.$max];
  BuatArrayHeader($hdr, $hdrid);
  $namadbf = "autodebet-$_SESSION[tahun]-$_SESSION[prodi].dbf";
  $dbfheader = array (
                      array("THSMSTRINA", 'C', 5),
					  array("KDFAKTRINA", 'C', 1),
					  array("KDJURTRINA", 'C', 1),
					  array("NIMHSTRINA", 'C', 9),
					  array("NMMHSTRINA", 'C', 30),
					  array("TAGIHTRINA", 'N', 9, 0),
					  array("STATUTRINA", 'C', 1),
					  array("NLHUTTRINA", 'N', 12, 0),
					  array("NLDENTRINA", 'N', 12, 0),
					  array("NLSEMTRINA", 'N', 12, 0),
					  array("NLKOKTRINA", 'N', 12, 0),
					  array("NLSKSTRINA", 'N', 12, 0),
					  array("NLPRATRINA", 'N', 12, 0),
					  array("NLSKITRINA", 'N', 12, 0),
					  array("TGDEBTRINA", 'D', 8),
					  array("NOBPMTRINA", 'C', 10),
					  array("JMSKSTRINA", 'N', 2, 0),
					  array("JMPRATRINA", 'N', 2, 0));
	If (!dbase_create("autodebet/$namadbf", $dbfheader)) {
	echo "Gagal membuat File DBF";
	exit;
	}
  $gab = array();
  //for ($i=0; $i<sizeof($hdr); $i++) $gab[$i] = $hdr[$i] . '(' . $hdrid[$i] . ')';
  //$_hdr = implode(';', $gab) . "\n";
  //$hdr = "No;NPM;KHS;Nama;Rekening;Biaya;Potongan;Bayar;Tarik;TotalSKS;BPM;Tagih;Status;$_hdr";
  $hdr = "THSMSTRINA;KDFAKTRINA;KDJURTRINA;NIMHSTRINA;NMMHSTRINA;TAGIHTRINA;STATUTRINA;NLHUTTRINA(30);NLDENTRINA(35);NLSEMTRINA(11);NLKOKTRINA(8);NLSKSTRINA(5);NLPRATRINA(6);NLSRITRINA(38);TGDEBTRINA;NOBPMTRINA;JMSKSTRINA;JMPRATRINA;\n";
  //fwrite($f,"NIM;Nama;Tagihan;Autodebet;BPM;Sumbangan;BPS;Pokok;BPP SKS;Skripsi;Praktikum;Ospek;Hutang;Denda;Status;\n");
  // Buat file
  $nmf = "tmp/$_SESSION[_Login].autodebet.csv";
  $f = fopen($nmf, 'w');
  //fwrite($f, $hdr);
  //fwrite($f,"NIM;Nama;Tagihan;Autodebet;BPM;Sumbangan;BPS;Pokok;BPP SKS;Skripsi;Praktikum;Ospek;Hutang;Denda;Status;\n");
  fwrite($f, "THSMSTRINA(5~C);KDFAKTRINA(1~C);KDJURTRINA(1~C);NIMHSTRINA(9~C);NMMHSTRINA(30~C);TAGIHTRINA(9~N);STATUTRINA(1~C);NLHUTTRINA(12~N);NLDENTRINA(12~N);NLSEMTRINA(12~N);NLKOKTRINA(12~N);NLPRATRINA(12~N);NLSKSTRINA(12~N);NLSRITRINA(12~N);TGDEBTRINA(8~D);NOBPMTRINA(10~N);JMSKSTRINA(2~N);JMPRATRINA(2~N);\n");
  fclose($f);
  // Buat file header
  $nmf = "tmp/$_SESSION[_Login].autodebet.hdr.csv";
  $f = fopen($nmf, 'w');
  fwrite($f, $hdr);
  fclose($f);
  echo "<p>
  Inisialisasi
  &raquo; Proses Keuangan</font>
  &raquo; <font size=+2><b>Buat File Bank</b></font>
  </p>
  <p>Proses ini akan membuat file untuk bank.</p>
  <p><IFRAME src='autodebet1.php?WZRD=file0' frameborder=0 height=300 width=300>
  </IFRAME></p>
  <p>Setelah proses ini selesai, Anda dapat mendownload file untuk bank.</p>
  ";
}
function BuatArrayHeader(&$hdr, &$hdrid) {
  $s = "select BIPOTNamaID, Nama
    from bipotnama
    where TrxID=1
    order by Urutan";
  $r = _query($s);
  $hdr = array();
  $hdrid = array();
  while ($w = _fetch_array($r)) {
    $hdr[] = $w['Nama'];
    $hdrid[] = $w['BIPOTNamaID'];
  }
}

function aplod0(){
	//$tahun = $_SESSION['tahun'];
	$Filedbf = $_FILES['nmf']['tmp_name'];
	$NamaFiledbf = $_FILES['nmf']['name'];
	$x = 0;
	
	$nmf = "autodebetup/$NamaFiledbf";
	if (move_uploaded_file($Filedbf, $nmf)){
		if (file_exists($nmf)){
			$conn = dbase_open($nmf, 0);
			if ($conn) {
				$dbfrec = dbase_numrecords($conn);
				if ($dbfrec) {
					for ($i=1;$i<=$dbfrec;$i++){
						$row = dbase_get_record_with_names($conn, $i);
						$BPM = substr_replace($row['NOBPMTRINA'], '2007-', 0, 4);
						$nobpm = GetaField('bayarmhsw', "BayarMhswID", $BPM, 'MhswID');
						if (!empty($nobpm)){
						//var_dump($row['STATUTRINA']); exit;
						$STATUS = trim($row['STATUTRINA']);
							if ($STATUS == 'S'){
								$x++;
								$khsid = GetaField('khs', "TahunID = '$row[THSMSTRINA]' and MhswID", $row['NIMHSTRINA'], 'KHSID');
								$_SESSION['BPT'.$x] = "$row[NLHUTTRINA](30);$row[NLDENTRINA](35);$row[NLSEMTRINA](11);$row[NLKOKTRINA](8);$row[NLPRATRINA](6);$row[NLSKSTRINA](5);$row[NLSRITRINA](38)";
								$_SESSION['ADUP'.$x] = "$row[NIMHSTRINA]~$khsid~$BPM~$row[TAGIHTRINA]~$row[THSMSTRINA]";
							} 
							else {}
						}
					}
					$_SESSION['ADUPPOS'] = 1;
					$_SESSION['ADUPPOSX'] = $x;
					echo "<p>File yang diupload akan diproses. Terdapat <b>$x</b> data yg akan diupload.</p>
								<p><IFRAME src='autodebet1.php?WZRD=aplod0' frameborder=0 height=300 width=300>
								</IFRAME></p>";
				}
			} 
			else {
					echo ErrorMsg("Gagal Membuka file Autodebet", 
					"File Autodebet gagal di buka, ulangi proses sekali lagi.");
			}
		} 
		else {
			echo ErrorMsg("Proses Upload File DBF Gagal",
      "File gagal diupload. Coba ulangi sekali lagi.
      <hr size=1>
      Opsi: <a href='?mnux=$_SESSION[mnux]'>Kembali</a>");
		}
	}
	else {
    $err = $_FILES['nmf']['error'][0];
    $nama = $_FILES['nmf']['name'];
    echo ErrorMsg("Gagal Upload",
    "File autodebet gagal diupload. File yg diupload: $nama. <br />
    Pesan error: $err");
	}
}

/*
function UpdateBipotMhsw($updtbpt, $mhswid, $tahun){
	$arrhdr = explode(';', $updtbpt);
  $hdr = array();
  for ($i=0; $i < sizeof($arrhdr); $i++) {
    $apa = explode('(',  $arrhdr[$i]);
    $apa[1] = str_replace(')', '', $apa[1]);
    $hdr[] = $apa[1];
		$jml[] = $apa[0];
  }
  $arr = array();
  for ($i = 0; $i < sizeof($hdr); $i++){
	$s = "update bipotmhsw set Dibayar='$jml[$i]'
				where MhswID = $mhswid
				and TahunID = $tahun
      	and BipotNamaID=$hdr[$i]";
	
	$r = _query($s);
	}
}
*/
/*
function aplod0() {
  $tahun = $_SESSION['tahun'];
  $nmf = "tmp/$_SESSION[_Login].autodebet.up.txt";
  $upl = $_FILES['nmf']['tmp_name'];
  if (move_uploaded_file($upl, $nmf)) {
    // proses
    $f = fopen($nmf, 'r');
    $isi = fread($f, filesize($nmf));
    fclose($f);
    // data
    $dat = explode(chr(10), $isi);
    // header
    $hdr = $dat[0];
    //echo($hdr);
    $arrhdr = explode(';', $hdr);
    $x = 0;
    for ($i=1; $i < sizeof($dat)-1; $i++) {
      // posisi status (Y/N) adalah pada field ke-12
			//echo $det[4];
      $det = explode(';', $dat[$i]);
      $yesno = $det[14];
	    $nobpm = $det[4];
			//echo $nobpm;
	    $ada = Getafield('bayarmhsw','BayarMhswID',$nobpm,'MhswID');
			//echo $ada;
	    if (empty($ada)) {}
	    else {
				//echo "$yesno <br />";
        if ($yesno[0] <> 'B') {}
        else {
					//echo "$yesno";
					$x++;
					$khsid = GetaField('khs',"TahunID = '$tahun' and MhswID",$det[0],'KHSID');
					$mhswid = $det[0];
					$byr = $det[2];
					//echo $khsid;
          $_SESSION['ADUP'.$x] = "$mhswid~$khsid~$nobpm~$byr";
          //echo $det[0] . '. ' . $det[1] . "<br />";
        }
			}	
      //echo $det[0] . '. ' . $det[1] . "<br />";
    }
    $_SESSION['ADUPPOS'] = 1;
    $_SESSION['ADUPPOSX'] = $x;
    echo "<p>File yang diupload akan diproses. Terdapat <b>$x</b> data yg akan diupload.</p>
    <p><IFRAME src='autodebet1.php?WZRD=aplod0' frameborder=0 height=300 width=300>
    </IFRAME></p>
    ";
  }
  else {
    $err = $_FILES['nmf']['error'][0];
    $nama = $_FILES['nmf']['name'];
    echo ErrorMsg("Gagal Upload",
    "File autodebet gagal diupload. File yg diupload: $nama. <br />
    Pesan error: $err");
  }
}
*/
// *** Main ***
TampilkanJudul("Autodebet");
$tahun = GetSetVar('tahun');
$prid = GetSetVar('prid');
$prodi = GetSetVar('prodi');
if (empty($_REQUEST['WZRD'])) Greet();
else {
Greet();
$_REQUEST['WZRD']();
}
?>
