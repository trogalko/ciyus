<?php
function Importdatakeuanganklinik(){
  $s = "select * from _byrklinik order by MhswID";
  $r = _query($s);
  
  while ($d = _fetch_array($r)){
    $mkid = GetaField('mk', "ProdiID='11' and MKKode", $d['MKKode'], 'MKID');
    $in = "insert into krs (MhswID, TahunID, MKID, MKKode, Bayar, Harga)
           values ('$d[MhswID]', '$d[TahunID]', '$mkid', '$d[MKKode]', '$d[Bayar]', '$d[Harga]')";
           
    $rin = _query($in);
    
    $refkrs = GetaField('krs', "MhswID = '$d[MhswID]' and TahunID='$d[TahunID]' and MKKode", $d['MKKode'], 'KRSID');
    
    $inbyr = "insert into `BayarMhsw` (`MhswID`, `BayarMhswRef`, `BayarMhswID`, `Jumlah`, `Tanggal`, `Keterangan`, `TahunID`, `RekeningID`, `TrxID`, `Proses`) 
              values('$d[MhswID]','$refkrs','$d[BayarMhswID]', '$d[Bayar]', '$d[Tanggal]','$d[Keterangan]','$d[TahunID]','$d[RekeningID]','1','1')";
  
    $rinbyr = _query($inbyr);
  }
}

include "sisfokampus.php";
HeaderSisfoKampus("Cek Data Mhsw Baru");
TampilkanJudul("Cek Data Mahasiswa Baru");

Importdatakeuanganklinik();

?>
