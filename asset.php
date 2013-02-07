<?

include_once "asset.cari.php";
include_once "class/dwolister.class.php";  

// *** Functions ***
function DaftarAsset() {
tampilkancariasset('asset', 1);
DaftarAst('asset', "gos=AssetEdt&md=0&AssetID==AssetID=", "Nama,Jumlah,Satuan,TglBeli,HargaBeli,LokasiID,Pemakai");
}

function getAjax(){
  echo <<<EOF
  <script language='JavaScript'>
    <!--
    function carikelajax(data) {
      var data = $('#kelid').val();
      $.ajax({  
        type: "POST",
        url: "getkel.php?KelompokID="+data,
        data: "KelompokID="+data,
        success: function(msg){
          var parsed = msg.split("|");
          for (var i=0; i<parsed.length; i++) {
            $('#mankom').val(parsed[0]);
            $('#manfis').val(parsed[1]);
            $('#prokom').val(parsed[2]);
            $('#profis').val(parsed[3]);
          }
        }
      });
    }
  -->
  </script>
EOF;
}
  
function AssetEdt() {
	global $KodeID;
	$md = $_REQUEST['md']+0;
  getAjax();
	if($md==0) {
      $w = GetFields('asset', 'AssetID', $_REQUEST['AssetID'], '*');
	    $AssetID = "<b><input type=hidden name='AssetID' value='$w[AssetID]' size=20 malength=20> $w[AssetID]</b>";
      $lks = GetOption2('lokasiasset', "concat(LokasiID, ' - ', Nama)", 'LokasiID', $w['LokasiID'], '', 	 'LokasiID');
      $klp = GetOption2('kelompokasset', "concat(KelompokID, ' - ', Nama)", 'KelompokID', $w['KelompokID'], '', 'KelompokID');
      $vdr = GetOption2('vendor', "concat(VendorID, ' - ', Nama)", 'VendorID', $w['VendorID'], '', 'VendorID');
 	    $jdl="Edit Data";
	}
	else{
      $w = array();
      $w['AssetID'] = GetaField('asset', 'KodeID', $KodeID, "max(AssetID)");
	    $w['TglBeli']=date('y-m-d');
	    $w['TglSusut']=date('y-m-d');
      $AssetID = "<input type=text name='AssetID' value='$w[AssetID]' size=20 malength=20>";
      $lks = GetOption2('lokasiasset', "concat(LokasiID, ' - ', Nama)", 'LokasiID', $_SESSION['KodeID'], '', 	 'LokasiID');
      $klp = GetOption2('kelompokasset', "concat(KelompokID, ' - ', Nama)", 'KelompokID', $_SESSION['KodeID'], '', 'KelompokID');
      $vdr = GetOption2('vendor', "concat(VendorID, ' - ', Nama)", 'VendorID', $_SESSION['VendorID'], '', 'VendorID');
	    $jdl="Tambah Data";
	}
    $tglbeli=GetDateOption($w['TglBeli'], 'TglBeli');
    $tglsusut=GetDateOption($w['TglSusut'], 'TglSusut');
    $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("AssetID, Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='asset'>
  <input type=hidden name='gos' value='AssetSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=4>$jdl</th></tr>
  <tr><td class=inp>Asset ID</td>
  <td class=ul colspan=4>$AssetID</td></tr>
  <tr><td class=inp>Nama</td>
  <td class=ul colspan=4><input type=text name='Nama' value='$w[Nama]' size=70 maxlength=80></td></tr>
  <tr><td class=inp>Tanggal Perolehan</td>
	  <td class=ul colspan=4>$tglbeli</td></tr>
  <tr><td class=inp>Tangal Disusutkan</td>
	  <td class=ul colspan=4>$tglsusut</td></tr>
  <tr><td class=inp>Q t y</td>
	  <td class=ul colspan=4><input type=text name='Jumlah' value='$w[Jumlah]' size=10 maxlength=6></td></tr>
  <tr><td class=inp>Satuan</td>
	  <td class=ul colspan=4><input type=text name='Satuan' value='$w[Satuan]' size=10 maxlength=8></td></tr>
  <tr><td class=inp>Harga Beli </td>
	  <td class=ul colspan=4><input type=text name='HargaBeli' value='$w[HargaBeli]' size=20></td></tr>  
  <tr><td class=inp>Lokasi </td>
	  <td class=ul colspan=4><select name='LokasiID'>$lks</select></td></tr>
  <tr><td class=inp>Kelompok </td>
	  <td class=ul><select name='KelompokID' id='kelid' onchange='carikelajax(this)'>$klp</select></td>
	  <td class=ul colspan=2>-</td>
  <tr><td class=inp>Manfaat Komersil</td>
	  <td class=ul><input type=text id='mankom' name='ManfaatKomersil' value='$w[ManfaatKomersil]' size=10 maxlength=6></td>
  <td class=inp>Manfaat Fiskal</td>
	  <td class=ul><input type=text id='manfis' name='ManfaatFiskal' value='$w[ManfaatFiskal]' size=10 maxlength=6></td></tr>
  <tr><td class=inp>Prosentase Komersil</td>
	  <td class=ul><input type=text id='prokom' name='ProsentaseKomersil' value='$w[ProsentaseKomersil]' size=10 maxlength=6></td>
      <td class=inp>Prosentase Fiskal</td>
	  <td class=ul><input type=text id='profis' name='ProsentaseFiskal' value='$w[ProsentaseFiskal]' size=10 maxlength=6></td> </tr>
  <tr><td class=inp>Kondisi</td>
	  <td class=ul colspan=4><input type=text name='Kondisi' value='$w[Kondisi]' size=70 maxlength=80></td></tr>
  <tr><td class=inp>Pemakai</td>
	  <td class=ul colspan=4><input type=text name='Pemakai' value='$w[Pemakai]' size=70 maxlength=80></td></tr>
  <tr><td class=inp>No. Purchase Order(PO)</td>
	  <td class=ul colspan=4><input type=text name='PurchaseOrder' value='$w[PurchaseOrder]'></td></tr>
  <tr><td class=inp>Vendor</td>
	  <td class=ul colspan=4><select name='VendorID'>$vdr</select></td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
  <td class=ul colspan=4><input type=checkbox name='NA' value='Y' $Na ></td></tr>
  <tr><td class=ul colspan=4><input type=submit name='Simpan' value='Simpan'>
  <input type=reset name='Reset' value='Reset'>
  <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=asset'\"></td></tr>
  </form></table></p>";
}

function AssetSav() {
  global $DefaultGOS, $KodeID;
  $md       =$_REQUEST['md']+0;
  $AssetID  =$_REQUEST['AssetID'];
  $nama     =$_REQUEST['Nama'];
  $tbl      ="$_REQUEST[TglBeli_y]-$_REQUEST[TglBeli_m]-$_REQUEST[TglBeli_d]";
  $tsst     ="$_REQUEST[TglSusut_y]-$_REQUEST[TglSusut_m]-$_REQUEST[TglSusut_d]";
  $jml      =$_REQUEST['Jumlah'];
  $stn      =$_REQUEST['Satuan'];
  $hrg      =$_REQUEST['HargaBeli'];
  $lks      =$_REQUEST['LokasiID'];
  $klp      =$_REQUEST['KelompokID'];
  $mkom     =$_REQUEST['ManfaatKomersil'];
  $mfis     =$_REQUEST['ManfaatFiskal'];
  $pkom     =$_REQUEST['ProsentaseKomersil'];
  $pfis     =$_REQUEST['ProsentaseFiskal'];
  $kds      =$_REQUEST['Kondisi'];
  $usr      =$_REQUEST['Pemakai'];
  $po       =$_REQUEST['PurchaseOrder'];
  $vdr      =$_REQUEST['VendorID'];

  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
  $s = "update asset set
  AssetID='$AssetID', Nama='$nama',  TglBeli='$tbl', TglSusut='$tsst', Jumlah='$jml', Satuan='$stn',  HargaBeli='$hrg', LokasiID='$lks', KelompokID='$klp', ManfaatKomersil='$mkom', ManfaatFiskal='$mfis', ProsentaseKomersil='$pkom', ProsentaseFiskal='$pfis', Kondisi='$kds', Pemakai='$usr', PurchaseOrder='$po', VendorID='$vdr', LoginEdit='$_SESSION[_Nama]', TglEdit='$Actiondate', NA='$NA' WHERE AssetID='$AssetID'";
    $r = _query($s);
    $DefaultGOS();
  }
  else {
    $ada = GetFields('asset', "KodeID='$KodeID' and AssetID", $AstID, '*');
    if (empty($ada)) {
      $s = "INSERT INTO asset (AssetID, Nama, TglBeli, TglSusut, Jumlah, Satuan, HargaBeli, LokasiID, KelompokID, ManfaatKomersil, ManfaatFiskal, ProsentaseKomersil, ProsentaseFiskal, Kondisi, Pemakai, PurchaseOrder, KodeID, LoginAdd, TglAdd, NA)
      VALUES('$AssetID', '$nama', '$tbl', '$tsst', '$jml', '$stn', '$hrg', '$lks', '$klp', '$mkom', '$mfis', '$pkom', '$pfis', '$kds', '$usr', '$po', '$_SESSION[KodeID]', '$_SESSION[_Nama]', '$Actiondate', '$NA')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=asset'; </script>";
    }
    else {
      echo ErrorMsg("Gagal Simpan",
      "Data pejabat <b>$JabatanID</b> sudah ada.<br />
      Anda tidak dapat memasukkan jabatan ini lebih dari 1 kali.");
      $DefaultGOS();
    }
  }
}

// *** Parameters ***
$asturt = GetSetVar('asturt', 'AssetID');
$klp = GetSetVar('KelompokID');
$astcr = GetSetVar('astcr');
$astkeycr = GetSetVar('astkeycr');
$astpage = GetSetVar('astpage');
$klp = GetSetVar('klp');
if ($astkeycr == 'Reset') {
  $astcr = '';
  $_SESSION['astcr'] = '';
  $astkeycr = '';
  $_SESSION['astkeycr'] = '';
}

$DefaultGOS = "DaftarAsset";
$gos = (empty($_REQUEST['gos']))? $DefaultGOS : $_REQUEST['gos'];
//$gos = (empty($_REQUEST['gos']))? 'cariasset' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Asset ");
$gos();


?>
