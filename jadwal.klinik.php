<?php
// Author: Emanuel Setio Dewo
// 05 April 2006

include_once "krs.lib.php";

// *** Functions ***
function DftrJdwl() {
  global $arrID;
  $arrVld = GetFields('tahun',
    "ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
    $_SESSION['tahun'], '*');
  if (empty($arrVld)) {
    echo ErrorMsg("Tahun Akademik Belum Dibuat",
    "Tahun Akademik <b>$_SESSION[tahun]</b> untuk Program <b>$_SESSION[prid]</b> dan Program Studi <b>$_SESSION[prodi]</b> belum dibuat.<br />
    Hubungi Kepala Akademik/Jurusan.");
  }
  else {
    TampilkanMenuJadwal();
    TampilkanJadwal();
  }
}
function TampilkanMenuJadwal(){
  echo "<p><a name='Atas'></a><a href='?mnux=jadwal.klinik&md=1&gos=JdwlEdt&md=1'>Tambah Jadwal</a></p>";
}
function TampilkanJadwal() {
  $hdrjdwl = "<tr><th class=ttl>ID</th>
    <th class=ttl>Periode</th>
    <th class=ttl>Rumah<br />Sakit</th>
    <th class=ttl>Kode<br />Matakuliah</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Jml<br />Mhsw</th>
    <th class=ttl>Biaya</th>
    
    <th class=ttl title='Hapus Jadwal'>Hapus</th>
    <th class=ttl title='Pra KRS'>Pra</th>
    <th class=ttl title='Cetak Daftar Peserta'>Daftar</th>
    <th class=ttl title='Isi Nilai Mhsw'>Nilai</th>
    <th class=ttl title='Nilai telah difinalisasi'>Final</th>
    </tr>";
  $s = "select j.*, r.Nama as RS,
    date_format(j.TglMulai, '%d/%m/%Y') as Mulai,
    date_format(j.TglSelesai, '%d/%m/%Y') as Selesai
    from jadwal j
      left outer join mk mk on j.MKID=mk.MKID
      left outer join rumahsakit r on j.RuangID=r.RSID
    where j.NamaKelas='KLINIK' and
      j.KodeID='$_SESSION[KodeID]' and j.TahunID='$_SESSION[tahun]'
      and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
      and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
    order by j.TglMulai, j.MKKode";
  $r = _query($s);
  // Tampilkan daftar jadwal
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  echo $hdrjdwl;
  while ($w = _fetch_array($r)) {
    $c = ($w['Final'] == 'Y')? "class=nac" : "class=ul";
    // Kelas Serial
    /*$arrdosen = explode('.', TRIM($w['DosenID'], '.'));
    $strdosen = implode(',', $arrdosen);
    $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama",
      "Login", "Nama", '<br />');
    */
    $hrg = ($w['HargaStandar'] == 'Y')? "<img src='img/$w[HargaStandar].gif'>" : number_format($w['Harga']);
    echo "<tr>
      <td class=inp1><a href='?mnux=jadwal.klinik&gos=JdwlEdt&md=0&JadwalID=$w[JadwalID]'><img src='img/edit.png'>
        $w[JadwalID]</a></td>
      <td $c>$w[Mulai]-$w[Selesai]</td>
      <td $c title='$w[RS]'>$w[RuangID]</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c>$w[SKS] ($w[SKSAsli])</td>
      <td $c align=right>$w[JumlahMhsw]/$w[Kapasitas]</td>
      <td $c align=center>$hrg</td>
      
      <td $c align=center title='Hapus'>
        <a href='?mnux=jadwal.klinik&gos=JdwlDel&JadwalID=$w[JadwalID]'><img src='img/del.gif'></a></td>
      <td $c align=center title='Pra KRS'><a href='?mnux=jadwal.klinik&gos=PraKRS&JadwalID=$w[JadwalID]'><img src='img/check.gif'></a></td>
      <td $c align=center><a href='jadwal.klinik.cetak.php?gos=DftrMhswKlinik&JadwalID=$w[JadwalID]' target=_blank><img src='img/printer.gif'></a></td>
      <td $c align=center><a href='?mnux=klinik.nilai&JadwalID=$w[JadwalID]'><img src='img/check.gif'></a></td>
      <td class=ul align=center><img src='img/book$w[Final].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function JdwlEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('jadwal', "JadwalID", $_REQUEST['JadwalID'], '*');
    $jdl = "Edit Jadwal Klinik";
  }
  else {
    $w = array();
    $w['JadwalID'] = 0;
    $w['MKID'] = 0;
    $w['KodeID'] = $_SESSION['KodeID'];
    $w['ProgramID'] = $_SESSION['prid'];
    $w['ProdiID'] = $_SESSION['prodi'];
    $w['DosenID'] = '';
    $w['RuangID'] = '';
    $w['TglMulai'] = date('Y-m-d');
    $w['TglSelesai'] = date('Y-m-d');
    $w['RencanaKehadiran'] = 4;
    $w['SKS'] = 0;
    $w['SKSAsli'] = 0;
    $w['Harga'] = 0;
    $w['Kapasitas'] = 0;
    $w['NA'] = 'N';
    $jdl = "Tambah Jadwal Klinik";
  }
  $optrs = GetOption2('rumahsakit', "concat(RSID, ' - ', Nama)", 'RSID', $w['RuangID'], '', 'RSID');
  $optmk = GetOption2('mk', "concat(MKKode, ' - ', Nama, ' (', SKS, ' SKS)')",
    "MKKode", $w['MKID'], 
    "KodeID='$_SESSION[KodeID]' and ProdiID='$_SESSION[prodi]'",
    'MKID');
  $optdsn = GetOption2('dosen', "concat(Nama, ', ', Gelar)", "Nama", $w['DosenID'], "Homebase in ('10', '11')", 'Login');
  $TglMulai = GetDateOption($w['TglMulai'], 'TglMulai');
  $TglSelesai = GetDateOption($w['TglSelesai'], 'TglSelesai');
  $NA = ($w['NA'] == 'Y')? 'checked' : '';
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' name='frmJadwal' method=POST>
  <input type=hidden name='mnux' value='jadwal.klinik'>
  <input type=hidden name='tahun' value='$_SESSION[tahun]'>
  <input type=hidden name='ProdiID' value='$w[ProdiID]'>
  <input type=hidden name='ProgramID' value='$w[ProgramID]'>
  <input type=hidden name='JadwalID' value='$w[JadwalID]'>
  <input type=hidden name='slnt' value='jadwal.klinik.sav'>
  <input type=hidden name='slntx' value='JdwlSav'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>Matakuliah</td><td class=ul><select name='MKID'>$optmk</select></td></tr>
  <tr><td class=inp>Dosen Penanggung jawab</td><td class=ul><select name='DosenID'>$optdsn</select></td></tr>
  <tr><td class=inp>Rumah Sakit</td><td class=ul><select name='RSID'>$optrs</select></td></tr>
  <tr><td class=inp>Biaya</td><td class=ul><input type=text name='Harga' value='$w[Harga]' size=20 maxlength=20></td></tr>
  <tr><td class=inp>Kapasitas</td><td class=ul><input type=text name='Kapasitas' value='$w[Kapasitas]' size=4 maxlength=3></td></tr>
  
  <tr><td class=ul colspan=2><b>Periode</b></td></tr>
  <tr><td class=inp>Mulai</td><td class=ul>$TglMulai</td></tr>
  <tr><td class=inp>Durasi</td>
    <td class=ul><input type=text name='RencanaKehadiran' value='$w[RencanaKehadiran]' size=4 maxlength=4>
      <input type=hidden name='_RencanaKehadiran' value='$w[RencanaKehadiran]'> minggu</td></tr>
  <tr><td class=inp>Selesai</td><td class=ul>$TglSelesai</td></tr>
  
  <tr><td class=ul colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=jadwal.klinik'\"></td></tr>
  </table></p>";
}
function JdwlDel() {
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, "*,
    date_format(TglMulai, '%d/%m/%Y') as Mulai, date_format(TglSelesai, '%d/%m/%Y') as Selesai");
  $jmlkrs = GetaField('krs', 'JadwalID', $JadwalID, "count(*)")+0;
  // Jika sudah ada yg ambil KRS
  if ($jmlkrs > 0) {
    echo ErrorMsg("Jadwal Tidak Dapat Dihapus",
      "Jadwal tidak dapat dihapus karena telah ada: <b>$jmlkrs</b> mhsw yang mengambil matakuliah ini.");
    DftrJdwl();
  }
  else {
    $NamaRS = GetaField('rumahsakit', 'RSID', $jdwl['RuangID'], 'Nama');
    echo Konfirmasi("Konfirmasi Hapus Jadwal",
    "Benar Anda akan menghapus jadwal ini?
    <p><table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><td class=inp>Matakuliah</td><td class=ul>$jdwl[MKKode] - $jdwl[Nama]</td></tr>
    <tr><td class=inp>Rumah Sakit</td><td class=ul>$jdwl[RuangID] - $NamaRS</td></tr>
    <tr><td class=inp>Periode</td><td class=ul>$jdwl[Mulai] - $jdwl[Selesai]</td></tr>
    </table></p>
    Pilihan: <input type=button name='Hapus' value='Hapus' onClick=\"location='?mnux=jadwal.klinik&gos=JdwlDel1&JadwalID=$JadwalID'\">
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=jadwal.klinik'\">");
  }
}
function JdwlDel1() {
  $s = "delete from jadwal where JadwalID='$_REQUEST[JadwalID]' ";
  $r = _query($s);
  DftrJdwl();
}
function TampilkanHeaderJadwal($w) {
  $NamaRS = GetaField('rumahsakit', 'RSID', $w['RuangID'], 'Nama');
  $TM = FormatTanggal($w['TglMulai']);
  $TS = FormatTanggal($w['TglSelesai']);
  echo <<<EOF
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Matakuliah</td><td class=ul>$w[MKKode] - $w[Nama]</td>
    <td class=inp>SKS</td><td class=ul>$w[SKSAsli]</td></tr>
  <tr><td class=inp>Rumah Sakit</td><td class=ul>$NamaRS</td>
    <td class=inp>Kelas</td><td class=ul>$w[NamaKelas]</td></tr>
  <tr><td class=inp>Periode</td>
    <td class=ul>$TM</td>
    <td class=inp>Sampai</td>
    <td class=ul>$TS</td></tr>
  </table></p>
EOF;
}

// ***************
// *** PRA KRS ***
// ***************

function PraKRS() {
 $JadwalID = $_REQUEST['JadwalID'];
 $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
 TampilkanHeaderJadwal($jdwl);
 DaftarMhswPraKRS($jdwl);
}
function DaftarMhswPraKRS($jdwl) {
  $s = "select kp.*, m.Nama as NamaMhsw, m.MhswIDAsalPT, m.IPKAsalPT,
    date_format(m.TglLulusAsalPT, '%d/%m/%Y') as TL
    from krspra kp
      left outer join mhsw m on m.MhswID=kp.MhswID
	where kp.JadwalID='$jdwl[JadwalID]'
    order by m.MhswID";
  $r = _query($s);
  // tuliskan javascript
  echo "<script>
  function MhswBebasDong(frm) {
    lnk = \"mhsw.klinik.bebas.php?JadwalID=\"+frm.JadwalID.value;
    win2 = window.open(lnk, \"\", \"width=600, height=600, scrollbars, status\");
    win2.creator = self;
  }
  </script>";
  
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <form action='?' name='PraKRSAdd' method=POST onSubmit='return CekMhswID(this)'>
    <input type=hidden name='mnux' value='jadwal.klinik'>
    <input type=hidden name='gos' value='PraKRSMhswAdd'>
    <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
    <tr><td class=inp>Tambahkan mahasiswa</td>
      <td class=ul><textarea name='MhswID' rows=1 cols=40></textarea>
      <input type=submit name='Simpan' value='Simpan'>
      <a href='javascript:MhswBebasDong(PraKRSAdd)'>Mhsw Bebas</a>
      </td></tr>
    </form></table>";
  $arrMK = GetArrayMKKlinik($jdwl);
  $arrKD = GetArrayMKKode($arrMK);
  $kolMK = TampilkanMKKlinik($arrMK);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mhsw</th>
    <th class=ttl>Oke?</th>
    <th class=ttl>Hapus?</th>
    <th class=ttl>Tgl Lulus</th>
    <th class=ttl>IPK S1</th>
    $kolMK
    </tr>";
  $n = 0;
  
  while ($w = _fetch_array($r)) {
    $n++;
    $arrMKMhsw = GetArrMKMhsw($arrKD, $w);
    if ($w['Oke'] == 'Y') {
      $img = "<td class=ul align=center><img src='img/Y.gif'></td>";
    }
    else {
      $img = "<form action='?' name='k1' method=POST>
        <input type=hidden name='mnux' value='jadwal.klinik'>
        <input type=hidden name='gos' value='KRSOK'>
        <input type=hidden name='JadwalID' value='$jdwl[JadwalID]'>
        <input type=hidden name='KRSID' value='$w[KRSID]'>
        <input type=hidden name='Oke' value='$w[Oke]'>
        <td class=ul align=center><input type=checkbox name='OKE' value='Y' onClick='this.form.submit()'></td>
        </form>";
    }
    $del = "<a href='?mnux=jadwal.klinik&gos=PraKRSDel&JadwalID=$jdwl[JadwalID]&KRSID=$w[KRSID]'><img src='img/del.gif'></a>";
    echo "<tr>
    <td class=inp>$n</td>
    <td class=ul>$w[MhswID]</td>
    <td class=ul>$w[NamaMhsw]</td>
    $img
    <td class=ul align=center>$del</td>
    <td class=ul>$w[TL]</td>
    <td class=ul align=right>$w[IPKAsalPT]</td>
    $arrMKMhsw
    </tr>";
  }
  echo "</table></p>";
}
function PraKRSDel() {
  $krspra = GetFields('krspra', 'KRSID', $_REQUEST['KRSID'], '*');
  // Hapus KRSPRA
  $s = "delete from krspra where KRSID='$_REQUEST[KRSID]'";
  $r = _query($s);
  // Hapus KRS
  $s = "delete 
    from krs 
    where MhswID='$krspra[MhswID]'
      and TahunID='$krspra[TahunID]'
      and JadwalID='$krspra[JadwalID]'";
  $r = _query($s);
  PraKRS();
}
function GetArrayMKKode($arr) {
  $a = array();
  for ($i=0; $i < sizeof($arr); $i++) {
    $s = explode(':', $arr[$i]);
    $a[] = $s[1];
  }
  return $a;
}
function GetArrMKMhsw($arr, $m) {
  $s = "select j.MKKode, k.NilaiAkhir, k.GradeNilai
    from krs k
      left outer join jadwal j on k.JadwalID=j.JadwalID
    where k.MhswID='$m[MhswID]' and k.NA='N'
    group by j.MKKode";
  $r = _query($s);
  $a = array();
  while ($w = _fetch_array($r)) {
    $key = array_search($w['MKKode'], $arr);
    //echo $w['MKKode'] . $key." -<br />";
    $a[$key] = $w['GradeNilai'];
  }
  $str = '';
  for ($i=0; $i < sizeof($a); $i++) {
    $nilai = $a[$i];
    $n = (empty($nilai))? '-' : $nilai;
    $str .= "<td class=ul align=center>$n</td>";
  }
  return $str;
}
function GetArrayMKKlinik($jdwl) {
  $Prodi = TRIM($jdwl['ProdiID'], '.');
  $kurid = GetaField("kurikulum", "ProdiID='$Prodi' and NA", 'N', 'KurikulumID');
  $s = "select MKID, MKKode, Nama
    from mk
    where KurikulumID='$kurid' order by MKKode";
  $r = _query($s);
  $arr = array();
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[MKID]:$w[MKKode]:$w[Nama]";
  }
  return $arr;
}
function TampilkanMKKlinik($arr) {
  $a = '';
  for ($i=0; $i<sizeof($arr); $i++) {
    $dat = explode(':', $arr[$i]);
    $a .= "<th class=ttl title='$dat[2]'>$dat[1]</th>";
  }
  return $a;
}
function PraKRSMhswAdd() {
  $MhswID = $_REQUEST['MhswID'];
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  $Mhsw2 = $_REQUEST['MhswID'];
  $Mhsw2 = TRIM($Mhsw2, ',');
  $Mhsws = explode(',', $Mhsw2);
  $pesan = '<ul>';
  foreach ($Mhsws as $MhswID)
    $pesan .= SimpanPraKRS($jdwl, $MhswID);
  $pesan .= "</ul>";
  $pesan .= "<hr size=1 color=silver>
    Opsi: <a href='?mnux=$_SESSION[mnux]&gos=PraKRS&JadwalID=$JadwalID'>Kembali</a></p>";
  echo Konfirmasi("Proses Pra KRS", $pesan);
}
function SimpanPraKRS($jdwl, $MhswID) {
  $ada = GetaField('krspra', "JadwalID='$JadwalID' and MhswID", $MhswID, 'KRSID');
  $mhsw = GetFields('mhsw', 'MhswID', $MhswID, "MhswID, Nama, ProgramID, ProdiID");
  $pesan = '';
  if (empty($ada)) {
    $khs = GetFields('khs', 
      "TahunID='$jdwl[TahunID]' 
      and KodeID='$_SESSION[KodeID]' 
      and INSTR('$jdwl[ProgramID]', '.$mhsw[ProgramID].') > 0 
      and INSTR('$jdwl[ProdiID]', '.$mhsw[ProdiID].') > 0
      and MhswID", $MhswID, '*');
    if (empty($khs)) $pesan = "<li>$mhsw[MhswID]. $mhsw[Nama]: <font color=red>Tidak bisa didaftarkan
      karena belum terdaftar di <b>$jdwl[TahunID]</b></li>"; 
    else {
	  // tambahkan mhsw
      $s = "insert into krspra (KHSID, MhswID, TahunID, JadwalID,
        MKID, MKKode, SKS, HargaStandar, Harga,
        LoginBuat, TanggalBuat)
        values('$khs[KHSID]', '$MhswID', '$jdwl[TahunID]', '$jdwl[JadwalID]',
        '$jdwl[MKID]', '$jdwl[MKKode]', $jdwl[SKS], 'N', '$jdwl[Harga]',
        '$_SESSION[_Login]', now())";
      $r = _query($s);
      $pesan = "<li>$mhsw[MhswID]. $mhsw[Nama]: Berhasil didaftarkan.</li>";
    }
  }
  else $pesan = "<li>$mhsw[MhswID]. $mhsw[Nama]: <font color=red>Tidak dapat didaftarkan
    karena sudah terdaftar di jadwal ini.</li>";
  return $pesan;
}
function KRSOK() {
  $KRSID = $_REQUEST['KRSID'];
  $JadwalID = $_REQUEST['JadwalID'];
  if (!empty($_REQUEST['OKE'])) {
    $k = GetFields('krspra', 'KRSID', $KRSID, '*');
    // Update KRSPRA
    $s = "update krspra set OKE='Y' where KRSID='$KRSID' ";
    $r = _query($s);
    // insert KRS
    $s = "insert into krs (KHSID, MhswID, TahunID,
      JadwalID, MKID, MKKode, SKS, HargaStandar, Harga,
      LoginBuat, TanggalBuat)
      values ('$k[KHSID]', '$k[MhswID]', '$k[TahunID]',
      '$k[JadwalID]', '$k[MKID]', '$k[MKKode]', $k[SKS], '$k[HargaStandar]', '$k[Harga]',
      '$_SESSION[_Login]', now())";
    $r = _query($s);
    $KRS = GetLastID();
    // insert bipotmhsw
    $s1 = "insert into bipotmhsw (BIPOTMhswRef, PMBMhswID, MhswID, TahunID,
      Nama, TrxID, Jumlah, Besar, Catatan,
      LoginBuat, TanggalBuat)
      values ($KRS, 1, '$k[MhswID]', '$k[TahunID]',
      '$k[MKKode]', 1, 1, '$k[Harga]', 'KLINIK',
      '$_SESSION[_Login]', now())";
    $r1 = _query($s1);
	  // Jumlah mhsw
	  $jml = GetaField('krs', "JadwalID", $JadwalID, "count(KRSID)")+0;
	  $sx = "update jadwal set JumlahMhsw=$jml where JadwalID='$JadwalID' ";
	  $rx = _query($sx);
	  // Update jumlah mhsw
	  UpdateJumlahMhsw($JadwalID);
  }
  PraKRS();
}


// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$JadwalID = GetSetVar('JadwalID');
$gos = (empty($_REQUEST['gos']))? 'DftrJdwl' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Jadwal Klinik $tahun");
TampilkanTahunProdiProgram('jadwal.klinik', '');
if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']) && !empty($_SESSION['KodeID']) && !empty($tahun)) {
  $gos();
}
?>
