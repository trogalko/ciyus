<?php
// Author: Emanuel Setio Dewo
// 26 Jan 2006

include_once "mhswbaru.sav.php";
include_once "mhswbaru.lib.php";
include_once "mhswbaru.import.php";
include_once "mhswkeu.lib.php";
include_once "mhswkeu.sav.php";

// *** Functions ***
function SrcPMB() {
  global $arrID;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='KodeID' value='$arrID[Kode]'>
  <input type=hidden name='mnux' value='mhswbaru'>
  
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp1>Periode :</td>
    <td class=ul><input type=text name='periode' value='$_SESSION[periode]' size=15></td></tr>
  <tr><td class=inp1>Cari Calon Mhsw:</td>
    <td class=ul><input type=text name='pmbid' value='$_SESSION[pmbid]' size=20 maxlength=50>
    <input type=submit name='Cari' value='PMBID'>
    <input type=submit name='Cari' value='Nama'></td></tr>
  </form></table><p>";
  
  if (!empty($_SESSION['periode'])) {
    $s = "select p.PMBID, p.Nama, p.NIM, p.ProdiID, p.LulusUjian, p.NilaiUjian,
      prg.Nama as PRG, prd.Nama as PRODI, sa.Nama as STT, sa.TanpaTest,
      bp.Nama as BPT, p.StatusMundur
      from pmb p
      left outer join program prg on p.ProgramID=prg.ProgramID
      left outer join prodi prd on p.ProdiID=prd.ProdiID
      left outer join statusawal sa on p.StatusAwalID=sa.StatusAwalID
      left outer join bipot bp on p.BIPOTID=bp.BIPOTID
      where p.PMBPeriodID = '$_SESSION[periode]' and 
      p.$_SESSION[Cari] like '%$_SESSION[pmbid]%'
      order by p.$_SESSION[Cari]";
    $r = _query($s);
    $n = 0;
    echo $jmlmhsw = HitungMhsw($_SESSION['periode']);
    echo "<p><table class=box cellspacing=1 cellpadding=4>";
    echo "<tr><th class=ttl>#</th>
      <th class=ttl>PMB ID</th>
      <th class=ttl>NIM</th>
      <th class=ttl>Nama</th>
      <th class=ttl>Program</th>
      <th class=ttl>Program Studi</th>
      <th class=ttl title='Master Biaya dan Potongan'>Master BIPOT</th>
      <th class=ttl>Status</th>
      <th class=ttl>Status Mundur</th>
      <th class=ttl>Diskon</th>
			<th class=ttl>% pembayaran</th>
      <th class=ttl>Data PMB</th>
      </tr>";
    $BLMLLS = 0;
    $SDHLLS = 0;
    $BYRSBB = 0;
    $BYRLNS = 0;
    while ($w = _fetch_array($r)) {
      $n++;
      // Cek apakah bisa diimport karena belum punya NIM?
      $img = "<img src='img/Y.gif'>";
      $c = 'class=ul';
      
      if (empty($w['NIM'])) {
        if ($w['TanpaTest'] == 'Y') {
          $_trm = "<a href='?mnux=mhswbaru&gos=ImprtPMB&trm=$w[PMBID]'>$img Tanpa Test</a>";
          $c = buatWarna($w['PMBID']); 
        }
        else {
          if ($w['LulusUjian'] == 'Y') {
              $_trm = "<a href='?mnux=mhswbaru&gos=ImprtPMB&trm=$w[PMBID]'>$img LULUS</a>";
              $c = buatWarna($w['PMBID']);
          }
          else {
            $_trm = 'Tidak/belum LULUS';            
            $c = 'class=inp1';
            $BLMLLS++;
          }
        }
      }
      else {
        $_trm = 'Sudah diproses';
        $c = 'class=cnnY';
      }
      $Diskon = $w['Diskon']+0;
      
      if($w['StatusMundur'] == 'Y') {
        $w['StatusMundur'] = 'MUNDUR';
        $wr = 'class=wrn';
      } else
      {
        $w['StatusMundur'] = '';
        $wr = $c;
      }
      
      echo "<SCRIPT LANGUAGE='JavaScript1.2'>
      <!--
      function DetailPMB(PMBID){
        lnk = \"pmb.inq.det.php?PMBID=\"+PMBID;
        win2 = window.open(lnk, '', 'width=600, height=600, scrollbars, status');
        win2.creator = self;
      }
      -->
      </script>
      ";
      $prsn = AmbilPersenPembayaran($w['PMBID'], $_SESSION['periode']);
      $psd = explode('|', $prsn);
      echo "<tr><td $c>$n</td>
      <td $c><a href='javascript:DetailPMB($w[PMBID])'>$w[PMBID]</a></td>
      <td $c>$w[NIM]&nbsp;</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[PRG]</td>
      <td $c>$w[PRODI]</td>
      <td $c>$w[BPT]&nbsp;</td>
      <td $c>$w[STT]</td>
      <td $wr align=center>$w[StatusMundur]</td>
      <td $c align=center>$psd[1]</td>
			<td $c align=center>$psd[0]</td>
      <td $c title='Import Data PMB'>$_trm</td>
      </tr>";
    }
    echo "</table></p>";
  }
}

