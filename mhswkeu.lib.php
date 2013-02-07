<?php
// Author: Emanuel Setio Dewo
// 10 March 2006

// script ini memiliki kaitan erat dengan script: "mhswkeu.sav.php"

// *** BIPOT ***
function TampilkanBiayaPotongan($mhsw, $khs) {
  $s = "select bm.*, bn.Nama, bn.RekeningID, rek.Nama as NamaRekening,
      (bm.Jumlah * bm.Besar) as TOT,
      format(bm.Jumlah * bm.Besar, 0) as TOTS,
      format(bm.Dibayar, 0) as BYR,
      bm.TrxID, b2.Prioritas,
      format(bm.Besar, 0) as BSR
    from bipotmhsw bm
      left outer join bipotnama bn on bn.BIPOTNamaID=bm.BIPOTNamaID
      left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
      left outer join rekening rek on bn.RekeningID=rek.RekeningID
    where bm.MhswID='$mhsw[MhswID]' and bm.TahunID='$khs[TahunID]'
    order by bm.TrxID, b2.Prioritas";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $a = "<p><table class=box cellspacing=1 cellpadding=4>";
  $header = "
    <tr><th class=ttl title='Prioritas'>Prio</th>
    <th class=ttl>Nama</th>
    <th class=ttl title='Rekening'>Rek</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Besar</th>
    <th class=ttl>Total</th>
    <th class=ttl>Dibayar</th>
    </tr>";
  $trx = -100;
  $totbia = 0;
  $totbyr = 0;
  while ($w = _fetch_array($r)) {
    if ($trx != $w['TrxID']) {
      $trx = $w['TrxID'];
      $trxnm = GetaField('trx', 'TrxID', $trx, 'Nama');
      $a .= "<tr><td class=ul colspan=6><b>$trxnm</b></td></tr>";
      $a .= $header;
    }
    $kurang = ($w['Jumlah'] * $w['Besar'] > $w['Dibayar'])? "class=wrn" : "class=ul";
    $totbia += $w['Jumlah'] * $w['Besar'];
    $totbyr += $w['Dibayar'];
    $pos = strpos('.1.2.60.70.', ".$_SESSION[_LevelID].");
    $Nama = ($w['BIPOTMhswRef'] >0)? GetaField("krs", "KRSID", $w['BIPOTMhswRef'], "MKKode") : $w['Nama'];
    if ($pos === false) $edt = $Nama;
    else $edt = "<a href='?mnux=mhswkeu.det&gos=MhswKeuSesi&ka=BipotMhswEdt&trx=$w[TrxID]&khsid=$khs[KHSID]&bipotmhswid=$w[BIPOTMhswID]&md=0&mhswid=$mhsw[MhswID]'><img src='img/edit.png'>
        $Nama</a>";
    $a .= "<tr>
      <td class=ul>$w[Prioritas]</td>
      <td class=ul title='$w[Catatan]'>$edt</td>
      <td class=ul><abbr title='$w[NamaRekening]'>$w[RekeningID]</abbr></td>
      <td class=ul align=right>$w[Jumlah]</td>
      <td class=ul align=right>$w[BSR]</td>
      <td class=ul align=right>$w[TOTS]</td>
      <td $kurang align=right>$w[BYR]</td>
      </tr>";
  }
  $kekurangan = $totbyr - $totbia;
  $c = ($kekurangan < 0)? 'class=wrn' : 'class=ul';
  $strkekurangan = number_format($kekurangan);
  return $a . "
    <tr><td colspan=6 align=right>Total :</td><td $c align=right><b>$strkekurangan</b></td></tr>
    </table></p>";
}
// Edit biaya dan potongan mahasiswa
function BipotMhswEdt($mhsw, $khs) {
  $md = $_REQUEST['md']+0;
  $trx = $_REQUEST['trx'];
  if ($md == 0) {
    $w = GetFields('bipotmhsw', 'BIPOTMhswID', $_REQUEST['bipotmhswid'], '*');
    $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w['BIPOTNamaID'], 'Nama');
    $jdl = "Edit Biaya & Potongan Mahasiswa";
    $optbipotnama = "<input type=hidden name='BIPOTNamaID' value='$w[BIPOT2ID]'><b>$Nama</b>";
  }
  else {
    $w = array();
    $w['BIPOTMhswID'] = 0;
    $w['MhswID'] = $mhsw['MhswID'];
    $w['Tahun'] = $khs['TahunID'];
    $w['BIPOT2ID'] = 0;
    $w['BIPOTNamaID'] = 0;
    $w['TrxID'] = $trx;
    $w['Jumlah'] = 1;
    $w['Besar'] = 0;
    $w['Dibayar'] = 0;
    $w['Dispensasi'] = '';
    $w['NomerDispensasi'] = '';
    $w['Catatan'] = '';
    $jdl = "Tambah Biaya & Potongan Mahasiswa";
    //GetOption2($_table, $_field, $_order='', $_default='', $_where='', $_value='', $not=0) {
    $opt2 = GetOption2("bipotnama", "Nama", "Nama", "", "TrxID=$trx", "BIPOTNamaID"); 
    $optbipotnama = "<select name='BIPOTNamaID'>$opt2</select>";
    $NoSK = "<tr><td class=ul>Nomor SK</td><td class=ul><input type=text name='NoSK' value='$khs[NoSurat]' size=20></td></tr>";
  }
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' method=POST>
    <input type=hidden name='mnux' value='mhswkeu.det'>
    <input type=hidden name='gos' value='MhswKeuSesi'>
    <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
    <input type=hidden name='khsid' value='$khs[KHSID]'>
    <input type=hidden name='slnt' value='mhswkeu.sav'>
    <input type=hidden name='slntx' value='BipotMhswSav'>
    <input type=hidden name='md' value='$md'>
    <input type=hidden name='BIPOTMhswID' value='$w[BIPOTMhswID]'>
    <input type=hidden name='BIPOT2ID' value='$w[BIPOT2ID]'>
    <tr><th class=ttl colspan=2>$jdl</th></tr>
    <tr><td class=ul>Biaya/Potongan</td><td class=ul>$optbipotnama</td></tr>
    <tr><td class=ul>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20></td></tr>
    <tr><td class=ul>Besar</td><td class=ul><input type=text name='Besar' value='$w[Besar]' size=20></td></tr>
    <tr><td class=ul>Dibayar</td><td class=ul><input type=text name='Dibayar' value='$w[Dibayar]' size=20></td></tr>
    <tr><td class=ul>Catatan</td><td class=ul><textarea name='Catatan' cols=30 rows=5>$w[Catatan]</textarea></td></tr>
    <tr><td colspan=2 class=ul><input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswkeu.det&gos=MhswKeuSesi&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]'\"></td></tr>
    </table></p>";
  return $a;
}

