<?php
// Author: Emanuel Setio Dewo
// 24 Jan 2007

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');

// *** Main ***
TampilkanJudul("Cek Tagihan KRS Mahasiswa");
TampilkanTahunProdiProgram('_cek_tagihan_krs', '');
// Tampilkan pesan
echo "<p>Tabel berikut ini membandingkan SKS yang diambil di KRS (tabel: krstemp) dengan
  jumlah SKS yang ditagihkan ke mhsw (tabel: bipotmhsw).
  Jika jumlah SKS-nya tidak sama, maka baris akan ditampilkan dengan warna merah. 
  Jika jumlah SKS-nya sama, maka baris akan ditampilkan dengan warna putih. <input type=button name='Cetak' value='Cetak' onClick='window.print()'></p>";
if (!empty($tahun) && !empty($prodi)) {
  TampilkanTagihanKRSMhsw(5);
}

// *** Functions ***
function TampilkanTagihanKRSMhsw($BNID) {
  $s = "select k.KHSID, k.MhswID, m.Nama, sum(k.SKS) as JmlSKS
    from krstemp k
      left outer join mhsw m on k.MhswID=m.MhswID
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.TahunID='$_SESSION[tahun]'
      and m.ProdiID='$_SESSION[prodi]'
      and k.NA='N'
      and j.JenisJadwalID='K'
      and j.JadwalSer=0
    group by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>SKS<br />Temp</th>
    <th class=ttl>SKS<br />Tagih</th>
    <th class=ttl>Rupiah<br />Tagih</th>
    <th class=ttl>Total</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $jml = GetFields('bipotmhsw', "MhswID='$w[MhswID]' and TahunID='$_SESSION[tahun]' and BIPOTNamaID", 
      $BNID, "Jumlah, Besar");
    $rupiah = number_format($jml['Besar']);
    $total = number_format($jml['Besar'] * $jml['Jumlah']);
    $c = ($jml['Jumlah'] != $w['JmlSKS'])? 'class=wrn' : 'class=ul';
		$link = ($jml['Jumlah'] != $w['JmlSKS']) ? "<a href='krs.cetak.php?khsid=$w[KHSID]&prn=1'>$w[MhswID]</a>" : $w['MhswID'];
    echo "<tr>
    <td class=inp>$n</td>
    <td $c>$link</td>
    <td $c>$w[Nama]</td>
    <td $c align=right>$w[JmlSKS]</td>
    <td $c align=right>$jml[Jumlah]</td>
    <td $c align=right>$rupiah</td>
    <td $c align=right>$total</td>
    </tr>";
  }
  echo "</table></p>";
}
?>
