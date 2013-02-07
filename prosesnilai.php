<html>
<head>
<title>Proses File Nilai</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.box {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	border: 1px solid #000000;
}
-->
</style>
<script language="JavaScript">

function checkForm()
{
	
	var gsoal;
	with(window.document.uploadform)
	{
		gsoal    = jmsoal;
		
	}
	
	if(trim(gsoal.value) == '')
	{
		alert('Jumlah soal belum ditentukan...!');
		gsoal.focus();
		return false;
	}
	else
	{
		return true;
	}
}
function trim(str)
{
	return str.replace(/^\s+|\s+$/g,'');
}
</script>
</head>

<body>
<?
function panggil() {
	echo "<p><table class=box cellspacing=1 cellpadding=4>
<form action='index.php' method='post' enctype='multipart/form-data' name='uploadform'>
	<input type=hidden name='mnux' value='prosesnilai'>
  <input type=hidden name='gos' value='ceknilai'>
  <table width='500' border='0' cellpadding='1' cellspacing='1' class='box'>
  <tr><td width='50'><b>Nama Test</b></td><td width='10' align='center'><b>:</b></td><td width='390'> <input type='text' name='namatest' size='50' class='box'></td></tr>
  <tr><td width='50'><input type='hidden' name='MAX_FILE_SIZE' value='2000000'><b>File Kunci</b></td><td width='10' align='center'><b>:</b></td><td width='390'> <input name='userfile1' type='file' class='box' id='userfile1' size='50'></td></tr>
  <tr><td width='50'><input type='hidden' name='MAX_FILE_SIZE' value='2000000'><b>File Jawaban</b></td><td width='10' align='center'><b>:</b></td><td width='390'> <input name='userfile2' type='file' class='box' id='userfile2' size='50'></td></tr>
  <tr><td width='50'><b>Jumlah Soal</b></td><td width='10' align='center'><b>:</b></td><td width='390'> <input type='text' name='jmsoal' size='20' class='box'>  (Max.100)</td></tr>
  <tr><td></td></tr>
  <tr><td width='80'><input name='upload' type='submit' class='box' id='upload' onClick='return checkForm();' value='Upload & Proses Nilai'></td></tr>
  </table>
</form>";
}
function ceknilai()
{
if(isset($_POST['upload']))
{
	$fileName1 = $_FILES['userfile1']['name'];
	$tmpName1  = $_FILES['userfile1']['tmp_name'];
	
	$fileName2 = $_FILES['userfile2']['name'];
	$tmpName2  = $_FILES['userfile2']['tmp_name'];
	
$connection = mysql_connect("localhost", "root", "m3tall1c4") or die ("Unable to connect to server");
$db = mysql_select_db("semarang", $connection) or die ("Unable to select database");

$sk = "delete from kunci";
mysql_query($sk);
$fpk = fopen($tmpName1,"r");
$noid = 0;
if (!feof($fpk)) {
while (list($sub1k, $sub2k, $sub3k, $sub4k, $sub5k) = fscanf($fpk, "%s\t%s\t%s\t%s\t%s\n")) {
$deretk = trim($sub1k.$sub2k.$sub3k.$sub4k.$sub5k);
$jmlk = strlen($deretk);
$data1k = substr($deretk,0,29);
$data2k = substr($deretk,-100);

$sqlk = "insert into kunci (kunci_jawab) values ('$data2k')";
		mysql_query($sqlk);
}
}

$s = "delete from jawab";
mysql_query($s);
$fp = fopen($tmpName2,"r");

if (!feof($fp)) {
while (list($sub1, $sub2, $sub3, $sub4, $sub5, $sub6, $sub7) = fscanf($fp, "%s\t%s\t%s\t%s\t%s\t%s\t%s\n")) {
$deret = trim($sub1.$sub2.$sub3.$sub4.$sub5.$sub6.$sub7);
$jml = strlen($deret);
$noid=$noid+1;
switch($jml) {
	case 138:
		$data1 = substr($deret,0,29);
		$data2 = substr($deret,29,9);
		$data3 = substr($deret,38,100);
		
		$sql = "insert into jawab (nim, jawaban) values ('$data2','$data3')";
		mysql_query($sql);
		break;
	case 146:
		$data1 = substr($deret,0,29);
		$data2 = substr($deret,29,9);
		$data3 = substr($deret,38,8);
		$data4 = substr($deret,46,100);
		
		$sql = "insert into jawab (nim, jawaban) values ('$data2','$data4')";
		mysql_query($sql);
		break;
	case 154:
		$data1 = substr($deret,0,29);
		$data2 = substr($deret,29,9);
		$data3 = substr($deret,38,8);
		$data4 = substr($deret,46,8);
		$data5 = substr($deret,54,100);
		
		$sql = "insert into jawab (nim, jawaban) values ('$data2','$data5')";
		mysql_query($sql);
		break;
	case 155:
		$data1 = substr($deret,0,29);
		$data2 = substr($deret,29,10);
		$data3 = substr($deret,39,8);
		$data4 = substr($deret,47,8);
		$data5 = substr($deret,55,100);
		
		$sql = "insert into jawab (nim, jawaban) values ('$data2','$data5')";
		mysql_query($sql);
		break;
	default:
		$potong = $jml-129;
		$data3 = substr($deret,-100);
		$data1 = substr($deret,29,$potong);
		$data2 = $noid." ".$data1;
		$sql = "insert into jawab (nim, jawaban) values ('$data2','$data3')";
		mysql_query($sql);
}
}
}
echo "<table border=1 cellpadding=2 cellspacing=0 class='box'>";

$bandingkan = "SELECT nim AS jnim, no_soal as jtampung, jawaban AS jjawab, benar AS jbenar, nilai AS jnilai FROM jawab ORDER BY jawab.nim ASC ";
$sqlkunci = "SELECT kunci_jawab FROM kunci";	
	$hasilsqle=mysql_query($bandingkan);
	$hasilsqlkunci=mysql_query($sqlkunci);
	
	$num = mysql_num_rows($hasilsqle);
	$fileku = "./tmp/ujian.txt";
	$tulis = fopen($fileku, 'w');
	$dataheader = "No"."\t"."NIM"."\t"."NAMA"."\t"."BENAR"."\t"."NILAI"."\n";
	$judultest = "Test ".$_POST['namatest']."\n";
	$jumlahsoal = "Jumlah Soal : ".$_POST['jmsoal']."\n";
	$jumlahtest = "Jumlah record yang diproses : ".$num."\n";
	fwrite($tulis, $judultest);
	fwrite($tulis, $jumlahsoal);
	fwrite($tulis, $jumlahtest);
	fwrite($tulis, $dataheader);
	echo "<font size = 3 color = blue><b>";
	echo "Test ".$_POST['namatest'];
	echo "<br>";
	echo "Jumlah Soal : ".$_POST['jmsoal'];
	echo "<br>";
    echo "Jumlah record yang diproses : ".$num;
	echo "</b></font><br><br>";
	
    $nopeserta = 0;
	$kuncinya=mysql_fetch_array($hasilsqlkunci);
	$tekskunci=$kuncinya[kunci_jawab];
	
	echo "<tr border=0><td colspan='5' align='center' border=0><A HREF='Downtxt.php?f=$fileku' target=_BLANK>Download as text file</A></td></tr>";
	echo "<tr><td align='center'>No.</td><td align='center'>NIM</td><td align='center'>Nama</td><td align='center'>Benar</td><td align='center'>Nilai</td><tr>";
	while($barise = mysql_fetch_array($hasilsqle)){ 
 	    $jmlbenar=0;
		$jmlsalah=0;
		$nilaites=0;
		
		$jml_char=$_POST['jmsoal'];
			
		for ($i = 0; $i<$jml_char;$i++) {
			$ambilkunci[$i]=substr($tekskunci,$i,1);
			$ambiljawaban[$i]=substr($barise[jjawab],$i,1);
			
			if ($ambiljawaban[$i]==$ambilkunci[$i]) {/*$status[$i]="benar";*/ $jmlbenar=$jmlbenar+1;}else{/*$status[$i]="salah";*/$jmlsalah=$jmlsalah+1;}
			
		} 
		//echo "Jawaban yg benar = $jmlbenar <br> Jawaban salah = $jmlsalah";
		$nilaites=($jmlbenar*100)/$jml_char;
		$entri = "UPDATE jawab SET benar = '$jmlbenar', salah = '$jmlsalah', nilai = '$nilaites' WHERE nim = '$barise[jnim]'";
		$hasil=mysql_query($entri);
}
$sqle = "SELECT jawab.nim AS jnimx, jawab.jawaban AS jjawabx, jawab.benar AS jbenarx, jawab.nilai AS jnilaix, mhsw.MhswID, mhsw.Nama AS m_namax FROM jawab LEFT OUTER JOIN mhsw ON jawab.nim = mhsw.MhswID ORDER BY jawab.nim ASC ";
$tampilkan=mysql_query($sqle);
while($barise2 = mysql_fetch_array($tampilkan)){ 
$nopeserta=$nopeserta+1;
echo "<tr><td align = 'right'>$nopeserta</td><td>$barise2[jnimx]</td><td>$barise2[m_namax]<td align = 'right'>$barise2[jbenarx]</td><td align = 'right'>$barise2[jnilaix]</td></tr>";
		$data = $nopeserta."\t".$barise2[jnimx]."\t".$barise2[m_namax]."\t".$barise2[jbenarx]."\t".$barise2[jnilaix]."\n";
		fwrite($tulis, $data);
}
	fclose($tulis);
	echo "</table>";
}
}

// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? "panggil" : $_REQUEST['gos'];
// *** Main ***
TampilkanJudul("Proses Cek Nilai Test");
$gos();

?>
</body>
</html>