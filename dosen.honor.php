<?php
// Author: Emanuel Setio Dewo
// March 2006
// www.sisfokampus.net

include_once "dosen.honor.lib.php";
//and d.Homebase='$_SESSION[prodi]'
//and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0

// *** Functions ***
function DftrDosenPerTahun() {
  $prd = ($_SESSION['prodi'] == '99')? "and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0" : "and j.ProdiID='.$_SESSION[prodi].'";
  $s = "select d.Login, d.Nama, d.IkatanID, concat(d.Nama, ', ', d.Gelar) as DSN, d.GolonganID, d.KategoriID,
    sd.Nama as StatusDSN, prd.Nama as Homebase, ikt.Besar, format(ikt.Besar, 0) as IKT,
    concat(gol.GolonganID, '-', gol.KategoriID) as CekGol,
    format(hd.TunjanganSKS, 0) as TSKS, 
    format(hd.TunjanganTransport, 0) as TTrans,
    format(hd.TunjanganTetap, 0) as TTtp,
    format(hd.Tambahan, 0) as TTamb,
    format(hd.Potongan, 0) as Pot,
    hd.*, j.prodiID 
    from presensi prs 
      left outer join jadwal j on prs.JadwalID=j.JadwalID
      left outer join dosen d on prs.DosenID=d.Login
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
      left outer join prodi prd on d.Homebase=prd.ProdiID
      left outer join golongan gol on d.GolonganID=gol.GolonganID and d.KategoriID=gol.KategoriID and d.Homebase=gol.ProdiID
      left outer join ikatan ikt on d.IkatanID=ikt.IkatanID
      left outer join honordosen hd on d.Login=hd.DosenID and hd.prodiID='$_SESSION[prodi]'
    where sd.HonorMengajar='Y' and d.NA='N'
      and hd.Tahun='$_SESSION[PeriodeTahun]' 
      and hd.Bulan='$_SESSION[PeriodeBulan]'
      and hd.Minggu='$_SESSION[PeriodeMinggu]'
      and prs.TahunID='$_SESSION[tahun]' 
      $prd
    group by prs.DosenID";
  $r = _query($s); $nmr = 0;
  //echo "<pre>$s</pre>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='dosen.honor.cetak.php' name='cetakhonor' method=POST target=_blank>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Dosen</th>
    <th class=ttl>Hadir/<br>Sem.</th>
    <th class=ttl colspan=2>Hitung</th>
    <th class=ttl>Status</th>
    <th class=ttl title='Golongan'>Gol</th>
    <th class=ttl title='Kategori'>Kat</th>
    <th class=ttl title='Tunjangan Ikatan'>Ikatan</th>
    <th class=ttl># Honor</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Trans</th>
    <th class=ttl>Paket</th>
    <th class=ttl>Tambahan</th>
    <th class=ttl>Potongan</th>
    <th class=ttl>Bruto</th>
    <th class=ttl>Pajak</th>
    <th class=ttl>Bersih</th>
    <th class=ttl><input type=submit name='Cetak' value='Cetak'></th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $nmr++;
    $htg = GetaField("presensi p left outer join jadwal j on p.JadwalID=j.JadwalID", 
      "p.DosenID='$w[Login]' and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0 and j.TahunID='$_SESSION[tahun]' and p.HonorDosenID='$w[HonorDosenID]' and p.hitung","Y", "count(PresensiID)")+0;
    $hdr = GetaField("presensi p left outer join jadwal j on p.JadwalID=j.JadwalID",
      "p.DosenID='$w[Login]' and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0 and j.TahunID", $_SESSION['tahun'], "count(p.JadwalID)")+0;
	if (empty($w['HonorDosenID'])) {
      $edt = "<a href='?mnux=dosen.honor&gos=HondosAdd&DosenID=$w[Login]'>Buat</a>";
      $ctk = "&nbsp;";
      $prs = "&nbsp;";
    }
    else {
      $edt = "<a href='?mnux=dosen.honor&gos=HondosEdt&Hondos=$w[HonorDosenID]'>$w[HonorDosenID] <img src='img/edit.png'></a>";
      $ctk = "<input type=checkbox name='Hondos[]' value='$w[HonorDosenID]' checked>
        <a href='dosen.honor.cetak.php?Hondos[]=$w[HonorDosenID]' target=_blank><img src='img/printer.gif'></a>";
      if ($w['Cetak'] > 0)
        $prs = "<a href='dosen.honor.cetak.php?Hondos[]=$w[HonorDosenID]' target=_blank title='Lihat Detail'><img src='img/check.gif'></a>";
      else
        $prs = "<a href='?mnux=dosen.honor&gos=HonDet&HonorDosenID=$w[HonorDosenID]&DosenID=$w[Login]' title='Edit Perhitungan'><img src='img/edit.png'></a>";
    }
    $bruto = $w['TunjanganSKS'] + $w['TunjanganTransport'] + $w['TunjanganTetap'] +
      $w['Tambahan'] + $w['Potongan'];
    $_bruto = number_format($bruto);
    $pajak = ($bruto * $w['Pajak']/100);
    $_pajak = number_format($pajak);
    $bersih = $bruto - $pajak;
    $_bersih = number_format($bersih);
    echo "<tr><td class=inp1>$nmr</td>
      <td class=ul nowrap>$w[Login]</td>
      <td class=ul>$w[DSN]</td>
      <td class=ul align=right>&nbsp;$hdr</td>
      <td class=ul align=right title='Hitung Absensi'>$prs</td>
      <td class=ul align=right>$htg</td>
      <td class=ul>$w[StatusDSN]</td>
      <td class=ul>$w[GolonganID]</td>
      <td class=ul>$w[KategoriID]</td>
      <td class=ul align=right>&nbsp;$w[IKT]</td>
      <td class=inp>$edt</td>
      <td class=ul align=right>$w[TSKS]</td>
      <td class=ul align=right>$w[TTrans]</td>
      <td class=ul align=right>$w[TTtp]</td>
      <td class=ul align=right>$w[TTamb]</td>
      <td class=ul align=right>$w[Pot]</td>
      <td class=ul align=right>$_bruto</td>
      <td class=ul align=right>$_pajak</td>
      <td class=ul align=right>$_bersih</td>
      <td class=ul align=center>$ctk</td>
      </tr>";
  }
  echo "</form></table></p>";
}
function HonDet() {
  $dsn = GetFields("dosen d
    left outer join prodi prd on d.Homebase=prd.ProdiID", 
    "d.Login", $_SESSION['DosenID'], 
    "d.*, prd.Nama as PRD");
  $HonorDosenID = $_REQUEST['HonorDosenID'];
  // Tampilkan daftar
  $s = "select p.*, j.MKKode, j.Nama, j.SKS, j.SKSAsli, j.SKSHonor,
    h.Nama as HR, j.NamaKelas, j.JenisJadwalID,
    date_format(p.Tanggal, '%d/%m/%Y') as TGL,
    time_format(p.JamMulai, '%H:%i') as JM,
    time_format(p.JamSelesai, '%H:%i') as JS,
    format(p.TunjanganSKS, 0) as TSKS,
    format(p.TunjanganTransport, 0) as TTrans,
    format(p.TunjanganTetap, 0) as TTtp
    from presensi p
      left outer join jadwal j on p.JadwalID=j.JadwalID
      left outer join hari h on date_format(p.Tanggal, '%w')=h.HariID
    where p.DosenID='$_SESSION[DosenID]' 
      and p.Tanggal <= '$_SESSION[TglSelesai]'
      and j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
      and (p.HonorDosenID='$HonorDosenID' or p.HonorDosenID=0)
      order by p.Tanggal, p.JamMulai";
 // echo "<pre>$s</pre>";
  $r = _query($s); $nmr = 0;
  $HSKS = 0; $HTrans = 0; $HTtp = 0;
  $belum = array();
  $isi = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl rowspan=2>#</th>
      <th class=ttl colspan=4>Perkuliahan</th>
      <th class=ttl colspan=3>Matakuliah</th>
      <th class=ttl colspan=2>SKS</th>
      <th class=ttl rowspan=2>Hitung?</th>
      <th class=ttl colspan=3>Honor</th>
      <th class=ttl rowspan=2>Edt</th>
      </tr>
    <tr><th class=ttl>Hari</th>
      <th class=ttl>Tanggal</th>
      <th class=ttl>Jam</th>
      <th class=ttl>Kode</th>
      <th class=ttl>Nama</th>
      <th class=ttl>Kelas</th>
      <th class=ttl>Jenis</th>
      <th class=ttl>Asli</th>
      <th class=ttl>Honor</th>
      <th class=ttl>SKS</th>
      <th class=ttl>Transport</th>
      <th class=ttl>Paket</th>
      </tr>";
  while ($w = _fetch_array($r)) {
    $nmr++;
    if ($w['Hitung'] == 'N') $belum[] = $w['PresensiID'];
    $c = ($w['Hitung'] == 'Y')? 'class=ul' : 'class=nac';
    $ck = ($w['Hitung'] == 'Y')? 'checked' : '';
    if ($w['Hitung'] == 'Y') {
      $HSKS += $w['SKSHonor'] * $w['TunjanganSKS'];
      $HTrans += $w['TunjanganTransport'];
      $HTtp += $w['TunjanganTetap'];
    }
    //$cek = GetFields('presensi', "Hitung = 'N' and Tanggal <", $_SESSION['TglSelesai'], '*');
    $isi .= "<tr><td class=inp>$nmr</td>
    <td $c>$w[HR]</td>
    <td $c>$w[TGL]</td>
    <td $c>$w[JM]-$w[JS]</td>
    <td $c>$w[MKKode]</td>
    <td $c>$w[Nama]</td>
    <td $c>$w[NamaKelas]&nbsp;</td>
    <td $c>$w[JenisJadwalID]</td>
    <td $c align=right>$w[SKSAsli]</td>
    <td $c align=right>$w[SKSHonor]</td>
    
    <form action='?' method=GET>
    <input type=hidden name='mnux' value='dosen.honor'>
    <input type=hidden name='gos' value='HonCek'>
    <input type=hidden name='PresensiID' value='$w[PresensiID]'>
    <input type=hidden name='HonorDosenID' value='$HonorDosenID'>
    <td class=ul align=center><input type=checkbox name='Hitung' value='Y' $ck onChange='this.form.submit()'></td>
    </form>
    
    <td $c align=right>$w[SKSHonor]x $w[TSKS]</td>
    <td $c align=right>$w[TTrans]</td>
    <td $c align=right>$w[TTtp]</td>
    <td $c align=center><a href='?mnux=dosen.honor&gos=PresEdt&HonorDosenID=$HonorDosenID&PresensiID=$w[PresensiID]'><img src='img/edit.png'></a></td>
    </tr>";
  }
  $_SKS = number_format($HSKS);
  $_Trans = number_format($HTrans);
  $_Ttp = number_format($HTtp);
  $isi .= "<tr><td colspan=11 align=right>Total :</td>
    <td class=inp>$_SKS</td>
    <td class=inp>$_Trans</td>
    <td class=inp>$_Ttp</td>
    </tr>";
  $isi .= "</table></p>";
  // Untuk centang semua
  $HonCekAll = implode(',', $belum);
  // Buat header
  $cen = "
  <form action='?' method=POST name='SimpanSemua'>
  <input type=hidden name='mnux' value='dosen.honor'>
  <input type=hidden name='gos' value='HonCekAll'>
  <input type=hidden name='HonorDosenID' value='$HonorDosenID'>
  <input type=hidden name='HonCekAll' value='$HonCekAll'>
  <td class=inp>Pilihan</td><td class=ul><input type=submit name='Centang' value='Centang Semua'></td>
  </form>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td class=inp>Kode</td><td class=ul>$dsn[Login]</td>
      <td class=inp>Nama</td><td class=ul>$dsn[Nama], $dsn[Gelar]</td></tr>
    <tr><td class=inp>Homebase</td><td class=ul>$dsn[PRD]</td>
      <td class=inp>Golongan-Kategori</td><td class=ul>$dsn[GolonganID]-$dsn[KategoriID]</td></tr>
    <tr><td class=inp>Pilihan</td>
      <td class=ul colspan=1><input type=button name='DftrHonor' value='Daftar Kwitansi Honor' onClick=\"location='?mnux=dosen.honor&gos=DftrKwiHon'\">
      <input type=button name='Kembali' value='Kembali ke Daftar' onClick=\"location='?mnux=dosen.honor&gos='\">
      </td>
      $cen</tr>
    </table></p>";
  echo $isi;
}
function HonCek() {
  $Hitung = (empty($_REQUEST['Hitung']))? 'N' : $_REQUEST['Hitung'];
  $hdid = ($Hitung == 'N')? 0 : $_REQUEST['HonorDosenID']+0;
  $dsn = GetFields('dosen', 'Login', $_SESSION['DosenID'], 'GolonganID, KategoriID, Homebase');
  $gol = GetFields('golongan', "GolonganID='$dsn[GolonganID]' and KategoriID='$dsn[KategoriID]' and ProdiID",
    $dsn['Homebase'], '*');
  if (empty($gol) || $Hitung=='N') {
    $gol['TunjanganFungsional'] = 0;
    $gol['TunjanganSKS'] = 0;
    $gol['TunjanganTransport'] = 0;
    $gol['TunjanganTetap'] = 0;
  }
  // Update presensi
  $s = "update presensi set Hitung='$Hitung', HonorDosenID=$hdid,
    TunjanganSKS='$gol[TunjanganSKS]', TunjanganTransport='$gol[TunjanganTransport]',
    TunjanganTetap='$gol[TunjanganTetap]'
    where PresensiID='$_REQUEST[PresensiID]' ";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  // Update Honor
  $honor = GetFields("presensi prs
    left outer join jadwal j on prs.JadwalID=j.JadwalID", 
    "prs.Hitung='Y' and prs.HonorDosenID", $hdid, 
    "sum(j.SKSHonor * prs.TunjanganSKS) as TSKS, sum(prs.TunjanganTransport) as TTrans,
    sum(prs.TunjanganTetap) as TTtp");
  $s1 = "update honordosen set TunjanganSKS='$honor[TSKS]',
    TunjanganTransport='$honor[TTrans]', TunjanganTetap='$honor[TTtp]'
    where HonorDosenID='$_REQUEST[HonorDosenID]' ";
  $r1 = _query($s1);
  if ($_REQUEST['_HonCekAll'] == 0) HonDet();
}
function HonCekAll() {
  $HonCekAll = $_REQUEST['HonCekAll'];
  if (!empty($HonCekAll)) {
    //$Hitung = (empty($_REQUEST['Hitung']))? 'N' : $_REQUEST['Hitung'];
    $_REQUEST['Hitung'] = 'Y';
    $_id = explode(',', $HonCekAll);
    foreach ($_id as $id) {
    	$_REQUEST['PresensiID'] = $id;
    	$_REQUEST['_HonCekAll'] = 1;
    	//echo "$id<br />";
    	HonCek();
    }
  }
  else echo ErrorMsg("Tidak Ada yg Diset", "Tidak ada presensi yang harus dicentang.");
  HonDet();
}
function PresEdt() {
  $pres = GetFields('presensi', 'PresensiID', $_REQUEST['PresensiID'], '*');
  $jdwl = GetFields('jadwal', 'JadwalID', $pres['JadwalID'], '*');
  $ck = ($pres['Hitung'] == 'Y')? 'checked' : '';
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='PresensiID' value='$_REQUEST[PresensiID]'>
  <input type=hidden name='mnux' value='dosen.honor'>
  <input type=hidden name='gos' value='PresSav'>
  
  <tr><td class=ul colspan=2><b>Edit Honor<?td></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - <b>$jdwl[Nama]</b> $jdwl[NamaKelas]</b></td></tr>
  <tr><td class=inp>SKS</td><td class=ul><b>$jdwl[SKS]</b> dari $jdwl[SKSAsli] SKS</td></tr>
  <tr><td class=inp>SKS honor</td><td class=ul><input type=text name='SKSHonor' value='$jdwl[SKSHonor]' size=3 maxlength=3> Berlaku untuk matakuliah ini</td></tr>
  <tr><td class=ul colspan=2><b>Honor</b></td></tr>
  <tr><td class=inp>» Hitung?</td><td class=ul><input type=checkbox name='Hitung' value='Y' $ck></td></tr>
  <tr><td class=inp>» SKS</td><td class=ul><input type=text name='TunjanganSKS' value='$pres[TunjanganSKS]' size=15 maxlength=20></td></tr>
  <tr><td class=inp>» Transport</td><td class=ul><input type=text name='TunjanganTransport' value='$pres[TunjanganTransport]' size=15 maxlength=20></td></tr>
  <tr><td class=inp>» Paket</td><td class=ul><input type=text name='TunjanganTetap' value='$pres[TunjanganTetap]' size=15 maxlength=20></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=dosen.honor&gos=HonDet'\"></td></tr>
  
  </form></table></p>";
}
function PresSav() {
  $SKSHonor = $_REQUEST['SKSHonor']+0;
  if (empty($_REQUEST['Hitung'])) {
    $Hitung = 'N';
    $TunjanganSKS = 0;
    $TunjanganTransport = 0;
    $TunjanganTetap = 0;
  }
  else {
    $Hitung = 'Y';
    $TunjanganSKS = $_REQUEST['TunjanganSKS']+0;
    $TunjanganTransport = $_REQUEST['TunjanganTransport']+0;
    $TunjanganTetap = $_REQUEST['TunjanganTetap']+0;
  }
  // update presensi
  $s = "update presensi set TunjanganSKS='$TunjanganSKS', Hitung='$Hitung',
    TunjanganTransport='$TunjanganTransport', TunjanganTetap='$TunjanganTetap'
    where PresensiID='$_REQUEST[PresensiID]' ";
  $r = _query($s);
  // update jadwal
  $JadwalID = GetaField('presensi', 'PresensiID', $_REQUEST['PresensiID'], 'JadwalID');
  $s = "update jadwal set SKSHonor='$SKSHonor' where JadwalID='$JadwalID' ";
  $r = _query($s);
  HonDet();
}
function HonDosPros() {
  $prd = ($_SESSION['prodi'] == '99')? "and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0" : "and j.ProdiID='.$_SESSION[prodi].'";
  $tahun = $_SESSION['tahun'];
  $prodi = $_SESSION['prodi'];
  $PeriodeTahun = $_SESSION['PeriodeTahun'];
  $PeriodeBulan = $_SESSION['PeriodeBulan'];
  $PeriodeMinggu = $_SESSION['PeriodeMinggu'];
  $TglMulai = $_SESSION['TglMulai'];
  $TglSelesai = $_SESSION['TglSelesai'];
  // Ambil daftar dosen
  $s = "select d.Login, d.Nama, d.IkatanID, d.GolonganID, d.KategoriID,
    ikt.Besar, gol.TunjanganFungsional, gol.TunjanganSKS,
    gol.TunjanganTransport, gol.TunjanganTetap
    from presensi prs
      left outer join dosen d on prs.DosenID=d.Login
      left outer join jadwal j on prs.JadwalID=j.JadwalID
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
      left outer join prodi prd on d.Homebase=prd.ProdiID
      left outer join golongan gol on d.GolonganID=gol.GolonganID and d.KategoriID=gol.KategoriID and d.Homebase=gol.ProdiID
      left outer join ikatan ikt on d.IkatanID=ikt.IkatanID
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
      and d.NA='N' 
      and prs.TahunID='$tahun'
	  $prd
    group by prs.DosenID";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  //exit;
  while ($w = _fetch_array($r)) {
    //$tot = HitungJumlahSKSMengajar($w['Login'], $tahun, $TglMulai, $TglSelesai);
    $hdr = GetFields("presensi p left outer join jadwal j on p.JadwalID=j.JadwalID",
      "p.DosenID='$w[Login]' and ('$TglMulai' <= p.Tanggal and p.Tanggal <= '$TglSelesai')
      $prd and j.TahunID", $_SESSION['tahun'], 
      "count(PresensiID) as HDR");
    $TSKS = $hdr['TSKS']+0;
    $HDR = $hdr['HDR']+0;
    $TTrans = $hdr['TTrans']+0;
    $TTtp = $hdr['TTtp']+0;
    $ada = GetFields("honordosen", "DosenID = '$w[Login]' and TahunID = '$_SESSION[tahun]' and Bulan = '$PeriodeBulan' and ProdiID", $_SESSION['prodi'], "*");
    if (empty($ada)) {
    $s1 = "insert into honordosen (TahunID, ProdiID, Tahun, Bulan, Minggu,
      Tanggal, TanggalMulai, TanggalSelesai, DosenID,
      TunjanganSKS, 
      TunjanganTransport, 
      TunjanganTetap,
      Tambahan, Potongan, Pajak, 
      LoginBuat, TanggalBuat)
      values ('$tahun', '$prodi', '$PeriodeTahun', '$PeriodeBulan', '$PeriodeMinggu',
      now(), '$TglMulai', '$TglSelesai', '$w[Login]',
      $TSKS, 
      $TTrans, 
      $TTtp,
      0, 0, 5, '$_SESSION[_Login]', now() )";
    //echo "<pre>$s1</pre>";
    $r1 = _query($s1);
    }
  }
  // Kembali ke tampilan awal
  DftrDosenPerTahun();
}
function HitungJumlahSKSMengajar($did, $thn, $TM, $TS) {
  $s = "select j.JadwalID, j.MKKode, j.SKSHonor, 
    count(p.PresensiID) as HDR, sum(j.SKSHonor) as TOTSKS
    from presensi p
      left outer join jadwal j on p.JadwalID=j.JadwalID
    where p.Hitung='Y'
      and ('$TM' <= p.Tanggal and p.Tanggal <= '$TS')
      and p.TahunID='$thn'
    group by j.JadwalID";
  $r = _query($s); $n = 0;
  $_tot = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_tot = $w['HDR'] * $w['TOTSKS'];
  }
  return $_tot+0;
}
// fungsi utk mengedit honor dosen
function HondosEdt() {
  global $arrBulan;
  $HondosID = $_REQUEST['Hondos'];
  $hd = GetFields('honordosen', "HonorDosenID", $HondosID, "*");
  $dsn = GetFields("dosen d
    left outer join prodi prd on d.Homebase=prd.ProdiID
    left outer join fakultas fak on prd.FakultasID=fak.FakultasID
    left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID",
    "d.Login", $hd['DosenID'],
    "d.*, prd.Nama as PRD, fak.Nama as FAK, d.StatusDosenID, sd.Nama as SD");
  // Tampilkan formulir

  $hdr = HeaderHonorDosenDetail($dsn, $hd);
  echo $hdr;
  $tambahan = AmbilTambahan($dsn, $hd);
  $tun = $hd['TunjanganJabatan1'] + $hd['TunjanganJabatan2'] + 
    $hd['TunjanganFungsional'] + $hd['TunjanganSKS'] +
    $hd['TunjanganTransport'] + $hd['TunjanganTetap'];
  $_tun = number_format($tun);
  $_tam = number_format($hd['Tambahan']);
  $_pot = number_format($hd['Potongan']);
  $SebelumPajak = $tun + $hd['Tambahan'] + $hd['Potongan'];
  $SetelahPajak = $SebelumPajak - ($SebelumPajak * $hd['Pajak'] / 100);
  $_TRM = number_format($SetelahPajak);
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td valign=top>
   
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='t1' method=POST>
    <input type=hidden name='mnux' value='dosen.honor'>
    <input type=hidden name='gos' value='HondosEdt'>
    <input type=hidden name='Hondos' value='$hd[HonorDosenID]'>
    <input type=hidden name='slnt' value='dosen.honor.sav'>
    <input type=hidden name='slntx' value='TunjanganSav'>

  <tr><td class=ul colspan=2><b>Tunjangan</b></td></tr>
  <tr><td class=inp>Jabatan 1</td>
    <td class=ul><input type=text name='TunjanganJabatan1' value='$hd[TunjanganJabatan1]' size=20 maxlength=25></tr>
  <tr><td class=inp>Jabatan 2</td>
    <td class=ul><input type=text name='TunjanganJabatan2' value='$hd[TunjanganJabatan2]' size=20 maxlength=25></tr>
  <tr><td class=inp>SKS</td>
    <td class=ul><input type=text name='TunjanganSKS' value='$hd[TunjanganSKS]' size=20 maxlength=25></tr>
  <tr><td class=inp>Transport</td>
    <td class=ul><input type=text name='TunjanganTransport' value='$hd[TunjanganTransport]' size=20 maxlength=25></tr>
    
  <tr><td class=inp1>Total Tunjangan</td><td class=ul align=right>$_tun</td></tr>
  <tr><td class=inp1>Total Tambahan</td><td class=ul align=right>$_tam</td></tr>
  <tr><td class=inp1>Total Potongan</td><td class=ul align=right>$_pot</td></tr>
  <tr><td class=inp1>Sebelum pajak</td><td class=ul align=right>$SebelumPajak</td></tr>
  <tr><td class=inp1>Pajak</td><td class=ul><input type=input name='Pajak' value='$hd[Pajak]' size=3 maxlength=2> %</td></tr>
  <tr><td class=inp1><b>Gaji Diterima</b></td><td class=ul align=right><b>$_TRM</b></td></tr>
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=dosen.honor'\"></td></tr>
  </form></table></p>
  </td>
  <td valign=top>
  $tambahan
  </td></tr>
  </table></p>";
}
function HeaderHonorDosenDetail($dsn, $hd) {
  $TM = FormatTanggal($hd['TanggalMulai']);
  $TS = FormatTanggal($hd['TanggalSelesai']);
  $bln = $arrBulan[$hd['Bulan']];
  echo "
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td colspan=4 class=ul><b>Edit Honor Dosen</td></tr>
  <tr><td class=inp>No Honor</td><td class=ul><b>$hd[HonorDosenID]</td>
    <td class=inp>Tahun Akademik</td><td class=ul>$hd[TahunID]</td></tr>
  <tr><td class=inp>Nama</td><td class=ul>$dsn[Nama], $dsn[Gelar]</td>
    <td class=inp>Kode Dosen</td><td class=ul>$dsn[Login]</td></tr>
  <tr><td class=inp>Periode</td><td class=ul>$bln $hd[Tahun]</td>
    <td class=inp>Tanggal</td><td class=ul>$TM - $TS</td></tr>
  <tr><td class=inp>Fak/Jur</td><td class=ul>$dsn[FAK]/$dsn[PRD]</td>
    <td class=inp>Status</td><td class=ul>$dsn[SD]</td></tr>
  </table><p>
  ";
}
function AmbilTambahan($dsn, $hd) {
  $Tambahan = number_format($hd['Tambahan']);
  $Potongan = number_format($hd['Potongan']);
  // Ambil tambahan
  $s = "select *, format(Besar, 0) as BSR
    from honordosentambahan
    where HonorDosenID='$hd[HonorDosenID]' and Besar>0
    order by Nama";
  $r = _query($s); $n=0;
  $a = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><td colspan=2 class=ul><b>Detail Tambahan</td></tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $a .= "<tr><td class=inp>$n. $w[Nama]</td>
      <td class=ul align=right>$w[BSR]</td></tr>";
  }
  $a .= "<tr><td class=inp1 align=right><b>Total :</b></td>
    <td class=inp2 align=right><b>$Tambahan</b></td></tr>";
  // Ambil potongan
  $a .= "<tr><td colspan=2 class=ul><b>Detail Potongan</td></tr>";
  $s1 = "select *, format(Besar, 0) as BSR
    from honordosentambahan
    where HonorDosenID='$hd[HonorDosenID]' and Besar<0
    order by Nama";
  $r1 = _query($s1); $n = 0;
  while ($w1 = _fetch_array($r1)) {
    $a .= "<tr><td class=inp>$n. $w1[Nama]</td>
      <td class=ul align=right>$w1[BSR]</td></tr>";
  }
  $a .= "<tr><td class=inp1 align=right><b>Total :</b></td>
    <td class=inp2 align=right><b>$Potongan</b></td></tr>";
  // Tampilkan
  $opt = GetOption2("honortambahan", "Nama", "Nama", '', '', "HonorTambahanID");
  
  return $a . "<form action='?' name='tmbh' method=POST>
    <input type=hidden name='mnux' value='dosen.honor'>
    <input type=hidden name='gos' value='HondosEdt'>
    <input type=hidden name='Hondos' value='$hd[HonorDosenID]'>
    <input type=hidden name='slnt' value='dosen.honor.sav'>
    <input type=hidden name='slntx' value='TambahanSav'>
    <tr><td class=ul colspan=2>Catatan:
      <ul> 
      <li>Isikan dengan angka positif jika merupakan penambahan.</li>
      <li>Isikan dengan angka negatif jika merupakan potongan.</li>
      </ul>
    </td></tr>
    <tr><td class=inp1 colspan=2><b>Tambahan/Potongan</td></tr>
    <tr><td class=ul><select name='HonorTambahanID'>$opt</select></td>
      <td class=ul><input type=text name='Besar' value='0' size=20 maxlength=20> 
      <input type=submit name='Tambah' value='Tambahkan'></td></tr>
    </form>
    
    </table></p>";
}
function HondosAdd() {
  $DosenID = $_REQUEST['DosenID'];
  if (empty($DosenID)) die (ErrorMsg("Gagal Buat", "Data dosen tidak ditemukan."));
  $dsn = GetFields("dosen d
    left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
    left outer join prodi prd on d.Homebase=prd.ProdiID
    left outer join golongan gol on d.GolonganID=gol.GolonganID
      and d.KategoriID=gol.KategoriID
      and d.Homebase=gol.ProdiID
    left outer join ikatan ikt on d.IkatanID=ikt.IkatanID",
    "d.Login", $DosenID,
    "d.Login, d.Nama, d.IkatanID, d.GolonganID, d.KategoriID,
    ikt.Besar, gol.TunjanganFungsional, gol.TunjanganSKS,
    gol.TunjanganTransport, gol.TunjanganTetap");
  $s = "insert into honordosen (TahunID, ProdiID, Tahun, Bulan, Minggu,
    Tanggal, TanggalMulai, TanggalSelesai, DosenID,
    TunjanganSKS, TunjanganTransport, TunjanganTetap,
    Tambahan, Potongan, Pajak,
    LoginBuat, TanggalBuat)
    values ('$_SESSION[tahun]', '$_SESSION[prodi]', '$_SESSION[PeriodeTahun]', '$_SESSION[PeriodeBulan]', '$_SESSION[PeriodeMinggu]',
    now(), '$TglMulai', '$TglSelesai', '$DosenID',
    0, 0, 0, 0, 0, 5,
    '$_SESSION[_Login]', now())";
  $r = _query($s);
  // Kembali
  DftrDosenPerTahun();
}
function DftrKwiHon() {
  HeaderHonorDosen($_SESSION['DosenID']);
  $s = "select *
    from honordosen hd
    where hd.DosenID='$_SESSION[DosenID]'
      and hd.TahunID='$_SESSION[tahun]'
    order by hd.Tanggal";
  $r = _query($s); $_TOT = 0;
  echo "<p>Berikut adalah daftar kwitansi honor dosen untuk periode ini:</p>";
  echo "<p><table class=box cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Minggu</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Jumlah</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $TGL = FormatTanggal($w['Tanggal']);
    $TOT = $w['TunjanganJabatan1'] + $w['TunjanganJabatan2'] +
      $w['TunjanganSKS'] + $w['TunjanganTransport'] +
      $w['TunjanganTetap'] + $w['Tambahan'] - $w['Potongan'];
    $TOT1 = $TOT - ($TOT * $w['Pajak']/100);
    $_TOT += $TOT1;
    $strTOT1 = number_format($TOT1);
    echo "<tr>
      <td class=inp>$w[HonorDosenID]</td>
      <td class=ul>$w[Minggu]</td>
      <td class=ul>$TGL</td>
      <td class=ul align=right>$strTOT1</td>
      </tr>";
  }
  $_strTOT = number_format($_TOT);
  echo "<tr><td class=ul colspan=3 align=right>Total :</td>
    <td class=ul align=right><font size=+1>$_strTOT</font></td></tr>
  </table></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$DosenID = GetSetVar('DosenID');
$prodi = GetSetVar('prodi');
$PeriodeMinggu = GetSetVar('PeriodeMinggu', 'M1');
$PeriodeBulan = GetSetVar('PeriodeBulan', date('m'));
$PeriodeTahun = GetSetVar('PeriodeTahun', date('Y'));
// Tanggal Mulai
$TglMulai_d = GetSetVar('TglMulai_d', 11);
$TglMulai_m = GetSetVar('TglMulai_m', date('m')-1);
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
// Tanggal Selesai
$TglSelesai_d = GetSetVar('TglSelesai_d', 10);
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));

$TglMulai = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
$_SESSION['TglMulai'] = $TglMulai;
$TglSelesai = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";
$_SESSION['TglSelesai'] = $TglSelesai;

$gos = (empty($_REQUEST['gos']))? 'DftrDosenPerTahun' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Honor Dosen");
TampilkanHeaderHonorDosen('dosen.honor');
if (!empty($tahun)) $gos();
?>
