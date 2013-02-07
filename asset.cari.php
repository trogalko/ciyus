<?
function tampilkancariasset($mnux='asset', $add=1){
  global $arrID;
	$optkel = GetOption2("kelompokasset", "concat(KelompokID, ' - ', Nama)", "KelompokID", $_SESSION['klp'], '', 'KelompokID');
    $ck_nama = ($_SESSION['asturt'] == 'Nama')? 'checked' : '';
    $ck_id = ($_SESSION['asturt'] == 'AssetID')? 'checked' : '';
    $stradd = ($add == 0)? '' : "<tr><td class=ul>Pilihan:</td>
    <td class=ul><a href='?mnux=asset&gos=AssetEdt&md=1'>Tambah Asset</td></tr>";


  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='astpage' value='1'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp>Cari Asset:</td>
  <td class=ul><input type=text name='astcr' value='$_SESSION[astcr]' size=18 maxlengh=10>
    <input type=submit name='astkeycr' value='Nama'>
    <input type=submit name='astkeycr' value='AssetID'>
    <input type=submit name='astkeycr' value='Reset'></td></tr>
  <tr><td class=inp>Urut berdasarkan:</td><td class=ul>
    <input type=radio name='asturt' value='AssetID' $ck_id>ID
    <input type=radio name='asturt' value='Nama' $ck_nama> Nama
    <input type=submit name='Urutkan' value='Urutkan'></td></tr>
  <tr><td class=inp> Kelompok :</td><td class=ul><select name='klp' OnChange='this.form.submit()'>$optkel</select></tr>
  $stradd
  </form></table></p>";

}

// =======================
function DaftarAst($mnux='', $lnk='', $fields='') {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
//  $lnk = "gos=AssetEdt&md=0&dsnid==AssetID="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=</td>";
  }
  $whr = array();
  if (!empty($_SESSION['astkeycr']) && !empty($_SESSION['astcr'])) {
    if ($_SESSION['astkeycr'] == 'AssetID') {
			$whr[]  = "$_SESSION[astkeycr] like '$_SESSION[astcr]%'";
		} else $whr[] = "$_SESSION[astkeycr] like '%$_SESSION[astcr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['klp'])) ? '' : "and KelompokID = '$_SESSION[klp]'";

  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['astpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=&astpage==PAGE='>=PAGE=</a>";
  $lst->tables = "asset left outer join lokasiasset ls
    on asset.LokasiID = ls.LokasiID
    where asset.KodeID='$_SESSION[KodeID]' $where $hom
    order by $_SESSION[asturt]";
  $lst->fields = "asset.*, format(asset.HargaBeli, 0) as HrgBeli, ls.Nama as Lokasi ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 border=0 cellpadding=4>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>ID</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Satuan</th>
    <th class=ttl>Tanggal Beli</th>
    <th class=ttl>Harga Beli</th>
    <th class=ttl>Lokasi</th>
    <th class=ttl>Pemakai</th>
	  <th class=ttl>NA</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
    <td class=cna=NA=><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0>&nbsp;=AssetID=</a></td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA= align=center>=Jumlah=</td>
    <td class=cna=NA= align=center>=Satuan=</td>
    <td class=cna=NA=>=TglBeli=</th>
    <td class=cna=NA= align=right>=HrgBeli=</td>
    <td class=cna=NA=>=Lokasi=</td>
    <td class=cna=NA=>=Pemakai=</td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

?>
