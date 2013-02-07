<?php
session_start();
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "krs.lib.php";

if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();

function formatnim() {
  $prodi = $_SESSION['prodi'];
  $_SESSION['FRMT-Pos-'. $prodi]++;
  $pos = $_SESSION['FRMT-Pos-'. $prodi];
  $max = $_SESSION['FRMT-Max-'. $prodi];
  $MhswID = $_SESSION['FRMT-MhswID-'. $prodi. $pos];
  $persen = ($max == 0)? 0 : number_format($pos/$max*100);
  $NIMBaru = '';
  if (!empty($MhswID)) {
    
    $NIMBaru = FormatNIMNya($MhswID);
    UpdateNIM($MhswID, $NIMBaru);
    
    echo "<p>Processing: <b>$MhswID</b></p>
    <p>Position: <b>$pos/$max</b></p>
    <p>NIM Lama: <b><font size=+3>$MhswID</font></b>, NIM Baru: <b><font size=+3>$NIMBaru</font></b></p>
    <p><font size=+4>$persen %</font></p>";
  }

  if ($pos < $max) {
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  }
  else echo "<hr><p>Proses Selesai</p>";
}

function FormatNIMNya($MhswID){
  $MhswID = trim($MhswID);
  $Tahun = substr($MhswID, 0, 4);
  
  $Prodi = substr($MhswID, 4, 2);
  
  $NoUrut = substr($MhswID, 6, 3);
  
  $NIMBaru = $Prodi.$Tahun.$NoUrut;
  
  return "$NIMBaru";
}

function UpdateNIM($NIMLama, $NIMBaru){
  //Update KHS
  $sKHS = "update khs set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rKHS = _query($sKHS);
  
  //Update KRS
  $sKRS = "update krs set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rKRS = _query($sKRS);
  
  //Update bipotmhsw
  $sBM = "update bipotmhsw set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rBM = _query($sBM);
  
  //Update bayarmhsw
  $sBYR = "update bayarmhsw set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rBYR = _query($sBYR);
  
  //Update bayarmhswcek
  $sBYRC = "update bayarmhswcek set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rBYRC = _query($sBYRC);
  
  //Update beasiswamhsw
  $sBEA = "update beasiswamhsw set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rBEA = _query($sBEA);
  
  //Update beasiswamhswdetail
  $sBEAD = "update beasiswamhswdetail set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rBEAD = _query($sBEAD);
  
  //Update cicilanmhsw
  $sCICIL = "update cicilanmhsw set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rCICIL = _query($sCICIL);
  
  //Update KRSPRA
  $sKRSP = "update krspra set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rKRSP = _query($sKRSP);
  
  //Update krstemp
  $sKRST = "update krstemp set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rKRST = _query($sKRST);
  
  //Update mhswpindahan
  $sMPIN = "update mhswpindahan set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rMPIN = _query($sMPIN);
  
  //Update mhswpindahansetara
  $sMPINS = "update mhswpindahansetara set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rMPINS = _query($sMPINS);
  
  //Update presensimhsw
  $sPRES = "update presensimhsw set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rPRES = _query($sPRES);
  
  //Update PMB
  $sMhsw = "update pmb set NIM='$NIMBaru' where NIM='$NIMLama'";
  $rMhsw = _query($sMhsw);
  
  //Update Mhsw
  $sMhsw = "update mhsw set MhswID='$NIMBaru' where MhswID='$NIMLama'";
  $rMhsw = _query($sMhsw);
  
}

?>
