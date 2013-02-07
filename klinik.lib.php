<?php
// Author: Emanuel Setio Dewo
// 12 June 2006

function TampilkanCariMhsw1($mnux='klinik.biyar', $gos='', $button = 1) {
  if ($button == 1) {
    $btn = "<input type=submit name='crkey' value='Nama'>";
  }
  else $btn = "";
  echo "<p><table class=box>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux'>
  <input type=hidden name='gos' value='$gos'>
  <tr>
  <td class=wrn><b>$_SESSION[KodeID]</b></td>
  <td class=ul>Cari Mahasiswa</td>
  <td class=ul><input type=text name='crval' value='$_SESSION[crval]' size=30 maxlength=50>
  <input type=submit name='crkey' value='NPM'>
  $btn
  </td></tr>
  </form></table></p>";
}
function TampilkanHeaderMhswKlinik($mhsw) {
  $dep = GetaField('depositmhsw', "Tutup='N' and MhswID", $mhsw['MhswID'], "sum(Jumlah-Dipakai)")+0;
  $_dep = number_format($dep);
  echo "<p><table class=box>
    <tr><td class=inp>NPM</td>
	  <td class=ul>$mhsw[MhswID]</td>
	  <td class=inp>Nama</td>
	  <td class=ul>$mhsw[Nama]</td></tr>
	<tr><td class=inp>Angkatan</td>
	  <td class=ul>$mhsw[TahunID]&nbsp;</td>
	  <td class=inp>Telepon, HP</td>
	  <td class=ul>$mhsw[Telepon], $mhsw[Handphone]</td></tr>
	<tr><td class=inp>Lulus S.Ked</td>
	  <td class=ul>$mhsw[LulusSekolah] $mhsw[LulusAsalPT]&nbsp;</td>
	  <td class=inp>IPK S.Ked</td>
	  <td class=ul>$mhsw[IPKAsalPT]&nbsp;</td>
	  </tr>
	<tr><td class=inp>Deposit</td>
	  <td class=ul>$_dep</td>
	  </tr>
  </table></p>";
}
function TampilkanDaftarMhsw($lnk='') {
  if (!empty($_SESSION['crkey']) && !empty($_SESSION['crval'])) {
    $arrkey = array('NPM'=>'MhswID', 'Nama'=>'Nama');
	  $whr = $arrkey[$_SESSION['crkey']] . " like '%$_SESSION[crval]%'";
	  $ord = $arrkey[$_SESSION['crkey']];
	// Tampilkan
	include_once "class/dwolister.class.php";
	$lst = new dwolister;
    $lst->maxrow = 20;
    $lst->page = $_SESSION['klinpage']+0;
    $lst->pageactive = "=PAGE=";
    $lst->pages = "<a href='?mnux=dosen&gos=&klinpage==PAGE='>=PAGE=</a>";
    $lst->tables = "mhsw m
      where m.$whr and ProdiID='11'
      order by m.$ord";
    $lst->fields = "m.MhswID, m.Nama, m.TahunID, Telepon, Handphone, Alamat, Kota, NA ";
    $lst->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
	<tr><th class=ttl>#</th>
	  <th class=ttl>NPM</th>
	  <th class=ttl>Nama</th>
	  <th class=ttl>Angkatan</th>
	  <th class=ttl>Telepon</th>
	  <th class=ttl>Handphone</th>
	  <th class=ttl>Alamat</th>
	  <th class=ttl>Kota</th>
    </tr>";
    $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
	  <td class=cna=NA= nowrap><a href='$lnk'><img src='img/edit.png'>
	    =MhswID=</a></td>
	  <td class=cna=NA=>=Nama=</td>
	  <td class=cna=NA=>=TahunID= &nbsp;</td>
	  <td class=cna=NA=>=Telepon= &nbsp;</td>
	  <td class=cna=NA=>=Handphone= &nbsp;</td>
	  <td class=cna=NA=>=Alamat= &nbsp;</td>
	  <td class=cna=NA=>=Kota= &nbsp;</td>
	  </tr>";
    $lst->footerfmt = "</table></p>";
    echo $lst->TampilkanData();
    $halaman = $lst->TampilkanHalaman();
    $total = $lst->MaxRowCount;
    $total = number_format($total);
    echo "<p>Halaman : " . $halaman . "<br />" .
      "Total: ". $total . "</p>";
  }
}

?>
