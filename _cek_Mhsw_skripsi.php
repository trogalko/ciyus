<?php

function TampilkanPesan() {
  echo "<p>Script ini akan melakukan pengecekan data mahasiswa pada tabel <b>KHS</b>:
  Apakah data mahasiswa tersebut sudah mengambil skripsi atau belum.
  </p>
  
  <p>Tekan tombol berikut ini untuk memproses data: 
  <input type=button name='Proses' value='Proses Data Skripsi' onClick=\"location='_Cek_Mhsw_skripsi.php?gos=CekSkripsi'\"></p>";
}

function CekSkripsi(){
$s = "select k.* , m.Nama from khs k left outer join mhsw m  on k.mhswid = m.mhswid 
where JumlahMK = 1 and k.tahunid = '20061' order by k.mhswid";
$r = _query($s);
echo "<p><table class=box><tr><th class=inp1>NPM</th><th class=inp1>Nama</th><th class=inp1>Kode Mata Kuliah</th><th class=inp1>Nama</th><th class=inp1>Biaya</th></tr>";
while ($w = _fetch_array($r)){
  if ($w['JumlahMK'] <= 1) {
    // Cek apakah matakuliah skripsi/tesis?
    $krs = GetFields('krs', "KHSID", $w['KHSID'], "*");
    $ta = GetaField("mk m left outer join jenispilihan jp on m.JenisPilihanID=jp.JenisPilihanID", 
      "MKID", $krs['MKID'], "jp.TA");
    $mata = GetFields("mk", "MKID", $krs['MKID'],"MKKode, Nama");
    //$bpppokok = GetaField("BipotMhsw left outer join bipotnama on bipotmhsw.bipotnamaid = bipotnama.bipotnamaid","bipotnama.bipotnamaid = 8 and tahunid = '20061' and mhswid",$w['MhswID'],'bipotnama.Nama');
    //if ($ta = 'Y') 
    echo "<tr><td class=ul>$w[MhswID]</td><td class=ul>$w[Nama]</td><td class=ul>$mata[MKKode]</td><td class=ul>$mata[Nama]</td><td class=ul>".number_format($w['Biaya'])."</td></tr>";
    //else ResetBPS($mhsw, $khs, $bipot, $ada, $pmbmhswid);
  }
}
echo "</table></p>".$ta;
}

$gos = (empty($_REQUEST['gos']))? "TampilkanPesan" : $_REQUEST['gos'];

include_once "sisfokampus.php";
HeaderSisfoKampus("Cek Data Skripsi Mahasiswa");
TampilkanJudul("Cek Data Skripsi Mahasiswa");
$gos();

?>