// *** Pembayaran ***
function BayarEdt() {
  $mhswid = $_REQUEST['mhswid'];
  $md = $_REQUEST['md']+0;
  $khsid = $_REQUEST['khsid'];
  $khs = GetFields('khs', 'KHSID', $khsid, '*');
  $kurang = $khs['Biaya'] - $khs['Bayar'] + $khs['Tarik'] - $khs['Potongan'];
  if ($kurang <> 0) {
    $_kurang = number_format($kurang);
    $strkurang = "<tr><td class=ul colspan=2>Kekurangan pembayaran dalam semester ini:<br />
      Rp. <b>$_kurang</b></td></tr>";
  }
  else {
    $strkurang = '';
  }
  $CicilanID = $_REQUEST['CicilanID']+0;
  if ($CicilanID > 0) $Cicilan = GetFields('cicilanmhsw', 'CicilanID', $CicilanID,
    "*, format(Jumlah, 0) as JML");

  if ($md == 0) {
    $w = GetFields('bayarmhsw', 'BayarMhswID', $_REQUEST['BayarMhswID'], '*');
  }
  else {
    $w = array();
    $w['RekeningID'] = '';
    $w['BuktiSetoran'] = '';
    $w['BayarMhswID'] = 0;
    $w['TahunID'] = $khs['TahunID'];
    $w['MhswID'] = $mhsw['MhswID'];
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
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)", "RekeningID", $w['RekeningID'], "KodeID='$_SESSION[KodeID]'", "RekeningID");
  // Tampilkan Form Pembayaran
  ScriptCekKelebihan($kurang);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CekKelebihan(this);\">
  <input type=hidden name='mnux' value='$_REQUEST[mnux]'>
  <input type=hidden name='gos' value='$_REQUEST[gosto]'>
  <input type=hidden name='slnt' value='mhswkeu.sav'>
  <input type=hidden name='slntx' value='BayarSav'>
  <input type=hidden name='md' value='$md'>

  <input type=hidden name='mhswid' value='$mhswid'>
  <input type=hidden name='khsid' value='$khs[KHSID]'>
  <input type=hidden name='CicilanID' value='$w[CicilanID]'>
  <input type=hidden name='BayarMhswID' value='$w[BayarMhswID]'>

  <tr><th class=ttl colspan=2>$Judul</th></tr>
  $KeteranganCicilan
  $strkurang
  <tr><td class=inp1>Masuk ke Rekening</td><td class=ul><select name='RekeningID'>$optrek</select></td></tr>
  <tr><td class=inp1>Bukti Setoran</td><td class=ul><input type=text name='BuktiSetoran' value='$w[BuktiSetoran]' size=20 maxlength=50></td></tr>
  <tr><td class=inp1>Tanggal Setor Bank</td><td class=ul>$Tgl</td></tr>
  <tr><td class=inp1>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$w[Jumlah]' size=20 maxlength=20></td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><input type=text name='Keterangan' value='$w[Keterangan]' size=50 maxlength=200></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswkeu.det&gos=MhswKeuSesi&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]'\"></td></tr>
  </form></table></p>";
}
function ScriptCekKelebihan($max=0) {
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
function DaftarPembayaran($mhsw, $khs, $mnux='', $gos='', $gosto='') {
  $arrTrxID = array(-1=>"Tarikan", 0=>"-", 1=>"Pembayaran");
  $s = "select bm.*, date_format(Tanggal, '%d/%m/%Y') as TGL,
    date_format(TanggalBuat, '%d/%m/%Y') as TGLTRX,
    format(Jumlah, 0) as JML, format(JumlahLain, 0) as JMLL
    from bayarmhsw bm
    where bm.MhswID='$mhsw[MhswID]' and bm.TahunID='$khs[TahunID]' and bm.TrxID <> -1
    order by bm.TrxID, bm.BayarMhswID";
  //echo $s;
  $r = _query($s);
  $hdr = "<tr><th class=ttl>No BPM</th>
    <th class=ttl>Tgl Bank</th>
    <th class=ttl>Bukti Setoran</th>
    <th class=ttl>Tgl Trx</th>
    <th class=ttl>Rekening</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Jumlah<br />Lain</th>
    <th class=ttl>Proses</th>
    </tr>";
  $a = "<p><table class=box cellspacing=1 cellpadding=4>";
  $tot = 0; $totl = 0;
  $trxid = -1050;
  // Jika Superuser & Keuangan
  $TidakBoleh = strpos(".1.60.70", ".$_SESSION[_Login].") === false;
  while ($w = _fetch_array($r)) {
    if ($trxid != $w['TrxID']) {
      $trxid = $w['TrxID'];
      $a .= "<tr><td class=ul colspan=4><b>" . $arrTrxID[$w['TrxID']]. "</b></td></tr>";
      $a .= $hdr;
    }
    $tot += $w['Jumlah'] * $w['TrxID'];
    $totl += $w['JumlahLain'];
    $c = ($w['Proses'] == 0)? "class=wrn" : "class=ul";
    $YN = ($w['Proses'] == 0)? 'N' : 'Y';
    $lnk = ($w['Proses'] == 0)? 
      "<a href='?mnux=$mnux&gos=$gos&pmbmhswid=1&mhswid=$mhsw[MhswID]&crmhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&bpmid=$w[BayarMhswID]&gosto=$gosto' title='Lakukan Konfirmasi Pembayaran'><img src='img/$YN.gif'></a>" : 
      "<img src='img/$YN.gif'>";
    $BPMEdt = ($TidakBoleh == false)? '' : ($w['Proses'] == 0)? '' : "<a href='?mnux=bpm.edt&bpmid=$w[BayarMhswID]&bck=mhswkeu.det&bckgos=MhswKeuSesi&MhswID=$w[MhswID]&khsid=$khs[KHSID]'>Edit</a>";
    $a .= "<tr><td class=ul>$w[BayarMhswID]</td>
      <td $c>$w[TGL]</td>
      <td $c>$w[BuktiSetoran]&nbsp;</td>
      <td $c>$w[TGLTRX]</td>
      <td $c>$w[RekeningID]&nbsp;</td>
      <td $c align=right>$w[JML]</td>
      <td $c align=right>$w[JMLL]</td>
      <td class=ul align=center>$lnk $BPMEdt</td>
      </tr>";
  }
  $_tot = number_format($tot);
  return $a . "
    <tr><td colspan=5 align=right>Total :</td>
    <td class=ul align=right><b>$_tot</b></td></tr>
    </table></p>";
}
function BayarBPM() {
  $mhswid = $_REQUEST['mhswid'];
  $pmbid = $_REQUEST['pmbid'];
  $khsid = $_REQUEST['khsid'];
  $bpmid = $_REQUEST['bpmid'];
  $pmbmhswid = $_REQUEST['pmbmhswid'];
  $bpm = GetFields("bayarmhsw bm left outer join rekening r on bm.RekeningID=r.RekeningID",
    "BayarMhswID", $bpmid, "bm.*, r.Nama as REK");
  $crmhswid = $mhswid;
  $Tanggal = GetDateOption(date('Y-m-d'), 'Tanggal');
  CheckFormScript('Jumlah,BuktiSetoran,Simpan');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_REQUEST[mnux]'>
  <input type=hidden name='gos' value='BayarSav'>
  <input type=hidden name='gosto' value='$_REQUEST[gosto]'>
  <input type=hidden name='md' value='0'>
  <input type=hidden name='mhswid' value='$mhswid'>
  <input type=hidden name='pmbid' value='$pmbid'>
  <input type=hidden name='trm' value='$pmbid'>
  <input type=hidden name='crmhswid' value='$crmhswid'>
  <input type=hidden name='khsid' value='$khsid'>
  <input type=hidden name='RekeningID' value='$bpm[RekeningID]'>
  <input type=hidden name='bpmid' value='$bpmid'>
  <input type=hidden name='pmbmhswid' value='$pmbmhswid'>
  
  <tr><th class=ttl colspan=2>Pembayaran/Proses BPM</th></tr>
  <tr><td class=inp1>Nomer BPM</td><td class=ul><b>$bpmid</b></td></tr>
  <tr><td class=inp1>Ke Rekening</td><td class=ul>$bpm[RekeningID] - <b>$bpm[REK]</b></td></tr>
  <tr><td class=inp1>Tanggal Disetor ke Bank</td><td class=ul>$Tanggal</td></tr>
  <tr><td class=inp1>No Bukti Setoran Bank</td><td class=ul><input type=text name='BuktiSetoran' value='$bpm[BuktiSetoran]' size=20 maxlength=50></td></tr>
  
  <tr><td class=inp1>Jumlah</td><td class=ul><input type=text name='Jumlah' value='$bpm[Jumlah]' size=20 maxlength=20></td></tr>
  <tr><td class=inp1>Jumlah Lain</td><td class=ul><input type=text name='JumlahLain' value='$bpm[JumlahLain]' size=20 maxlength=20> *) Tidak dihitung di balance.</td></tr>
  <tr><td class=inp1>Keterangan</td><td class=ul><input type=text name='Keterangan' value='$bpm[Keterangan]' size=50 maxlength=200></td></tr>
  <tr><td class=inp1>Alokasi Pembayaran</td><td class=ul>
    <input type=radio name='Simpan' value='2' checked> Alokasi Otomatis, 
    <input type=radio name='Simpan' value='1'> Alokasi Manual</td></tr>
  <tr><td colspan=2><input type=submit name='TombolSimpan' value='Simpan Pembayaran'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_REQUEST[mnux]&crmhswid=$crmhswid&trm=$pmbid&pmbmhswid=$pmbmhswid&gos=$_REQUEST[gosto]'\"></td></tr>
  </table></p>";
  /*<tr><td class=inp1>Jenis Pembayaran</td>
    <td class=ul><input type=radio name='JenisPembayaran' value=1> Akademik,
    <input type=radio name='JenisPembayaran' value=2>Lain-lain</td></tr>*/
}
function HitungBiayaBayarMhsw($MhswID='', $KHSID='') {
  $MhswID = (empty($MhswID))? $_REQUEST['mhswid'] : $MhswID;
  $KHSID = (empty($KHSID))? $_REQUEST['khsid'] : $KHSID;
  $khs = GetFields('khs', 'KHSID', $KHSID, '*');
  if (!empty($MhswID) && !empty($KHSID)) {
    $bia = GetaField('bipotmhsw', "TrxID=1 and TahunID='$khs[TahunID]' and MhswID", 
      $MhswID, "sum(Jumlah*Besar)")+0;
    $pot = GetaField('bipotmhsw', "TrxID=-1 and TahunID='$khs[TahunID]' and MhswID",
      $MhswID, "sum(Dibayar)")+0;
    $byr = GetaField('bayarmhsw', "TrxID=1 and Proses=1 and TahunID='$khs[TahunID]' and MhswID", 
      $MhswID, "sum(Jumlah)")+0;
    $trk = GetaField('bayarmhsw', "TrxID=-1 and Proses=1 and TahunID='$khs[TahunID]' and MhswID",
      $MhswID, "sum(Jumlah)")+0;
    $jmll = GetaField('bayarmhsw', "TrxID=1 and Proses=1 and TahunID='$khs[TahunID]' and MhswID",
      $MhswID, "sum(JumlahLain)")+0;
    $Dibayar = GetaField("bipotmhsw", "TahunID='$khs[TahunID]' and trxID = 1 and MhswID", $mhsw['MhswID'], "sum(Dibayar)");
    // Simpan
    $s = "update khs set Biaya=$bia, Potongan=$pot, Bayar=$byr, Tarik=$trk, JumlahLain=$jmll where KHSID=$KHSID";
    $r = _query($s);
    ProsesBipotMhsw($byr, $khs, $mhsw);
  }
}

