<?php
// Author: Emanuel Setio Dewo
// 05 March 2006

function PrcBIPOTSesi() {
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  $mhswid = $_REQUEST['mhswid'];
  $pmbmhswid = $_REQUEST['pmbmhswid'];
  $DariKRS = $_REQUEST['DariKRS']+0; // Apakah dari modul KRS?
  $mhsw = GetFields('mhsw', "MhswID", $mhswid, '*');
  // Apakah semester Pendek??
  $SP = (substr($khs['TahunID'], -1, 1) == 3) ? 1 : 0;
  $BPTSP = GetaField('bipot', 'SP', 'Y', 'BIPOTID');
  $mhsw['BIPOTID'] = ($SP == 1) ? $BPTSP : $mhsw['BIPOTID'];
  
  // Ambil Daftar biaya (Bipot2)
  $s = "select b2.*, bn.BIPOTNamaID, bn.Nama, saat.Nama as SAAT,
    format(b2.Jumlah, 0) as JML
    from bipot2 b2
      left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
      left outer join saat saat on b2.SaatID=saat.SaatID
    where b2.BIPOTID='$mhsw[BIPOTID]' 
      and b2.Otomatis='Y'
      and INSTR(b2.StatusAwalID, '.$mhsw[StatusAwalID].')>0
      and INSTR(b2.StatusMhswID, '.$mhsw[StatusMhswID].')>0
      and b2.NA = 'N'
    order by b2.Prioritas";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  while ($w = _fetch_array($r)) {
    // Hitung, seharusnya berapa kali ditarik?
    if ($w['KaliSesi'] == 0) PrcBIPOTSesi1($mhsw, $khs, $w, $pmbmhswid);
    else {
      $kali = GetaField('bipotmhsw', "MhswID='$mhswid' and BIPOTNamaID", $w['BIPOTNamaID'], "count(BIPOTMhswID)");
      // Jika belum melebihi:
      if ($kali < $w['KaliSesi']) PrcBIPOTSesi1($mhsw, $khs, $w, $pmbmhswid);
    } 
  }
  include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($mhswid, $khs['KHSID']);
}
function PrcBIPOTSesi1($mhsw, $khs, $w, $pmbmhswid=1) {
  //$ada = GetFields('bipotmhsw', "TahunID='$khs[TahunID]' and PMBMhswID=$pmbmhswid and MhswID='$mhsw[MhswID]' and BIPOT2ID", $w['BIPOT2ID'], '*');
  $ada = GetFields('bipotmhsw', "TahunID='$khs[TahunID]' and MhswID='$mhsw[MhswID]' and BIPOTNamaID", $w['BIPOTNamaID'], '*');
  if ($w['GunakanScript'] == 'Y') InsertBIPOTScript($mhsw, $khs, $w, $ada, $pmbmhswid);
  else {
    // Jika belum ada di biaya mhsw, maka insert
    if (empty($ada)) {
      if ($w['GunakanGradeNilai'] == 'Y') {
        if (strpos($w['GradeNilai'], ".$mhsw[GradeNilai].") === false) { }
        else InsertBIPOTMhsw($w, $khs, $pmbmhswid);
      } 
      else InsertBIPOTMhsw($w, $khs, $pmbmhswid);
    }
    // Tapi jika sudah ada, maka update
    else {
      $s = "update bipotmhsw set Besar='$w[Jumlah]'
        where BIPOTMhswID='$ada[BIPOTMhswID]'";
      $r = _query($s);
    }
  } // end else if (empty...
}
function InsertBIPOTMhsw($bipot, $khs, $pmbmhswid=1) {
  $s = "insert into bipotmhsw (MhswID, TahunID, BIPOT2ID, BIPOTNamaID,
    PMBMhswID, TrxID, Jumlah, Besar,
    Catatan,
    LoginBuat, TanggalBuat)
    values ('$khs[MhswID]', '$khs[TahunID]', '$bipot[BIPOT2ID]', '$bipot[BIPOTNamaID]',
    '$pmbmhswid', '$bipot[TrxID]', 1, '$bipot[Jumlah]',
    '$bipot[Catatan]',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
}
function InsertBIPOTScript($mhsw, $khs, $bipot, $ada, $pmbmhswid=1) {
  include_once "script/$bipot[NamaScript].php";
  $bipot['NamaScript']($mhsw, $khs, $bipot, $ada, $pmbmhswid);
}
function HitungBiaya($mhsw, $khs) {
  $biaya = GetaField("bipotmhsw", "TrxID=1 and TahunID='$khs[TahunID]' and MhswID", $mhsw['MhswID'], "sum(Jumlah*Besar)")+0;
  $bayar = GetaField("bayarmhsw", "TahunID='$khs[TahunID]' and Proses=1 and MhswID", $mhsw['MhswID'], "sum(Jumlah)")+0;
  $s = "update khs
    set Biaya='$biaya', Bayar='$bayar'
    where KHSID='$khs[KHSID]' and NA='N'";
  $r = _query($s);
}
function BipotMhswSav() {
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields('mhsw', 'MhswID', $mhswid, '*');
  $md = $_REQUEST['md'] +0;
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Besar = $_REQUEST['Besar']+0;
  $Dibayar = $_REQUEST['Dibayar']+0;
  $BIPOTNamaID = $_REQUEST['BIPOTNamaID'];
  $BIPOT2ID = $_REQUEST['BIPOT2ID'];
  $BIPOTMhswID = $_REQUEST['BIPOTMhswID'];
  $Catatan = sqling($_REQUEST['Catatan']);
  // simpan
  if ($md == 0) {
    $s = "update bipotmhsw set Jumlah='$Jumlah', Besar='$Besar', Dibayar='$Dibayar', Catatan='$Catatan'
      where BIPOTMhswID='$BIPOTMhswID' ";
    $r = _query($s);
  }
  else {
    $BIPOT2ID = $_REQUEST['BIPOT2ID'];
    $bn = GetFields('bipotnama', "BIPOTNamaID", $BIPOTNamaID, "*");
    $s = "insert into bipotmhsw (MhswID, TahunID, BIPOT2ID,
      BIPOTNamaID, TrxID, Jumlah, Besar, Dibayar, Catatan,
      LoginBuat, TanggalBuat)
      values ('$mhswid', '$khs[TahunID]', '$BIPOT2ID',
      '$BIPOTNamaID', '$bn[TrxID]', '$Jumlah', '$Besar', '$Dibayar', '$Catatan',
      '$_SESSION[_Login]', now())";
    $r = _query($s);  
  }
  //HitungBiaya($mhsw, $khs);
  include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($mhswid, $khs['KHSID']);
}
function CicilanMhswSav() {
  $khsid = $_REQUEST['khsid'];
  $mhswid = $_REQUEST['mhswid'];
  $md = $_REQUEST['md']+0;

  $CicilanID = $_REQUEST['CicilanID'];
  $Urutan = $_REQUEST['Urutan']+0;
  $Judul = sqling($_REQUEST['Judul']);
  $DariTanggal = "$_REQUEST[DariTanggal_y]-$_REQUEST[DariTanggal_m]-$_REQUEST[DariTanggal_d]";
  $SampaiTanggal = "$_REQUEST[SampaiTanggal_y]-$_REQUEST[SampaiTanggal_m]-$_REQUEST[SampaiTanggal_d]";
  $Jumlah = $_REQUEST['Jumlah']+0;
  $Keterangan = sqling($_REQUEST['Keterangan']);

  // Simpan
  if ($md == 0) {
    $s = "update cicilanmhsw set Judul='$Judul', Urutan='$Urutan',
      DariTanggal='$DariTanggal', SampaiTanggal='$SampaiTanggal',
      Jumlah=$Jumlah, Keterangan='$Keterangan'
      where CicilanID=$CicilanID";
    $r = _query($s);
  }
  else {
    $khs = GetFields('khs', 'KHSID', $khsid, '*');
    $s = "insert into cicilanmhsw (Judul, DariTanggal, SampaiTanggal,
      Urutan, MhswID, TahunID,
      Jumlah, Keterangan)
      values('$Judul', '$DariTanggal', '$SampaiTanggal',
      '$Urutan', '$mhswid', '$khs[TahunID]',
      $Jumlah, '$Keterangan')";
    $r = _query($s);
  }
}
function BayarSav() {
  $Simpan = substr($_REQUEST['Simpan'], 0, 1);
  if ($_REQUEST['Jumlah']+0 > 0) {
    if (!empty($_REQUEST['BuktiSetoran'])) {
      if ($Simpan == '1') BayarSavManual();
      else BayarSavAuto();
    }
    else echo ErrorMsg("Bukti Setoran Kosong",
      "Bukti Setoran dari Bank tidak boleh kosong.
      <hr size=1 color=silver>
      Pilihan: <a href='?mnux=bpm&gos=BPMMhsw'>Kembali</a>");
  }
  elseif ($_REQUEST['JumlahLain']+0>0) {
    BayarSavLain();
  }
  else echo ErrorMsg("Jumlah Pembayaran 0",
    "Jumlah pembayaran tidak boleh 0. Harus memiliki nilai.
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=bpm&gos=BPMMhsw'>Kembali</a>");
}
function BayarSavLain() {
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields("mhsw", "MhswID", $mhswid, '*');
  $pmbid = $_REQUEST['pmbid'];
  $pmbmhswid = $_REQUEST['pmbmhswid'];
  $khsid = $_REQUEST['khsid'];
  if ($pmbmhswid == 0) $TahunID = GetaField('pmb', 'PMBID', $pmbid, 'PMBPeriodID');
  else $TahunID = GetaField('khs', 'KHSID', $khsid, 'TahunID');
  $RekeningID = $_REQUEST['RekeningID'];
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $BayarMhswID = $_REQUEST['bpmid'];
  $JumlahLain = $_REQUEST['JumlahLain']+0;
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $s = "update # set BuktiSetoran='$BuktiSetoran', Tanggal='$Tanggal', JumlahLain='$JumlahLain',
      Keterangan='$Keterangan', Proses=1,
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BayarMhswID='$BayarMhswID' ";
  // Simpan data asli
  $str = str_replace('#', 'bayarmhsw', $s);
  $r = _query($str);
  // Simpan data cek
  $str = str_replace('#', 'bayarmhswcek', $s);
  $r = _query($str);
  // HitungBiaya($mhsw, $khs);
  include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($mhswid, $khsid);
  if ($act == 1) 
    if (!empty($_REQUEST['gosto'])) {
      echo "<script>window.location='?gos=$_REQUEST[gosto]';</script>";
    }
}
function BayarSavAuto($act=1) {
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields("mhsw", "MhswID", $mhswid, '*');
  $pmbid = $_REQUEST['pmbid'];
  $pmbmhswid = $_REQUEST['pmbmhswid'];
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  if ($pmbmhswid == 0) $TahunID = GetaField('pmb', 'PMBID', $pmbid, "PMBPeriodID");
  else $TahunID = GetaField('khs', 'KHSID', $khsid, 'TahunID');
  $CicilanID = $_REQUEST['CicilanID'];
  $RekeningID = $_REQUEST['RekeningID'];
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $BayarMhswID = $_REQUEST['bpmid'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $JumlahLain = $_REQUEST['JumlahLain']+0;
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $md = $_REQUEST['md']+0;
  // Simpan
  if ($md == 0) {
    $s = "update # set BuktiSetoran='$BuktiSetoran', Tanggal='$Tanggal', Jumlah='$Jumlah',
      JumlahLain='$JumlahLain',
      Keterangan='$Keterangan', Proses=1,
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BayarMhswID='$BayarMhswID' ";
    // update data
    $str = str_replace('#', 'bayarmhsw', $s);
    $r = _query($str);
    // update data cek
    $str = str_replace('#', 'bayarmhswcek', $s);
    $r = _query($str);
  }
  // Insert
  else {
    // fase 1: tambahkan transaksi
    $BayarMhswID = GetNextBPM();
    // bayarmhsw
    $s = "insert into # 
      (BayarMhswID, TahunID, PMBID, MhswID, RekeningID, BuktiSetoran,
      PMBMhswID, TrxID, Tanggal, Jumlah, JumlahLain, CicilanID, Keterangan,
      LoginBuat, TanggalBuat)
      values('$BayarMhswID', '$TahunID', '$pmbid', '$mhswid', '$RekeningID', '$BuktiSetoran',
      '$pmbmhswid', 1, '$Tanggal', $Jumlah, $JumlahLain, $CicilanID, '$Keterangan',
      '$_SESSION[_Login]', now())";
    // Simpan ke bayarmhsw
    $str = str_replace('#', 'bayarmhsw', $s);
    $r = _query($str);
    // simpan ke bayarmhswcek
    $str = str_replace('#', 'bayarmhswcek', $s);
    $r = _query($str);
  }
  // Jika cicilan, maka data cicilan di set
  if ($CicilanID > 0) {
    $sc = "update cicilanmhsw set SudahDibayar='Y'
      where CicilanID='$CicilanID' ";
    $rc = _query($sc);
  }
  // Berlaku untuk insert & konfirmasi
  // Distribusikan pembayaran
    $whr = ($pmbmhswid == 0)? "and bm.PMBID='$pmbid' " : "and bm.MhswID='$mhswid' ";
    $sbia = "select bm.*, bn.RekeningID
      from bipotmhsw bm
        left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
        left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
      where bm.TahunID='$TahunID'
        $whr
        and bn.TrxID=1
        and bn.RekeningID='$RekeningID'
        and (bm.Jumlah*bm.Besar > bm.Dibayar)
        and bm.TahunID='$TahunID'
      order by b2.Prioritas";
    $rbia = _query($sbia);
    //echo "<pre>$sbia</pre>";
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
        //echo "<pre>$sbyr</pre>";
      } // End IF
      // Update data BIPOT
      $fld = ($pmbmhswid == 0)? "PMBID" : "MhswID";
      $nli = ($pmbmhswid == 0)? $pmbid : $mhswid;
      $totaldibayar = GetaField("bayarmhsw2 bm2
        left outer join bayarmhsw bm on bm2.BayarMhswID=bm.BayarMhswID", 
        "bm.TahunID='$TahunID' and bm2.BIPOTMhswID=$wbia[BIPOTMhswID] and bm.$fld", $nli, "sum(bm2.Jumlah)")+0;
      $sbpt = "update bipotmhsw
        set Dibayar=$totaldibayar, Draft='N' where BIPOTMhswID='$wbia[BIPOTMhswID]' ";
      $rbpt = _query($sbpt);
      //echo "<pre>$sbpt</pre>";
    } // End While
    
    if ($pmbmhswid == 1) {
    // Update Total Bayar
      $TotalBayar = GetaField('bayarmhsw', "TahunID='$TahunID' and MhswID", $mhswid, "sum(Jumlah)")+0;
      $sk = "update khs set Bayar=$TotalBayar where KHSID=$khsid";
      $rk = _query($sk);
    }
    else {
      $TotalBayar = GetaField('bayarmhsw', "PMBMhswID=0 and PMBID", $pmbid, "sum(Jumlah)")+0;
      $sk = "update pmb set TotalSetoranMhsw=$TotalBayar where PMBID='$pmbid' ";
      $rk = _query($sk);
    }
  //HitungBiaya($mhsw, $khs);
  include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($mhswid, $khsid);
  if ($act == 1) 
    if (!empty($_REQUEST['gosto'])) {
      echo "<script>window.location='?gos=$_REQUEST[gosto]';</script>";
    }
}
function BayarSavManual() {
  $mhswid = $_REQUEST['mhswid'];
  $mhsw = GetFields('mhsw', 'MhswID', $mhswid, '*');
  $pmbid = $_REQUEST['pmbid'];
  $pmbmhswid = $_REQUEST['pmbmhswid'];
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  if ($pmbmhswid == 0) $TahunID = GetaField('pmb', 'PMBID', $pmbid, "PMBPeriodID");
  else $TahunID = GetaField('khs', 'KHSID', $khsid, 'TahunID');
  $CicilanID = $_REQUEST['CicilanID'];
  $RekeningID = $_REQUEST['RekeningID'];
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $BayarMhswID = $_REQUEST['bpmid'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $JumlahLain = $_REQUEST['JumlahLain']+0;
  $_Jumlah = number_format($Jumlah);
  $_JumlahLain = number_format($JumlahLain);
  $Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $md = $_REQUEST['md']+0;
  $Detail = AmbilDetailPembayaran($mhswid, $pmbid, $pmbmhswid, $khsid, $RekeningID, $Jumlah);
  // Tampilkan header
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl colspan=3>Pembayaran/Proses BPM</th></tr>
  <tr><td class=inp>Nomer BPM</td>
    <td class=ul colspan=2><b>$BayarMhswID</b></td></tr>
  <tr><td class=inp>No Rekening</td>
    <td class=ul colspan=2><b>$RekeningID</b></td></tr>
  <tr><td class=inp>Tanggal Disetor ke Bank</td>
    <td class=ul colspan=2><b>$Tanggal</b></td></tr>
  <tr><td class=inp>No. Bukti Setoran Bank</td>
    <td class=ul colspan=2><b>$BuktiSetoran</b></td></tr>
  <tr><td class=inp>Jumlah</td>
    <td class=ul colspan=2>$_Jumlah</td></tr>
  <tr><td class=inp>Jumlah Lain</td>
    <td class=ul colspan=2>$_JumlahLain</td></tr>
  <tr><td class=inp>Keterangan</td>
    <td class=ul colspan=2>$Keterangan</td></tr>
  <tr><th class=ttl colspan=3>Detail Pembayaran</th></tr>
  <tr><td class=ul colspan=3>Isikan dengan nilai yang akan dibayarkan.</td></tr>
  
  <!--Data BPM-->
  <form action='?' name='bpm' method=POST onSubmit=\"return HitungTotalBIPOTMhsw(bpm);\">
  <input type=hidden name='mnux' value='$_REQUEST[mnux]'>
  <input type=hidden name='gosto' value='$_REQUEST[gosto]'>
  <input type=hidden name='gos' value='BayarSavManual1'>
  <input type=hidden name='mhswid' value='$mhswid'>
  <input type=hidden name='pmbid' value='$pmbid'>
  <input type=hidden name='trm' value='$pmbid'>
  <input type=hidden name='pmbmhswid' value='$pmbmhswid'>
  <input type=hidden name='khsid' value='$khsid'>
  <input type=hidden name='CicilanID' value='$CicilanID'>
  <input type=hidden name='RekeningID' value='$RekeningID'>
  <input type=hidden name='BuktiSetoran' value='$BuktiSetoran'>
  <input type=hidden name='bpmid' value='$BayarMhswID'>
  <input type=hidden name='BayarMhswID' value='$BayarMhswID'>
  <input type=hidden name='Jumlah' value='$Jumlah'>
  <input type=hidden name='JumlahLain' value='$JumlahLain'>
  <input type=hidden name='Tanggal' value='$Tanggal'>
  <input type=hidden name='Keterangan' value='$Keterangan'>
  <input type=hidden name='md' value='$md'>
  $Detail
  <tr><td class=inp id='TOTAL1'>Total</td><td class=ul id='TOTAL' colspan=2><input type=text READONLY name='Total' value='$Jumlah' size=20 maxlength=15></td></tr>
  <tr><td class=ul colspan=3><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_REQUEST[mnux]&gos=$_REQUEST[gosto]&trm=$pmbid&pmbid=$pmbid&crmhswid=$mhswid'\"></td></tr>
  </table></p>";
}
function AmbilDetailPembayaran($mhswid, $pmbid, $pmbmhswid, $khsid, $RekeningID, $Jumlah) {
  $_Jumlah = number_format($Jumlah);
  if ($pmbmhswid == 0) {
    $mhsw = GetFields('pmb', 'PMBID', $pmbid, '*');
    $thn = $mhsw['PMBPeriodID'];
    $_fld = "bm.PMBID";
    $_nli = $pmbid;
  }
  else {
    $mhsw = GetFields('mhsw', 'MhswID', $mhswid, '*');
    $khs = GetFields('khs', 'KHSID', $khsid, '*');
    $thn = $khs['TahunID'];
    $_fld = "bm.MhswID";
    $_nli = $mhswid;
  }
  // Ambil bipotmhsw
  $s0 = "select bm.*, bn.Nama, bn.RekeningID
    from bipotmhsw bm
      left outer join bipotnama bn on bm.BIPOTNamaID=bn.BIPOTNamaID
    where $_fld='$_nli'
      and bn.TrxID=1
      and bn.RekeningID='$RekeningID'
      and (bm.Jumlah*bm.Besar > bm.Dibayar)
      and bm.TahunID='$thn'
    order by bn.Urutan";
  $r0 = _query($s0);
  $a = '';
  $dtl = array();
  $sisa = $Jumlah;
  while ($w0 = _fetch_array($r0)) {
    $harus = $w0['Jumlah'] * $w0['Besar'] - $w0['Dibayar'];
    if ($sisa > 0) {
      $bayar = ($harus > $sisa)? $sisa : $harus;
      $sisa = $sisa - $bayar;
    }
    else $bayar = 0;
    $dtl[] = $w0['BIPOTMhswID'];
    $kurang = number_format($harus);
    $a .= "<tr><td class=inp title='$w0[BIPOTMhswID]'>$w0[Nama]</td>
      <td class=ul><input type=text name='BIPOTMhswID_$w0[BIPOTMhswID]' value='$bayar' size=20 maxlength=15 onChange='HitungBayarBIPOTMhsw(bpm)'></td>
      <td class=ul align=right>$kurang</td>
      </tr>";
  }
  // Tuliskan javascript
  //tot = parseInt(frm.Tugas1.value) +
  //parseInt(frm.Tugas2.value) +
  $_strdtl = "tot = "; $_str = array();
  for ($i=0; $i < sizeof($dtl); $i++) {
    $_str[] = "parseInt(frm.BIPOTMhswID_" . $dtl[$i] . ".value)";
  }
  $_strdtl .= implode(' + ', $_str) . ';';
  echo "
  <script language='javascript'>
  <!--
  function UbahClass(id, newClass) {
    identity=document.getElementById(id);
    identity.className=newClass;
  }
  function HitungBayarBIPOTMhsw(frm) {
    $_strdtl
    if (tot > $Jumlah) {
      UbahClass('TOTAL1', 'wrn');
      UbahClass('TOTAL', 'wrn');
    }
    else {
      UbahClass('TOTAL1', 'inp');
      UbahClass('TOTAL', 'ul');
    }
    frm.Total.value = tot;
  }
  function HitungTotalBIPOTMhsw(frm) {
    $_strdtl
    if (tot > $Jumlah) alert('Jumlah pembayaran melebihi: $_Jumlah');
    return tot == $Jumlah;
  }
  -->
  </script>
  ";
  $_dtl = "<input type=hidden name='arrBIPOTMhswID' value='" . implode(',', $dtl) . "'>";
  return $_dtl . $a;
}
function BayarSavManual1() {
  $mhswid = $_REQUEST['mhswid'];
  $pmbid = $_REQUEST['pmbid'];
  $pmbmhswid = $_REQUEST['pmbmhswid'];
  $khsid = $_REQUEST['khsid'];
  if ($pmbmhswid == 0) $TahunID = GetaField('pmb', 'PMBID', $pmbid, "PMBPeriodID");
  else $TahunID = GetaField('khs', 'KHSID', $khsid, 'TahunID');
  $CicilanID = $_REQUEST['CicilanID'];
  $RekeningID = $_REQUEST['RekeningID'];
  $BuktiSetoran = sqling($_REQUEST['BuktiSetoran']);
  $BayarMhswID = $_REQUEST['bpmid'];
  $Jumlah = $_REQUEST['Jumlah']+0;
  $_Jumlah = number_format($Jumlah);
  $JumlahLain = $_REQUEST['JumlahLain']+0;
  $_JumlahLain = number_format($JumlahLain);
  //$Tanggal = "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
  $Tanggal = $_REQUEST['Tanggal'];
  //echo "<font size=+1>$Tanggal</font>";
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $md = $_REQUEST['md']+0;
  
  if ($md == 0) {
    $s = "update # set BuktiSetoran='$BuktiSetoran', Tanggal='$Tanggal', 
      Jumlah='$Jumlah', JumlahLain='$JumlahLain',
      Keterangan='$Keterangan', Proses=1,
      LoginEdit='$_SESSION[_Login]', TanggalEdit=now()
      where BayarMhswID='$BayarMhswID' ";
    // update data asli
    $str = str_replace('#', 'bayarmhsw', $s);
    $r = _query($str);
    // update data cek
    $str = str_replace('#', 'bayarmhswcek', $s);
    $r = _query($str);
  }
  // tambah transaksi
  else {
    // fase 1: tambahkan transaksi
    $BayarMhswID = GetNextBPM();
    $s = "insert into #
      (BayarMhswID, TahunID, PMBID, MhswID, RekeningID, BuktiSetoran,
      PMBMhswID, TrxID, Tanggal, Jumlah, JumlahLain, CicilanID, Keterangan,
      LoginBuat, TanggalBuat)
      values('$BayarMhswID', '$TahunID', '$pmbid', '$mhswid', '$RekeningID', '$BuktiSetoran',
      '$pmbmhswid', 1, '$Tanggal', $Jumlah, $JumlahLain, $CicilanID, '$Keterangan',
      '$_SESSION[_Login]', now())";
    // Insert data asli
    $str = str_replace('#', 'bayarmhsw', $s);
    $r = _query($str);
    // Insert data cek
    $str = str_replace('#', 'bayarmhswcek', $s);
    $r = _query($str);
  }
  // Jika cicilan, maka data cicilan di set
  if ($CicilanID > 0) {
    $sc = "update cicilanmhsw set SudahDibayar='Y'
      where CicilanID='$CicilanID' ";
    $rc = _query($sc);
  }
  // Tuliskan isinya (BayarMhsw2)
  $_BIPOTMhswID = $_REQUEST['arrBIPOTMhswID'];
  if (!empty($_BIPOTMhswID)) {
    $arrBIPOTMhswID = explode(',', $_BIPOTMhswID);
    for ($i=0; $i < sizeof($arrBIPOTMhswID); $i++) {
      $_bmid = $arrBIPOTMhswID[$i];
      $_jml = $_REQUEST['BIPOTMhswID_'.$_bmid]+0;
      $s1 = "insert into bayarmhsw2
        (BayarMhswID, BIPOTMhswID, Jumlah,
        LoginBuat, TanggalBuat)
        values ('$BayarMhswID', '$_bmid', $_jml,
        '$_SESSION[_Login]', now())";
      $r1 = _query($s1);
      // Update data BIPOT
      $fld = ($pmbmhswid == 0)? "PMBID" : "MhswID";
      $nli = ($pmbmhswid == 0)? $pmbid : $mhswid;
      $totaldibayar = GetaField("bayarmhsw2 bm2
        left outer join bayarmhsw bm on bm2.BayarMhswID=bm.BayarMhswID", 
        "bm.TahunID='$TahunID' and bm2.BIPOTMhswID=$_bmid and bm.$fld", $nli, "sum(bm2.Jumlah)")+0;
      $sbpt = "update bipotmhsw
        set Dibayar=$totaldibayar where BIPOTMhswID='$_bmid' ";
      $rbpt = _query($sbpt);
      //echo "<pre>$sbpt</pre>";
    }
  }
  if ($pmbmhswid == 1) {
  // Update Total Bayar
    $TotalBayar = GetaField('bayarmhsw', "TahunID='$TahunID' and MhswID", $mhswid, "sum(Jumlah)")+0;
    $sk = "update khs set Bayar=$TotalBayar where KHSID=$khsid";
    $rk = _query($sk);
  }
  else {
    $TotalBayar = GetaField('bayarmhsw', "PMBMhswID=0 and PMBID", $pmbid, "sum(Jumlah)")+0;
    $sk = "update pmb set TotalSetoranMhsw=$TotalBayar where PMBID='$pmbid' ";
    $rk = _query($sk);
  }
  include_once "mhswkeu.lib.php";
  HitungBiayaBayarMhsw($mhswid, $khsid);
    if (!empty($_REQUEST['gosto'])) {
      echo "<script>window.location='?gos=$_REQUEST[gosto]';</script>";
    }
}
?>