function HitungMhsw($periode){
  $thn = substr($periode, 0, 4);
  $s = "select PMBPeriodID from pmb where left(PMBPeriodID, 4) = '$thn' group by PMBPeriodID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  $arrTHN = array();
  
  while ($w = _fetch_array($r)){
    $BLMLLS = GetaField('pmb', "StatusMundur = 'N' and LulusUjian = 'N' and PMBPeriodID", $w['PMBPeriodID'], "count(PMBID)")+0;
    $SDHLLS = GetaField('pmb', "StatusMundur = 'N' and LulusUjian = 'Y' and TotalSetoranMhsw = 0 and PMBPeriodID", $w['PMBPeriodID'], "count(PMBID)")+0;
    $SDHBYR = GetaField('pmb', "StatusMundur = 'N' and LulusUjian = 'Y' and NIM is null and TotalSetoranMhsw > 0 and PMBPeriodID", $w['PMBPeriodID'], "count(PMBID)")+0;
    $SDHLNS = GetaField('pmb', "StatusMundur = 'N' and NIM is not null and PMBPeriodID", $w['PMBPeriodID'], "count(PMBID)")+0;
    $SDHMDR = GetaField('pmb', "StatusMundur = 'Y' and PMBPeriodID", $w['PMBPeriodID'], 'count(PMBID)')+0;
    $TOTSEM = $BLMLLS + $SDHLLS + $SDHBYR + $SDHLNS + $SDHMDR;
    $GRNTOT += $TOTSEM;
    
    $tblTITLES .= "<th class=ttl>$w[PMBPeriodID]</th>";
    $tblBLMLLS .= "<td class = ul title=$w[PMBPeriodID] align=right>$SDHLLS</td>";
    $tblSDHLLS .= "<td class = ul title=$w[PMBPeriodID] align=right>$BLMLLS</td>";
    $tblSDHBYR .= "<td class = ul title=$w[PMBPeriodID] align=right>$SDHBYR</td>";
    $tblSDHLNS .= "<td class = ul title=$w[PMBPeriodID] align=right>$SDHLNS</td>";
    $tblSDHMDR .= "<td class = ul title=$w[PMBPeriodID] align=right>$SDHMDR</td>";
    $tblTOTSEM .= "<td class = inp1 align=right>$TOTSEM</td>";
    $TOTBLMLLS += $BLMLLS;
    $TOTSDHLLS += $SDHLLS;
    $TOTSDHBYR += $SDHBYR;
    $TOTSDHLNS += $SDHLNS;
    $TOTSDHMDR += $SDHMDR;
  }
  
  $tbl  = "<p><table class=box cellspacing=1 cellpadding=4>
           </tr><th class=ttl>Warna</th><th class=ttl>Keterangan</th>$tblTITLES<th class=ttl>TOTAL</th></tr>
           <tr><td class=ul>&nbsp;</td><td class=ul>Sudah Lulus belum bayar</td>$tblBLMLLS<td class = inp1 align=right>$TOTSDHLLS</td></tr>
           <tr><td class=inp1>&nbsp;</td><td class=ul>Tidak/Belum Lulus</td>$tblSDHLLS<td class = inp1 align=right>$TOTBLMLLS</td></tr>
           <tr><td class=inp2>&nbsp;</td><td class=ul>Sudah Bayar Sebagian</td>$tblSDHBYR<td class = inp1 align=right>$TOTSDHBYR</td></tr>
           <tr><td class=cnnY>&nbsp;</td><td class=ul>Sudah Proses Generate NPM</td>$tblSDHLNS<td class = inp1 align=right>$TOTSDHLNS</td></tr>
           <tr><td class=wrn>&nbsp;</td><td class=ul>Proses Mundur</td>$tblSDHMDR<td class = inp1 align=right>$TOTSDHMDR</td></tr>
           <tr><td class=ul colspan=2>Total per Periode</td>$tblTOTSEM<td class=inp2 align=center><b>$GRNTOT</b></td></tr>
           </table></p>";
           
  return $tbl;
}