function ProsesBipotMhsw($byr, $khs){
  $s = "select BipotNamaID, (Jumlah * Besar) as JML, BipotMhswID from
        bipotmhsw
        where TahunID='$khs[TahunID]' 
        and TrxID = 1 
        and MhswID = '$khs[MhswID]'";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    if ($byr >= $w['JML']) {
      $s0 = "update bipotmhsw set Dibayar = '$w[JML]' where BipotNamaID = '$w[BipotNamaID]' and BipotMhswID = '$w[BipotMhswID]'";
      $r0 = _query($s0);
      $byr = $byr - $w['JML'];
    } 
    elseif (($byr != 0) and ($byr < $w['JML'])) {
      $s0 = "update bipotmhsw set Dibayar = '$byr' where BipotNamaID = '$w[BipotNamaID]' and BipotMhswID = '$w[BipotMhswID]'";
      $r0 = _query($s0);
      $byr = $byr - $byr;
    }
    else {
      $s0 = "update bipotmhsw set Dibayar = 0 where BipotNamaID = '$w[BipotNamaID]' and BipotMhswID = '$w[BipotMhswID]'";
      $r0 = _query($s0);
    }
  }
}

// *** Summary Keuangan Mhsw ***
function TampilkanSummaryKeuMhsw($mhsw, $khs) {
  $balance = $khs['Bayar'] - $khs['Biaya'] + $khs['Potongan'] - $khs['Tarik'];
  $Dibayar = GetaField("bipotmhsw", "TahunID='$khs[TahunID]' and trxID = 1 and MhswID", $mhsw['MhswID'], "sum(Dibayar)");
  if ($khs['Biaya']>0 && ($Dibayar - $khs['Potongan']) != $khs['Bayar']) {
    echo SimpleErrorMsg("Data kewajiban yg dibayar dengan BPM (pembayaran) tidak sinkron. Harap lakukan koreksi pembayaran di kewajiban.");
  }
  $blc = ($balance < 0)? 'class=wrn' : 'class=ul';
  // format tampilan
  $_balance = number_format($balance);
  $BIA = number_format($khs['Biaya']);
  $BYR = number_format($khs['Bayar']);
  $POT = number_format($khs['Potongan']);
  $TRK = number_format($khs['Tarik']);
  $JMLL = number_format($khs['JumlahLain']);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>Tot Biaya</th>
    <th class=ttl>Tot Bayar</th>
    <th class=ttl>Tot Potongan</th>
    <th class=ttl>Tot Tarikan</th>
    <th class=ttl>Balance</th>
    <th class=ttl>Jumlah<br />Lain</th>
    <th class=ttl>Hitung<br />Ulang</th>
  </tr>
  <tr><td class=ul align=right>$BIA</td>
    <td class=ul align=right>$BYR</td>
    <td class=ul align=right>$POT</td>
    <td class=ul align=right>$TRK</td>
    <td $blc align=right><b>$_balance</td>
    <td class=ul align=right>$JMLL</td>
    <td class=ul><a href='?slnt=mhswkeu.lib&slntx=HitungBiayaBayarMhsw&mhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&mnux=$_REQUEST[mnux]&gos=$_REQUEST[gos]'>Hitung</a></td>
  </tr>
  </table></p>";
}
function TampilkanCetakBPM($mhsw, $khs, $pmbmhswid=1) {
  $optrek = GetOption2('rekening', "concat(RekeningID, ' - ', Nama)",
    "RekeningID", $_SESSION['rekid'], '', 'RekeningID');
  if ($pmbmhswid == 1) {
    $det = ($_SESSION['TampilkanDetail'] == 1)? "<a href='?mnux=$_REQUEST[mnux]&gos=$_REQUEST[gos]&crmhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&TampilkanDetail=0'>Sembunyikan Detail</a>" :
      "<a href='?mnux=$_REQUEST[mnux]&gos=$_REQUEST[gos]&crmhswid=$mhsw[MhswID]&khsid=$khs[KHSID]&TampilkanDetail=1'>Tampilkan Detail</a>";
  } else $det = '';
  // Apakah akan mencetak BPM Blank?
  if ($_SESSION['bpmblank'] == 0) {
    $ck0 = 'checked'; $ck1 = '';
  }
  else {
    $ck0 = ''; $ck1 = 'checked';
  }
  $a = "<p><table class=box cellspacing=1 cellpadding=4>";
  // Proses BIPOT
  $a .= ($pmbmhswid == 1)? "<form action='?' name='data' method=POST>
  <input type=hidden name='slnt' value='mhswkeu.sav'>
  <input type=hidden name='slntx' value='PrcBIPOTSesi'>
  <input type=hidden name='crmhswid' value='$mhsw[MhswID]'>
  <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
  <input type=hidden name='khsid' value='$khs[KHSID]'>
  <input type=hidden name='tahun' value='$khs[TahunID]'>
  <input type=hidden name='pmbmhswid' value='$pmbmhswid'>
  <input type=hidden name='gos' value='$_REQUEST[gos]'>
  <tr><td class=wrn>Proses</td>
    <td class=inp1>Proses BIPOT?</td>
    <td class=ul><input type=submit name='Proses' value='Proses'></td></tr>
  </form>" : '';
  // Cetak BPM
  $a .= "<form action='?' name='data' method=POST>
  <input type=hidden name='gos' value='BPMCetak'>
  <input type=hidden name='pmbid' value='$mhsw[PMBID]'>
  <input type=hidden name='mhswid' value='$mhsw[MhswID]'>
  <input type=hidden name='khsid' value='$khs[KHSID]'>
  <input type=hidden name='pmbmhswid' value='$pmbmhswid'>
  <tr><td class=wrn rowspan=2>Cetak</td>
    <td class=inp1>Cetak BPM?</td>
    <td class=ul><select name='rekid'>$optrek</select>
    <input type=submit name='Cetak' value='Cetak'>
    </td></tr>
  <tr><td class=inp1>BPM Blank?</td>
    <td class=ul><input type=radio name='bpmblank' value='0' $ck0> Blank
    <input type=radio name='bpmblank' value='1' $ck1> Dengan detail biaya</td></tr>
  </form>
  
  <tr><td class=ul colspan=3>Pilihan: $det</td></tr>
  </table></p>";
  return $a;
}
function BPMCetak() {
  //include_once "bpm.cetak.php";
  $lnk = "rekid=$_REQUEST[rekid]&pmbid=$_REQUEST[pmbid]&mhswid=$_REQUEST[mhswid]&khsid=$_REQUEST[khsid]&pmbmhswid=$_REQUEST[pmbmhswid]&bpmblank=$_REQUEST[bpmblank]";
  echo "<script>
    win2 = window.open('bpm.cetak.php?$lnk', \"\", \"width=600, height=600, scrollbars, status\");
    win2.creator = self;
    </script>";
  echo "<script>window.location='?';</script>";
}
function PembayaranBPM($mhsw, $khs) {
  
}
?>
