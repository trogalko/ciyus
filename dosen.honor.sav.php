<?php
// Author: Emanuel Setio Dewo
// 21 April 2006

function TunjanganSav() {
  $Hondos = $_REQUEST['Hondos'];
  $TunjanganJabatan1 = $_REQUEST['TunjanganJabatan1']+0;
  $TunjanganJabatan2 = $_REQUEST['TunjanganJabatan2']+0;
  $TunjanganSKS = $_REQUEST['TunjanganSKS']+0;
  $TunjanganTransport = $_REQUEST['TunjanganTransport']+0;
  $Pajak = $_REQUEST['Pajak'];
  $prodi = $_REQUEST['prodi'];
  // Simpan
  $s = "update honordosen set TunjanganJabatan1=$TunjanganJabatan1, TunjanganJabatan2=$TunjanganJabatan2,
    TunjanganSKS=$TunjanganSKS, TunjanganTransport=$TunjanganTransport, Pajak=$Pajak
    where HonorDosenID=$Hondos";
  $r = _query($s);
}
function TambahanSav() {
  $Hondos = $_REQUEST['Hondos'];
  $HonorTambahanID = $_REQUEST['HonorTambahanID'];
  $Besar = $_REQUEST['Besar']+0;
  if ($Besar!=0 && !empty($HonorTambahanID)) {
    $Nama = GetaField('honortambahan', "HonorTambahanID", $HonorTambahanID, 'Nama');
    $DosenID = GetaField("honordosen", "HonorDosenID", $Hondos, "DosenID");
    // Tambahkan tambahan
    $s = "insert into honordosentambahan (HonorDosenID, HonorTambahanID,
      DosenID, Nama, Besar,
      LoginBuat, TanggalBuat)
      values ('$Hondos', '$HonorTambahanID',
      '$DosenID', '$Nama', $Besar,
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    echo $s;
      // Update tambahan
      $TotTambahan = GetaField("honordosentambahan", "Besar>0 and HonorDosenID", $Hondos, "sum(Besar)");
      $TotPotongan = GetaField("honordosentambahan", "Besar<0 and HonorDosenID", $Hondos, "sum(Besar)");
      $s1 = "update honordosen set Tambahan='$TotTambahan', Potongan='$TotPotongan' 
        where HonorDosenID=$Hondos";
      $r1 = _query($s1);
  }
}
?>
