<?php
// Author: Emanuel Setio Dewo
// 01 Maret 2006

function BayarSav1() {
  $pmbid = $_REQUEST['pmbid'];
  $CicilanID = $_REQUEST['CicilanID'];
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $BayarMhswID = $_REQUEST['BayarMhswID'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $RekeningID = $_REQUEST['RekeningID'];
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
  }
  // Insert
  else {
    // false 1: tambahkan transaksi
    $BayarMhswID = GetNextBPM();
    $s = "insert into bayarmhsw
      (BayarMhswID, TahunID, RekeningID, PMBID, PMBMhswID, BuktiSetoran,
      TrxID, Tanggal, Jumlah, CicilanID, Keterangan,
      LoginBuat, TanggalBuat)
      values('$BayarMhswID', '0', '$RekeningID', '$pmbid', 0, '$BuktiSetoran',
      1, '$Tanggal', $Jumlah, $CicilanID, '$Keterangan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    // Jika cicilan, maka data cicilan di set
    if ($CicilanID > 0) {
      $sc = "update cicilanmhsw set SudahDibayar='Y'
        where CicilanID='$CicilanID' ";
      $rc = _query($sc);
    }
    // Distribusikan pembayaran
    $sbia = "select bm.*
      from bipotmhsw bm
        left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
        left outer join bipotnama bn on b2.BiPOTNamaID=bn.BIPOTNamaID
      where bm.TahunID='0'
        and bm.PMBID='$pmbid'
        and b2.TrxID=1
        and bn.RekeningID='$RekeningID'
        and (bm.Jumlah*bm.Besar > bm.Dibayar)
      order by b2.Prioritas";
    $rbia = _query($sbia);
    $sisa = $Jumlah;
    while ($wbia = _fetch_array($rbia)) {
      //echo $sisa . '<br />';
      if ($sisa > 0) {
        $harus = $wbia['Jumlah'] * $wbia['Besar'] - $wbia['Dibayar'];
        $bayar = ($harus > $sisa)? $sisa : $harus;
        $sisa = $sisa - $bayar;
        // Tambahkan data pembayaran
        $sbyr = "insert into bayarmhsw2
          (BayarMhswID, BIPOTMhswID, Jumlah,
          LoginBuat, TanggalBuat)
          values
          ('$BayarMhswID', '$wbia[BIPOTMhswID]', $bayar,
          '$_SESSION[_Login]', now())";
        $rbyr = _query($sbyr);

        // Update data BIPOT
        $sbpt = "update bipotmhsw
          set Dibayar=Dibayar+$bayar where BIPOTMhswID='$wbia[BIPOTMhswID]' ";
        $rbpt = _query($sbpt);
      } // End IF
    } // End While
    // Update Total Bayar
    $TotalBayar = GetaField('bayarmhsw', 
      "TahunID='0' and PMBMhswID=0 and PMBID", $pmbid, "sum(Jumlah)")+0;
    $sk = "update pmb set TotalSetoranMhsw=$TotalBayar where PMBID='$pmbid'";
    $rk = _query($sk);
  } // End INSERT
}

// *** Pembayaran ***
function BayarEdt1() {
  $pmbid = $_REQUEST['pmbid'];
  $pmb = GetFields('pmb p left outer join
    program prg on p.ProgramID=prg.ProgramID
    left outer join prodi prd on p.ProdiID=prd.ProdiID',
    'p.PMBID', $pmbid,
    "p.*, prg.Nama as PRG, prd.Nama as PRD");
  echo HeaderCAMA($pmb);
  $md = $_REQUEST['md']+0;
  //$kurang = $pmb['TotalBiayaMhsw'] - $pmb['TotalBayarMhsw'];
  $CicilanID = $_REQUEST['CicilanID']+0;
  if ($CicilanID > 0) $Cicilan = GetFields('cicilanmhsw', 'CicilanID', $CicilanID,
    "*, format(Jumlah, 0) as JML");
  $kurang = $Cicilan['Jumlah']+0;
  $_kurang = number_format($kurang);
  if ($md == 0) {
    $w = GetFields('bayarmhsw', "BayarMhswID", $_REQUEST['BayarMhswID'], '*');
  }
  else {
    $w = array();
    $w['BuktiSetoran'] = '';
    $w['BayarMhswID'] = '';
    $w['TahunID'] = '0';
    $w['RekeningID'] = '';
    $w['MhswID'] = $pmb['PMBID'];
    $w['Tanggal'] = date('Y-m-d');
    $w['Jumlah'] = ($CicilanID > 0)? $Cicilan['Jumlah'] : 0;
    $w['CicilanID'] = $CicilanID;
    $w['Keterangan'] = '';
  }
  $Judul = ($CicilanID > 0)? "Pembayaran Cicilan" : "Pembayaran";
  $KeteranganCicilan = ($CicilanID > 0)? "<tr><td class=ul colspan=2>Anda akan melakukan pembayaran cicilan: <br />
    Judul: <b>$Cicilan[Judul]</b><br />
    Dengan jumlah: Rp. <b>$Cicilan[JML]</b>.</td></tr>" : '';
  $Tgl = GetDateOption($w['Tanggal'], 'Tanggal');
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)", 'RekeningID',
    $w['RekeningID'], "KodeID='$_SESSION[KodeID]'", 'RekeningID');
  // Tampilkan Form Pembayaran
  ScriptCekKelebihan($kurang);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CekKelebihan(this);\">
  <input type=hidden name='mnux' value='mhswbaru'>
  <input type=hidden name='gos' value='ImprtPMB'>
  <input type=hidden name='slnt' value='mhswbaru.sav'>
  <input type=hidden name='slntx' value='BayarSav'>
  <input type=hidden name='md' value='$md'>

  <input type=hidden name='pmbid' value='$pmb[PMBID]'>
  <input type=hidden name='trm' value='$pmb[PMBID]'>
  <input type=hidden name='CicilanID' value='$w[CicilanID]'>
  <input type=hidden name='BayarMhswID' value='$w[BayarMhswID]'>

  <tr><th class=ttl colspan=2>$Judul</th></tr>
  $KeteranganCicilan
  $strkurang
  <tr><td class=inp1>Dibayarkan ke rekening</td><td class=ul><select name='RekeningID'>$optrek</select></td></tr>
  <tr><td class=inp1>Bukti Setoran</td><td class=ul><input type=text name='BuktiSetoran' value='$w[BuktiSetoran]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Tanggal Bayar</td><td class=ul>$Tgl</td></tr>
  <tr><td class=inp1>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20 maxlength=20></td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><input type=text name='Keterangan' value='$w[Keterangan]' size=50 maxlength=100></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswbaru&gos=ImprtPMB&trm=$pmb[PMBID]&pmbid=$pmb[PMBID]'\"></td></tr>
  </form></table></p>";
}
function ScriptCekKelebihan1($max=0) {
  $_max = number_format($max);
  echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function CekKelebihan(frm) {
    if (frm.Jumlah.value > $max) alert("Pembayaran Kelebihan. Anda tidak dapat membayar lebih dari $_max");
    return (frm.Jumlah.value <= $max);
  }
  //-->
  </SCRIPT>
END;
}

?>
