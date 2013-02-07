<?php
// Author: Emanuel Setio Dewo
// 13 April 2006

include_once "mhsw.hdr.php";

// *** Functions ***
function MhswDet() {
  $m = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join statusawal sa on m.StatusAwalID=sa.StatusAwalID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID",
    "m.MhswID", $_SESSION['mhswid'],
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT, sm.Nama as SM, sa.Nama as SA");
  $hdr = "TampilkanHeader".$_SESSION['UkuranHeader'];
  $hdr($m, 'mhsw.inq', 'MhswDet');
  return $m;
}
function inqMhswPribadi($m) {
  $agm = GetaField('agama', 'Agama', $m['Agama'], 'Nama');
  $TL = FormatTanggal($m['TanggalLahir']);
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>Pribadi</b></td></tr>
  <tr><td class=inp>Jenis Kelamin</td><td class=ul>$m[Kelamin]</td></tr>
  <tr><td class=inp>Agama</td><td class=ul>$agm</td></tr>
  <tr><td class=inp>Tempat, Tgl Lahir</td><td class=ul>$m[TempatLahir], $TL</td></tr>
  <tr><td class=inp>Warga Negara</td><td class=ul>$m[WargaNegara] $m[Kebangsaan]</td></tr>
  
  <tr><td class=ul colspan=2><b>Alamat Sesuai KTP</b></td></tr>
  <tr><td class=inp>Alamat</td><td class=ul>$m[Alamat]</td></tr>
  <tr><td class=inp>Kota, Kode Pos</td><td class=ul>$m[Kota] $m[KodePos]</td></tr>
  <tr><td class=inp>RT/RW</td><td class=ul>$m[RT]/$m[RW]</td></tr>
  <tr><td class=inp>Propinsi, Negara</td><td class=ul>$m[Propinsi], $m[Negara]</td></tr>
  <tr><td class=inp>Telepon, HP</td><td class=ul>$m[Telepon], $m[Handphone]</td></tr>
  <tr><td class=inp>E-mail</td><td class=ul>$m[Email]</td></tr>
  
  <tr><td class=ul colspan=2><b>Alamat Tinggal di Jakarta</b></td></tr>
  <tr><td class=inp>Alamat</td><td class=ul>$m[AlamatAsal]</td></tr>
  <tr><td class=inp>Kota, Kode Pos</td><td class=ul>$m[KotaAsal] $m[KodePosAsal]</td></tr>
  <tr><td class=inp>RT/RW</td><td class=ul>$m[RTAsal]/$m[RWAsal]</td></tr>
  <tr><td class=inp>Propinsi, Negara</td><td class=ul>$m[PropinsiAsal], $m[NegaraAsal]</td></tr>
  <tr><td class=inp>Telepon, HP</td><td class=ul>$m[TeleponAsal], $m[HandphoneAsal]</td></tr>
  <tr><td class=inp>E-mail</td><td class=ul>$m[Email]</td></tr>
  </table></p>";
}
function inqMhswOrtu($m) {
  // Data Ayah
  $HidupAyah = GetaField('hidup', 'Hidup', $m['HidupAyah'], 'Nama');
  $AgamaAyah = GetaField('agama', 'Agama', $m['AgamaAyah'], 'Nama');
  $PendidikanAyah = GetaField('pendidikanortu', 'Pendidikan', $m['PendidikanAyah'], 'Nama');
  $PekerjaanAyah = GetaField('pekerjaanortu', 'Pekerjaan', $m['PekerjaanAyah'], 'Nama');
  // Data Ibu
  $HidupIbu = GetaField('hidup', 'Hidup', $m['HidupIbu'], 'Nama');
  $AgamaIbu = GetaField('agama', 'Agama', $m['AgamaIbu'], 'Nama');
  $PendidikanIbu = GetaField('pendidikanortu', 'Pendidikan', $m['PendidikanIbu'], 'Nama');
  $PekerjaanIbu = GetaField('pekerjaanortu', 'Pekerjaan', $m['PekerjaanIbu'], 'Nama');
  echo "<p><table class=bsc>
  <tr>
    <td class=ul colspan=2><b>Data Ayah</b></td></tr>
    <tr><td class=inp>Nama</td><td class=ul>$m[NamaAyah]</td></tr>
    <tr><td class=inp>Status</td><td class=ul>$HidupAyah</td></tr>
    <tr><td class=inp>Agama</td><td class=ul>$AgamaAyah</td></tr>
    <tr><td class=inp>Pendidikan</td><td class=ul>$PendidikanAyah</td></tr>
    <tr><td class=inp>Pekerjaan</td><td class=ul>$PekerjaanAyah</td></tr>
    
    <td class=ul colspan=2><b>Data Ibu</b></td></tr>
    <tr><td class=inp>Nama</td><td class=ul>$m[NamaIbu]</td></tr>
    <tr><td class=inp>Status</td><td class=ul>$HidupIbu</td></tr>
    <tr><td class=inp>Agama</td><td class=ul>$AgamaIbu</td></tr>
    <tr><td class=inp>Pendidikan</td><td class=ul>$PendidikanIbu</td></tr>
    <tr><td class=inp>Pekerjaan</td><td class=ul>$PekerjaanIbu</td></tr>
  </table></p>";
}
function inqMhswAkademik($m) {
  $PMB = GetFields('pmb', 'PMBID', $m['PMBID'], "PMBFormulirID, PMBPeriodID, GradeNilai");
  $JF = GetaField('pmbformulir', 'PMBFormulirID', $PMB['PMBFormulirID'], 
    "concat(Nama, ' (', JumlahPilihan, ' pilihan, Rp. ', format(Harga, 0), ')')");
  $sesi = GetaField('khs', 'MhswID', $m['MhswID'], "max(Sesi)")+0;
  $nmsek = GetaField('asalsekolah', 'SekolahID', $m['AsalSekolah'], "concat(Nama, ', ', Kota)");
  $nmjur = GetaField('jurusansekolah', 'JurusanSekolahID', $m['JurusanSekolah'], "concat(Nama, ' - ', NamaJurusan)");
  $TL = FormatTanggal($m['TglLulusAsalPT']);
  $nmpt = GetaField('perguruantinggi', 'PerguruanTinggiID', $m['AsalPT'], "concat(Nama, ', ', Kota)");
  $Cuti = GetArrayTable("select TahunID from khs where StatusMhswID = 'C' and MhswID = '$m[MhswID]' order By TahunID", '', 'TahunID', ', ', '');
	echo "<p><table class=bsc cellspacing=1 cellpadding=4>
  <tr><td class=ul colspan=2><b>Data Akademik</b></td></tr>
  <tr><td class=inp>Program</td><td class=ul>$m[PRG]</td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul>$m[PRD]</td></tr>
  <tr><td class=inp>Status</td><td class=ul>$m[SM]</td></tr>
  <tr><td class=inp>Status Masuk</td><td class=ul>$m[SA]</td></tr>
  <tr><td class=inp>Sesi/Smt Terakhir</td><td class=ul>$sesi</td></tr>
  <tr><td class=inp>Batas Studi</td><td class=ul>$m[BatasStudi] &nbsp;</td></tr>
  <tr><td class=inp>Pernah Cuti</td><td class=ul>$Cuti &nbsp;</td></tr>
	
  <tr><td class=ul colspan=2><b>Data PMB</b></td></tr>
  <tr><td class=inp>No PMB</td><td class=ul>$m[PMBID]</td></tr>
  <tr><td class=inp>Periode</td><td class=ul>$PMB[PMBPeriodID] &nbsp;</td></tr>
  <tr><td class=inp>Jenis Formulir</td><td class=ul>$JF &nbsp;</td></tr>
  <tr><td class=inp>Grade Test</td><td class=ul>$PMB[GradeNilai] &nbsp;</td></tr>
  
  <tr><td class=ul colspan=2><b>Asal Sekolah</td></tr>
  <tr><td class=inp>Sekolah</td><td class=ul><span class=oke>$m[AsalSekolah]</span> $nmsek ($m[JenisSekolahID])&nbsp;</td></tr>
  <tr><td class=inp>Jurusan</td><td class=ul>$m[JurusanSekolah] $nmjur&nbsp;</td></tr>
  <tr><td class=inp>Nilai Sekolah</td><td class=ul>$m[NilaiSekolah]&nbsp;</td></tr>
  
  <tr><td class=ul colspan=2><b>Asal Perguruan Tinggi</td></tr>
  <tr><td class=inp>Perguruan Tinggi</td><td class=ul><span class=oke>$m[AsalPT]</span> $nmpt</td></tr>
  <tr><td class=inp>Program Studi</td><td class=ul>$m[ProdiAsalPT] &nbsp;</td></tr>
  <tr><td class=inp>Tgl Lulus</td><td class=ul>$TL &nbsp;</td></tr>
  <tr><td class=inp>IPK</td><td class=ul>$m[IPKAsalPT] &nbsp;</td></tr>
  </table></p>";
}
function inqMhswSemester_x($m) {
  $s = "select khs.*, sm.Nama as SM
    from khs khs
      left outer join statusmhsw sm on khs.StatusMhswID=sm.StatusMhswID
    where khs.MhswID='$m[MhswID]'
    order by khs.Sesi";
  $r = _query($s);
  echo "<p><table class=bsc cellspacing=1 cellpadding=4>";
  while ($w = _fetch_array($r)) {
    $krs = GetMhswKRS($w);
    echo "<tr>
      <td class=inp1>$w[Sesi]</td>
      <td class=inp>Tahun</td>
      <td class=ul>$w[TahunID]</td>
      <td class=inp>Status</td>
      <td class=ul>$w[SM]</td>
      <td class=inp>Jml MK</td>
      <td class=ul>$w[JumlahMK]</td>
      <td class=inp>Tot SKS</td>
      <td class=ul>$w[TotalSKS]</td>
      <td class=inp>IPS</td>
      <td class=ul>$w[IP]</td>
      </tr>
      <tr><td></td><td colspan=10>$krs</td></tr>";
  }
  echo "</table></p>";
}
function inqMhswSemester($m) {
  $s = "select k.*, k.GradeNilai, mk.Nama, j.NamaKelas
    from krs k
      left outer join mk mk on mk.MKID=k.MKID
      left outer join jadwal j on j.JadwalID=k.JadwalID
    where k.MhswID='$m[MhswID]' and 
      (j.JadwalSer = 0 or j.JadwalSer is NULL) and
      (j.JenisJadwalID <> 'R' or j.JenisJadwalID is NULL)
    order by k.TahunID, mk.MKKode";
  $r = _query($s);
  $a = ''; $n = 0; $thn = 'abcdefghijklmnopqrstuvwxyz';
  if (_num_rows($r)>0) {
    $hdr = "<tr><th class=ttl>#</th>
      <th class=ttl>Kode</th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl>Kelas</th>
      <th class=ttl>SKS</th>
      <th class=ttl>Grade</th>
      <th class=ttl>Bobot</th>
      </tr>";
    echo "<table class=bsc cellspacing=1 cellpadding=4>";
    while ($w = _fetch_array($r)) {
      $n++;
      if ($thn != $w['TahunID']) {
        $thn = $w['TahunID'];
        echo "<tr><td class=ul colspan=6>Semester: <font size=+1>$thn</font></td></tr>";
        echo $hdr;
      }
      echo "<tr><td class=inp>$n</td>
        <td class=ul>$w[MKKode]</td>
        <td class=ul>$w[Nama]</td>
        <td class=ul>$w[NamaKelas]</td>
        <td class=inp align=right>$w[SKS]</td>
        <td class=ul align=center>$w[GradeNilai]</td>
        <td class=inp align=right>$w[BobotNilai]</td>
        </tr>";
    }
    echo "</table></p>";
    echo "<p><table class=box cellspacing=1>
    <tr><td class=inp>Perolehan SKS</td><td class=ul>$m[TotalSKS]</td>
      <td class=inp>Index Prestasi Kumulatif</td><td class=ul>$m[IPK]</td>
      </tr>
    <tr><td class=inp>Catatan</td>
      <td class=ul colspan=3>Dihitung berdasarkan nilai tertinggi.</td></tr>
    </table></p>";
  }
}
function inqMhswMK($m) {
  $s = "select krs.*, mk.Nama, kn.KoreksiNilaiID, kn.SK, kn.Perihal, kn.GradeLama
    from krs krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join koreksinilai kn on krs.KRSID=kn.KRSID
      left outer join jadwal j on j.JadwalID=krs.JadwalID
    where krs.MhswID='$m[MhswID]' and 
       (j.JadwalSer = 0 or j.JadwalSer is NULL) and
      (j.JenisJadwalID <> 'R' or j.JenisJadwalID is NULL)
    order by krs.MKKode asc, krs.BobotNilai desc";
  $r = _query($s); $n = 0; $mk = 'abcdefghijklmnopqrstuvwxyz';
  echo "<p><table class=box cellspacing=1>
  <tr><th class=ttl>#</th>
  <th class=ttl>Kode</th>
  <th class=ttl>Matakuliah</th>
  <th class=ttl>Tahun</th>
  <th class=ttl>Nilai</th>
  <th class=ttl>Bobot</th>
  <th class=ttl>Koreksi</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    if ($mk != $w['MKKode']) {
      $mk = $w['MKKode'];
      $_mk = $mk;
      $_nm = $w['Nama'];
      $n++;
      $_n = $n;
      $c = "class=ul";
    }
    else {
      $_mk = '&nbsp;';
      $_nm = '&nbsp;';
      $_n = '&nbsp;';
      $c = "class=nac";
    }
    if (empty($w['KoreksiNilaiID'])) {
      $kn = "&nbsp;";
    }
    else {
      $kn = "($w[GradeLama]), SK: $w[SK]";
    }
    echo "<tr><td class=inp>$_n</td>
    <td class=ul>$_mk</td>
    <td class=ul>$_nm</td>
    <td $c>$w[TahunID]</td>
    <td $c>$w[GradeNilai]</td>
    <td $c align=right>$w[BobotNilai]</td>
    <td class=ul>$kn</td>
    </tr>";
  }
  echo "</table></p>";
  // Summary
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Total SKS</td><td class=ul>$m[TotalSKS]</td>
  <td class=inp>IPK</td><td class=ul>$m[IPK]</td></tr>
  </table></p>";
}
function TuliskanScriptLihatKeu() {
  echo <<<END
  <script language="javascript">
  <!--
  function LihatKeu(ID) {
    lnk = "mhsw.inq.det.popup.php?KHSID="+ID;
    win2 = window.open(lnk, "", "width=720, height=600, scrollbars, status");
    win2.creator = self;
  }
  -->
  </script>
END;
}
function inqMhswKeuangan($m) {
  TuliskanScriptLihatKeu();
  $s = "select khs.*,
    format(SaldoAwal, 0) as SALA,
    format(Biaya, 0) as BIA,
    format(Potongan, 0) as POT,
    format(Bayar, 0) as BYR,
    format(Tarik, 0) as TRK,
    format(SaldoAwal-Biaya+Potongan+Bayar-Tarik, 0) as AKH,
    (SaldoAwal-Biaya+Potongan+Bayar-Tarik) as _AKH
    from khs khs
    where khs.MhswID='$m[MhswID]'
    order by khs.Sesi";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo "<tr><th class=ttl>Sesi</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Saldo Awal</th>
    <th class=ttl>Biaya2</th>
    <th class=ttl>Potongan2</th>
    <th class=ttl>Pembayaran2</th>
    <th class=ttl>Tarikan2</th>
    <th class=ttl>Saldo Akhir</th>
    </tr>";
  $akhir = 0;
  while ($w = _fetch_array($r)) {
    $_akh = ($w['_AKH'] == 0)? 'class=oke' : 'class=wrn';
    $akhir += $w['_AKH'];
    echo "<tr>
      <td class=inp>$w[Sesi]</td>
      <td class=ul>$w[TahunID]</td>
      <td class=ul align=right>$w[SALA]</td>
      <td class=ul align=right>$w[BIA]</td>
      <td class=ul align=right>$w[POT]</td>
      <td class=ul align=right>$w[BYR]</td>
      <td class=ul align=right>$w[TRK]</td>
      <td $_akh align=right>$w[AKH]</td>
      <td class=ul align=center><a href='javascript:LihatKeu($w[KHSID])'><img src='img/zoom.png'></a></td></tr>
      </tr>";
  }
  $total = number_format($akhir);
  $c = ($akhir == 0)? 'class=ul' : 'class=wrn';
  echo "<tr><td colspan=7 align=right>Total :</td><td $c align=right><b>$total</b></td></tr>";
  echo "</table></p>";
}
function inqTugasAkhir($m) {
  // Tampilkan status mhsw
  $sk = (empty($m[SKKeluar]))? "&nbsp;" : $m['SKKeluar'] . ', ' . FormatTanggal($m['TglSKKeluar']);
  echo "<p><table class=box cellspacing=1>
  <tr><td class=inp>Status Mhsw</td>
    <td class=ul>$m[StatusMhswID] - $m[SM]</td></tr>
  <tr><td class=inp>SK Keluar/Lulus</td>
    <td class=ul>$sk</td></tr>
  </table></p>";
  
  // Tampilkan data TA
  $s = "select ta.*
    from ta ta
    where MhswID='$m[MhswID]'
    order by ta.TAID";
  $r = _query($s);
  if (_num_rows($r) > 0) {
    echo "<p><table class=bsc cellspacing=1>";
    while ($w = _fetch_array($r)) {
      if ($w['Lulus'] == 'Y') {
        $c = "class=ul";
      }
      else {
        $c = "class=nac";
      }
      $Pembimbing = GetDosenTA($w, $w['TAID'], 0);
      $Penguji = GetDosenTA($w, $w['TAID'], 1);
      $tglu = FormatTanggal($w['TglUjian']);
      echo "
      <tr><td class=inp>Judul</td>
        <td $c>$w[Judul]</td></tr>
      <tr><td class=inp>Pembimbing</td>
        <td $c>$Pembimbing</td></tr>
      <tr><td class=inp>Keterangan</td>
        <td $c>$w[Keterangan]</td></tr>
      <tr><td class=inp>Lulus?</td>
        <td $c><img src='img/$w[Lulus].gif'></td></tr>
      <tr><td class=inp>Tanggal Ujian</td>
        <td $c>$tglu</td></tr>
      <tr><td class=inp>Penguji</td>
        <td $c>$Penguji</td></tr>
      <tr><td class=inp>Nilai</td>
        <td $c>$w[GradeNilai] ($w[BobotNilai])</td></tr>
      <tr><td colspan=2><hr></td></tr>";
    }
    echo "</table></p>";
  }
  else echo Konfirmasi("Tidak Ada Data",
    "Tidak ada data tugas akhir untuk mahasiswa ini.<br />
    Mahasiswa belum mengambil tugas akhir.");
}
function GetDosenTA($m, $taid, $tipe = 0) {
  $dsn = array(0=>"Pembimbing", 1=>"Penguji");
  $a = "<ol>";
  $a .= "<li>".GetaField("dosen", "Login", $m[$dsn[$tipe]], "concat(Nama, ', ', Gelar)")."</li>";
  $s = "select d.Login, d.Nama, d.Gelar, concat(d.Nama, ', ', d.Gelar) as DSN
    from tadosen td
      left outer join dosen d on td.DosenID=d.Login
    where td.TAID=$taid and Tipe=$tipe
    order by td.TAID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $a .= "<li>$w[DSN]</li>";
  }
  return "$a</ol>";
}
function inqMhswPres($m) {
  $arrPrestasi = array(-1=>"Wanprestasi", 0=>"-", 1=>"Prestasi");
  $s = "select *
    from prestasi
    where MhswID='$m[MhswID]'
    order by JenisPrestasi, Tanggal";
  $r = _query($s);
  $jml = _num_rows($r);
  // tampilkan
  if ($jml == 0)
    echo Konfirmasi("Tidak Ada Catatan",
      "Tidak ada catatan prestasi atau tindakan untuk mahasiswa ini.");
  else {
  $n = 0;
  $_pres = -125;
  echo "<p><table class=box cellspacing=1>";
  while ($w = _fetch_array($r)) {
    if ($_pres != $w['JenisPrestasi']) {
      $_pres = $w['JenisPrestasi'];
      $JenisPrestasi = $arrPrestasi[$_pres];
      echo "<tr><td class=ul colspan=5><font size=+1>$JenisPrestasi</font></td></tr>";
      echo "<tr><th class=ttl>#</th>
        <th class=ttl>Judul</th>
        <th class=ttl>Keterangan</th>
        <th class=ttl>Tanggal</th>
        </tr>";
      $n = 0;
    }
    $n++;
    $tgl = FormatTanggal($w['Tanggal']);
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul>$w[Judul]</td>
      <td class=ul>$w[Keterangan]</td>
      <td class=ul>$tgl</td>
      </tr>";
  }
  echo "</table></p>";
  }
}

// *** Sub Menu ***
$arrMenuInqMhsw = array("Data Pribadi->inqMhswPribadi",
  "Orang Tua->inqMhswOrtu",
  "Data Akademik->inqMhswAkademik",
  "History Semester->inqMhswSemester",
  "History Matakuliah->inqMhswMK",
  "Prestasi & Wanprestasi->inqMhswPres",
  "Tugas Akhir->inqTugasAkhir",
  "Keuangan->inqMhswKeuangan");

// *** Parameters ***
$mhswid = GetSetVar('mhswid');
$UkuranHeader = GetSetVar('UkuranHeader', 'Besar');
$inqMhsw = GetSetVar('inqMhsw', 'inqMhswPribadi');
$gos = empty($_REQUEST['gos'])? 'inqMhswPribadi' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Detail Mahasiswa");
if (!empty($_SESSION['mhswid'])) {
  $m = MhswDet();  
  TampilkanSubMenu('mhsw.inq.det', $arrMenuInqMhsw, 'inqMhsw', $inqMhsw);
  $inqMhsw($m);
}
?>
