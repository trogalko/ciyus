<?php
// Author: Emanuel Setio Dewo
// 24 Jan 2007

TampilkanJudul("Cek Tagihan KRS Mahasiswa");
TampilkanTahunProdiProgram('_cek_tagihan_doble', '');

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? "TampilkanTagihanKRSMhsw" : $_REQUEST['gos'];
$gos();
// *** Main ***

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
  $s = "select MhswID, bn.Nama as NamaTagihan, bm.BipotMhswID as BIPOTID, bm.Dibayar as Bayar
    from bipotmhsw bm
      left outer join bipotnama bn on bn.BipotNamaID = bm.BipotNamaID
    where bm.TahunID='$_SESSION[tahun]'
      and left(MhswID, 2)='$_SESSION[prodi]'
      and bn.BipotNamaID = '35'
    order by MhswID";
  $r = _query($s); $n = 0;
  
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>JUM</th>
    <th class=ttl>Nama Tagihan</th>
    <th class=ttl>Bayar</th>
    <th class=ttl>Delete</th>
    </tr>";
    
    $c = "class=ul";
  while ($w = _fetch_array($r)) {
    $n++;
    $jum = GetaField('bipotmhsw', "TahunID = '$_SESSION[tahun]' and BipotNamaID = '35' and MhswID", $w['MhswID'], 'count(MhswID)');
    if ($jum == 1) $c = "class=wrn";
    else $c = "class=ul";
    echo "<tr>
    <td class=inp>$n</td>
    <td $c>$w[MhswID]</td>
    <td $c>$jum</td>
    <td $c>$w[NamaTagihan]</td>
    <td $c align=right>$w[Bayar]</td>
    <TD $c align=center><a href=?mnux=_cek_tagihan_doble&gos=deletedata&BipotMhswID=$w[BIPOTID]>Delete</a></td>
    </tr>";
  }
  echo "</table></p>";
}

function deletedata(){
  $id = $_REQUEST['BipotMhswID'];
  $s = "delete from bipotmhsw where BipotMhswID = '$id'";
  $r = _query($s);
  
  TampilkanTagihanKRSMhsw(5);
}
?>