function buatWarna($pmbid){
  $adaga = Getafield("pmb", 'pmbid', $pmbid, 'TotalSetoranMhsw')+0;
  return ($adaga == 0) ? '' : 'class=inp2';
}

function AmbilPersenPembayaran($pmbid, $periode){
	$s = "select (Jumlah * Besar) as Biaya, DiBayar from bipotmhsw 
        where PMBID = '$pmbid'
        and TrxID = 1";
  
  $r = _query($s);
  $DSKN = GetaField('bipotmhsw', "PMBID = '$pmbid' and TrxID", '-1', "Besar");
  
  while ($w = _fetch_array($r)) {
    $TOTBIA += $w['Biaya'];
    $TOTBYR += $w['DiBayar'];
  }
  
  $Persentase = ($TOTBIA == 0) ? 0 : $TOTBYR/$TOTBIA * 100;
  $PersentaseDSKN = ($TOTBIA == 0) ? 0 : $DSKN/$TOTBIA * 100;
	$p = ceil($Persentase) . " %";
	$d = floor($PersentaseDSKN) . " %";
	return $p . '|' . $d;
}

function ImprtPMB() {
  $trm = $_REQUEST['trm'];
  $w = GetFields("pmb p
    left outer join program prg on p.ProgramID=prg.ProgramID
    left outer join prodi prd on p.ProdiID=prd.ProdiID",
    'p.PMBID', $trm, 'p.*, prg.Nama as PRG, prd.Nama as PRD');
  if (empty($w['BIPOTID'])) {
    $w['BIPOTID'] = GetaField("bipot", "ProgramID='$w[ProgramID]' and ProdiID='$w[ProdiID]' and Def", 'Y', 'BIPOTID');
    // Simpan dulu ah...
    $s = "update pmb set BIPOTID='$w[BIPOTID]' where PMBID='$w[PMBID]' ";
    $r = _query($s);
  }

  echo HeaderCAMA($w);
  echo "<p><a href='#stp1'>Step 1</a> | <a href='#stp2a'>Step 2a</a> | <a href='#stp2b'>Step 2b</a> |
    <a href='#stp3'>Step 3</a> &raquo; 
    <a href='?mnux=mhswbaru&gos=ImprtPMB&trm=$trm'><b>Refresh</b></a>";
  // Cek BIPOT dulu
  CekBIPOT($w, $bpt);
  TampilkanBIPOTCAMA($w);
  TampilkanInstallment($w);
  TampilkanBayarCAMA($w);
  TampilkanBuatMhswID($w);
}
function TampilkanBuatMhswID($cama) {
  CheckFormScript("TahunNIM");
  echo "<p><a name='stp3'></a><h3>Step 3. Buat NPM</h3></p> <hr size=1 color=silver />";
  echo "<blockquote>";
  $TotalBiaya = GetaField('bipotmhsw', "PMBMhswID=0 and PMBID", $cama['PMBID'], "sum(Jumlah*Besar)")+0;
  $TotalBayar = GetaField('bayarmhsw', "PMBMhswID=0 and PMBID", $cama['PMBID'], "sum(Jumlah)")+0;
  //echo "Biaya: " . $TotalBiaya . "<br />";
  //echo "Bayar: " . $TotalBayar;

  if ($TotalBiaya > $TotalBayar) {
    // Jika telah didispensasi
    $kurang = $TotalBiaya - $TotalBayar;
    $prd = GetFields('prodi', 'ProdiID', $cama['ProdiID'], '*');
    $persen = ($TotalBiaya > 0)? 100 - ($kurang / $TotalBiaya * 100) : 0;

    if ($cama['Dispensasi'] == 'Y') {
      $ket = $cama['CatatanDispensasi'];
      $ket = str_replace(chr(13), "<br />", $ket);
      echo Konfirmasi("Dispensasi",
      "Calon mahasiswa ini telah mendapatkan dispensasi dengan nomer surat: <b>$cama[DispensasiID]</b><br />
      Berikut adalah catatan dispensasinya:
      <p><table class=box cellspacing=1 cellpadding=4>
      <tr><td class=inp1>$ket</td></tr>
      </table></p>
      Harap keterangan di atas diperhatikan dan dipenuhi.
      <hr size=1 color=silver>
      <form action='?' method='POST' onSubmit=\"return CheckForm(this);\">
      <input type=hidden name=mnux value=mhswbaru>
      <input type=hidden name=gos value=ImprtPMB1>
      <input type=hidden name=trm value=$cama[PMBID]>
      Angkatan Masuk (mis: 20071) : <input type=text maxlength=5 size=7 name=TahunNIM value='$_SESSION[TahunNIM]'>  <br />
      Proses Generate NPM : <input type=submit name=submit value=Generate NIM>
      </form>");
    }
    // Jika persentase pembayaran sudah mencukupi
    elseif ($persen >= $prd['PersenPMB']) {
      $_persen = number_format($persen, 2);
      echo Konfirmasi("Persentase Pembayaran",
      "Mahasiswa belum lunas tetapi sudah memenuhi syarat pembayaran minimal untuk prodi <b>$prd[Nama]</b>,<br />
      yaitu <b>$prd[PersenPMB]</b>% dari biaya masuk.<br />
      Calon sudah membayar: <b>$_persen</b> % dari ketentuan prodi <b>($prd[ProdiID]) $prd[Nama]</b> sebesar $prd[PersenPMB] %.
      <hr size=1 color=silver>
      <form action='?' method='POST' onSubmit=\"return CheckForm(this);\">
      <input type=hidden name=mnux value=mhswbaru>
      <input type=hidden name=gos value=ImprtPMB1>
      <input type=hidden name=trm value=$cama[PMBID]>
      Angkatan Masuk (mis: 20071) : <input type=text maxlength=5 size=7 name=TahunNIM value='$_SESSION[TahunNIM]'>  <br />
      Proses Generate NPM : <input type=submit name=submit value=Generate NIM>
      </form>");
    }
    // Jika belum didispensasi
    else {
      $minimal = $TotalBiaya * $prd['PersenPMB'] / 100;
      $_minimal = number_format($minimal, 2);
      echo ErrorMsg("Belum Lunas",
      "Calon mahasiswa tidak dapat diproses karena belum melunasi biaya-biayanya.<br />
      Minimum pembayaran prodi <b>($prd[ProdiID]) $prd[Nama]</b> untuk dapat diproses adalah sebesar: <b>$prd[PersenPMB]</b>%.<br />
      Jadi jumlah minimal yang sudah harus dibayar mahasiswa adalah: Rp. <b>$_minimal</b>
      <hr size=1 color=silver>
      Pilihan: <input type=button name='Proses' value='Lanjutkan Proses NPM dengan Dispensasi' onClick=\"location='?mnux=mhswbaru&gos=Dispensasi&pmbid=$cama[PMBID]'\">
      ");
    }
  }
  else echo Konfirmasi("Prosedur Proses",
    "Anda dapat memproses calon mahasiswa ini karena telah melunasi pembayarannya.
    <hr size=1 color=silver>
    <form action='?' method='POST' onSubmit=\"return CheckForm(this);\">
    <input type=hidden name=mnux value=mhswbaru>
    <input type=hidden name=gos value=ImprtPMB1>
    <input type=hidden name=trm value=$cama[PMBID]>
    Angkatan Masuk (mis: 20071) : <input type=text maxlength=5 size=7 name=TahunNIM value='$_SESSION[TahunNIM]'>  <br />
    Proses Generate NPM : <input type=submit name=submit value=Generate NIM>
    </form>"); 
    //Pilihan: <input type=button name='Proses' value='Proses NPM' onClick=\"location='?mnux=mhswbaru&gos=ImprtPMB1&trm=$cama[PMBID]'\">
    
  echo "</blockquote>";
}

function TampilkanBayarCAMA($cama) {
  include_once "mhswkeu.lib.php";
  $thn = $cama['PMBPeriodID'];
  $krg = number_format($cama['TotalBiayaMhsw'] - $cama['TotalSetoranMhsw'], 0);
  $tgl = (empty($cama['TanggalSetoranMhsw']))? date('Y-m-d') : $cama['TanggalSetoranMhsw'];
  $_tgl = GetDateOption($tgl, 'TanggalSetoranMhsw');
  echo "<p><a name='stp2b'></a><h3>Step 2b. Cetak dan Masukkan Bukti Setoran Pembayaran</h3></p> <hr size=1 color=silver />";
  echo "<blockquote>Jika calon mahasiswa telah membayar semua ketentuan biaya, maka masukkan nomer bukti setoran
  dan jumlah yang dibayarkan di sini. Jika ingin membayar berdasarkan installment/cicilan, gunakan fasilitas
  bayar pada data installment/cicilan di <a href='#stp2a'>step 2a</a>.</blockquote>";
  // Tampilkan fasilitas cetak BPM
  $khs = array(); $khs['KHSID'] = 0;
  echo "<blockquote>".TampilkanCetakBPM($cama, $khs, 0)."</blockquote>";
  // Ambil data
  $s = "select byrm.*, date_format(byrm.Tanggal, '%d/%m/%Y') as TGL,
    format(byrm.Jumlah, 0) as JML
    from bayarmhsw byrm
    where byrm.PMBID='$cama[PMBID]' and byrm.TahunID='$thn'
      and byrm.PMBMhswID=0
    order by byrm.BayarMhswID";
  $r = _query($s);
  $tot = 0;
  echo "<blockquote>
    <a href='?mnux=mhswbaru&gos=ImprtPMB&pmbid=$cama[PMBID]&trm=$cama[PMBID]'>Refresh Data</a>
    </blockquote>";
  echo "<blockquote><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>No Kwi</th>
    <th class=ttl>No Bukti<br />Setoran</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Proses</th>
    <th class=ttl>Kwitansi</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $tot += $w['Jumlah'];
    //?mnux=bpm&gos=BayarBPM&mhswid=2006-10-0001&crmhswid=2006-10-0001&khsid=2&bpmid=20060010&gosto=BPMMhsw
    $prc = ($w['Proses'] == 0)? 'class=ul' : 'class=nac';
    $byr = ($w['Proses'] == 0)? "<a href='?mnux=mhswbaru&gos=BayarBPM&pmbid=$w[PMBID]&trmid=$w[PMBID]&crmhswid=$w[PMBID]&khsid=0&bpmid=$w[BayarMhswID]&gosto=ImprtPMB&pmbmhswid=0'><img src='img/check.gif'></a>" : "&nbsp;";
    echo "<tr>
      <td $prc>$w[BayarMhswID]</td>
      <td $prc>$w[BuktiSetoran]&nbsp;</td>
      <td $prc>$w[TGL]</td>
      <td $prc align=right>$w[JML]</td>
      <td $prc align=center>$byr</td>
      <td $prc align=center><img src='img/printer.gif'></td>
      </tr>";
  }
  $_tot = number_format($tot);
  $cl = ($cama['TotalBiayaMhsw'] > $tot)? 'class=wrn' : 'class=ul';
  echo "<tr><td colspan=3 align=right>Total :</td><td $cl align=right><b>$_tot</td></tr>
  </table></blockquote>";
}

function CekBIPOT($w) {
  $s0 = "select b2.*, bn.Nama
    from bipot2 b2
    left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where b2.BIPOTID='$w[BIPOTID]' and b2.SaatID=1 
    and b2.NA='N' and b2.Otomatis='Y'
      and INSTR(b2.StatusAwalID, '.$w[StatusAwalID].')>0
    order by b2.Prioritas";
  $r0 = _query($s0);
  $thn = $w['PMBPeriodID'];
  while ($w0 = _fetch_array($r0)) {
    $sdh = GetFields('bipotmhsw',"PMBMhswID=0 and TahunID='$thn' and PMBID='$w[PMBID]' and BIPOT2ID", $w0['BIPOT2ID'], "*");
    if ($w0['GunakanScript'] == 'Y') {
      $khs = array();
      $khs['TahunID'] = $thn;
      $khs['Sesi'] = 1;
      InsertBIPOTScript1($w, $khs, $w0, $sdh);
    }
    else if (empty($sdh)) {
      $ada = GetFields('bipotmhsw', "PMBID = '$w[PMBID]' and BipotNamaID", $w0['BipotNamaID'], '*');
      if (empty($ada)) {
	if ($w0['GunakanGradeNilai'] == 'Y') {
	  if (strpos($w0['GradeNilai'], ".$w[GradeNilai].") === false) {}
	  else InsertBIPOT($w, $w0);
	} else InsertBIPOT($w, $w0);
      } else {
	$s1 = "update bipotmhsw set Besar='$w0[Jumlah]', Bipot2ID = '$w0[BIPOT2ID]', LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where BIPOTMhswID='$ada[BIPOTMhswID]'";
	$r1 = _query($s1);
      }
    }
  }
  
  $byr = GetaField('bayarmhsw', "TrxID=1 and TahunID='$thn' and PMBID", 
      $w['PMBID'], "sum(Jumlah)")+0;
  if ($byr != 0) {
    //ProsesBipotMhswPMB($byr, $w['PMBID'], $thn);
  } 
  // update total biaya
  $TOTBIA = GetaField('bipotmhsw',
    "PMBMhswID=0 and TahunID='0' and PMBID", $w['PMBID'], "sum(Jumlah*Besar)") +0;
  $sbi = "update pmb set TotalBiayaMhsw=$TOTBIA where PMBID='$w[PMBID]' ";
  $rbi = _query($sbi);
}
function ProsesBipotMhswPMB($byr, $PMBID, $thn){
  $s = "select BipotNamaID, (Jumlah * Besar) as JML, BipotMhswID from
        bipotmhsw
        where TahunID='$thn' 
        and TrxID = 1 
        and PMBID = '$PMBID'";
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
function InsertBIPOT($w, $w0) {
  $thn = $w['PMBPeriodID'];
  $s = "insert into bipotmhsw (PMBID, BIPOTNamaID, BIPOT2ID, TrxID,
    PMBMhswID, TahunID,
    Jumlah, Besar,
    LoginBuat, TanggalBuat)
    values ('$w[PMBID]', '$w0[BIPOTNamaID]', '$w0[BIPOT2ID]', '$w0[TrxID]',
    0, '$thn',
    '1', '$w0[Jumlah]',
    '$_SESSION[_Login]', now())";
  $r = _query($s);
}
function InsertBIPOTScript1($mhsw, $khs, $bipot, $ada) {
  include "script/$bipot[NamaScript].php";
  //echo "<h1>$bipot[NamaScript]</h1>";
  $khs['TahunID'] = $mhsw['PMBPeriodID'];
  $khs['Sesi'] = 1;
  //$mhsw['MhswID'] = $mhsw['PMBID'];
  $bipot['NamaScript']($mhsw, $khs, $bipot, $ada, 0);
}
function ImprtPMB1() {
  $trm = $_REQUEST['trm'];
  if (!empty($trm)) {
    $w = GetFields('pmb', 'PMBID', $trm, '*');
    //$w['BIPOTID'] = $_REQUEST['bpt'];
    if (empty($w['NIM'])) {
      if (!empty($w['ProdiID'])) {
        $MhswID = ImportPMB($w, $_SESSION['TahunNIM']);
        // Tampilkan pesan
        echo Konfirmasi("Proses Import Berhasil",
        "Data PMB <b>$w[PMBID]</b> telah berhasil diimport.<br>
        Calon telah menjadi mahasiswa dengan NIM: <b>$MhswID</b>.
        <hr size=1 color=silver>
        Pilihan: <a href='?mnux=mhswakd&gos=MhswAkdEdt&mhswid=$MhswID'>Edit Data Akademik Mahasiswa</a>");
      }
      else echo ErrorMsg("Proses Import Gagal",
      "Calon mahasiswa belum ditentukan program studinya.<br />
      Hubungi bagian admisi.");
    }
    else echo ErrorMsg('Proses Import Gagal',
      "Calon telah menjadi mahasiswa atau tidak lulus ujian.<br>
      Periksa data PMB calon tersebut.");
  }
  else echo ErrorMsg('Proses Import Gagal', "Tidak dapat mengimport data PMB.");

  SrcPMB();
}
function Dispensasi() {
  $w = GetFields('pmb', 'PMBID', $_REQUEST['pmbid'], '*');
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=400>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='mhswbaru'>
  <input type=hidden name='gos' value='DispensasiSav'>
  <input type=hidden name='pmbid' value='$w[PMBID]'>
  <input type=hidden name='trm' value='$w[PMBID]'>

  <tr><th class=ttl colspan=2>Dispensasi Pembayaran</th></tr>
  <tr><td class=ul colspan=2>Surat dispensasi akan dicek apakah memang benar untuk
    calon mahasiswa yang bersangkutan.</td></tr>
  <tr><td class=inp1>No PMB</td><td class=ul>$w[PMBID]</td></tr>
  <tr><td class=inp1>Nama</td><td class=ul>$w[Nama]</td></tr>
  <tr><td class=inp1>Nomer Surat Dispensasi</td><td class=ul><input type=text name='DispensasiID' value='$w[DispensasiID]' size=40 maxlength=50></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=mhswbaru'\"></td></tr>
  </table></p>";
}
function DispensasiSav() {
  $pmbid = $_REQUEST['pmbid'];
  $cama = GetFields('pmb', 'PMBID', $pmbid, "PMBID, Nama");
  $DispensasiID = $_REQUEST['DispensasiID'];
  $ada = GetFields('dispensasi', 'DispensasiID', $DispensasiID, '*');
  if (!empty($ada)) {
    if ($ada['MhswID'] != $pmbid)
      echo ErrorMsg("Dispensasi Gagal",
      "Surat Dispensasi ini tidak diperuntukkan bagi No PMB: <b>$pmbid</b>");
    else {
      $ket = $ada['Keterangan'];
      $ket = str_replace(chr(13), "<br />", $ket);
      echo Konfirmasi("Proses Dispensasi?",
      "Surat Dispensasi telah sesuai untuk calon mahasiswa ini.<br />
      <p><table class=box cellspacing=1 cellpadding=4>
      <tr><td class=inp1>No. PMB</td><td class=ul>$cama[PMBID]</td></tr>
      <tr><td class=inp1>Nama</td><td class=ul>$cama[Nama]</td></tr>
      </table></p>

      <p>Berikut adalah keterangan dalam surat dispensasi:</p>
      <p><table class=box cellspacing=1 cellpadding=4 width=100%>
      <tr><td class=inp1>$ket</td></tr>
      </table></p>
      <p>Harap calon mahasiswa memenuhi dan mematuhi ketentuan yang tertuang dalam
      surat dispensasi ini.</p>
      <hr size=1 color=silver>
      Pilihan: <input type=button name='Proses' value='Proses Dispensasi'
        onClick=\"location='?mnux=mhswbaru&gos=DispensasiSav1&pmbid=$pmbid&trm=$pmbid&DispensasiID=$DispensasiID'\"> |
      <input type=button name='Batal' value='Batal Proses' onClick=\"location='?mnux=mhswbaru'\">");
    }
  }
  else {
    echo ErrorMsg("Dispensasi Gagal",
    "Dispensasi dengan nomer: <b>$DispensasiID</b> tidak ditemukan.");
    Dispensasi();
  }
}
function DispensasiSav1() {
  $pmbid = $_REQUEST['pmbid'];
  $DispensasiID = $_REQUEST['DispensasiID'];
  $ada = GetFields('dispensasi', 'DispensasiID', $DispensasiID, '*');

  $s = "update pmb set Dispensasi='Y',
    DispensasiID='$DispensasiID',
    JudulDispensasi='$ada[Judul]',
    CatatanDispensasi='$ada[Keterangan]'
    where PMBID='$pmbid' ";
  $r = _query($s);
  ImprtPMB();
}
function TampilkanInstallment($cm) {
  echo "<p><a name='stp2a'></a><h3>Step 2a. Setup Installment (Cicilan Pembayaran)</h3></p> <hr size=1 color=silver />";
  echo "<blockquote>Jika calon mahasiswa tidak membayar lunas semua biaya, calon mahasiswa dapat
    mengajukan permohonan pengangsuran pembayaran.
    Berikut adalah perencanaan pembayaran dari semua biaya:</blockquote>";
  $s = "select *,
    format(Jumlah, 0) as JML,
    date_format(DariTanggal, '%d/%m/%Y') as DR,
    date_format(SampaiTanggal, '%d/%m/%Y') as SMP
    from cicilanmhsw
    where PMBID='$cm[PMBID]' and PMBMhswID=0 and TahunID='0'
    order by Urutan";
  $r = _query($s);
  echo "<blockquote><a href='?mnux=mhswbaru.cicilan&pmbid=$cm[PMBID]'>Edit Installment/Cicilan</a></blockquote>";
  echo "<blockquote><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>No</th>
    <th class=ttl rowspan=2>Bayar</th>
    <th class=ttl rowspan=2>Judul</th>
    <th class=ttl rowspan=2>Jumlah</th>
    <th class=ttl colspan=2>Dibayarkan</th>
    <th class=ttl rowspan=2>Keterangan</th>
    </tr>
    <tr><th class=ttl>Dari</th><th class=ttl>Sampai</th></tr>";
  while ($w = _fetch_array($r)) {
    $ttl += $w['Jumlah'];
    if ($w['SudahDibayar'] == 'Y') {
      $lnkbyr = "<img src='img/Y.gif'>";
      $c = 'class=nac';
    }
    else {
      $lnkbyr = "<a href='?mnux=mhswbaru&gos=BayarEdt&md=1&CicilanID=$w[CicilanID]&pmbid=$cm[PMBID]'>Bayarkan</a>";
      $c = 'class=ul';
    }
    echo "<tr>
    <td class=inp1>$w[Urutan]</td>
    <td class=ul align=center>$lnkbyr</td>
    <td $c>$w[Judul]</td>
    <td $c align=right>$w[JML]</td>
    <td $c>$w[DR]</td>
    <td $c>$w[SMP]</td>
    <td $c>$w[Keterangan]&nbsp;</td>
    </tr>";
  }
  $_ttl = number_format($ttl, 0);
  echo "<tr><td class=ul colspan=3 align=right>Total :</td>
    <td class=ul align=right><b>$_ttl</td></tr>
    </table></blockquote>";
}

// *** Parameters ***
$pmbid = GetSetVar('pmbid');
$Cari = GetSetVar('Cari');
$TahunNIM = GetSetVar('TahunNIM');
$periode = GetSetVar('periode');
$gos = (empty($_REQUEST['gos']))? 'SrcPMB' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Mahasiswa Baru");
$gos();
?>
