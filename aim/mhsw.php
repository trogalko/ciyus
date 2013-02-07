<?php
// Author: Emanuel Setio Dewo
// 28 May 2006
// http://www.sisfokampus.net

// *** Functions ***
function DM($mhsw) {
  $tglLahir = FormatTanggal($mhsw['TanggalLahir']);
  $foto = $mhsw['Foto'];
  $foto = (empty($foto))? "img/tux001.jpg" : $foto;
  $PA = GetaField('dosen', "Login", $mhsw['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
  echo "<p><table class=bsc>
  <tr><td class=ttl colspan=4>Data Pribadi</td></tr>
  <tr><td class=inp>Nomer Pokok Mhsw</td>
      <td class=ul>$mhsw[MhswID]</td>
      <td class=inp>Program</td>
      <td class=ul>$mhsw[ProgramID] - $mhsw[PRG]</td>
      <td class=bsc rowspan=20 valign=top><img src='../$foto' vspace=10 hspace=10 height=150 width=120></td>
      </tr>
  <tr><td class=inp>Nama Mhsw</td>
      <td class=ul>$mhsw[Nama]</td>
      <td class=inp>Program Studi</td>
      <td class=ul>$mhsw[ProdiID] - $mhsw[PRD]</td>
      </tr>
  <tr><td class=inp>Angkatan</td>
      <td class=ul>$mhsw[TahunID]</td>
      <td class=inp>Status Mhsw</td>
      <td class=ul>$mhsw[STA] &nbsp;</td>
      </tr>
  <tr><td class=inp>Batas Studi</td>
      <td class=ul>$mhsw[BatasStudi] &nbsp;</td>
      <td class=inp>Pembimbing Akademik</td>
      <td class=ul>$PA &nbsp;</td>
      </tr>
  <tr><td class=inp>Jenis Kelamin</td>
      <td class=ul>$mhsw[KEL] &nbsp;</td>
      <td class=inp>Agama</td>
      <td class=ul>$mhsw[AGM] &nbsp;</td>
      </tr>
  <tr><td class=inp>Tempat, Tgl Lahir</td>
      <td class=ul>$mhsw[TempatLahir], $tglLahir</td>
      <td class=inp>Status Perkawinan</td>
      <td class=ul>$mhsw[KWN] &nbsp;</td>
      </tr>
  <tr><td class=ttl colspan=4>Alamat</td></tr>
  <tr><td class=inp>E-mail</td>
      <td class=ul colspan=3>$mhsw[Email] &nbsp;</td>
      </tr>
  <tr><td class=inp># Telepon</td>
      <td class=ul>$mhsw[Telephone] &nbsp;</td>
      <td class=inp># Handphone</td>
      <td class=ul>$mhsw[Handphone] &nbsp;</td>
      </tr>
  <tr><td class=inp>Alamat</td>
      <td class=ul colspan=3>$mhsw[Alamat] &nbsp;</td>
      </tr>
  <tr><td class=inp>RT/RW</td>
      <td class=ul>$mhsw[RT]/$mhsw[RW]</td>
      <td class=inp>Kode Pos</td>
      <td class=ul>$mhsw[KodePos] &nbsp;</td>
      </tr>
  <tr><td class=inp>Kota</td>
      <td class=ul>$mhsw[Kota] &nbsp;</td>
      <td class=inp>Propinsi</td>
      <td class=ul>$mhsw[Propinsi] &nbsp;</td>
      </tr>
  <tr><td class=inp>Negara</td>
      <td class=ul colspan=3>$mhsw[Negara] &nbsp;</td>
      </tr>
  
  <tr><td class=ttl colspan=4>Orang Tua</td></tr>
  <tr><td class=inp>Nama Ayah</td>
      <td class=ul>$mhsw[NamaAyah] &nbsp;</td>
      <td class=inp>Nama Ibu</td>
      <td class=ul>$mhsw[NamaIbu] &nbsp;</td>
      </tr>
  <tr><td class=inp># Telepon</td>
      <td class=ul>$mhsw[TeleponOrtu] &nbsp;</td>
      <td class=inp># Handphone</td>
      <td class=ul>$mhsw[HandphoneOrtu] &nbsp;</td>
      </tr>
  </table></p>";
}
function KHS($mhsw) {
  HeaderAnjunganMhsw($mhsw);
  TampilkanPilihanTahunMhsw($mhsw, 'mhsw', 'KHS');
  // Data
  $s = "select k.JadwalID, k.KRSID, k.SKS, k.BobotNilai, k.GradeNilai, k.MKKode, 
    mk.Nama as Nama
    from krs k
      left outer join mk mk on k.MKID=mk.MKID
      left outer join jadwal j on j.JadwalID = k.JadwalID
    where k.MhswID='$mhsw[MhswID]'
      and k.TahunID='$_SESSION[__TahunID]'
      and (j.JenisJadwalID <> 'R' 
      or j.JenisJadwalID is NULL)
    order by k.MKKode";
  $r = _query($s);
  $khs = GetFields('khs', "MhswID='$mhsw[MhswID]' and TahunID", $_SESSION['__TahunID'], '*');
  $bal = $khs['Biaya'] - $khs['Bayar'] - $khs['Potongan'] + $khs['Tarik'];
  $_bal = number_format($bal);
  echo "<p><table class=box cellspacing=1>
  <tr><th class=ttl>#</th>
      <th class=ttl>Kode</th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl>SKS</th>
      <th class=ttl>Grade</th>
      <th class=ttl>Bobot</th>
      </tr>";
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    if ($bal > 0) {
      $w['GradeNilai'] = "<font color=red>*</font>";
      $w['BobotNilai'] = "<font color=red>*</font>";
      $ket = "<p><table class=box><tr><td class=inp><font color=red>*</font></td>
              <td class=inp>Anda masih memiliki utang sebesar : Rp.$_bal</td><tr></table></p>";
    }
    echo "<tr>
      <td class=ul>$n</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$w[SKS]</td>               
      <td class=ul align=center>$w[GradeNilai]</td>
      <td class=ul align=right>$w[BobotNilai]</td>
      </tr>";
  }
  echo "</table></p>";
  echo $ket;
  // Tampilkan rekap KHS
  echo "<p><table class=box>
  <tr><td class=inp>Total SKS</td>
    <td class=ul align=right>$khs[TotalSKS] &nbsp;</td>
    </tr>
  <tr><td class=inp>Jumlah Matakuliah</td>
    <td class=ul align=right>$khs[JumlahMK] &nbsp;</td>
    </tr>
  <tr>
    <td class=inp>IPS</td>
    <td class=ul align=right>$khs[IPS] &nbsp;</td>
    </tr>
  </table></p>";
}
function JDWL($mhsw) {
  HeaderAnjunganMhsw($mhsw);
  $thna = GetaField("tahun", "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and NA", 'N', 'TahunID');
  TampilkanPilihanJadwalMhsw($mhsw, $mnux='mhsw', $sub='JDWL');
	echo "<p>Berikut adalah jadwal Tahun Akademik: <b>$thna</b>. Silakan hubungi Tata Usaha fakultas untuk informasi lebih lanjut.</p>";
  // Tampilkan jadwal
  $s = "select j.JadwalID, j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, 
    j.HariID, j.DosenID, j.RuangID, j.JamMulai, j.JamSelesai, 
    h.Nama as HR, jj.Nama as JJ,
    concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwal j
      left outer join hari h on j.HariID=h.HariID
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
    where j.TahunID='$thna'
      and INSTR(j.ProgramID, '.$mhsw[ProgramID].')>0
      and INSTR(j.ProdiID, '.$mhsw[ProdiID].')>0
    order by j.HariID";
		
	$s0 = "select k.*, j.*, jj.Nama as JJ, d.Nama as DSN,
    sk.Nama as SK, sk.Ikut, sk.Hitung, k.SKS as SKSnya,
    time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS
    from krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join statuskrs sk on k.StatusKRSID=sk.StatusKRSID
			left outer join dosen d on j.DosenID=d.Login
    where k.MhswID = '$mhsw[MhswID]'
			and k.TahunID = '$thna'
      and k.NA='N'
    order by j.HariID, j.JamMulai, j.MKKode ";
	//echo $_SESSION['__JJadwal'];
	if ($_SESSION['__JJadwal'] == 1)  $s1 = $s0;
	else $s1 = $s;
  $r = _query($s1);
  $hdr = "<tr><th class=ttl>#</th>
      <th class=ttl>Jam</th>
      <th class=ttl>Ruang</th>
      <th class=ttl>Kode</th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl>Kelas</th>
      <th class=ttl>Kuliah</th>
      <th class=ttl>SKS</th>
      <th class=ttl>Dosen Pengampu</th>
      </tr>";
  echo "<p><table class=box cellspacing=1>";
  $hr = -1; $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      echo "<tr><td class=ttl colspan=10>$w[HR]</td></tr>";
      echo $hdr;
    }
    $jm = substr($w['JamMulai'], 0, 5);
    $js = substr($w['JamSelesai'], 0, 5);
    echo "<tr>
    <td class=ul>$n</td>
    <td class=ul>$jm - $js</td>
    <td class=ul>$w[RuangID]</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[NamaKelas]</td>
    <td class=ul>$w[JJ]</td>
    <td class=ul>$w[SKS] ($w[SKSAsli])</td>
    <td class=ul>$w[DSN]</td>
    </tr>";
  }
  echo "</table></p>";
  
}
function BIA($mhsw) {
  HeaderAnjunganMhsw($mhsw);
  TampilkanPilihanTahunMhsw($mhsw, 'mhsw', 'BIA');
  $s = "select bm.*, 
    bn.Nama, format(bm.Besar, 0) as BSR,
    format(bm.Dibayar, 0) as BYR,
    (bm.Jumlah*bm.Besar) as SUBTTL, format(bm.Jumlah * bm.Besar, 0) as _SUBTTL
    from bipotmhsw bm
      left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
      left outer join bipotnama bn on b2.BIPOTNamaID=bn.BIPOTNamaID
    where bm.MhswID='$mhsw[MhswID]'
      and bm.TahunID='$_SESSION[__TahunID]'
    order by bn.Urutan";
  $r = _query($s);
  // Tampilkan
  echo "<p><table class=bsc cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Deskripsi</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Besar</th>
    <th class=ttl>Biaya</th>
    </tr>";
  $n=0; $TTL = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $TTL += $w['SUBTTL'];
    echo "<tr><td class=ul>$n</td>
    <td class=ul>$w[Nama] &nbsp;</td>
    <td class=ul align=right>$w[Jumlah]</td>
    <td class=ul align=right>$w[BSR]</td>
    <td class=ul align=right>$w[_SUBTTL]</td>
    </tr>";
  }
  $_TTL = number_format($TTL);
  echo "<tr><td class=bsc colspan=4 align=right>TOTAL :</td>
  <td class=ttl align=right>$_TTL</td></tr>
  </table></p>";
}
function BYR($mhsw) {
  HeaderAnjunganMhsw($mhsw);
  TampilkanPilihanTahunMhsw($mhsw, 'mhsw', 'BYR');
  $s = "select bm.BayarMhswID, bm.RekeningID, bm.Tanggal, bm.Jumlah, 
    format(bm.Jumlah, 0) as JML, bm.Keterangan
    from bayarmhsw bm
    where bm.TahunID='$_SESSION[__TahunID]'
      and bm.MhswID='$mhsw[MhswID]'
    order by bm.BayarMhswID";
  $r = _query($s); $n = 0; $ttl = 0;
  echo "<p><table class=bsc cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>B P M</th>
    <th class=ttl>Tanggal</th>
    <th class=ttl>Ke Rekening</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Keterangan</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $ttl += $w['Jumlah'];
    $tgl = FormatTanggal($w['Tanggal']);
    echo "<tr><td class=ul>$n</td>
    <td class=ul>$w[BayarMhswID]</td>
    <td class=ul>$tgl</td>
    <td class=ul>$w[RekeningID]</td>
    <td class=ul align=right>$w[JML]</td>
    <td class=ul>$w[Keterangan]&nbsp;</td>
    </tr>";
  }
  $_ttl = number_format($ttl);
  echo "<tr><td colspan=4 align=right>Total :</td><td class=ul align=right>$_ttl</td></tr>";
  echo "</table>";
}
function ABSN($mhsw) {
  HeaderAnjunganMhsw($mhsw);
  $thna = GetaField("tahun", "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and NA", 'N', 'TahunID');
  echo "<p>Berikut adalah persentase absensi Anda untuk tahun akademik <b>$thna</b>. 
    Hubungi Tata Usaha jika ada data yang salah atau untuk memperoleh informasi lebih lengkap.</p>";
  $s = "select k.KRSID, k.Presensi, j.MKKode, j.Nama, j.NamaKelas, k.SKS, j.Kehadiran, j.JadwalID, j.JenisJadwalID
    from krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.TahunID='$thna'
      and k.MhswID='$mhsw[MhswID]'
    order by j.MKKode";
  $r = _query($s); $n = 0;
  echo "<p><table class=bsc cellspacing=1>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>Kelas</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Kehadiran</th>
    <th class=ttl>Persen</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $Kehadiran = GetaField('presensimhsw', "MhswID = '$mhsw[MhswID]' and JadwalID", $w['JadwalID'], "sum(Nilai)")+0;
    echo $kehadiran;
    $n++;
    $prsn = ($w['Kehadiran'] == 0)? '0' : $Kehadiran/$w['Kehadiran'] * 100;
	$fprsn = number_format($prsn,1);
    $jns = ($w['JenisJadwalID'] == 'K') ? '' : '(R)';
    echo "<tr><td class=ul>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama] $jns</td>
    <td class=ul>$w[NamaKelas] &nbsp;</td>
    <td class=ul align=right>$w[SKS]</td>
    <td class=ul align=right>$Kehadiran/$w[Kehadiran]</td>
    <td class=ul align=right>$fprsn%</td>
    </tr>";
  }
  echo "</table></p>";
  echo "<p><table class=box><tr><td><b>R</b> = <i>Responsi</i></td></tr></table></p>";
}
function KEU($mhsw) {
  HeaderAnjunganMhsw($mhsw, 'mhsw', 'KEU');
  $thna = GetaField("tahun", "ProgramID='$mhsw[ProgramID]' and ProdiID='$mhsw[ProdiID]' and NA", 'N', 'TahunID');
  TampilkanBalanceKeuanganMhsw($mhsw);
}
function TampilkanBalanceKeuanganMhsw($mhsw) {
  $s = "select k.*, sm.Nama as STA 
    from khs k
      left outer join statusmhsw sm on k.StatusMhswID=sm.StatusMhswID
    where k.MhswID='$mhsw[MhswID]'
    order by k.TahunID";
  $r = _query($s); $n=0;
  echo "<p><table class=bsc cellspacing=1>
    <tr><th class=ttl>#</th>
        <th class=ttl>Tahun</th>
        <th class=ttl>Status</th>
        <th class=ttl>SKS</th>
        <th class=ttl>Biaya</th>
        <th class=ttl>Potongan</th>
        <th class=ttl>Bayar</th>
        <th class=ttl>Tarikan</th>
        <th class=ttl>Balance</th>
    </tr>";
  $tot = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $bia = number_format($w['Biaya']);
    $byr = number_format($w['Bayar']);
    $trk = number_format($w['Tarik']);
    $pot = number_format($w['Potongan']);
    $bal = $w['Biaya'] - $w['Bayar'] - $w['Potongan'] + $w['Tarik'];
    $tot += $bal;
    $_bal = number_format($bal);
    echo "<tr><td class=ul>$n</td>
      <td class=ul>$w[TahunID]</td>
      <td class=ul>$w[STA]</td>
      <td class=ul align=right>$w[TotalSKS]</td>
      <td class=ul align=right>$bia</td>
      <td class=ul align=right>$pot</td>
      <td class=ul align=right>$byr</td>
      <td class=ul align=right>$trk</td>
      <td class=ul align=right>$_bal</td>
      </tr>";
  }
  $_tot = number_format($tot);
  echo "<tr><td colspan=8 align=right>Total :</td>
    <td class=ul align=right>$_tot</td></tr>";
  echo "</table></p>";
}
function HIST($mhsw) {
  HeaderAnjunganMhsw($mhsw, 'mhsw', 'KEU');
  // Data akan diurutkan berdasarkan
  $urt = GetSetVar('__urt', 'Sesi');
  if (empty($urt)) {
    $urt = 'Sesi';
    $_SESSION['__urt'] = $urt;
  }
  $arr = array("Sesi", "Matakuliah");
  $opturt = "";
  for ($i = 0; $i < sizeof($arr); $i++) {
    $nm = $arr[$i];
    $sel = ($urt == $nm)? 'selected' : '';
    $opturt .= "<option name='$nm' $sel>$nm</option>";
  }
  echo "<p><form action='?' method=POST>
    <input type=hidden name='mnux' value='mhsw'>
    <input type=hidden name='sub' value='HIST'>
    Urutkan daftar berdasarkan: <select name='__urt' onChange=\"this.form.submit()\">$opturt</select>
    </form></p>";
  
  // data
  if ($urt == 'Sesi') {
    $_urt = "k.TahunID, k.MKKode";
  } else {
    $_urt = "k.MKKode, k.TahunID";
  }
  $hdr = "<tr><td class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Grade</th>
    <th class=ttl>Bobot</th>
    <th class=ttl>Tahun</th>
    </tr>";
  $s = "select k.*, mk.Nama
    from krs k
      left outer join mk mk on k.MKID=mk.MKID
      left outer join jadwal j on j.JadwalID = k.JadwalID
    where k.MhswID='$mhsw[MhswID]'
        and j.JenisJadwalID = 'K'
    order by $_urt";
  $r = _query($s); $n = 0; $ss = '';
  echo "</p><table class=bsc cellspacing=1>";
  if ($urt != 'Sesi') echo $hdr;
  while ($w = _fetch_array($r)) {
    $khs = GetFields('khs', "MhswID='$mhsw[MhswID]' and TahunID", $w['TahunID'], '*');
    $bal = $khs['Biaya'] - $khs['Bayar'] - $khs['Potongan'] + $khs['Tarik'];
    $_bal = number_format($bal);
    if ($bal > 0) {
      $w['GradeNilai'] = "<font color=red>*</font>";
      $w['BobotNilai'] = "<font color=red>*</font>";
      $ket = "<p><table class=box><tr><td class=inp><font color=red>*</font></td>
              <td class=inp>Anda masih memiliki utang sebesar : Rp.$_bal</td><tr></table></p>";  
    }
    if ($urt == 'Sesi') {
      if ($ss != $w['TahunID']) {
        $ss = $w['TahunID'];
        echo "<tr><td class=ttl colspan=10>Semester: $w[TahunID]</td></tr>";
        echo $hdr;
      }
    }
    else {
    }
    $n++;
    echo "<tr>
    <td class=ul>$n</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$w[SKS]</td>
    <td class=ul align=center>$w[GradeNilai]</td>
    <td class=ul align=right>$w[BobotNilai]</td>
    <td class=ul align=right>$w[TahunID]</td>
    </tr>";
  }
  echo "</table></p>";
  echo $ket;
}
function LOGOUT() {
  ResetLogin();
  LoginMhsw();
}

// *** Parameters ***
$sub = GetSetVar('sub', 'DM');
$_SESSION['__TahunID'] = GetSetVar("__TahunID");
if (empty($_SESSION['__TahunID'])) {
  $_SESSION['__TahunID'] = GetaField('khs', "MhswID", $_SESSION['__Login'], "TahunID");
}
$__JJadwal = GetSetVar('__JJadwal', 1);

// *** Main ***
$mhsw = GetFields("mhsw m
  left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
  left outer join program prg on m.ProgramID=prg.ProgramID
  left outer join prodi prd on m.ProdiID=prd.ProdiID
  left outer join agama agm on m.Agama=agm.Agama
  left outer join kelamin kel on m.Kelamin=kel.Kelamin
  left outer join statussipil kwn on m.StatusSipil=kwn.StatusSipil", 
  "MhswID", $_SESSION['__Login'], 
  "m.*, sm.Nama as STA, 
  prg.Nama as PRG, prd.Nama as PRD, agm.Nama as AGM, kel.Nama as KEL,
  kwn.Nama as KWN");
if (!empty($mhsw)) $sub($mhsw);
?>
