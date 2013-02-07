<?php
// Author: Emanuel Setio Dewo
// 30 Januari 2007

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');

// *** Main ***
TampilkanJudul("Cek Perubahan Data Pembayaran Mhsw");
TampilkanTahunProdiProgram('_cek_perubahan_data_pembayaran', '');

// Tampilkan pesan
echo "<p>Berikut adalah daftar perubahan yg terjadi di pembayaran mhsw. <input type=button name='Cetak' value='Cetak' onClick='window.print()'></p>";

if (!empty($tahun)) TampilkanPerubahanData();

// *** Functions ***
function TampilkanPerubahanData() {
  $_whrprd = (empty($_SESSION['prodi']))? '' : "and m.ProdiID = '$_SESSION[prodi]'";
  $s = "select bm.BayarMhswID, bm.MhswID,
      bm.BayarMhswRef, bm.TahunID, 
      bm.TrxID, bm.PMBMhswID, bm.Bank, bm.BuktiSetoran,
	  bm.Tanggal, bm.Jumlah as Jumlah1, bm.JumlahLain as JumlahLain1,
	  bm.Proses, bm.Keterangan, 
	  bm.LoginBuat as LoginBuat1, bm.TanggalBuat as TanggalBuat1,
	  bm.LoginEdit as LoginEdit1, bm.TanggalEdit as TanggalEdit1,
	  
      bmc.BayarMhswID, 
	  bmc.Jumlah as Jumlah2, bmc.JumlahLain as JumlahLain2,
	  bmc.LoginBuat as LoginBuat2, bmc.TanggalBuat as TanggalBuat2,
	  bmc.LoginEdit as LoginEdit2, bmc.TanggalEdit as TanggalEdit2,
	  m.Nama as NamaMhsw
    from bayarmhsw bm
	  inner join bayarmhswcek bmc on bm.BayarMhswID=bmc.BayarMhswID
	  inner join mhsw m on bmc.MhswID=m.MhswID
	where bm.TahunID='$_SESSION[tahun]'
	  $_whrprd
	  and (bm.Jumlah <> bmc.Jumlah or bm.JumlahLain <> bmc.JumlahLain)
	order by bm.MhswID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1>";
  echo "<tr><th class=ttl rowspan=2>#</th>
	<th class=ttl rowspan=2>NPM/Nama Mhsw</th>
	<th class=ttl rowspan=2>Keterangan</th>
	<th class=ttl colspan=2>Data Sekarang</th>
	<th class=ttl rowspan=2>&raquo;</th>
	<th class=ttl colspan=2>Data Asli</th>
	<th class=ttl colspan=2>Pembuat Transaksi</th>
	<th class=ttl colspan=2>Pembuat Perubahan</th>
	</tr>";
  echo "<tr><th class=ttl>Jumlah</th>
    <th class=ttl>Lain2</th>
	<th class=ttl>Jumlah</th>
	<th class=ttl>Lain2</th>
	<th class=ttl>Oleh</th>
	<th class=ttl>Tgl</th>
	<th class=ttl>Oleh</th>
	<th class=ttl>Tgl</th>
	</tr>";
  while ($w = _fetch_array($r)) {
    $n++;
	$c = "class=ul";
	$Jumlah1 = number_format($w['Jumlah1']);
	$JumlahLain1 = number_format($w['JumlahLain1']);
	$Jumlah2 = number_format($w['Jumlah2']);
	$JumlahLain2 = number_format($w['JumlahLain2']);
    echo "<tr><td class=inp>$n</td>
	<td $c>$w[MhswID]<br>
	       $w[NamaMhsw]</td>
	<td $c>$w[Keterangan]&nbsp;</td>
	<td $c align=right>$Jumlah1</td>
	<td $c align=right>$JumlahLain1</td>
	<td class=ul>&raquo;</td>
	<td $c align=right>$Jumlah2</td>
	<td $c align=right>$JumlahLain2</td>
	<td $c>$w[LoginBuat1]</td>
	<td $c>$w[TanggalBuat1]</td>
	<td $c>$w[LoginEdit1]</td>
	<td $c>$w[TanggalEdit1]</td>
	</tr>";
  }
  echo "</table></p>";
}
?>
